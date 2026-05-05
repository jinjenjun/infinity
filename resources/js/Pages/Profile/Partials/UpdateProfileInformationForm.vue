<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone ?? '',
    address: user.address ?? '',
    birthday: user.birthday ?? '',
    gender: user.gender ?? '',
    age: user.age ?? '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                個人資料
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                更新您的個人資料與電子信箱。
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <!-- Name -->
            <div>
                <InputLabel for="name" value="姓名" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="電子信箱" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Phone -->
            <div>
                <InputLabel for="phone" value="電話號碼" />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    maxlength="20"
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <!-- Address -->
            <div>
                <InputLabel for="address" value="地址" />
                <TextInput
                    id="address"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.address"
                    autocomplete="street-address"
                />
                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <!-- Birthday -->
            <div>
                <InputLabel for="birthday" value="生日" />
                <TextInput
                    id="birthday"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.birthday"
                />
                <InputError class="mt-2" :message="form.errors.birthday" />
            </div>

            <!-- Gender -->
            <div>
                <InputLabel for="gender" value="性別" />
                <select
                    id="gender"
                    v-model="form.gender"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">請選擇</option>
                    <option value="1">男</option>
                    <option value="0">女</option>
                    <option value="2">其他</option>
                </select>
                <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <!-- Age -->
            <div>
                <InputLabel for="age" value="年齡" />
                <TextInput
                    id="age"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.age"
                    min="0"
                    max="150"
                />
                <InputError class="mt-2" :message="form.errors.age" />
            </div>

            <!-- Email 驗證提示 -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    您的電子信箱尚未驗證。
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        點此重新發送驗證信。
                    </Link>
                </p>
                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    驗證信已重新發送至您的電子信箱。
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">儲存</PrimaryButton>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                        已儲存。
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>