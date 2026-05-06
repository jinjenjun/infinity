<script setup>
import '@/../scss/main.scss';
import { onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import * as Heads from '@/Heads';
import * as helpers from '@/Libs/helpers.js';
import ElImgSwiper from '@/Components/ElImgSwiper.vue';
import ElCourseSwiper from '@/Components/ElCourseSwiper.vue';
import ElIconButton from '@/Components/ElIconButton.vue';

// NOTE: 接收course show的prop
const props = defineProps({
  courseHomepageProp: [Array, Object],
});
const emit = defineEmits(['courseHomepageEmit']);

// NOTE: 接收各元件傳出的emit
const emitImgSwiper = (data) => {
  return data;
};

const emitCourseSwiper = (data) => {
  return data;
};

const billboardSwiperData = {
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
};

const courseSwiperData = {
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
};

const billboardDataTransfer = (data) => {
  return data?.map((item) => {
    return {
      id: item?.id || '',
      url: item?.courseUrl,
      image: item?.imgUrl || '',
      isTargetBlank: true,
    };
  });
};

const courseDataTransfer = (data) => {
  const items = Object?.values(data || []);
  return items?.map((item) => {
    return {
      id: item?.ulid || '',
      route: 'platform.courses.show',
      image: item?.coverUrl || '',
      tag: [].concat(item?.categoryName || []).map(String),
      title: helpers.multiTitleTransfer(item?.name, item?.title),
      salesPrice: item?.product?.salesPrice,
      listPrice: item?.product?.list_price,
      teacher: item?.teacher.name || '',
      duration: item?.formattedDuration || '',
      isTargetBlank: true,
      publishStatus: item?.status || null,
    };
  });
};

const generateInitData = () => {
  billboardSwiperData.textContent = billboardDataTransfer(
    props.courseHomepageProp.billboard.content,
  );
  courseSwiperData.textContent = courseDataTransfer(props.courseHomepageProp.course?.content?.data);
  emit('courseHomepageEmit', {
    billboard: billboardSwiperData.textContent,
    course: courseSwiperData.textContent,
  });
};

onMounted(() => {
  generateInitData();
});

watch(
  () => props,
  () => {
    generateInitData();
  },
  { immediate: true },
);
</script>
<template>
  <Head :title="Heads.course.courseHomepageHead.title">
    <meta v-for="(tag, index) in Heads.course.courseHomepageHead.meta" :key="index" v-bind="tag" />
  </Head>

  <div class="default-page text-primary min-h-screen">
    <div class="mb-[40px] grid grid-cols-12">
      <div class="border-gray col-span-12 border-l">
        <ElImgSwiper :img-swiper-prop="billboardSwiperData" @img-swiper-emit="emitImgSwiper" />
      </div>
    </div>

    <div class="mt-[20px] grid grid-cols-12">
      <div class="col-span-12 mb-[30px] flex items-center justify-between">
        <div>
          <span
            class="block pr-[20px] text-xl font-bold md:inline-block md:pr-[30px] md:text-[32px] lg:pr-[40px] lg:text-[40px]"
          >
            熱門課程推薦
          </span>
        </div>
        <ElIconButton
          :icon-button-prop="{
            name: '查看全部已購買課程',
            icon: 'ri-arrow-right-s-line',
            paddingRight: '5px',
          }"
          @click="helpers.forwardRoute('/member/courses')"
        />
      </div>
      <div class="border-gray col-span-12 border-l">
        <ElCourseSwiper
          :course-swiper-prop="courseSwiperData"
          @course-swiper-emit="emitCourseSwiper"
        />
      </div>
      <div class="col-span-12 items-center justify-start">
        <hr class="border-gray w-full border-t-2" />
      </div>
    </div>
  </div>
</template>
