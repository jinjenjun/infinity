<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import ElInfoButton from '@/Components/ElInfoButton.vue';
import '@/../scss/main.scss';

const { auth } = usePage().props;
</script>

<template>
    <Head title="Dashboard" />

    <PlatformLayout>
        <div class="text-card-title mt-5 px-5">
          <p>歡迎加入，{{ auth.user.name }} (權限等級：{{ auth.roles.join(', ') }})</p>
        </div>
    </PlatformLayout>
</template>