<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    admins: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({ invite_code: '' });

const submit = () => {
    form.post(route('profile.invite-code'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">會員邀請碼</h2>
            <p class="mt-1 text-sm text-gray-600">
                輸入邀請碼即可加入該管理者的會員，解鎖對方的會員專屬商品。
            </p>
        </header>

        <p v-if="admins.length" class="mt-4 text-sm text-gray-700">
            目前所屬：{{ admins.join('、') }}
        </p>

        <form class="mt-6 space-y-6" @submit.prevent="submit">
            <div>
                <InputLabel for="join_invite_code" value="邀請碼" />
                <TextInput
                    id="join_invite_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.invite_code"
                    placeholder="例如：ABCD-EFGH"
                    style="text-transform: uppercase"
                />
                <InputError class="mt-2" :message="form.errors.invite_code" />
            </div>

            <PrimaryButton :disabled="form.processing">加入</PrimaryButton>
        </form>
    </section>
</template>
