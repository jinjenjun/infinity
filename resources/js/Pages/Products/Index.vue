<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import PlatformLayout from '@/Layouts/PlatformLayout.vue';
import ElProductCardList from '@/Components/ElProductCardList.vue';
import ElInfoButton from '@/Components/ElInfoButton.vue';
import ElInfoPagination from '@/Components/ElInfoPagination.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const screenWidth = ref(window.innerWidth);
const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');

watch(
    () => props.filters,
    (newFilters) => {
        search.value = newFilters?.search || '';
        selectedCategory.value = newFilters?.category || '';
    },
    { deep: true },
);

const categoryOption = computed(() => [
    { label: '所有分類', value: '' },
    ...props.categories.map((item) => ({
        label: item.name,
        value: item.slug,
    })),
]);

const paginationSetting = computed(() => ({
    currentPage: props.products.current_page,
    pageSize: props.products.per_page,
    total: props.products.total,
    pagerCount: screenWidth.value >= 1024 ? 5 : screenWidth.value >= 768 ? 3 : 2,
}));

const productCardListData = computed(() => ({
    backgroundImageRatio: '4/3',
    backgroundImageHeightLg: '300px',
    backgroundImageHeightMd: '240px',
    backgroundImageHeightSm: '180px',
    textContent:
        props.products.data?.map((item) => ({
            id: item?.uuid || '',
            image: item?.image ? `/storage/${item.image}` : '',
            title: item?.name || '',
            listPrice: item?.price || '',
            salesPrice: item?.discounted_price || '',
            description: item?.description || '',
            tag: item?.category?.name ? [item.category.name] : [],
            hashtag: item?.has_discount
                ? [`折扣價 NT$${item.discounted_price}`]
                : [`NT$${item.price}`],
            route: item?.uuid ? route('products.show', { uuid: item.uuid }) : null,
        })) ?? [],
}));

const navigate = (page = 1) => {
    router.get(
        route('products.index'),
        {
            search: search.value || undefined,
            category: selectedCategory.value || undefined,
            page,
        },
        { preserveState: true, replace: true },
    );
};

const handleCurrentChange = (page) => navigate(page);

onMounted(() => {
    window.addEventListener('resize', () => {
        screenWidth.value = window.innerWidth;
    });
});
</script>

<template>
    <Head title="商品列表" />
    <PlatformLayout>
      <div class="my-10 flex flex-col gap-4 md:flex-row">
        <div class="flex gap-4">
          <ElSelect
            v-model="selectedCategory"
            placeholder="所有分類"
            class="custom-select-small h-[45px]"
            @change="navigate(1)"
          >
            <ElOption
              v-for="item in categoryOption"
              :key="item.value"
              :label="item.label"
              :value="item.value"
              :class="{
                'text-secondary font-bold': selectedCategory === item.value,
                'text-primary': selectedCategory !== item.value,
              }"
              class="my-2 text-[18px]!"
            />
          </ElSelect>
          <input
            v-model="search"
            type="text"
            placeholder="請輸入商品名稱"
            class="placeholder:text-card-title h-[45px] w-[150px] rounded px-3 md:w-[240px]"
            @keyup.enter="navigate()"
          />
        </div>
        <ElInfoButton
          class="h-[45px] w-[315px] md:w-[150px] lg:w-[240px]"
          @click="navigate()"
        >
          搜尋
        </ElInfoButton>
      </div>
      <ElProductCardList class="mt-5" :product-card-prop="productCardListData" />
      <ElInfoPagination
        :current-page="paginationSetting.currentPage"
        :page-size="paginationSetting.pageSize"
        :total="paginationSetting.total"
        :pager-count="paginationSetting.pagerCount"
        @page-change="handleCurrentChange"
        class="mt-[30px]"
      />
    </PlatformLayout>
</template>
