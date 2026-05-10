<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import '@/../scss/main.scss';

const props = defineProps({
    order: Object,
    isAdmin: Boolean,
    isSuperAdmin: Boolean,
});

const statusMap = {
    pending:   { label: '待付款', class: 'bg-yellow-100 text-yellow-700' },
    paid:      { label: '已付款', class: 'bg-green-100 text-green-700' },
    failed:    { label: '付款失敗', class: 'bg-red-100 text-red-700' },
    cancelled: { label: '已取消', class: 'bg-gray-100 text-gray-500' },
};

const status = statusMap[props.order.status];

const downloadPdf = () => {
    window.open(route('orders.pdf', props.order.id), '_blank');
};
</script>

<template>
    <Head :title="`訂單 ${order.order_number}`" />

    <PlatformLayout>
        <div class="blur-transition py-10 px-2 min-h-[60vh]">

            <Link
                :href="route('orders.index')"
                class="mb-8 inline-flex items-center text-sm color-dark-gray hover:color-primary transition"
            >
                ← 返回訂單列表
            </Link>

            <div class="mx-auto max-w-2xl mt-4">

                <h1 class="mb-6 text-2xl font-bold color-primary border-b border-gray-200 pb-4">
                    訂單明細
                </h1>

                <!-- 訂單狀態卡 -->
                <div class="mb-6 rounded-lg border border-gray-100 bg-gray-50 px-6 py-5">
                    <div class="flex flex-wrap justify-between gap-4 text-sm">
                        <div>
                            <p class="color-dark-gray mb-1">訂單編號</p>
                            <p class="font-bold color-primary tracking-wide">{{ order.order_number }}</p>
                        </div>
                        <div>
                            <p class="color-dark-gray mb-1">訂單狀態</p>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="status?.class">
                                {{ status?.label }}
                            </span>
                        </div>
                        <div>
                            <p class="color-dark-gray mb-1">建立時間</p>
                            <p class="font-medium color-primary">
                                {{ new Date(order.created_at).toLocaleString('zh-TW') }}
                            </p>
                        </div>
                        <div>
                            <p class="color-dark-gray mb-1">訂單合計</p>
                            <p class="text-xl font-bold color-primary">
                                NT$ {{ Number(order.total).toLocaleString() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- admin 顯示購買人 -->
                <div v-if="isAdmin" class="mb-6 rounded-lg border border-gray-100 px-6 py-5">
                    <h2 class="mb-4 text-sm font-semibold color-primary border-l-4 pl-3" style="border-color:#4b2c25;">
                        購買人資訊
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex gap-4">
                            <span class="color-dark-gray w-16">姓名</span>
                            <span class="color-primary">{{ order.user?.name }}</span>
                        </div>
                        <div class="flex gap-4">
                            <span class="color-dark-gray w-16">Email</span>
                            <span class="color-primary">{{ order.user?.email }}</span>
                        </div>
                    </div>
                </div>

                <!-- 收件資訊 -->
                <div class="mb-6 rounded-lg border border-gray-100 px-6 py-5">
                    <h2 class="mb-4 text-sm font-semibold color-primary border-l-4 pl-3" style="border-color:#4b2c25;">
                        收件資訊
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex gap-4">
                            <span class="color-dark-gray w-16">收件人</span>
                            <span class="color-primary">{{ order.recipient_name }}</span>
                        </div>
                        <div class="flex gap-4">
                            <span class="color-dark-gray w-16">電話</span>
                            <span class="color-primary">{{ order.recipient_phone }}</span>
                        </div>
                        <div class="flex gap-4">
                            <span class="color-dark-gray w-16">地址</span>
                            <span class="color-primary">{{ order.recipient_address }}</span>
                        </div>
                    </div>
                </div>

                <!-- 商品明細 -->
                <div class="mb-6 rounded-lg border border-gray-100 px-6 py-5">
                    <h2 class="mb-4 text-sm font-semibold color-primary border-l-4 pl-3" style="border-color:#4b2c25;">
                        商品明細
                    </h2>
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="flex justify-between items-center py-3 border-b border-gray-100 text-sm"
                    >
                        <div>
                            <p class="font-medium color-primary">{{ item.product_name }}</p>
                            <p class="text-xs color-dark-gray">x {{ item.quantity }}</p>
                        </div>
                        <p class="font-semibold color-primary">
                            NT$ {{ (item.price * item.quantity).toLocaleString() }}
                        </p>
                    </div>

                    <div class="flex justify-between items-center pt-4 font-bold color-primary">
                        <span>合計</span>
                        <span>NT$ {{ Number(order.total).toLocaleString() }}</span>
                    </div>
                </div>

                <!-- 操作按鈕 -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button
                        class="flex-1 bg-primary text-white py-3 rounded font-semibold hover:opacity-80 transition"
                        @click="downloadPdf"
                    >
                        下載 PDF 帳單
                    </button>
                    <Link
                        :href="route('orders.index')"
                        class="flex-1 text-center border border-gray-300 py-3 rounded font-semibold color-dark-gray hover:color-primary hover:border-primary transition"
                    >
                        返回訂單列表
                    </Link>
                </div>

            </div>
        </div>
    </PlatformLayout>
</template>