<script setup>
import '@/../scss/main.scss';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import * as Heads from '@/Heads';
import ElBookSwiper from '@/Components/ElBookSwiper.vue';
import ElInfoMenu from '@/Components/ElInfoMenu.vue';
import ElImgSwiper from '@/Components/ElImgSwiper.vue';

const popoverWidth = ref(300);
const screenWidth = ref(window.innerWidth);
const updateSwiperItems = () => {
  if (screenWidth.value >= 1024) {
    popoverWidth.value = 300;
  } else if (screenWidth.value >= 768) {
    popoverWidth.value = 200;
  } else {
    popoverWidth.value = 150;
  }
};

const handleResize = () => {
  screenWidth.value = window.innerWidth;
  updateSwiperItems();
};

// NOTE: 接收shop index的prop
const props = defineProps({
  shopHomepageProp: [Array, Object],
});
// NOTE: 送出emit到shop index
const emit = defineEmits(['shopHomepageEmit']);

// NOTE: 接收各元件傳出的emit
const emitImgSwiper = (data) => {
  return data;
};

const emitBestsellingMenu = (data) => {
  if (data === '電子書') {
    bestsellingSwiperData.value.textContent = bestsellingDataTransfer(
      props.shopHomepageProp.bestselling.content,
      'ebook',
    );
  } else if (data === '有聲書') {
    bestsellingSwiperData.value.textContent = bestsellingDataTransfer(
      props.shopHomepageProp.others.audioBook,
      'audioBook',
    );
  }
};

const emitBestsellingSwiper = (data) => {
  return data;
};

const emitGuestMenu = (data) => {
  if (data === '電子書') {
    guestSwiperData.value.textContent = guestDataTransfer(
      props.shopHomepageProp.guest.content,
      'ebook',
    );
  } else if (data === '有聲書') {
    guestSwiperData.value.textContent = guestDataTransfer(
      props.shopHomepageProp.others.audioBook,
      'audioBook',
    );
  }
};
const emitGuestSwiper = (data) => {
  return data;
};
const emitNewBookMenu = (data) => {
  if (data === '電子書') {
    newBookSwiperData.value.textContent = newBookDataTransfer(
      props.shopHomepageProp.newBook.content,
      'ebook',
    );
  } else if (data === '有聲書') {
    newBookSwiperData.value.textContent = newBookDataTransfer(
      props.shopHomepageProp.others.audioBook,
      'audioBook',
    );
  }
};

const emitNewBookSwiper = (data) => {
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

const guestMenuData = ref({
  menu: props.shopHomepageProp.guest.menu,
  marginLg: '2px 25px',
  marginMd: '2px 10px',
  marginSm: '2px 0px',
});

const guestSwiperData = ref({
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
  textHeightMd: '230px',
  textHeightSm: '210px',
});

const bestsellingMenuData = ref({
  menu: props.shopHomepageProp.bestselling.menu,
  marginLg: '2px 25px',
  marginMd: '2px 10px',
  marginSm: '2px 0px',
});

const bestsellingSwiperData = ref({
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
  textHeightMd: '230px',
  textHeightSm: '210px',
});

const newBookMenuData = ref({
  menu: props.shopHomepageProp.newBook.menu,
  marginLg: '2px 25px',
  marginMd: '2px 10px',
  marginSm: '2px 0px',
});

const newBookSwiperData = ref({
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
  textHeightMd: '230px',
  textHeightSm: '210px',
});

const billboardDataTransfer = (data, bookType) => {
  const items = Object?.values(data || []);
  let bookRoute = '';

  if (bookType === 'ebook') {
    bookRoute = 'platform.book.show';
  } else if (bookType === 'audioBook') {
    bookRoute = 'platform.audios.show';
  }

  return items?.map((item) => {
    const bookIdMatch = item?.url?.match(/\/book\/([^\/]+)/);
    return {
      id: bookIdMatch ? bookIdMatch[1] : '',
      route: bookRoute,
      image: item?.imgUrl || '',
      isTargetBlank: true,
    };
  });
};
const guestDataTransfer = (data, bookType) => {
  const items = Object?.values(data || []);
  let filterItems = [];
  let bookRoute = '';

  if (bookType === 'ebook') {
    bookRoute = 'platform.book.show';
    filterItems = items?.filter((item) => item.type === 1 || item.type === 2);
  } else if (bookType === 'audioBook') {
    bookRoute = 'platform.audios.show';
    filterItems = items?.filter((item) => item.type === 3);
  }

  return filterItems.map((item) => {
    return {
      id: item?.audio_book?.id || item?.id || '',
      route: bookRoute,
      image: item?.audio_book?.coverUrl || item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: item?.title || '',
      salesPrice: item?.audio_book?.sales_price || item?.salesPrice || '',
      listPrice: item?.audio_book?.list_price || item?.list_price || '',
      isDiscount: item?.isDiscount || false,
      event: item?.events || [],
      category: item?.categories || [],
      isTargetBlank: true,
    };
  });
};

const bestsellingDataTransfer = (data, bookType) => {
  const items = Object?.values(data || []);
  let filterItems = [];
  let bookRoute = '';

  if (bookType === 'ebook') {
    bookRoute = 'platform.book.show';
    filterItems = items?.filter((item) => item.type === 1 || item.type === 2);
  } else if (bookType === 'audioBook') {
    bookRoute = 'platform.audios.show';
    filterItems = items?.filter((item) => item.type === 3);
  }

  return filterItems.map((item) => {
    return {
      id: item?.audio_book?.id || item?.id || '',
      route: bookRoute,
      image: item?.audio_book?.coverUrl || item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: item?.title || '',
      salesPrice: item?.audio_book?.sales_price || item?.salesPrice || '',
      listPrice: item?.audio_book?.list_price || item?.list_price || '',
      isDiscount: item?.isDiscount || false,
      event: item?.events || [],
      category: item?.categories || [],
      isTargetBlank: true,
    };
  });
};

const newBookDataTransfer = (data, bookType) => {
  const items = Object?.values(data || []);
  let filterItems = [];
  let bookRoute = '';

  if (bookType === 'ebook') {
    bookRoute = 'platform.book.show';
    filterItems = items?.filter((item) => item.type === 1 || item.type === 2);
  } else if (bookType === 'audioBook') {
    bookRoute = 'platform.audios.show';
    filterItems = items?.filter((item) => item.type === 3);
  }

  return filterItems.map((item) => {
    return {
      id: item?.audio_book?.ulid || item?.id || '',
      route: bookRoute,
      image: item?.audio_book?.coverUrl || item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: item?.title || '',
      salesPrice: item?.salesPrice || '',
      listPrice: item?.list_price || '',
      isDiscount: item?.isDiscount || false,
      event: item?.events || [],
      category: item?.categories || [],
      isTargetBlank: true,
    };
  });
};

const generateInitData = () => {
  billboardSwiperData.value.textContent = billboardDataTransfer(
    props.shopHomepageProp.billboard.content,
  );
  guestSwiperData.value.textContent = guestDataTransfer(
    props.shopHomepageProp.guest.content,
    'ebook',
  );
  bestsellingSwiperData.value.textContent = bestsellingDataTransfer(
    props.shopHomepageProp.bestselling.content,
    'ebook',
  );
  newBookSwiperData.value.textContent = newBookDataTransfer(
    props.shopHomepageProp.newBook.content,
    'ebook',
  );
};

onMounted(() => {
  generateInitData();
  updateSwiperItems();
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});
</script>
<template>
  <Head :title="Heads.shop.shopHomepageHead.title">
    <meta v-for="(tag, index) in Heads.shop.shopHomepageHead.meta" :key="index" v-bind="tag" />
  </Head>

  <div class="default-page text-primary min-h-screen">
    <div class="mb-[40px] grid grid-cols-12">
      <div class="border-gray col-span-12 border-l">
        <ElImgSwiper :img-swiper-prop="billboardSwiperData" @img-swiper-emit="emitImgSwiper" />
      </div>
    </div>

    <!--    TODO: 後端會員尚未建立暢銷書籍資料，建置完後再開放此部分-->
    <!--    <div class="mt-[20px] grid grid-cols-12">-->
    <!--      <div class="col-span-12 lg:mb-3">-->
    <!--        <div class="flex grid grid-cols-12 grid-rows-1 items-start justify-center">-->
    <!--          <div class="col-span-6 md:col-span-8 lg:col-span-6 lg:pt-[6px]">-->
    <!--            <span-->
    <!--              class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"-->
    <!--            >暢銷書籍推薦</span-->
    <!--            >-->
    <!--            <span class="text-xs font-bold md:text-base lg:text-xl">看看大家都在看什麼</span>-->
    <!--          </div>-->
    <!--          <div class="col-span-6 flex items-end justify-center md:col-span-4 lg:col-span-6">-->
    <!--            <div class="w-full">-->
    <!--              <ElInfoMenu :info-menu-prop="bestsellingMenuData" @info-menu-emit="emitBestsellingMenu" />-->
    <!--            </div>-->
    <!--          </div>-->
    <!--        </div>-->
    <!--      </div>-->
    <!--      <div class="border-gray col-span-12 border-l">-->
    <!--        <ElBookSwiper :book-swiper-prop="bestsellingSwiperData" @book-swiper-emit="emitBestsellingSwiper" />-->
    <!--      </div>-->
    <!--      <div class="col-span-12 flex flex-col items-center justify-center">-->
    <!--        <hr class="border-gray w-full border-t-2" />-->
    <!--      </div>-->
    <!--    </div>-->
    <!--    <div class="col-span-12 flex items-center justify-end mt-5">-->
    <!--      <ElIconButton :icon-button-prop="{ name: '查看完整書籍排行榜', icon: 'ri-arrow-right-s-line', paddingRight: '5px'}" @click="helpers.forwardRoute(route('platform.shop.leaderboard'))"/>-->
    <!--    </div>-->

    <!--    TODO: 後端會員尚未建立新上架書籍資料，建置完後再開放此部分-->
    <div class="mt-[20px] grid grid-cols-12">
      <div class="col-span-12 lg:mb-3">
        <div class="flex grid grid-cols-12 grid-rows-1 items-start justify-center">
          <div class="col-span-6 md:col-span-8 lg:col-span-6 lg:pt-[6px]">
            <span
              class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"
              >新上架書籍</span
            >
            <span class="text-xs font-bold md:text-base lg:text-xl">搶先一步閱讀知識</span>
          </div>
          <div class="col-span-6 flex items-end justify-center md:col-span-4 lg:col-span-6">
            <div class="w-full">
              <ElInfoMenu :info-menu-prop="newBookMenuData" @info-menu-emit="emitNewBookMenu" />
            </div>
          </div>
        </div>
      </div>
      <div
        class="border-gray col-span-12"
        :class="{ 'border-l': newBookSwiperData.textContent.length > 0 }"
      >
        <ElBookSwiper :book-swiper-prop="newBookSwiperData" @book-swiper-emit="emitNewBookSwiper" />
      </div>
      <div class="col-span-12 flex flex-col items-center justify-center">
        <hr class="border-gray w-full border-t-2" />
      </div>
    </div>

    <!--    TODO: 後端會員尚未建立猜你想看資料，建置完後再開放此部分-->
    <!--    <div class="mt-[20px] grid grid-cols-12">-->
    <!--      <div class="col-span-12 lg:mb-3">-->
    <!--        <div class="flex grid grid-cols-12 grid-rows-1 items-start justify-center">-->
    <!--          <div class="col-span-6 md:col-span-8 lg:col-span-6 lg:pt-[6px]">-->
    <!--            <span-->
    <!--              class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"-->
    <!--              >猜你想看</span-->
    <!--            >-->
    <!--            <span class="text-xs font-bold md:text-base lg:text-xl">快來知識的書海中遨遊</span>-->
    <!--          </div>-->
    <!--          <div class="col-span-6 flex items-end justify-center md:col-span-4 lg:col-span-6">-->
    <!--            <div class="w-full">-->
    <!--              <ElInfoMenu :info-menu-prop="guestMenuData" @info-menu-emit="emitGuestMenu" />-->
    <!--            </div>-->
    <!--          </div>-->
    <!--        </div>-->
    <!--      </div>-->
    <!--      <div class="border-gray col-span-12 border-l">-->
    <!--        <ElBookSwiper :book-swiper-prop="guestSwiperData" @book-swiper-emit="emitGuestSwiper" />-->
    <!--      </div>-->
    <!--      <div class="col-span-12 flex flex-col items-center justify-center">-->
    <!--        <hr class="border-gray w-full border-t-2" />-->
    <!--      </div>-->
    <!--    </div>-->
  </div>
</template>
