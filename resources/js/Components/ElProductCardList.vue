<script setup>
import { ref, onMounted, watch } from 'vue';
import '@/../scss/main.scss';
import * as helpers from '@/Libs/helpers.js';

const props = defineProps({
  productCardProp: [Array, Object],
});
const emit = defineEmits(['productCardEmit']);

const emitProductCardData = () => {
  emit('productCardEmit', 'productCardList元件傳出資料');
};

const screenWidth = ref(window.innerWidth);
const backgroundImageRatio = ref();
const visibleItems = ref([]);
const backgroundImageHeight = ref(props.productCardProp.backgroundImageHeightLg);
const updateVisibleItems = () => {
  if (screenWidth.value >= 1024) {
    visibleItems.value = props.productCardProp.textContent;
    backgroundImageHeight.value = props.productCardProp.backgroundImageHeightLg;
  } else if (screenWidth.value >= 768) {
    visibleItems.value = props.productCardProp.textContent;
    backgroundImageHeight.value = props.productCardProp.backgroundImageHeightMd;
  } else {
    visibleItems.value = props.productCardProp.textContent;
    backgroundImageHeight.value = props.productCardProp.backgroundImageHeightSm;
  }
};

onMounted(() => {
  updateVisibleItems();
  window.addEventListener('resize', () => {
    screenWidth.value = window.innerWidth;
    updateVisibleItems();
  });
});
watch(
  () => props,
  () => {
    updateVisibleItems();
    backgroundImageRatio.value = props?.productCardProp?.backgroundImageRatio || '4/3';
  },
  { immediate: true, deep: true },
);
</script>

<template>
  <div class="border-gray grid w-full grid-cols-12">
    <div
      class="border-gray relative col-span-6 cursor-pointer border-r p-3 pb-5 lg:col-span-3"
      v-for="(item, index) in visibleItems"
      :key="index"
    >
      <a
        :href="item.route"
        class="flex w-full items-end justify-center rounded bg-cover bg-center bg-no-repeat"
        :style="{
          backgroundImage: `url(${item.image})`,
          aspectRatio: backgroundImageRatio,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
        }"
      />
      <div class="overflow-hidden">
        <div class="my-2">
          <ElPopover trigger="click" placement="bottom" :width="100">
            <template #reference>
              <div class="h-full w-full cursor-pointer overflow-hidden truncate">
                <span
                  v-for="(item, index) in item.tag"
                  :key="index"
                  class="bg-light-brown mr-2 inline-block rounded-sm p-1 text-xs"
                >
                  <span class="line-clamp-1 text-xs">{{ item }}</span>
                </span>
              </div>
            </template>
            <template #default>
              <div class="h-full w-full cursor-pointer overflow-hidden">
                <span
                  v-for="(item, index) in item.tag"
                  :key="index"
                  class="bg-light-brown m-1 inline-block rounded-sm p-1 text-xs"
                >
                  <span class="text-xs">{{ item }}</span>
                </span>
              </div>
            </template>
          </ElPopover>
        </div>
        <a
          :href="item.route"
          class="text-card-title mb-2 line-clamp-2 h-[40px] md:h-[50px] lg:h-[55px]"
        >
          {{ item.title }}
        </a>
        <a
          :href="item.route"
          class="mb-2 line-clamp-3 whitespace-normal wrap-break-word break-all text-xs lg:text-base"
          v-html="item.description"
        />
        <div class="flex h-[40px] flex-col items-end justify-end md:h-[45px] lg:h-[50px]">
            <span
              class="text-xs line-through"
              v-if="helpers.parseNumber(item.listPrice) !== helpers.parseNumber(item.salesPrice)"
            >
              NTD {{ helpers.numberComma(item.listPrice) }}
            </span>
            <span
              class="text-card-description font-bold"
            >
              NTD {{ helpers.numberComma(item.salesPrice) }}
            </span>
        </div>
      </div>
    </div>
  </div>
</template>
