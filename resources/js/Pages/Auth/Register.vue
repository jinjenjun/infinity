<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    birthday: '',
    gender: '',  
    age: '',     
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
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
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
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
            <div class="mt-4">
                <InputLabel for="phone" value="電話號碼" />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    autocomplete="tel"
                    maxlength="20"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <!-- Address -->
            <div class="mt-4">
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
            <div class="mt-4">
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
            <div class="mt-4">
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
            <div class="mt-4">
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

            <!-- Password -->
            <div class="mt-4">
                <InputLabel for="password" value="密碼（8~12碼）" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    minlength="8"
                    maxlength="12"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="確認密碼" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    minlength="8"
                    maxlength="12"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit -->
            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>