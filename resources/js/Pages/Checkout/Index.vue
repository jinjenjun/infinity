<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import '@/../scss/main.scss';

const props = defineProps({
    cart: Object,
    total: Number,
    user: Object,
});

const form = useForm({
    recipient_name:    props.user.name ?? '',
    recipient_phone:   '',
    recipient_address: '',
});

const submit = () => {
    form.post(route('checkout.store'));
};
</script>

<template>
    <Head title="結帳" />

    <PlatformLayout>
        <div class="blur-transition py-10 px-2 min-h-[60vh]">

            <h1 class="mb-8 text-2xl font-bold color-primary border-b border-gray-200 pb-4">
                結帳
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                <!-- 左：收件資訊 -->
                <div>
                    <h2 class="text-base font-semibold color-primary mb-5">收件資訊</h2>

                    <div class="space-y-5">
                        <!-- 姓名 -->
                        <div>
                            <label class="block text-sm color-dark-gray mb-1">收件人姓名</label>
                            <input
                                v-model="form.recipient_name"
                                type="text"
                                class="w-full rounded px-4 py-2 text-sm"
                                placeholder="請輸入姓名"
                            />
                            <p v-if="form.errors.recipient_name" class="mt-1 text-xs color-alert-danger">
                                {{ form.errors.recipient_name }}
                            </p>
                        </div>

                        <!-- 電話 -->
                        <div>
                            <label class="block text-sm color-dark-gray mb-1">聯絡電話</label>
                            <input
                                v-model="form.recipient_phone"
                                type="tel"
                                class="w-full rounded px-4 py-2 text-sm"
                                placeholder="請輸入電話"
                            />
                            <p v-if="form.errors.recipient_phone" class="mt-1 text-xs color-alert-danger">
                                {{ form.errors.recipient_phone }}
                            </p>
                        </div>

                        <!-- 地址 -->
                        <div>
                            <label class="block text-sm color-dark-gray mb-1">收件地址</label>
                            <textarea
                                v-model="form.recipient_address"
                                rows="3"
                                class="w-full rounded px-4 py-2 text-sm"
                                placeholder="請輸入地址"
                            />
                            <p v-if="form.errors.recipient_address" class="mt-1 text-xs color-alert-danger">
                                {{ form.errors.recipient_address }}
                            </p>
                        </div>

                        <!-- 付款方式 -->
                        <div>
                            <label class="block text-sm color-dark-gray mb-1">付款方式</label>
                            <div class="flex items-center gap-2 border border-gray-200 rounded px-4 py-3 bg-gray-50">
                                <span class="text-sm color-primary font-medium">💳 信用卡（綠界金流）</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 右：訂單摘要 -->
                <div>
                    <h2 class="text-base font-semibold color-primary mb-5">訂單摘要</h2>

                    <div class="border border-gray-100 rounded-lg overflow-hidden">
                        <div
                            v-for="item in cart.items"
                            :key="item.id"
                            class="flex items-center gap-4 px-4 py-4 border-b border-gray-100"
                        >
                            <img
                                v-if="item.product.image"
                                :src="`/storage/${item.product.image}`"
                                :alt="item.product.name"
                                class="w-12 h-12 object-cover rounded"
                            />
                            <div v-else class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center text-xs color-dark-gray">
                                無圖
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium color-primary">{{ item.product.name }}</p>
                                <p class="text-xs color-dark-gray">x {{ item.quantity }}</p>
                            </div>
                            <p class="text-sm font-semibold color-primary">
                                ${{ (item.price * item.quantity).toLocaleString() }}
                            </p>
                        </div>

                        <!-- 合計 -->
                        <div class="flex justify-between items-center px-4 py-4 bg-gray-50">
                            <span class="text-sm color-dark-gray">訂單合計</span>
                            <span class="text-xl font-bold color-primary">
                                ${{ Number(total).toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <!-- 送出按鈕 -->
                    <button
                        class="mt-6 w-full bg-primary text-white py-3 rounded font-semibold hover:opacity-80 transition tracking-wide"
                        :class="{ 'disable-execution': form.processing }"
                        @click="submit"
                    >
                        <span v-if="form.processing">處理中...</span>
                        <span v-else>確認結帳，前往付款 →</span>
                    </button>

                    <p class="mt-3 text-xs color-dark-gray text-center">
                        點擊後將導向綠界信用卡付款頁面
                    </p>
                </div>

            </div>
        </div>
    </PlatformLayout>
</template>