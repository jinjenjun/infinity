<script setup>
import { computed, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import '@/../scss/main.scss';
import ElHeaderBar from '@/Components/ElHeaderBar.vue';
import ElFooterBar from '@/Components/ElFooterBar.vue';

const page = usePage();

const mainMenu = [
   { title: '購物商城', route: 'products', active: true, branch: ['products'] },
   { title: '項目二', route: 'admin', active: true, branch: [] },
   { title: '項目三', route: 'admin', active: true, branch: [] }
]

const branchExtension = {
  products: [],
};

const headerBarData = ref([]);

const generateHeaderBar = () => {
  const map = {};
  mainMenu?.forEach((item) => {
    if (!map[item.title]) {
      map[item.title] = [];
    }
    map[item.title].push(item.route);
  });
  Object.entries(branchExtension).forEach(([key, extensions]) => {
    const menuItem = mainMenu?.find((item) => item.slug === key);
    if (menuItem) {
      const title = menuItem.title;
      if (!map[title]) {
        map[title] = [];
      }
      map[title] = Array.from(new Set([...map[title], ...extensions]));
    }
  });
  headerBarData.value = (mainMenu ?? [])
    .filter((item) => item.active === true)
    .map((item) => ({
      title: item.title,
      route: item.route,
      active: item.active,
      branch: map[item.title] || [],
    }));
};

const footerBarData = ref([
  {
    category: '網頁資訊',
    content: [
      { title: '關於我們', route: '', allow: true },
    ],
  },
]);

const emitHeaderBarData = (data) => {
  return data;
};
const emitFooterBarData = (data) => {
  return data;
};

onMounted(() => {
  generateHeaderBar();
  console.log('headerBarData:', headerBarData.value);
});
</script>

<template>
  <div class="text-primary grid grid-cols-12 px-[5vw] md:px-[12vw] lg:px-[9vw]">
    <div class="blur-transition z-10 col-span-12">
      <ElHeaderBar
        v-if="headerBarData.length"
        :header-bar-prop="headerBarData"
        @header-bar-emit="emitHeaderBarData"
      />
    </div>
    <div class="col-span-12">
      <slot />
    </div>
    <div class="col-span-12">
      <ElFooterBar :footer-bar-prop="footerBarData" @footer-bar-emit="emitFooterBarData" />
    </div>
  </div>
</template>
