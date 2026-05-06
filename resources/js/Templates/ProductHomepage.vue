<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import * as helpers from '@/Libs/helpers.js';
import Loading from 'vue-loading-overlay';
import ElProductCart from '@/Components/ElProductCart.vue';
import ElInfoBookmark from '@/Components/ElInfoBookmark.vue';
import ElInfoMenu from '@/Components/ElInfoMenu.vue';

const purchaseNum = ref(1);
const page = usePage();
const productData = ref();
const isLoading = ref(false);

const pageShow = ref({
  bookIntro: true,
});

const bookMenuData = {
  menu: {
    menuItem: [{ name: '商品資訊' }],
    disabledMenuItem: [],
    fontSize: 'large',
  },
  marginLg: '2px 10px',
  marginMd: '2px 0px',
  marginSm: '2px 0px',
};

const props = defineProps({
  productHomepageProp: [Array, Object],
});

const emit = defineEmits(['productHomepageEmit']);

const emitBookMenu = (data) => {
  pageShow.value.bookIntro = false;
  pageShow.value.bookIndex = false;
  pageShow.value.bookComment = false;
  pageShow.value.bookArticle = false;
  if (data === '商品資訊') {
    pageShow.value.bookIntro = true;
  }
};

const emitAddToCart = async (data) => {
  if (data.addStatus) {
    if (page?.props?.isFromChina !== true) {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        event: '電子書 - 加入購物車',
        ecommerce: {
          items: [data.order],
        },
      });
    }
    isLoading.value = true;
    await helpers.delay(2000);
    helpers.forwardRoute(`${page.props.product.id}`);
  }
};

const handleQtyChange = (q) => {
  purchaseNum.value = q;
};

watch(
  () => props.productHomepageProp,
  (newVal) => {
    productData.value = newVal;
  },
  { immediate: true },
);
</script>

<template>
  <Teleport to="body">
    <Loading
      v-if="isLoading"
      :active="isLoading"
      :is-full-page="true"
      loader="dots"
      color="#d55928"
      :z-index="9999"
    />
  </Teleport>
  <div
    class="default-page text-primary text-card-description px-5 lg:px-[5vw]"
    v-if="productData?.product"
  >
    <div class="mt-[40px] flex grid grid-cols-12 items-center justify-center">
      <div class="col-span-12 flex flex-col items-center justify-center md:col-span-6 md:pr-5">
        <div class="flex w-full flex-col items-end justify-end md:w-[230px] lg:w-[330px] lg:p-5">
          <a
            class="mb-1 mb-5 block w-full rounded bg-cover bg-center bg-no-repeat"
            :style="{
              backgroundImage: `url(${productData?.product?.img})`,
              paddingTop: productData?.backgroundImageRatio,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
            }"
          >
          </a>
          <ElInfoBookmark
            modelType="product"
            :modelId="String(productData?.product?.id)"
            type="bg-circle"
            size="large"
          />
        </div>
      </div>
      <div
        class="col-span-12 flex flex-col items-center justify-center pt-5 md:col-span-6 md:items-start"
      >
        <div class="mb-4 flex flex-col items-start justify-start">
          <p class="text-page-title pb-5">
            {{ productData?.product?.title }}
          </p>
          <p class="pb-5 text-[18px]" v-if="productData?.product?.subtitle">
            {{ productData?.product?.subtitle }}
          </p>
          <div class="flex w-full flex-col items-start justify-start">
            <div class="mt-5 mb-[30px] flex w-full md:w-auto">
              <ElInputNumber
                class="mr-2 w-[200px]!"
                v-model="purchaseNum"
                :min="1"
                @change="handleQtyChange"
              />
              <ElProductCart
                classes="flex border border-secondary p-2 rounded cursor-pointer"
                :cart-content="productData?.cartContent"
                :product-id="`${productData?.product?.id}`"
                :product-title="`電子書 - ${productData?.product?.title}`"
                :product-list-price="helpers.parseNumber(productData?.product?.list_price)"
                :product-sales-price="helpers.parseNumber(productData?.product?.sales_price)"
                :product-quantity="purchaseNum"
                :product-type="Number(productData?.product?.type ?? 0)"
                :already-bought="productData?.alreadyBought"
                @add-to-cart-emit="emitAddToCart"
              >
                <i class="ri-shopping-cart-2-line text-secondary text-2xl" />
              </ElProductCart>
            </div>
          </div>
          <div class="text-[18px] font-bold">
            <p class="pb-2">
              <span class="pr-2">發行商:</span
              ><span class="font-normal">{{ productData?.product?.publisher_name }}</span>
            </p>
            <p
              class="pb-2"
              :class="
                helpers.numberComma(productData?.product?.list_price) !==
                helpers.numberComma(productData?.product?.sales_price)
                  ? 'text-secondary block line-through'
                  : 'hidden'
              "
            >
              <span class="pr-2">售價:</span
              ><span class="font-normal"
                >NTD {{ helpers.numberComma(productData?.product?.list_price) }}</span
              >
            </p>
            <p class="pb-2">
              <span class="pr-2"
                >{{
                  helpers.priceTag(
                    productData?.product?.list_price,
                    productData?.product?.sales_price,
                  )
                }}:</span
              ><span class="font-normal"
                >NTD {{ helpers.numberComma(productData?.product?.sales_price) }}</span
              >
            </p>
            <div
              class="flex w-full items-start justify-start pb-2"
              v-if="productData?.events?.length"
            >
              <div class="flex items-start justify-start">
                <span
                  class="border-secondary mr-2 mb-1 rounded border p-1 font-bold"
                  v-for="event in productData?.events"
                  >{{ event.title }}</span
                >
              </div>
            </div>
            <div v-if="productData?.product?.product_detail?.intro">
              <p class="mb-5">商品簡介:</p>
              <div class="font-normal" v-html="productData?.product?.product_detail?.intro" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="border-gray mt-[40px] flex grid grid-cols-12 items-center justify-center border-b-2 pb-[40px]"
    >
      <div class="border-gray col-span-12 h-full border-t-2">
        <ElInfoMenu :info-menu-prop="bookMenuData" @info-menu-emit="emitBookMenu" />
        <div class="text-primary w-full py-[40px] lg:px-[50px]">
          <div class="book-intro" v-if="pageShow.bookIntro">
            <p class="tiptap" v-html="productData?.product?.product_detail?.description"></p>
            <p
              class="text-card-title my-5"
              v-if="!productData?.product?.product_detail?.description"
            >
              無相關內容顯示
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
