<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { useStore } from 'vuex';
import { vuexData } from '@/../store';
import * as helpers from '@/Libs/helpers.js';
import Loading from 'vue-loading-overlay';
import ElInfoMenu from '@/Components/ElInfoMenu.vue';
import ElAddToCart from '@/Components/ElAddToCart.vue';
import ElInfoBookmark from '@/Components/ElInfoBookmark.vue';
import ElStarRate from '@/Components/ElStarRate.vue';

const store = useStore();
const page = usePage();
const isLoading = ref(false);

const pageShow = ref({
  bookIntro: true,
  bookIndex: false,
  bookComment: false,
  bookArticle: false,
});

const props = defineProps({
  bookHomepageProp: [Array, Object],
});

const emit = defineEmits(['bookHomepageEmit']);

const emitBookMenu = (data) => {
  pageShow.value.bookIntro = false;
  pageShow.value.bookIndex = false;
  pageShow.value.bookComment = false;
  pageShow.value.bookArticle = false;
  if (data === '內容資訊') {
    pageShow.value.bookIntro = true;
  } else if (data === '書籍目錄') {
    pageShow.value.bookIndex = true;
  } else if (data === '讀者書評') {
    pageShow.value.bookComment = true;
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

const boughtAndForward = (data) => {
  emitAddToCart(data);
  if (data.addStatus) {
    helpers.forwardRoute(`/cart`);
  }
};

const bookMenuData = {
  menu: {
    menuItem: [{ name: '內容資訊' }, { name: '書籍目錄' }, { name: '讀者書評' }],
    disabledMenuItem: [],
    startMenuItem: {},
    fontSize: 'large',
  },
  marginLg: '2px 10px',
  marginMd: '2px 0px',
  marginSm: '2px 0px',
};

const productData = ref();
const commentData = ref([]);

let moreDataStatus = {
  ebook: true,
  audioBook: true,
  anotherProduct: true,
};

let spliceQuantity = {
  ebook: 10,
};

let addQuantity = {
  ebook: 10,
};

const lazyLoading = () => {
  let lastScrollTop = 0;
  let alertShown = false;
  let isScrolling = false;

  window.addEventListener('scroll', function () {
    const scrollPosition = window.scrollY + window.innerHeight;
    const pageHeight = document.documentElement.scrollHeight;
    let dataLength;

    if (window.scrollY > lastScrollTop && pageHeight - scrollPosition <= 500) {
      if (!alertShown && !isScrolling) {
        alertShown = true;
        isScrolling = true;

        setTimeout(() => {
          if (pageShow.value.bookComment) {
            dataLength = productData?.value?.product?.ratings?.length;
            spliceQuantity.ebook += addQuantity.ebook;
            if (moreDataStatus.ebook) {
              commentData.value = productData?.value?.product?.ratings?.slice(
                0,
                spliceQuantity.ebook,
              );
            }

            if (spliceQuantity.ebook > dataLength && moreDataStatus.ebook) {
              isLoading.value = false;
              moreDataStatus.ebook = false;
            }
          }

          isLoading.value = false;
          isScrolling = false;
          alertShown = false;
        }, 300);
      }
    } else if (window.scrollY < lastScrollTop) {
      alertShown = false;
    }

    lastScrollTop = window.scrollY <= 0 ? 0 : window.scrollY;
  });
};

onMounted(() => {
  lazyLoading();
});

watch(
  () => props.bookHomepageProp,
  (newVal) => {
    const startMenuData = vuexData.unlock.headMenu.getMenu(store)?.startMenuItem;
    startMenuData?.name === bookMenuData?.menu?.menuItem?.[2]?.name
      ? (bookMenuData.menu.startMenuItem = startMenuData)
      : null;
    bookMenuData.menu.startMenuItem?.name
      ? emitBookMenu(bookMenuData.menu.startMenuItem.name)
      : null;
    vuexData.unlock.headMenu.setMenu(store, {});
    productData.value = newVal;
    commentData.value = productData?.value?.product?.ratings;
  },
  { immediate: true, deep: true },
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
      <div
        class="col-span-12 flex flex-col items-center justify-center md:col-span-6 md:items-start lg:items-center lg:px-5"
      >
        <div
          class="flex w-full flex-col items-end justify-start pb-5 md:w-[240px] lg:w-[350px] lg:px-5"
        >
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
        class="col-span-12 flex flex-col items-center justify-center md:col-span-6 md:items-start lg:items-start lg:px-5"
      >
        <div class="mb-4 flex flex-col items-start justify-start">
          <p class="text-page-title pb-2">{{ productData?.product?.title }}</p>
          <p class="pb-2 text-[18px]">{{ productData?.product?.subtitle }}</p>
          <p class="text-[18px]">{{ productData?.product?.original_title }}</p>
          <div class="flex w-full flex-col items-start justify-start">
            <div class="mt-5 mb-3 flex w-full md:w-auto">
              <ElAddToCart
                classes="w-full md:w-auto md:min-w-[220px] lg:min-w-[300px] text-card-description mr-2 p-2 lg:py-2 bg-secondary cursor-pointer flex items-center justify-center rounded border border-none font-bold text-white shadow-none active:brightness-110"
                :cart-content="productData?.cartContent"
                :product-id="`${productData?.product?.id}`"
                :product-title="`電子書 - ${productData?.product?.title}`"
                :product-list-price="helpers.parseNumber(productData?.product?.list_price)"
                :product-sales-price="helpers.parseNumber(productData?.product?.sales_price)"
                :already-bought="productData?.alreadyBought"
                @add-to-cart-emit="boughtAndForward"
              >
                <span class="text-base lg:text-[18px]">立即購買</span>
                <i class="ri-arrow-right-s-line text-base text-white lg:text-[18px]" />
              </ElAddToCart>
              <ElAddToCart
                classes="flex border border-secondary p-2 rounded cursor-pointer"
                :cart-content="productData?.cartContent"
                :product-id="`${productData?.product?.id}`"
                :product-title="`電子書 - ${productData?.product?.title}`"
                :product-list-price="helpers.parseNumber(productData?.product?.list_price)"
                :product-sales-price="helpers.parseNumber(productData?.product?.sales_price)"
                :already-bought="productData?.alreadyBought"
                @add-to-cart-emit="emitAddToCart"
              >
                <i class="ri-shopping-cart-2-line text-secondary text-2xl" />
              </ElAddToCart>
            </div>
            <a
              class="border-primary mb-5 flex w-full cursor-pointer items-center justify-center rounded border py-[10px] text-base font-bold md:w-auto md:min-w-[270px] lg:min-w-[350px] lg:text-[18px]"
              :href="route('epub-reader.preview', { ebookableId: productData.product.id })"
              target="_blank"
            >
              試閱書籍
            </a>
          </div>
          <div class="text-[18px] font-bold">
            <p v-if="productData?.product?.authors.length" class="pb-3">
              <span>作者: </span
              ><span class="font-normal" v-for="author in productData?.product?.authors">{{
                author.name
              }}</span>
            </p>
            <p v-if="productData?.product?.translators.length" class="pb-3">
              <span>譯者: </span
              ><span class="font-normal" v-for="translator in productData?.product?.translators">{{
                translator.name
              }}</span>
            </p>
            <p v-if="productData?.product?.illustrators.length" class="pb-3">
              <span>繪者: </span
              ><span
                class="font-normal"
                v-for="illustrator in productData?.product?.illustrators"
                >{{ illustrator.name }}</span
              >
            </p>
            <p class="pb-3">
              <span class="pr-2">出版社:</span
              ><span class="font-normal">{{ productData?.product?.publisher_name }}</span>
            </p>
            <p class="pb-3">
              <span class="pr-2">出版日期:</span
              ><span class="font-normal">{{ productData?.product?.publish_date }}</span>
            </p>
            <p class="pb-3">
              <span class="pr-2">語言:</span
              ><span class="font-normal">{{ productData?.product?.lang_label }}</span>
            </p>
            <p
              class="pb-3"
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
            <p class="pb-3">
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
          </div>
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
        </div>
      </div>
    </div>

    <div
      class="border-gray mt-[40px] flex grid grid-cols-12 items-center justify-center border-b-2 pb-[40px]"
    >
      <div class="border-gray col-span-12 h-full border-t-2 lg:col-span-8 lg:border-r-2">
        <ElInfoMenu :info-menu-prop="bookMenuData" @info-menu-emit="emitBookMenu" />
        <div class="text-primary tiptap-content w-full max-w-full overflow-x-auto px-2 py-[40px]">
          <div class="book-intro" v-if="pageShow.bookIntro">
            <p class="tiptap" v-html="productData?.product?.product_detail?.description"></p>
            <p
              class="text-card-title my-5"
              v-if="!productData?.product?.product_detail?.description"
            >
              無相關內容顯示
            </p>
          </div>
          <div class="book-index" v-if="pageShow.bookIndex">
            <p class="tiptap" v-html="productData?.product?.product_detail?.contents"></p>
            <p class="text-card-title my-5" v-if="!productData?.product?.product_detail?.contents">
              無相關內容顯示
            </p>
          </div>
          <div class="book-comment" v-if="pageShow.bookComment">
            <p class="text-card-title mt-5" v-if="productData?.product?.ratings.length === 0">
              無相關內容顯示
            </p>
            <div v-if="productData?.product?.ratings.length > 0">
              <div
                v-for="(item, index) in commentData"
                :key="index"
                class="border-light-gray border-t-2 pt-5"
                :class="{ 'border-none': index === 0 }"
              >
                <ElStarRate
                  :star-rate-prop="{
                    color: 'text-primary',
                    fontSize: 'text-card-title',
                    cursor: 'cursor-default',
                    defaultGrade: item.score,
                    clickAllow: false,
                  }"
                  class="pb-5"
                />
                <div class="text-card-title pb-5">發表者：{{ item.user.name }}</div>
                <div class="text-card-title pb-5 leading-[1.8] font-normal lg:text-[18px]">
                  {{ item.comment }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="border-gray col-span-12 flex h-full flex-col items-center justify-start border-t-2 lg:col-span-4 lg:px-4"
      >
        <div class="w-full">
          <div class="mt-[40px] mb-4 flex flex-col items-start justify-start">
            <div class="flex flex-col items-end justify-end pb-2">
              <p class="bg-secondary rounded px-2 py-1 text-xs font-bold text-white">
                {{
                  helpers.priceTag(
                    productData?.product?.list_price,
                    productData?.product?.sales_price,
                  )
                }}
              </p>
              <p class="text-page-title pr-2">
                NTD
                {{
                  helpers.showPrice(
                    productData?.product?.list_price,
                    productData?.product?.sales_price,
                  )
                }}
              </p>
            </div>
            <div
              class="flex w-full items-start justify-start pb-5"
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
            <div class="flex w-full flex-col items-start justify-start">
              <div class="mb-5 flex w-full">
                <ElAddToCart
                  classes="w-full text-card-description mr-2 p-2 lg:py-2 bg-secondary cursor-pointer flex items-center justify-center rounded border border-none font-bold text-white shadow-none active:brightness-110"
                  :cart-content="productData?.cartContent"
                  :product-id="`${productData?.product?.id}`"
                  :product-title="`電子書 - ${productData?.product?.title}`"
                  :product-list-price="helpers.parseNumber(productData?.product?.list_price)"
                  :product-sales-price="helpers.parseNumber(productData?.product?.sales_price)"
                  :already-bought="productData?.alreadyBought"
                  @add-to-cart-emit="boughtAndForward"
                >
                  <span class="text-base lg:text-[18px]">立即購買</span>
                </ElAddToCart>
                <ElAddToCart
                  classes="flex border border-secondary p-2 rounded cursor-pointer"
                  :cart-content="productData?.cartContent"
                  :product-id="`${productData?.product?.id}`"
                  :product-title="`電子書 - ${productData?.product?.title}`"
                  :product-list-price="helpers.parseNumber(productData?.product?.list_price)"
                  :product-sales-price="helpers.parseNumber(productData?.product?.sales_price)"
                  :already-bought="productData?.alreadyBought"
                  @add-to-cart-emit="emitAddToCart"
                >
                  <i class="ri-shopping-cart-2-line text-secondary text-2xl" />
                </ElAddToCart>
              </div>
            </div>
            <div class="text-[18px] font-bold">
              <p class="pb-2"><span class="pr-2">商品資訊</span></p>
              <p class="pb-2">
                <span class="pr-2">ISBN:</span
                ><span class="font-normal">{{ productData?.product?.isbn }}</span>
              </p>
              <p class="pb-2">
                <span class="pr-2">商品格式:</span
                ><span class="font-normal">{{ productData?.product?.type_label }}</span>
              </p>
              <p class="pb-2">
                <span class="pr-2">字數:</span
                ><span class="font-normal">{{ productData?.product?.word_count }}</span>
              </p>
              <p v-if="productData?.product?.authors.length" class="pb-2">
                <span class="pr-2">作者:</span
                ><span class="font-normal" v-for="author in productData?.product?.authors">{{
                  author.name
                }}</span>
              </p>
              <p v-if="productData?.product?.translators.length" class="pb-2">
                <span class="pr-2">譯者:</span
                ><span
                  class="font-normal"
                  v-for="translator in productData?.product?.translators"
                  >{{ translator.name }}</span
                >
              </p>
              <p v-if="productData?.product?.illustrators.length" class="pb-2">
                <span class="pr-2">繪者:</span
                ><span
                  class="font-normal"
                  v-for="illustrator in productData?.product?.illustrators"
                  >{{ illustrator.name }}</span
                >
              </p>
              <p class="pb-2">
                <span class="pr-2">出版社:</span
                ><span class="font-normal">{{ productData?.product?.publisher_name }}</span>
              </p>
              <p class="pb-2">
                <span class="pr-2">出版日期:</span
                ><span class="font-normal">{{ productData?.product?.publish_date }}</span>
              </p>
              <p class="pb-2">
                <span class="pr-2">語言: </span
                ><span class="font-normal">{{ productData?.product?.lang_label }}</span>
              </p>
              <p v-if="productData?.product?.territory_label" class="pb-2">
                <span class="font-normal"
                  ><i class="ri-error-warning-fill pr-2 text-2xl"></i
                  >{{ productData?.product?.territory_label }}</span
                >
              </p>
              <p class="pt-5">
                <span class="pr-2" v-if="productData?.product?.authors[0]?.intro">作者簡介</span>
              </p>
              <p
                class="mb-5 font-normal"
                style="line-height: 1.8"
                v-html="productData?.product?.authors[0]?.intro"
              ></p>
              <p class="mb-2" v-if="productData?.product?.product_detail?.intro">
                <span>書籍簡介</span>
              </p>
              <p
                class="mb-5 font-normal"
                style="line-height: 1.8"
                v-html="productData?.product?.product_detail?.intro"
              ></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped></style>
