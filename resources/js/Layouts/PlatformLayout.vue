<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import '@/../scss/main.scss';
import ElHeaderBar from '@/Components/ElHeaderBar.vue';
import ElFooterBar from '@/Components/ElFooterBar.vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const roles = computed(() => auth.value?.roles ?? []);

const isSuperAdmin = computed(() => roles.value.includes('superadmin'));
const isAdmin = computed(() => roles.value.includes('admin'));
const isAdminOrAbove = computed(() => isSuperAdmin.value || isAdmin.value);

const mainMenu = computed(() => {
    const menu = [
        { title: '購物商城', route: 'products', active: true, branch: ['products'] },
        { title: '購物車', route: 'cart', active: true, branch: ['cart'] },
        { title: '我的訂單', route: 'orders', active: true, branch: ['orders'] },
    ];

    return menu;
});

const headerBarData = computed(() => {
    return (mainMenu.value ?? [])
        .filter((item) => item.active === true)
        .map((item) => ({
            title: item.title,
            route: item.route,
            active: item.active,
            branch: [item.route],
        }));
});

const footerBarData = ref([
    {
        category: '網頁資訊',
        content: [
            { title: '關於我們', route: '', allow: true },
        ],
    },
]);

const emitHeaderBarData = (data) => data;
const emitFooterBarData = (data) => data;
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
