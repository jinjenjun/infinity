<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
});
</script>

<template>
    <Head :title="product.name" />
    <PlatformLayout>
        <div class="py-12">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                <!-- 返回按鈕 -->
                <Link
                    :href="route('products.index')"
                    class="mb-6 inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                >
                    ← 返回商品列表
                </Link>

                <div class="mt-4 grid grid-cols-1 gap-8 md:grid-cols-2">

                    <!-- 圖片 -->
                    <div class="overflow-hidden rounded-lg bg-gray-100">
                        <img
                            v-if="product.image"
                            :src="`/storage/${product.image}`"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-64 items-center justify-center text-gray-400">
                            無圖片
                        </div>
                    </div>

                    <!-- 商品資訊 -->
                    <div class="flex flex-col justify-between">
                        <div>
                            <p class="text-sm text-gray-500">{{ product.category?.name }}</p>
                            <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ product.name }}</h1>

                            <!-- 價格 -->
                            <div class="mt-4">
                                <span v-if="product.has_discount" class="text-lg line-through text-gray-400">
                                    NT$ {{ product.price }}
                                </span>
                                <span class="ml-2 text-2xl font-bold text-indigo-600">
                                    NT$ {{ product.discounted_price }}
                                </span>
                                <span v-if="product.has_discount" class="ml-2 rounded bg-red-100 px-2 py-1 text-sm text-red-600">
                                    {{ Math.round((1 - product.discount) * 100) }}% OFF
                                </span>
                            </div>

                            <!-- 庫存 -->
                            <p class="mt-3 text-sm text-gray-500">
                                庫存：
                                <span :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ product.stock > 0 ? product.stock + ' 件' : '已售完' }}
                                </span>
                            </p>

                            <!-- 描述 -->
                            <div class="mt-6">
                                <h2 class="text-lg font-semibold text-gray-900">商品描述</h2>
                                <p class="mt-2 text-gray-600 leading-relaxed">
                                    {{ product.description ?? '暫無描述' }}
                                </p>
                            </div>
                        </div>

                        <!-- 加入購物車按鈕（之後實作） -->
                        <div class="mt-8">
                            <button
                                :disabled="product.stock === 0"
                                class="w-full rounded-lg bg-indigo-600 px-6 py-3 text-white font-semibold hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ product.stock > 0 ? '加入購物車' : '已售完' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PlatformLayout>
</template>