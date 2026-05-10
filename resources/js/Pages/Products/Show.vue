<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import '@/../scss/main.scss';

const props = defineProps({
    product: Object,
});

const quantity = ref(1);
const loading = ref(false);

const addToCart = () => {
    if (loading.value) return;
    loading.value = true;

    router.post(route('cart.add'), {
        product_id: props.product.id,
        quantity: quantity.value,
    }, {
        preserveScroll: true,
        onFinish: () => { loading.value = false; },
    });
};

const decrease = () => { if (quantity.value > 1) quantity.value--; };
const increase = () => { if (quantity.value < props.product.stock) quantity.value++; };
</script>

<template>
    <Head :title="product.name" />
    <PlatformLayout>
        <div class="blur-transition py-10 px-2">

            <!-- 返回按鈕 -->
            <Link
                :href="route('products.index')"
                class="mb-8 inline-flex items-center text-sm color-dark-gray hover:color-primary transition"
            >
                ← 返回商品列表
            </Link>

            <div class="mt-4 grid grid-cols-1 gap-10 md:grid-cols-2">

                <!-- 圖片 -->
                <div class="overflow-hidden rounded-lg bg-gray-100 shadow-sm">
                    <img
                        v-if="product.image"
                        :src="`/storage/${product.image}`"
                        :alt="product.name"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-64 items-center justify-center color-dark-gray text-sm">
                        無圖片
                    </div>
                </div>

                <!-- 商品資訊 -->
                <div class="flex flex-col justify-between">
                    <div>
                        <p class="text-sm color-dark-gray">{{ product.category?.name }}</p>
                        <h1 class="mt-2 text-3xl font-bold color-primary">{{ product.name }}</h1>

                        <!-- 價格 -->
                        <div class="mt-5 flex items-baseline gap-3">
                            <span v-if="product.has_discount" class="text-lg line-through color-dark-gray">
                                NT$ {{ Number(product.price).toLocaleString() }}
                            </span>
                            <span class="text-3xl font-bold color-primary">
                                NT$ {{ Number(product.discounted_price).toLocaleString() }}
                            </span>
                            <span
                                v-if="product.has_discount"
                                class="rounded px-2 py-1 text-xs font-semibold"
                                style="background-color:#fef2f2; color:#c80000;"
                            >
                                {{ Math.round((1 - product.discount) * 100) }}% OFF
                            </span>
                        </div>

                        <!-- 庫存 -->
                        <p class="mt-3 text-sm">
                            庫存：
                            <span :class="product.stock > 0 ? 'color-alert-success' : 'color-alert-danger'">
                                {{ product.stock > 0 ? product.stock + ' 件' : '已售完' }}
                            </span>
                        </p>

                        <!-- 描述 -->
                        <div class="mt-6">
                            <h2 class="text-base font-semibold color-primary mb-2">商品描述</h2>
                            <p class="text-sm color-dark-gray leading-relaxed">
                                {{ product.description ?? '暫無描述' }}
                            </p>
                        </div>
                    </div>

                    <!-- 數量 + 加入購物車 -->
                    <div class="mt-10 space-y-4">

                        <!-- 數量選擇 -->
                        <div class="flex items-center gap-4">
                            <span class="text-sm color-dark-gray">數量</span>
                            <div class="flex items-center gap-3 border border-gray-200 rounded px-3 py-1">
                                <button
                                    class="w-7 h-7 flex items-center justify-center text-lg leading-none hover:color-primary transition"
                                    :disabled="quantity <= 1"
                                    @click="decrease"
                                >−</button>
                                <span class="w-6 text-center font-medium">{{ quantity }}</span>
                                <button
                                    class="w-7 h-7 flex items-center justify-center text-lg leading-none hover:color-primary transition"
                                    :disabled="quantity >= product.stock"
                                    @click="increase"
                                >+</button>
                            </div>
                        </div>

                        <!-- 加入購物車按鈕 -->
                        <button
                            :disabled="product.stock === 0 || loading"
                            class="w-full rounded py-3 font-semibold text-white tracking-wide transition"
                            :class="product.stock > 0 ? 'bg-primary hover:opacity-80' : 'bg-gray-300 cursor-not-allowed'"
                            @click="addToCart"
                        >
                            <span v-if="loading">加入中...</span>
                            <span v-else-if="product.stock > 0">加入購物車</span>
                            <span v-else>已售完</span>
                        </button>

                        <!-- 直接前往購物車 -->
                        <Link
                            :href="route('cart.index')"
                            class="block text-center text-sm color-dark-gray underline hover:color-primary transition"
                        >
                            查看購物車
                        </Link>

                    </div>
                </div>
            </div>
        </div>
    </PlatformLayout>
</template>