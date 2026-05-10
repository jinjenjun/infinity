<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import '@/../scss/main.scss';

const props = defineProps({
    cart: Object,
    total: Number,
});

const updateQuantity = (itemId, quantity) => {
    if (quantity < 1) return;
    router.patch(route('cart.update', itemId), { quantity }, {
        preserveScroll: true,
    });
};

const removeItem = (itemId) => {
    router.delete(route('cart.remove', itemId), {
        preserveScroll: true,
    });
};

const goCheckout = () => {
    router.get(route('checkout.index'));
};
</script>

<template>
    <Head title="購物車" />

    <PlatformLayout>
        <div class="blur-transition min-h-[60vh] py-10 px-2">

            <!-- 標題 -->
            <h1 class="mb-8 text-2xl font-bold color-primary border-b border-gray-200 pb-4">
                購物車
            </h1>

            <!-- 空購物車 -->
            <div
                v-if="!cart.items || cart.items.length === 0"
                class="flex flex-col items-center justify-center py-24 text-center"
            >
                <p class="text-lg color-dark-gray mb-6">購物車是空的</p>
                <Link
                    :href="route('products.index')"
                    class="bg-primary text-white px-6 py-2 rounded text-sm hover:opacity-80 transition"
                >
                    繼續購物
                </Link>
            </div>

            <!-- 購物車內容 -->
            <div v-else>

                <!-- 欄位標題（桌機） -->
                <div class="hidden md:grid grid-cols-12 gap-4 text-sm color-dark-gray border-b border-gray-200 pb-3 mb-2">
                    <div class="col-span-5">商品</div>
                    <div class="col-span-2 text-center">單價</div>
                    <div class="col-span-3 text-center">數量</div>
                    <div class="col-span-1 text-right">小計</div>
                    <div class="col-span-1"></div>
                </div>

                <!-- 商品列表 -->
                <div
                    v-for="item in cart.items"
                    :key="item.id"
                    class="grid grid-cols-12 items-center gap-4 border-b border-gray-100 py-5"
                >
                    <!-- 商品圖 + 名稱 -->
                    <div class="col-span-12 md:col-span-5 flex items-center gap-4">
                        <img
                            v-if="item.product.image"
                            :src="`/storage/${item.product.image}`"
                            :alt="item.product.name"
                            class="w-16 h-16 object-cover rounded shadow-sm"
                        />
                        <div
                            v-else
                            class="w-16 h-16 rounded bg-gray-100 flex items-center justify-center text-xs color-dark-gray"
                        >
                            無圖片
                        </div>
                        <div>
                            <p class="font-semibold color-primary">{{ item.product.name }}</p>
                            <p class="text-xs color-dark-gray md:hidden mt-1">
                                單價：${{ Number(item.price).toLocaleString() }}
                            </p>
                        </div>
                    </div>

                    <!-- 單價（桌機） -->
                    <div class="hidden md:flex col-span-2 justify-center text-sm">
                        ${{ Number(item.price).toLocaleString() }}
                    </div>

                    <!-- 數量控制 -->
                    <div class="col-span-7 md:col-span-3 flex items-center justify-center gap-3">
                        <button
                            class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center text-lg leading-none hover:border-primary hover:color-primary transition"
                            @click="updateQuantity(item.id, item.quantity - 1)"
                        >−</button>
                        <span class="w-6 text-center font-medium">{{ item.quantity }}</span>
                        <button
                            class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center text-lg leading-none hover:border-primary hover:color-primary transition"
                            @click="updateQuantity(item.id, item.quantity + 1)"
                        >+</button>
                    </div>

                    <!-- 小計 -->
                    <div class="col-span-4 md:col-span-1 text-right font-bold text-sm color-primary">
                        ${{ (item.price * item.quantity).toLocaleString() }}
                    </div>

                    <!-- 刪除 -->
                    <div class="col-span-1 flex justify-end">
                        <button
                            class="color-dark-gray hover:text-red-500 transition text-lg"
                            title="移除"
                            @click="removeItem(item.id)"
                        >✕</button>
                    </div>
                </div>

                <!-- 合計 + 結帳 -->
                <div class="mt-10 flex flex-col items-end gap-5">
                    <div class="text-right">
                        <p class="text-sm color-dark-gray mb-1">訂單合計</p>
                        <p class="text-3xl font-bold color-primary">
                            ${{ Number(total).toLocaleString() }}
                        </p>
                    </div>
                    <button
                        class="bg-primary text-white px-10 py-3 rounded font-semibold hover:opacity-80 transition tracking-wide"
                        @click="goCheckout"
                    >
                        前往結帳 →
                    </button>
                    <Link
                        :href="route('products.index')"
                        class="text-sm color-dark-gray underline hover:color-primary transition"
                    >
                        繼續購物
                    </Link>
                </div>

            </div>
        </div>
    </PlatformLayout>
</template>