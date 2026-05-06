<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import * as Heads from '@/Heads';
import * as helpers from '@/Libs/helpers.js';
import * as publicDoc from '@/../../public/public-doc';
import '@/../scss/main.scss';
import ElSideBar from '@/Components/ElSideBar.vue';
import ElInfoMenu from '@/Components/ElInfoMenu.vue';
import ElIconButton from '@/Components/ElIconButton.vue';
import ElInfoSwiper from '@/Components/ElInfoSwiper.vue';
import ElImgSwiper from '@/Components/ElImgSwiper.vue';
import ElNewsSwiper from '@/Components/ElNewsSwiper.vue';
import ElCelebritySwiper from '@/Components/ElCelebritySwiper.vue';
import ElBookSwiper from '@/Components/ElBookSwiper.vue';
import ElNewsRow from '@/Components/ElNewsRow.vue';
import { vuexData } from '../../store/index.js';
import ElCourseSwiper from '@/Components/ElCourseSwiper.vue';

const origin = window.location.origin;
const page = usePage();
const screenWidth = ref(window.innerWidth);
const postType = ref(1);
const newsLength = ref(7);
let spliceQuantity = {
  ebook: 10,
  audioBook: 10,
  anotherProduct: 10,
};

const knowledgeSelector = ref('');
let knowledgeOption = [];

const newsSelector = ref('');
const newsOption = computed(() => {
  return (
    props.homeHomepageProp?.news?.menu?.menuItem?.map((item) => ({
      value: item.title,
      label: item.title,
    })) ?? []
  );
});

const popupAdStatus = ref(false);
const sideAdStatus = ref(false);

const popupAdData = ref({
  // item_id: 'homepage_popup_ad_1' || '無相關資料',
  // client: '台灣商務印書館' || '無客戶名稱',
  // product: '三顆石頭就是一堵牆' || '無產品名稱',
  // route: 'https://www.cptw.com.tw/book/07080004/54' || '無相關資料',
  // imageUrl: '/img/ad_popup.jpg' || null,
});

const sideAdData = ref({
  // item_id: 'homepage_side_ad_1' || '無相關資料',
  // client: '台灣商務印書館' || '無客戶名稱',
  // product: '論點東西洋史合輯' || '無產品名稱',
  // route: 'https://www.cptw.com.tw/book/07010053' || '無相關資料',
  // imageUrl: '/img/ad_side.png' || null,
});

const rowAdData = ref([
  // {
  //   item_id: 'homepage_banner_ad_1' || '無相關資料',
  //   client: '台灣商務印書館' || '無客戶名稱',
  //   product: '一線之間' || '無產品名稱',
  //   route: 'https://www.cptw.com.tw/book/04030028' || '無相關資料',
  //   imgUrl: '/img/ad_block_001.jpg' || null,
  // },
  // {
  //   item_id: 'homepage_banner_ad_2' || '無相關資料',
  //   client: '台灣商務印書館' || '無客戶名稱',
  //   product: '在19世紀瘋露營' || '無產品名稱',
  //   route: 'https://www.cptw.com.tw/book/04030026' || '無相關資料',
  //   imgUrl: '/img/ad_block_002.jpg' || null,
  // },
  // {
  //   item_id: 'homepage_banner_ad_3' || '無相關資料',
  //   client: '台灣商務印書館' || '無客戶名稱',
  //   product: '尼采思想漫遊' || '無產品名稱',
  //   route: 'https://www.cptw.com.tw/book/01000262' || '無相關資料',
  //   imgUrl: '/img/ad_block_003.jpg' || null,
  // },
  // {
  //   item_id: 'homepage_banner_ad_4' || '無相關資料',
  //   client: '台灣商務印書館' || '無客戶名稱',
  //   product: '金光閃閃大銀幕' || '無產品名稱',
  //   route: 'https://www.cptw.com.tw/book/09030015' || '無相關資料',
  //   imgUrl: '/img/ad_block_004.jpg' || null,
  // },
]);

// NOTE: 接收home index的prop
const props = defineProps({
  homeHomepageProp: [Array, Object],
});

// NOTE: 送出emit到home index
const emit = defineEmits(['homeHomepageEmit']);

// NOTE: 接收各元件傳出的emit
const emitSideBar = (data) => {
  return data;
};
const emitNewsSwiper = (data) => {
  return data;
};
const emitInfoSwiper = (data) => {
  return data;
};

const emitNewsSideBar = (data) => {
  newsSelector.value = data;
  const targetItem = props.homeHomepageProp?.news?.menu?.menuItem?.find(
    (item) => item.title === data,
  );

  const newsData = newsDataTransfer(props.homeHomepageProp?.news?.content, targetItem?.id);

  if (postType.value === 1) {
    newsRowData.value.textContent = newsData;
  } else {
    newsSwiperData.value.textContent = newsData;
  }
};

const emitKnowledgeSelector = (data) => {
  knowledgeSelector.value = data;
};
const emitCourseSwiper = (data) => {
  return data;
};
const emitCourseMenu = (data) => {
  return data;
};
const emitBestsellingMenu = (data) => {
  if (data === '有聲書') {
    bestsellingSwiperData.value.textContent = bestsellingDataTransfer(
      props.homeHomepageProp.bestselling.content.audioBook,
      'audioBook',
    );
  } else if (data === '電子書') {
    bestsellingSwiperData.value.textContent = bestsellingDataTransfer(
      props.homeHomepageProp.bestselling.content.ebook,
      'ebook',
    );
  }
};
const emitBestsellingSwiper = (data) => {
  return data;
};
const emitCelebritySwiper = (data) => {
  return data;
};
const emitImgSwiper = (data) => {
  return data;
};
const emitNewsRow = (data) => {
  return data;
};

const billboardSwiperData = ref({
  textContent: '',
  spaceBetween: 0,
  slidesPerViewLg: 1,
  slidesPerViewMd: 1,
  slidesPerViewSm: 1,
  autoplay: {
    delay: 2500,
    disableOnInteraction: true,
  },
  pagination: {
    clickable: true,
  },
  navigation: true,
  backgroundImageRatio: '29.91%',
  textHeightLg: '280px',
  textHeightMd: '220px',
  textHeightSm: '200px',
});

const newsRowData = ref({
  textContent: '',
  containerHeightLg: '45px',
  containerHeightMd: '40px',
  containerHeightSm: '35px',
  commentHidden: false,
});

const newsSwiperData = ref({
  textContent: '',
  spaceBetween: 0,
  slidesPerViewLg: 3,
  slidesPerViewMd: 3,
  slidesPerViewSm: 2,
  pagination: {
    clickable: true,
  },
  navigation: true,
  backgroundImageRatio: '75%',
  textHeightLg: '200px',
  textHeightMd: '180px',
  textHeightSm: '150px',
});

const knowledgeSwiperData = ref({
  textContent: '',
  spaceBetween: 0,
  slidesPerViewLg: 3,
  slidesPerViewMd: 3,
  slidesPerViewSm: 2,
  pagination: {
    clickable: true,
  },
  navigation: true,
  backgroundImageRatio: '75%',
  textHeightLg: '280px',
  textHeightMd: '220px',
  textHeightSm: '200px',
});

const courseMenuData = {
  menu: props.homeHomepageProp.study.menu,
  marginLg: '2px 10px',
  marginMd: '2px 10px',
  marginSm: '2px 5px',
};

const courseSwiperData = ref({
  textContent: '',
  spaceBetween: 0,
  slidesPerViewLg: 4,
  slidesPerViewMd: 3,
  slidesPerViewSm: 2,
  pagination: {
    clickable: true,
  },
  navigation: true,
  backgroundImageRatio: '75%',
  textHeightLg: '350px',
  textHeightMd: '280px',
  textHeightSm: '260px',
});

const bestsellingMenuData = ref({
  menu: props.homeHomepageProp.bestselling.menu,
  marginLg: '2px 10px',
  marginMd: '2px 10px',
  marginSm: '2px 5px',
});

const bestsellingSwiperData = ref({
  textContent: '',
  spaceBetween: 0,
  slidesPerViewLg: 5,
  slidesPerViewMd: 3,
  slidesPerViewSm: 2,
  pagination: {
    clickable: true,
  },
  navigation: true,
  backgroundImageRatio: '133.33%',
  textHeightLg: '280px',
  textHeightMd: '260px',
  textHeightSm: '240px',
});

const celebritySwiperData = ref({
  textContent: '',
  spaceBetween: 0,
  slidesPerViewLg: 4,
  slidesPerViewMd: 3,
  slidesPerViewSm: 2,
  pagination: {
    clickable: true,
  },
  navigation: true,
  backgroundImageRatio: '133.33%',
  textHeightLg: '280px',
  textHeightMd: '220px',
  textHeightSm: '200px',
});

const billboardDataTransfer = (data) => {
  return (data || [])
    .filter((item) => item.area === 1)
    .map((item) => ({
      id: item.id || '',
      url: item.url,
      image: item.imgUrl || '',
      isTargetBlank: item.is_target_blank,
    }));
};

const dateTransfer = (date) => {
  return date.match(/^\d{4}-\d{2}-\d{2}/)[0];
};

const newsDataTransfer = (data, targetId) => {
  const targetItem = data?.find((item) => item?.id === targetId);

  if (!targetItem) return [];
  postType.value = targetItem?.type || 1;

  const newsList = Array.isArray(targetItem.news) ? targetItem.news : [];
  return newsList.slice(0, newsLength.value).map((subItem) => ({
    id: subItem?.id || '',
    route: `${origin}/news/${subItem?.id}`,
    image: subItem?.cover_url || '',
    video: subItem?.first_video_url || '',
    tag: targetItem?.name || '',
    title: subItem?.title || '',
    date: dateTransfer(subItem?.created_at) || '',
    postType: targetItem?.type === 2 ? 'video' : 'article',
    backgroundImageRatio: '56.25%',
  }));
};

const knowledgeDataTransfer = (data) => {
  return data?.map((item) => {
    return {
      id: item?.id || '',
      route: 'platform.posts.show',
      image: item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: item?.title || '',
      description: item?.overview || '',
      hashtag: item?.tags?.map((tag) => tag?.name?.zh_TW) || [],
      isTargetBlank: true,
    };
  });
};

const courseDataTransfer = (data) => {
  const items = Object.values(data || {});
  return items?.map((item) => {
    return {
      id: item?.ulid || '',
      route: 'platform.courses.show',
      image: item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: helpers.multiTitleTransfer(item?.name, item?.title),
      salesPrice: item?.sales_price,
      listPrice: item?.list_price,
      teacher: item?.teacher?.name || '',
      duration: item?.formatted_duration || '',
      isTargetBlank: true,
      publishStatus: item?.status || null,
    };
  });
};

const bestsellingDataTransfer = (data, bookType) => {
  const items = Object.values(data || {});
  let filterItems = [];
  let productRoute = '';

  if (bookType === 'ebook') {
    productRoute = 'platform.book.show';
    filterItems = items
      ?.filter((item) => item.type === 1 || item.type === 2)
      .splice(0, spliceQuantity.ebook);
  } else if (bookType === 'audioBook') {
    productRoute = 'platform.audios.show';
    filterItems = items?.filter((item) => item.type === 3).splice(0, spliceQuantity.audioBook);
  } else if (bookType === 'anotherProduct') {
    productRoute = 'platform.book.show';
    filterItems = items?.filter((item) => item.type === 5).splice(0, spliceQuantity.anotherProduct);
  } else {
    productRoute = 'platform.book.show';
    filterItems = items
      ?.filter((item) => item.type === 1 || item.type === 2)
      .splice(0, spliceQuantity.ebook);
  }

  return filterItems.map((item) => ({
    id: item?.id || '',
    route: productRoute,
    image: item?.coverUrl || '',
    tag: [].concat(item?.categoryName || []).map(String),
    title: item?.title || '',
    salesPrice: item?.salesPrice || '',
    listPrice: item?.list_price || '',
    isDiscount: item?.isDiscount || false,
    event: item?.events || [],
    category: item?.categories || [],
    isTargetBlank: true,
  }));
};

const celebrityDataTransfer = (data) => {
  return data?.map((item) => {
    return {
      id: item?.id || '',
      route: 'platform.celebrities.show',
      image: item?.mediaUrl || '',
      tag: [],
      title: item?.name + ' | ' + item?.title || '',
      description: item?.profileText || '',
      hashtag: item?.tags?.map((tag) => tag?.name?.zh_TW) || [],
      isTargetBlank: true,
    };
  });
};

const popupAdTransfer = (data) => {
  const first = (data || []).find((item) => item.area === 2);

  if (!first) return null;

  popupAdStatus.value = true;
  return {
    item_id: first.id || '',
    client: '',
    product: first.title || '',
    route: first.url || '',
    imageUrl: first.imgUrl || '',
    isTargetBlank: first.is_target_blank,
  };
};

const sideAdTransfer = (data) => {
  const first = (data || []).find((item) => item.area === 3);

  if (!first) return null;

  sideAdStatus.value = true;
  return {
    item_id: first.id || '',
    client: '',
    product: first.title || '',
    route: first.url || '',
    imageUrl: first.imgUrl || '',
    isTargetBlank: first.is_target_blank,
  };
};

const generateInitData = () => {
  const homepage = props.homeHomepageProp || {};
  billboardSwiperData.value.textContent = billboardDataTransfer(homepage.billboard?.content);

  const newsMenuItems = homepage.news?.menu?.menuItem || [];
  const firstNewsItem = newsMenuItems.find((item) => item?.type === 2) ?? newsMenuItems[0];
  const firstNewsId = firstNewsItem?.id;

  newsRowData.value.textContent = newsDataTransfer(homepage.news?.content, firstNewsId);
  newsSwiperData.value.textContent = newsDataTransfer(homepage.news?.content, firstNewsId);
  knowledgeSwiperData.value.textContent = knowledgeDataTransfer(homepage.knowledge?.content);
  courseSwiperData.value.textContent = courseDataTransfer(homepage.course?.content);
  bestsellingSwiperData.value.textContent = bestsellingDataTransfer(
    homepage.bestselling?.content?.ebook,
    'ebook',
  );
  celebritySwiperData.value.textContent = celebrityDataTransfer(homepage.celebrity?.content);
  popupAdData.value = popupAdTransfer(homepage.billboard?.content);
  sideAdData.value = sideAdTransfer(homepage.billboard?.content);
};

const showPopupAd = () => {
  setTimeout(() => {
    popupAdStatus.value = true;
  }, 1000);
};

const enterPopupAd = () => {
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: '廣告 - 點擊首頁蓋版廣告',
    ecommerce: {
      items: [
        {
          item_id: popupAdData.value.item_id,
          client: popupAdData.value.client,
          product: popupAdData.value.product,
          title: `${popupAdData.value.client} - ${popupAdData.value.product}`,
          route: popupAdData.value.route,
        },
      ],
    },
  });
  if (popupAdData.value.route === '無相關資料') return;
  window.open(popupAdData.value.route, '_blank', 'noopener,noreferrer');
};

const closePopupAd = () => {
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: '廣告 - 關閉首頁蓋版廣告',
    ecommerce: {
      items: [
        {
          item_id: popupAdData.value.item_id,
          client: popupAdData.value.client,
          product: popupAdData.value.product,
          title: `${popupAdData.value.client} - ${popupAdData.value.product}`,
          route: popupAdData.value.route,
        },
      ],
    },
  });
  popupAdStatus.value = false;
};

const enterSideAd = () => {
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: '廣告 - 點擊首頁側邊廣告',
    ecommerce: {
      items: [
        {
          item_id: sideAdData.value.item_id,
          client: sideAdData.value.client,
          product: sideAdData.value.product,
          title: `${sideAdData.value.client} - ${sideAdData.value.product}`,
          route: sideAdData.value.route,
        },
      ],
    },
  });
  if (sideAdData.value.route === '無相關資料') return;
  window.open(sideAdData.value.route, '_blank', 'noopener,noreferrer');
};

const closeSideAd = () => {
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: '廣告 - 關閉首頁側邊廣告',
    ecommerce: {
      items: [
        {
          item_id: sideAdData.value.item_id,
          client: sideAdData.value.client,
          product: sideAdData.value.product,
          title: `${sideAdData.value.client} - ${sideAdData.value.product}`,
          route: sideAdData.value.route,
        },
      ],
    },
  });
  if (sideAdData.value.route === '無相關資料') return;
  sideAdStatus.value = false;
};

const enterRowAd = (route, id, client, product) => {
  if (page?.props?.isFromChina !== true) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: '廣告 - 點擊首頁banner廣告',
      ecommerce: {
        items: [
          {
            item_id: id,
            client: client,
            product: product,
            title: `${client} - ${product}`,
            route: route,
          },
        ],
      },
    });
  }

  if (!route || route.trim() === '' || route === '無相關資料') return;

  window.open(route, '_blank', 'noopener,noreferrer');
};

const updateSwiperItems = () => {
  if (screenWidth.value >= 1024) {
    newsLength.value = 7;
  } else if (screenWidth.value >= 768) {
    newsLength.value = 3;
  } else {
    newsLength.value = 3;
  }
};

onMounted(() => {
  updateSwiperItems();
  generateInitData();
  newsSelector.value = newsOption.value?.[0]?.label;
  knowledgeOption = props.homeHomepageProp.knowledge.menu.menuItem.map((item) => {
    return {
      value: item.title,
      label: item.title,
    };
  });
  knowledgeSelector.value = knowledgeOption?.[0]?.label;
  window.addEventListener('resize', () => {
    screenWidth.value = window.innerWidth;
    updateSwiperItems();
  });
});
</script>
<template>
  <Head :title="Heads.home.homepageHead.title">
    <meta v-for="(tag, index) in Heads.home.homepageHead.meta" :key="index" v-bind="tag" />
  </Head>

  <teleport to="body">
    <div
      class="blur-transition popup-ad bg-opacity-50 fixed inset-0 z-1500 flex items-start justify-center bg-black"
      v-if="popupAdStatus"
    >
      <div class="relative mt-[100px]">
        <div
          class="text-card-description bg-opacity-70 absolute -top-6 -right-6 flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-black text-white lg:-top-10 lg:-right-10 lg:h-8 lg:w-8"
          @click="closePopupAd"
        >
          ✕
        </div>
        <img
          :src="popupAdData.imageUrl"
          alt=""
          class="max-w-[200px] md:max-w-[300px] lg:max-w-[500px]"
          @click="enterPopupAd"
        />
      </div>
    </div>
  </teleport>

  <teleport to="body">
    <div class="fixed bottom-10 left-3 z-1000" v-if="sideAdStatus">
      <div class="relative">
        <div
          class="bg-opacity-70 absolute -top-6 -right-6 flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-black text-xs text-white"
          @click="closeSideAd"
        >
          ✕
        </div>
        <img
          :src="sideAdData.imageUrl"
          alt=""
          class="max-w-[50px] lg:max-w-[80px]"
          @click="enterSideAd"
        />
      </div>
    </div>
  </teleport>

  <div class="default-page text-primary min-h-screen">
    <div class="mt-[20px] grid grid-cols-12">
      <div class="border-gray col-span-12 cursor-pointer border-l">
        <ElImgSwiper :img-swiper-prop="billboardSwiperData" @img-swiper-emit="emitImgSwiper" />
      </div>
    </div>

    <div class="grid grid-cols-12 gap-2">
      <div class="col-span-3 mt-5 lg:col-span-3" v-for="(item, index) in rowAdData" :key="index">
        <div
          :id="item.item_id"
          class="block aspect-4/3 w-full cursor-pointer bg-cover bg-center"
          :style="{ backgroundImage: `url(${item.imgUrl})` }"
          @click="enterRowAd(item.route, item.item_id, item.client, item.product)"
        />
      </div>
    </div>

    <div class="border-gray mt-[40px] grid grid-cols-12 border-t-2 pt-5">
      <div class="col-span-12 items-end justify-center lg:mb-3">
        <div class="grid grid-cols-12 grid-rows-1">
          <div class="col-span-4 flex items-end justify-start md:col-span-3">
            <div class="mb-4 block flex inline-block flex-col">
              <div class="py-1 text-xs font-bold md:py-3 md:text-base lg:pb-[30px] lg:text-xl">
                Bestselling
              </div>
              <div class="block text-xl font-bold md:inline-block md:text-[24px] lg:text-[40px]">
                暢銷書籍
              </div>
            </div>
          </div>
          <div class="col-span-8 flex items-end justify-center md:col-span-9">
            <div class="w-full">
              <ElInfoMenu
                :info-menu-prop="bestsellingMenuData"
                @info-menu-emit="emitBestsellingMenu"
              />
            </div>
          </div>
        </div>
      </div>
      <div
        class="border-gray col-span-12"
        :class="{ 'border-l': bestsellingSwiperData.textContent.length > 0 }"
      >
        <ElBookSwiper
          :book-swiper-prop="bestsellingSwiperData"
          @book-swiper-emit="emitBestsellingSwiper"
        />
      </div>
      <div class="col-span-12 flex flex-col items-center justify-center">
        <hr class="border-gray w-full border-t-2" />
        <div class="mt-5 flex w-full items-center justify-center md:justify-end">
          <ElIconButton
            :icon-button-prop="{
              name: '查看更多暢銷好書',
              icon: 'ri-arrow-right-s-line',
              paddingRight: '5px',
            }"
            @click="helpers.forwardRoute('/shop/leaderboard')"
          />
        </div>
      </div>
    </div>

    <div class="mt-[20px] grid grid-cols-12 lg:mt-[60px]">
      <div class="col-span-12 flex flex-col pb-5">
        <div class="flex items-center justify-between">
          <div class="mb-3 block flex inline-block flex-col lg:hidden">
            <div class="py-1 text-xs font-bold md:py-3 md:text-base lg:py-[30px] lg:text-xl">
              Knowledge
            </div>
            <div
              class="block text-xl font-bold md:inline-block md:text-[24px] lg:pb-[30px] lg:text-[40px]"
            >
              知識分享
            </div>
          </div>

          <div class="custom-page w-[150px] lg:hidden">
            <ElSelect
              v-model="knowledgeSelector"
              placeholder="最新發布"
              class="custom-select-small"
            >
              <ElOption
                v-for="item in knowledgeOption"
                :key="item.value"
                :label="item.label"
                :value="item.value"
                :class="{
                  'text-secondary': knowledgeSelector === item.value,
                  'text-primary': knowledgeSelector !== item.value,
                }"
                class="my-2 text-[18px]!"
                @click="emitKnowledgeSelector(item.value)"
              />
            </ElSelect>
          </div>
        </div>
        <hr class="border-gray w-full border-t-2" />
      </div>
      <div class="border-gray col-span-12 hidden border-r py-5 lg:col-span-4 lg:block">
        <div class="mb-5 block flex inline-block flex-col">
          <div class="py-1 text-xs font-bold md:py-3 md:text-base lg:pb-[30px] lg:text-xl">
            Culture
          </div>
          <div
            class="block text-xl font-bold md:inline-block md:text-[24px] lg:pb-[20px] lg:text-[40px]"
          >
            藝文共賞
          </div>
        </div>
        <ElSideBar
          :side-bar-prop="props.homeHomepageProp.knowledge.menu"
          :active-item="knowledgeSelector"
          @side-bar-emit="emitSideBar"
        />
      </div>
      <div class="border-gray col-span-12 border-l lg:col-span-8">
        <ElInfoSwiper :info-swiper-prop="knowledgeSwiperData" @info-swiper-emit="emitInfoSwiper" />
      </div>
      <div class="col-span-12 flex flex-col items-center justify-center">
        <hr class="border-gray w-full border-t-2" />
        <div class="mt-5 flex w-full items-center justify-center md:justify-end">
          <ElIconButton
            :icon-button-prop="{
              name: '查看更多藝文內容',
              icon: 'ri-arrow-right-s-line',
              paddingRight: '5px',
            }"
            @click="helpers.forwardRoute('/lounge')"
          />
        </div>
      </div>
    </div>

    <div class="mt-[20px] grid grid-cols-12">
      <div class="col-span-12 items-end justify-center lg:mb-3">
        <div class="grid grid-cols-12 grid-rows-1">
          <div class="col-span-4 flex items-end justify-start md:col-span-3">
            <div class="mb-4 block flex inline-block flex-col">
              <div class="py-1 text-xs font-bold md:py-3 md:text-base lg:pb-[30px] lg:text-xl">
                Online Course
              </div>
              <div class="block text-xl font-bold md:inline-block md:text-[24px] lg:text-[40px]">
                優質課程
              </div>
            </div>
          </div>
          <div class="col-span-8 flex items-end justify-center md:col-span-9">
            <div class="mb-5 w-full">
              <hr class="border-gray border-t-2" />
            </div>
          </div>
        </div>
      </div>
      <div class="border-gray col-span-12 border-l">
        <ElCourseSwiper
          :course-swiper-prop="courseSwiperData"
          @course-swiper-emit="emitCourseSwiper"
        />
      </div>
      <div class="col-span-12 flex flex-col items-center justify-center">
        <hr class="border-gray w-full border-t-2" />
        <div class="mt-5 flex w-full items-center justify-center md:justify-end">
          <ElIconButton
            :icon-button-prop="{
              name: '查看更多優質課程',
              icon: 'ri-arrow-right-s-line',
              paddingRight: '5px',
            }"
            @click="helpers.forwardRoute('/courses/category/4')"
          />
        </div>
      </div>
    </div>

    <div class="mt-[20px] grid grid-cols-12">
      <div class="col-span-12 items-end justify-center lg:mb-3">
        <div class="grid grid-cols-12 grid-rows-1">
          <div class="col-span-4 flex items-end justify-start md:col-span-3">
            <div class="mb-4 block flex inline-block flex-col">
              <div class="py-1 text-xs font-bold md:py-3 md:text-base lg:pb-[30px] lg:text-xl">
                Celebrity
              </div>
              <div class="block text-xl font-bold md:inline-block md:text-[24px] lg:text-[40px]">
                風雲人物
              </div>
            </div>
          </div>
          <div class="col-span-8 flex items-end justify-center md:col-span-9">
            <div class="mb-5 w-full">
              <hr class="border-gray border-t-2" />
            </div>
          </div>
        </div>
      </div>
      <div class="border-gray col-span-12 border-l">
        <ElCelebritySwiper
          :celebrity-swiper-prop="celebritySwiperData"
          @celebrity-swiper-emit="emitCelebritySwiper"
        />
      </div>
      <div class="col-span-12 flex flex-col items-center justify-center">
        <hr class="border-gray w-full border-t-2" />
        <div class="mt-5 flex w-full items-center justify-center md:justify-end">
          <ElIconButton
            :icon-button-prop="{
              name: '查看更多風雲人物',
              icon: 'ri-arrow-right-s-line',
              paddingRight: '5px',
            }"
            @click="helpers.forwardRoute('/celebrities')"
          />
        </div>
      </div>
    </div>

    <!--    TODO: 若需要開啟最新消息區塊時，請將下方程式碼註解解除-->
    <!--    <div class="mt-[20px] grid grid-cols-12 lg:mt-[60px]">-->
    <!--      <div class="col-span-12 flex flex-col pb-5">-->
    <!--        <div class="flex items-center justify-between">-->
    <!--          <div class="mb-3 block flex inline-block flex-col lg:hidden">-->
    <!--            <div class="py-1 text-xs font-bold md:py-3 md:text-base lg:py-[30px] lg:text-xl">-->
    <!--              Hot News-->
    <!--            </div>-->
    <!--            <div-->
    <!--              class="block text-xl font-bold md:inline-block md:text-[24px] lg:pb-[30px] lg:text-[40px]"-->
    <!--            >-->
    <!--              熱門快訊-->
    <!--            </div>-->
    <!--          </div>-->
    <!--          <div class="custom-page w-[150px] lg:hidden">-->
    <!--            <ElSelect v-model="newsSelector" placeholder="即時訊息" class="custom-select-small">-->
    <!--              <ElOption-->
    <!--                v-for="item in newsOption"-->
    <!--                :key="item.value"-->
    <!--                :label="item.label"-->
    <!--                :value="item.value"-->
    <!--                :class="{-->
    <!--                  'text-secondary': newsSelector === item.value,-->
    <!--                  'text-primary': newsSelector !== item.value,-->
    <!--                }"-->
    <!--                class="my-2 text-[18px]!"-->
    <!--                @click="emitNewsSideBar(item.value)"-->
    <!--              />-->
    <!--            </ElSelect>-->
    <!--          </div>-->
    <!--        </div>-->
    <!--        <hr class="border-gray w-full border-t-2" />-->
    <!--      </div>-->
    <!--      <div class="border-gray col-span-12 hidden py-5 lg:col-span-4 lg:block lg:border-r">-->
    <!--        <div class="mb-5 block flex inline-block flex-col">-->
    <!--          <div class="py-1 text-xs font-bold md:py-3 md:text-base lg:pb-[30px] lg:text-xl">-->
    <!--            Hot News-->
    <!--          </div>-->
    <!--          <div-->
    <!--            class="block text-xl font-bold md:inline-block md:text-[24px] lg:pb-[20px] lg:text-[40px]"-->
    <!--          >-->
    <!--            熱門快訊-->
    <!--          </div>-->
    <!--        </div>-->
    <!--        <ElSideBar-->
    <!--          :side-bar-prop="props.homeHomepageProp.news.menu"-->
    <!--          :active-item="newsSelector"-->
    <!--          @side-bar-emit="emitNewsSideBar"-->
    <!--        />-->
    <!--      </div>-->
    <!--      <div class="border-gray col-span-12 lg:col-span-8 lg:border-l">-->
    <!--        <ElNewsRow-->
    <!--          :info-row-prop="newsRowData"-->
    <!--          @info-row-emit="emitNewsRow"-->
    <!--          v-if="postType === 1"-->
    <!--        />-->
    <!--        <ElNewsSwiper-->
    <!--          :news-swiper-prop="newsSwiperData"-->
    <!--          @news-swiper-emit="emitNewsSwiper"-->
    <!--          v-if="postType === 2"-->
    <!--        />-->
    <!--      </div>-->
    <!--      <div class="col-span-12 flex flex-col items-center justify-center">-->
    <!--        <hr class="border-gray mt-5 w-full border-t-2" />-->
    <!--        <div class="mt-5 flex w-full items-center justify-center md:justify-end">-->
    <!--          <ElIconButton-->
    <!--            :icon-button-prop="{-->
    <!--              name: '查看更多最新消息',-->
    <!--              icon: 'ri-arrow-right-s-line',-->
    <!--              paddingRight: '5px',-->
    <!--            }"-->
    <!--            @click="-->
    <!--              helpers.forwardRoute(-->
    <!--                `/news/category/${props.homeHomepageProp.news.menu.menuItem?.[0]?.id}`,-->
    <!--              )-->
    <!--            "-->
    <!--          />-->
    <!--        </div>-->
    <!--      </div>-->
    <!--    </div>-->
  </div>
</template>
