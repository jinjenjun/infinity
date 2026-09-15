<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { Head } from '@inertiajs/vue3';
import { ElNotification } from 'element-plus';
import { useStore } from 'vuex';
import { vuexData } from '@/../store';
import * as APIs from '@/APIs';
import * as helpers from '@/Libs/helpers';
import * as validation from '@/Libs/validation.js';
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import ElIconButton from '@/Components/ElIconButton.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    adImgUrl: {
        type: String,
    },
});

const store = useStore();

const form = ref({
    email: '',
    password: '',
    remember: false,
});

const checkNameList = {
    email: '電子郵件',
    password: '密碼',
};

const checkErrorList = ref({
    emailError: computed(() =>
        validation.messageContent.checkEmail(form.value.email, checkNameList.email),
    ),
    passwordError: computed(() =>
        validation.messageContent.checkPassword(form.value.password, checkNameList.password),
    ),
});

// 後端回傳、綁定在特定欄位的錯誤（例如登入失敗、帳號鎖定）
const serverErrorList = ref({
    email: '',
    password: '',
});

// 非欄位型錯誤（session 過期、伺服器錯誤、網路異常）
const generalError = ref('');

const checkStatus = ref(false);
const processing = ref(false);

const showPassword = ref(false);
const showBorderStatus = ref({
    password: false,
});

const clearErrors = () => {
    serverErrorList.value = { email: '', password: '' };
    generalError.value = '';
};

const applyResponseError = (error) => {
    const statusCode = error?.response?.status;
    const errors = error?.response?.data?.errors;

    if (statusCode === 422 && errors) {
        serverErrorList.value.email = errors.email?.[0] || '';
        serverErrorList.value.password = errors.password?.[0] || '';
        return;
    }

    if (statusCode === 419) {
        generalError.value = '登入狀態已過期，請重新整理頁面後再試一次。';
        return;
    }

    if (!error?.response) {
        generalError.value = '無法連線到伺服器，請確認網路連線後再試一次。';
        return;
    }

    generalError.value = '系統發生錯誤，請稍後再試。';
};

const submit = () => {
    if (processing.value) {
        return;
    }

    checkStatus.value = true;
    clearErrors();

    const noErrors = Object.values(checkErrorList.value).every((error) => error === '');
    if (!noErrors) {
        return;
    }

    processing.value = true;

    APIs.unlock.account
        .login(form.value)
        .then((res) => {
            ElNotification.success({
                title: '登入成功',
                offset: 100,
            });

            vuexData.unlock.member.setLoginData(store, {
                email: form.value.remember ? form.value.email : '',
                remember: form.value.remember,
            });

            // 延遲導頁，讓「登入成功」通知有時間顯示出來再整頁跳轉
            setTimeout(() => {
                helpers.forwardRoute(res?.data?.redirect || route('home'));
            }, 2000);
        })
        .catch((error) => {
            applyResponseError(error);
            processing.value = false;
        });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const showBorder = (e) => {
    if (e.target.closest('.password-field')) {
        showBorderStatus.value.password = true;
    }
};

const hideBorder = (e) => {
    if (!e.target.closest('.password-field')) {
        showBorderStatus.value.password = false;
    }
};

const emailSanitize = (event) => {
    form.value.email = helpers.sanitizeEmailInput(event.target.value);
};

onMounted(() => {
  document.addEventListener('click', hideBorder);

  const rememberData = vuexData.unlock.member.getLoginData(store);
  if (rememberData.remember) {
    Object.assign(form, rememberData);
  }
});

onBeforeUnmount(() => {
  document.removeEventListener('click', hideBorder);
});
</script>

<template>
    <PlatformLayout>
        <Head title="會員登入" />

        <div class="grid grid-cols-12" @click="hideBorder">
            <div
                class="col-span-12 flex items-start justify-center px-[5vw] py-5 md:col-span-5 lg:py-[30px]"
            >
                <div class="w-full">
                    <div class="text-card-title mt-0 flex flex-col items-start justify-start lg:mt-[60px]">
                        <p class="text-page-title">會員登入</p>
                        <div class="text-card-description mt-[30px] flex items-center justify-center">
                            <p class="mr-4">還沒註冊嗎？</p>
                            <a :href="route('register')" class="text-secondary underline">立即註冊</a>
                        </div>

                        <div v-if="status" class="mt-4 text-sm font-medium text-green-600">
                            {{ status }}
                        </div>

                        <div
                            v-if="generalError"
                            class="color-alert-danger text-card-description mt-4 w-full rounded border border-current p-3"
                        >
                            {{ generalError }}
                        </div>

                        <div class="mt-5 flex w-full flex-col items-start justify-start">
                            <label for="email">Email</label>
                            <input
                                id="email"
                                type="email"
                                class="mt-2 w-full rounded"
                                :value="form.email"
                                @input="emailSanitize"
                                autocomplete="username"
                            />
                            <span
                                class="color-alert-danger text-card-description mt-2"
                                v-if="checkStatus && checkErrorList.emailError"
                                >{{ checkErrorList.emailError }}</span
                            >
                            <span
                                class="color-alert-danger text-card-description mt-2"
                                v-else-if="serverErrorList.email"
                                >{{ serverErrorList.email }}</span
                            >
                        </div>

                        <div class="mt-5 flex w-full flex-col items-start justify-start">
                            <label for="password">密碼</label>
                            <span
                                class="password-field mt-2 flex w-full items-center justify-center rounded border bg-white"
                                :class="
                                    showBorderStatus.password ? 'border-secondary border-2' : 'border-dark-gray'
                                "
                                @click="showBorder"
                            >
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="password m-1 w-full !border-none !outline-none px-2 py-1 focus:!border-none focus:!outline-none"
                                    v-model="form.password"
                                    autocomplete="current-password"
                                    @keyup.enter="submit"
                                />
                                <i
                                    class="color-dark-gray mx-2 cursor-pointer text-xl font-normal"
                                    :class="showPassword ? 'ri-eye-line' : 'ri-eye-close-line'"
                                    @click="togglePassword"
                                />
                            </span>
                            <span
                                class="color-alert-danger text-card-description mt-2"
                                v-if="checkStatus && checkErrorList.passwordError"
                                >{{ checkErrorList.passwordError }}</span
                            >
                            <span
                                class="color-alert-danger text-card-description mt-2"
                                v-else-if="serverErrorList.password"
                                >{{ serverErrorList.password }}</span
                            >
                        </div>

                        <div class="mt-5 flex w-full items-center justify-between">
                            <span class="flex items-center justify-start">
                                <ElCheckbox v-model="form.remember"
                                    ><span class="text-primary text-card-description font-bold"
                                        >記住我</span
                                    ></ElCheckbox
                                >
                            </span>
                            <a
                                :href="route('password.request')"
                                class="text-secondary text-card-description font-bold underline"
                                v-if="canResetPassword"
                                >忘記密碼？</a
                            >
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col items-start justify-start font-bold">
                        <ElIconButton
                            class="mt-3 w-full"
                            :icon-button-prop="{ name: '立即登入', disable: processing }"
                            @click="submit"
                        />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center md:col-span-7">
                <div
                    class="hidden w-full bg-cover bg-center bg-no-repeat md:block"
                    :style="{
                        backgroundImage: adImgUrl ? `url(${adImgUrl})` : undefined,
                        paddingTop: '133.33%',
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                    }"
                />
            </div>
        </div>
    </PlatformLayout>
</template>
