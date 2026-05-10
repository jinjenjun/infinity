<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import '@/../scss/main.scss';

const props = defineProps({
    orders: Array,
    isAdmin: Boolean,
    isSuperAdmin: Boolean,
});

const statusMap = {
    pending:   { label: '待付款', class: 'bg-yellow-100 text-yellow-700' },
    paid:      { label: '已付款', class: 'bg-green-100 text-green-700' },
    failed:    { label: '付款失敗', class: 'bg-red-100 text-red-700' },
    cancelled: { label: '已取消', class: 'bg-gray-100 text-gray-500' },
};
</script>

<template>
    <Head title="訂單管理" />

    <PlatformLayout>
        <div class="blur-transition py-10 px-2 min-h-[60vh]">

            <h1 class="mb-8 text-2xl font-bold color-primary border-b border-gray-200 pb-4">
                {{ isAdmin ? '訂單管理' : '我的訂單' }}
            </h1>

            <!-- 無訂單 -->
            <div v-if="orders.length === 0" class="py-24 text-center color-dark-gray">
                <p class="text-lg">目前沒有訂單</p>
                <Link :href="route('products.index')" class="mt-4 inline-block text-sm underline hover:color-primary">
                    去逛逛商品
                </Link>
            </div>

            <!-- 訂單列表 -->
            <div v-else class="space-y-4">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="rounded-lg border border-gray-100 px-6 py-5 hover:border-gray-300 transition"
                >
                    <div class="flex flex-wrap justify-between items-start gap-4">

                        <div class="space-y-1">
                            <p class="font-bold color-primary tracking-wide">{{ order.order_number }}</p>
                            <p class="text-xs color-dark-gray">
                                {{ new Date(order.created_at).toLocaleString('zh-TW') }}
                            </p>
                            <p v-if="isAdmin" class="text-xs color-dark-gray">
                                購買人：{{ order.user?.name }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="statusMap[order.status]?.class"
                            >
                                {{ statusMap[order.status]?.label }}
                            </span>
                            <p class="font-bold color-primary">
                                NT$ {{ Number(order.total).toLocaleString() }}
                            </p>
                            <Link
                                :href="route('orders.show', order.id)"
                                class="text-sm underline color-dark-gray hover:color-primary transition"
                            >
                                查看明細
                            </Link>
                        </div>

                    </div>

                    <!-- 商品簡覽 -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span
                            v-for="item in order.items"
                            :key="item.id"
                            class="rounded bg-gray-100 px-2 py-1 text-xs color-dark-gray"
                        >
                            {{ item.product_name }} x{{ item.quantity }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </PlatformLayout>
</template>