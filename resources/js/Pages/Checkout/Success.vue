<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import '@/../scss/main.scss';

const props = defineProps({
    order: Object,
});

const statusMap = {
    pending:   { label: '待付款', class: 'color-alert-danger' },
    paid:      { label: '已付款', class: 'color-alert-success' },
    failed:    { label: '付款失敗', class: 'color-alert-danger' },
    cancelled: { label: '已取消', class: 'color-dark-gray' },
};

const status = statusMap[props.order.status] ?? { label: props.order.status, class: '' };

const downloadPdf = () => {
    window.open(route('orders.pdf', props.order.id), '_blank');
};
</script>

<template>
    <Head title="訂單確認" />

    <PlatformLayout>
        <div class="blur-transition py-10 px-2 min-h-[60vh]">

            <!-- 標題區 -->
            <div class="mb-10 text-center">
                <div class="mb-4 text-5xl">🎉</div>
                <h1 class="text-2xl font-bold color-primary">訂單已建立！</h1>
                <p class="mt-2 text-sm color-dark-gray">感謝您的購買，以下是您的訂單明細。</p>
            </div>

            <div class="mx-auto max-w-2xl">

                <!-- 訂單狀態卡 -->
                <div class="mb-6 rounded-lg border border-gray-100 bg-gray-50 px-6 py-5">
                    <div class="flex flex-wrap justify-between gap-4 text-sm">
                        <div>
                            <p class="color-dark-gray mb-1">訂單編號</p>
                            <p class="font-bold color-primary tracking-wide">{{ order.order_number }}</p>
                        </div>
                        <div>
                            <p class="color-dark-gray mb-1">訂單狀態</p>
                            <p class="font-bold" :class="status.class">{{ status.label }}</p>
                        </div>
                        <div>
                            <p class="color-dark-gray mb-1">付款方式</p>
                            <p class="font-bold color-primary">信用卡</p>
                        </div>
                        <div>
                            <p class="color-dark-gray mb-1">訂單合計</p>
                            <p class="text-xl font-bold color-primary">
                                NT$ {{ Number(order.total).toLocaleString() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 收件資訊 -->
                <div class="mb-6 rounded-lg border border-gray-100 px-6 py-5">
                    <h2 class="mb-4 text-sm font-semibold color-primary border-l-4 border-primary pl-3"
                        style="border-color: #4b2c25;">
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
                    <h2 class="mb-4 text-sm font-semibold color-primary border-l-4 pl-3"
                        style="border-color: #4b2c25;">
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

                    <div class="flex justify-between items-center pt-4 text-base font-bold color-primary">
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
                        :href="route('products.index')"
                        class="flex-1 text-center border border-gray-300 py-3 rounded font-semibold color-dark-gray hover:color-primary hover:border-primary transition"
                    >
                        繼續購物
                    </Link>
                </div>

            </div>
        </div>
    </PlatformLayout>
</template>