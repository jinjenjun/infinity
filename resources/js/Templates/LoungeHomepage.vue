<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import * as Heads from '@/Heads';
import * as helpers from '@/Libs/helpers';
import '@/../scss/main.scss';
import ElInfoRow from '@/Components/ElInfoRow.vue';
import ElImgSwiper from '@/Components/ElImgSwiper.vue';
import ElInfoSwiper from '@/Components/ElInfoSwiper.vue';
import ElCelebritySwiper from '@/Components/ElCelebritySwiper.vue';

const screenWidth = ref(window.innerWidth);

let knowledgeOption = [];

// NOTE: 接收lounge index的prop
const props = defineProps({
  loungeHomepageProp: [Array, Object],
});
// NOTE: 送出emit到lounge index
const emit = defineEmits(['loungeHomepageEmit']);

// NOTE: 接收各元件傳出的emit
const emitDiscussionSwiper = (data) => {
  return data;
};
const emitDiscussionRow = (data) => {
  return data;
};
const emitArticleSwiper = (data) => {
  return data;
};
const emitSectionSwiper = (data) => {
  return data;
};
const emitGuestSwiper = (data) => {
  return data;
};

const emitCelebritySwiper = (data) => {
  return data;
};

const billboardData = ref();

const articleSwiperData = {
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
  textHeightLg: '280px',
  textHeightMd: '220px',
  textHeightSm: '200px',
};

const sectionSwiperData = {
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
  textHeightLg: '280px',
  textHeightMd: '220px',
  textHeightSm: '200px',
};

const guestSwiperData = {
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
  textHeightLg: '280px',
  textHeightMd: '220px',
  textHeightSm: '200px',
};

const discussionSwiperData = {
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
  backgroundImageRatio: '75%',
  textHeightLg: '280px',
  textHeightMd: '220px',
  textHeightSm: '200px',
};

const discussionRowData = {
  textContent: '',
  containerHeightLg: '20%',
  containerHeightMd: '20%',
  containerHeightSm: '20%',
  commentHidden: false,
};

const knowledgeSwiperData = {
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
};

const celebritySwiperData = {
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
};

const billboardDataTransfer = (data) => {
  return data?.map((item) => {
    return {
      id: item?.id || '',
      route: 'platform.posts.show',
      image: item?.coverUrl || '',
      title: item?.title || '',
      likeCount: item?.likes || 0,
      commentCount: item?.commentsCount || 0,
      viewCount: 0,
      isTargetBlank: true,
    };
  });
};

const articleDataTransfer = (data) => {
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

const sectionDataTransfer = (data) => {
  return data?.map((item) => {
    return {
      id: item?.id || '',
      route: 'platform.sections.show',
      image: item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: item?.title || '',
      description: item?.overview || '',
      hashtag: item?.tags?.map((tag) => tag?.name?.zh_TW) || [],
      isTargetBlank: true,
    };
  });
};

const guestDataTransfer = (data) => {
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

const discussionDataTransfer = (data) => {
  return data?.map((item) => {
    return {
      id: item?.id || '',
      route: 'platform.posts.show',
      image: item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: item?.title || '',
      description: item?.overview || '',
      hashtag: item?.tags?.map((tag) => tag?.name?.zh_TW) || [],
      likeCount: item?.likes || 0,
      commentCount: item?.commentsCount || 0,
      viewCount: 0,
      isTargetBlank: true,
    };
  });
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

const generateInitData = () => {
  billboardData.value = billboardDataTransfer(props.loungeHomepageProp.billboard.content);
  articleSwiperData.textContent = articleDataTransfer(props.loungeHomepageProp.article.content);
  sectionSwiperData.textContent = sectionDataTransfer(props.loungeHomepageProp.section.content);
  guestSwiperData.textContent = guestDataTransfer(props.loungeHomepageProp.guest.content);
  discussionSwiperData.textContent = discussionDataTransfer(
    props.loungeHomepageProp.discussion.content,
  );
  discussionRowData.textContent = discussionDataTransfer(
    props.loungeHomepageProp.discussion.content,
  );
  knowledgeSwiperData.textContent = knowledgeDataTransfer(
    props.loungeHomepageProp.knowledge.content,
  );
  celebritySwiperData.textContent = celebrityDataTransfer(
    props.loungeHomepageProp.celebrity.content,
  );
  sectionSwiperData.textContent = sectionDataTransfer(props.loungeHomepageProp.section.content);
};

onMounted(() => {
  generateInitData();
  knowledgeOption = props?.loungeHomepageProp?.knowledge?.menu?.menuItem?.map((item) => {
    return {
      value: item.title,
      label: item.title,
    };
  });
  window.addEventListener('resize', () => {
    screenWidth.value = window.innerWidth;
  });
});

watch(
  () => props,
  () => {
    generateInitData();
  },
  { immediate: true, deep: true },
);
</script>
<template>
  <Head :title="Heads.lounge.loungeHomepageHead.title">
    <meta v-for="(tag, index) in Heads.lounge.loungeHomepageHead.meta" :key="index" v-bind="tag" />
  </Head>

  <div class="default-page text-primary">
    <div class="column-content min-h-screen">
      <div class="flex grid grid-cols-12 items-center justify-center gap-2 lg:gap-6">
        <a
          class="relative col-span-12 flex items-end justify-center overflow-hidden rounded md:col-span-8"
          :style="{ aspectRatio: '4 / 3' }"
          :href="route(billboardData[0].route, { id: billboardData[0].id })"
          target="_blank"
          v-if="billboardData && billboardData.length > 0 && billboardData[0] !== ''"
        >
          <img
            class="absolute top-0 left-0 h-full w-full object-cover"
            :src="billboardData[0].image"
            :alt="billboardData[0].title || 'billboard image'"
            loading="lazy"
          />
          <ElCard
            class="relative z-10 m-3 flex w-full items-center justify-center md:h-[23%] lg:mb-[50px] lg:h-[150px]"
            style="background-color: rgba(0, 0, 0, 0.2)"
          >
            <p class="mb-2 line-clamp-1 text-xs text-white">
              {{ billboardData[0].subtitle }}
            </p>
            <p class="mb-2 line-clamp-1 text-base font-bold text-white lg:text-2xl">
              {{ billboardData[0].title }}
            </p>
            <p
              class="h-0 w-0 wrap-break-word break-all whitespace-normal md:line-clamp-2 md:w-full md:text-sm md:text-white"
            >
              {{ billboardData[0].description }}
            </p>
          </ElCard>
        </a>
        <div class="col-span-12 rounded md:col-span-4">
          <div class="grid grid-cols-12 grid-rows-1 gap-2 md:grid-rows-2 lg:gap-4">
            <a
              class="relative col-span-6 flex items-end justify-center overflow-hidden rounded md:col-span-12"
              :style="{ aspectRatio: '4 / 3' }"
              :href="route(billboardData[1].route, { id: billboardData[1].id })"
              target="_blank"
              v-if="billboardData && billboardData.length > 1 && billboardData[1] !== ''"
            >
              <img
                class="absolute top-0 left-0 h-full w-full object-cover"
                :src="billboardData[1].image"
                :alt="billboardData[1].title || 'billboard image'"
                loading="lazy"
              />
              <ElCard
                class="relative z-10 m-3 flex w-full items-center justify-center md:h-[50%] lg:mb-[50px] lg:h-[150px]"
                style="background-color: rgba(0, 0, 0, 0.2)"
              >
                <p class="mb-2 line-clamp-1 text-xs text-white">
                  {{ billboardData[1].subtitle }}
                </p>
                <p
                  class="line-clamp-1 text-base font-bold wrap-break-word break-all whitespace-normal text-white md:line-clamp-2 lg:text-2xl"
                >
                  {{ billboardData[1].title }}
                </p>
              </ElCard>
            </a>
            <a
              class="relative col-span-6 flex items-end justify-center overflow-hidden rounded md:col-span-12"
              :style="{ aspectRatio: '4 / 3' }"
              :href="route(billboardData[1].route, { id: billboardData[2].id })"
              target="_blank"
              v-if="billboardData && billboardData.length > 2 && billboardData[2] !== ''"
            >
              <img
                class="absolute top-0 left-0 h-full w-full object-cover"
                :src="billboardData[2].image"
                :alt="billboardData[2].title || 'billboard image'"
                loading="lazy"
              />
              <ElCard
                class="relative z-10 m-3 flex w-full items-center justify-center md:h-[50%] lg:mb-[50px] lg:h-[150px]"
                style="background-color: rgba(0, 0, 0, 0.2)"
              >
                <p class="mb-2 line-clamp-1 text-xs text-white">
                  {{ billboardData[2].subtitle }}
                </p>
                <p
                  class="line-clamp-1 text-base font-bold wrap-break-word break-all whitespace-normal text-white md:line-clamp-2 lg:text-2xl"
                >
                  {{ billboardData[2].title }}
                </p>
              </ElCard>
            </a>
          </div>
        </div>
      </div>

      <div class="md:[60px] mt-[40px] grid grid-cols-12 gap-4 lg:mt-[120px]">
        <div class="col-span-12 mb-5">
          <span class="text-xl font-bold md:text-[32px] lg:text-[40px]">大家都在討論...</span>
          <hr class="border-gray mt-5 w-full border-t-2" />
        </div>
        <div class="col-span-12 mt-2 rounded md:col-span-4 lg:col-span-3">
          <ElImgSwiper
            :img-swiper-prop="discussionSwiperData"
            @img-swiper-emit="emitDiscussionSwiper"
          />
        </div>
        <div class="col-span-12 rounded md:col-span-8 lg:col-span-9">
          <ElInfoRow :info-row-prop="discussionRowData" @info-row-emit="emitDiscussionRow" />
        </div>
        <div class="col-span-12 mt-1">
          <hr class="border-gray w-full border-t-2" />
        </div>
      </div>

      <div class="mt-[40px] grid grid-cols-12 lg:mt-[60px]">
        <div class="col-span-12 mb-5">
          <span
            class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"
            >最新文章</span
          >
          <span class="text-xs font-bold md:text-base lg:text-xl"
            >最新趨勢與洞見，為你帶來全新啟發</span
          >
          <hr class="border-gray mt-5 w-full border-t-2" />
        </div>
        <div class="border-gray col-span-12 border-l">
          <ElInfoSwiper
            :info-swiper-prop="articleSwiperData"
            @info-swiper-emit="emitArticleSwiper"
          />
        </div>
        <div class="col-span-12">
          <hr class="border-gray w-full border-t-2" />
        </div>
      </div>

      <!--      TODO: 後端資料尚未完成，開發連載時再開啟-->
      <!--      <div class="mt-[40px] grid grid-cols-12 lg:mt-[60px]">-->
      <!--        <div class="col-span-12 mb-5">-->
      <!--          <span-->
      <!--            class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"-->
      <!--            >最新連載</span-->
      <!--          >-->
      <!--          <span class="text-xs font-bold md:text-base lg:text-xl"-->
      <!--            >用文字連接世界脈動，進行深入探討</span-->
      <!--          >-->
      <!--          <hr class="border-gray mt-5 w-full border-t-2" />-->
      <!--        </div>-->
      <!--        <div class="border-gray col-span-12 border-l">-->
      <!--          <ElInfoSwiper-->
      <!--            :info-swiper-prop="sectionSwiperData"-->
      <!--            @info-swiper-emit="emitSectionSwiper"-->
      <!--          />-->
      <!--        </div>-->
      <!--        <div class="col-span-12">-->
      <!--          <hr class="border-gray w-full border-t-2" />-->
      <!--        </div>-->
      <!--      </div>-->

      <div class="mt-[40px] grid grid-cols-12 lg:mt-[60px]">
        <div class="col-span-12 mb-5">
          <span
            class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"
            >猜你喜歡</span
          >
          <span class="text-xs font-bold md:text-base lg:text-xl"
            >快來看看你可能會感興趣的文章吧！</span
          >
          <hr class="border-gray mt-5 w-full border-t-2" />
        </div>
        <div class="border-gray col-span-12 border-l">
          <ElInfoSwiper :info-swiper-prop="guestSwiperData" @info-swiper-emit="emitGuestSwiper" />
        </div>
        <div class="col-span-12">
          <hr class="border-gray w-full border-t-2" />
        </div>
      </div>

      <div class="mt-[40px] grid grid-cols-12 lg:mt-[60px]">
        <div class="col-span-12 mb-5">
          <span
            class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"
          >風雲人物</span
          >
          <span class="text-xs font-bold md:text-base lg:text-xl"
          >從個人抉擇看見時代的演進變化</span
          >
          <hr class="border-gray mt-5 w-full border-t-2" />
        </div>
        <div class="border-gray col-span-12 border-l">
          <ElCelebritySwiper
            :celebrity-swiper-prop="celebritySwiperData"
            @celebrity-swiper-emit="emitCelebritySwiper"
          />
        </div>
      </div>
      <div class="col-span-12 items-center justify-start">
        <hr class="border-gray w-full border-t-2" />
      </div>
    </div>
  </div>
</template>
