<script setup>
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
const selectedCategory = ref(props.filters?.category ?? '');

const applyFilter = () => {
    router.get(route('products.index'), {
        search: search.value,
        category: selectedCategory.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([search, selectedCategory], () => {
    applyFilter();
});
</script>

<template>
    <Head title="商品列表" />
    <PlatformLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- 搜尋和篩選 -->
                <div class="mb-8 flex flex-col gap-4 sm:flex-row">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="搜尋商品..."
                        class="w-full rounded-md border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:w-64"
                    />
                    <select
                        v-model="selectedCategory"
                        class="w-full rounded-md border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:w-48"
                    >
                        <option value="">所有分類</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.slug"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <!-- 商品卡片 -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Link
                        v-for="product in products.data"
                        :key="product.id"
                        :href="route('products.show', product.uuid)"
                        class="overflow-hidden rounded-lg bg-white shadow-md transition hover:shadow-lg"
                    >
                        <!-- 圖片 -->
                        <div class="h-48 overflow-hidden bg-gray-100">
                            <img
                                v-if="product.image"
                                :src="`/storage/${product.image}`"
                                :alt="product.name"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full items-center justify-center text-gray-400">
                                無圖片
                            </div>
                        </div>

                        <!-- 內容 -->
                        <div class="p-4">
                            <p class="text-sm text-gray-500">{{ product.category?.name }}</p>
                            <h3 class="mt-1 text-lg font-semibold text-gray-900">{{ product.name }}</h3>
                            <div class="mt-2">
                                <span v-if="product.has_discount" class="text-sm line-through text-gray-400">
                                    NT$ {{ product.price }}
                                </span>
                                <span class="text-lg font-bold text-indigo-600">
                                    NT$ {{ product.discounted_price }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">庫存：{{ product.stock }}</p>
                        </div>
                    </Link>
                </div>

                <!-- 沒有商品 -->
                <div v-if="products.data.length === 0" class="mt-12 text-center text-gray-500">
                    沒有找到符合的商品
                </div>

                <!-- 分頁 -->
                <div class="mt-8 flex justify-center gap-2">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="rounded px-3 py-1 text-sm"
                        :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    />
                </div>
            </div>
        </div>
    </PlatformLayout>
</template>