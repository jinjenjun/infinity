<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useStore } from 'vuex';
import { vuexData } from '@/../store';
import * as APIs from '@/APIs';
import * as helpers from '@/Libs/helpers.js';
import ElInfoButton from '@/Components/ElInfoButton.vue';
import ElInfoMenu from '@/Components/ElInfoMenu.vue';
import ElIconButton from '@/Components/ElIconButton.vue';

const store = useStore();

const props = defineProps({
  accountStats: Object,
  memberCenterProp: [Array, Object],
});

const page = usePage();

const user = computed(() => page.props.auth.user);

const avatar = computed(() => {
  return user.value.media.length > 0
    ? user.value.media[0].original_url
    : '/assets/img/celebrities/blank.png';
});

const form = ref({
  name: user.value.name,
  email: user.value.email,
});

const memberMenuData = {
  menu: {
    menuItem: [
      {
        name: '會員資料',
        route: route('platform.member.index'),
        icon: 'ri-account-circle-fill',
        allow: true,
      },
      {
        name: '我的收藏',
        route: route('platform.member.bookmarks'),
        icon: 'ri-bookmark-line',
        allow: true,
      },
      {
        name: '我的發布',
        route: route('platform.member.posts'),
        icon: 'ri-file-cloud-line',
        allow: true,
      },
      {
        name: '我的課程',
        route: route('platform.member.courses'),
        icon: 'ri-video-line',
        allow: true,
      },
      {
        name: '我的書櫃',
        route: route('platform.member.bookshelf'),
        icon: 'ri-book-read-line',
        allow: true,
      },
      {
        name: '我的訂單',
        route: route('platform.member.orders'),
        icon: 'ri-file-list-line',
        allow: true,
      },
      {
        name: '我的優惠券',
        route: route('platform.member.coupons'),
        icon: 'ri-money-dollar-box-line',
        allow: true,
      },
      {
        name: '我的點數',
        route: route('platform.member.points'),
        icon: 'ri-money-dollar-circle-line',
        allow: true,
      },
    ],
    disabledMenuItem: [],
    startMenuItem: { name: props?.memberCenterProp?.menu?.startMenuItem?.name || '會員資料' },
  },
  marginLg: '2px 10px',
  marginMd: '2px 0px',
  marginSm: '2px 0px',
};

const memberData = ref({
  user: {
    name: page?.props?.auth?.user?.name || '使用者名稱',
    avatar_url: page?.props?.auth?.user?.avatar_url || '',
    role: page?.props?.auth?.user?.roles?.[0]?.name || '已註冊會員',
  },
  postData: [
    { name: '已發布文章', textContent: props?.accountStats?.posts_count || 0 },
    { name: '已購買書籍', textContent: props?.accountStats?.ebooks_count || 0 },
    { name: '已購買課程', textContent: props?.accountStats?.courses_count || 0 },
  ],
  memberLevel: [
    {
      name: '黃銅會員',
      textContent: '半年內累積消費金額NTD 1200',
      img: '/img/bronze_member_icon.svg',
      now: true,
      fulfill: true,
    },
    {
      name: '銀級會員',
      textContent: '半年內累積消費金額NTD 2400',
      img: '/img/silver_member_icon.svg',
      now: false,
      fulfill: false,
    },
    {
      name: '白金會員',
      textContent: '半年內累積消費金額NTD 3600',
      img: '/img/gold_member_icon.svg',
      now: false,
      fulfill: false,
    },
  ],
  buyPoint: usePage()?.props?.auth?.user?.buy_points || 0,
  giftPoint: usePage()?.props?.auth?.user?.gift_points || 0,
  spent: 1200,
});

const logout = () => {
  APIs.unlock.account.logout(route('logout')).then((res) => {
    ElNotification.success({
      title: '登出成功',
      offset: 100,
    });

    const bookmarkData = {
      title: '',
    };
    vuexData.unlock.bookmark.setBookmarkData(store, bookmarkData);

    router.reload();
  });
};
const emitMemberMenu = (data) => {
  document.querySelector('.default-page').classList.add('custom-head-bar');
  const item = memberMenuData.menu.menuItem.find((item) => item.name === data);
  item ? window.location.assign(item.route) : null;
};
</script>

<template>
  <div class="default-page blur-transition">
    <div class="border-light-gray grid grid-cols-12 md:border-b-2">
      <div
        class="border-light-gray col-span-12 flex flex-col items-start justify-start md:col-span-5 md:border-r-2 lg:col-span-4"
      >
        <div
          class="border-light-gray mt-[30px] flex grid w-full grid-cols-12 items-center justify-center border-b-2 py-5 md:py-0"
        >
          <div class="col-span-4 md:col-span-12">
            <div
              class="border-light-gray flex w-full flex-col items-start justify-start md:flex-row md:items-center md:border-b-2 md:pb-[30px]"
            >
              <div
                class="mr-2 mb-2 h-[50px] w-[50px] rounded-full bg-cover bg-center bg-no-repeat md:mr-5 md:mb-0 md:h-[90px] md:w-[90px]"
                :style="{
                  backgroundImage: 'url(' + avatar + ')',
                  backgroundSize: 'cover',
                  backgroundPosition: 'center',
                }"
              />
              <div
                class="flex w-[100px] flex-col items-start justify-start md:w-[120px] lg:w-[150px]"
              >
                <span class="flex items-center justify-center pb-2">
                  <span class="text-secondary text-card-description font-bold">{{
                    memberData.user.role
                  }}</span>
                </span>
                <span
                  class="w-full pb-2 text-xs font-bold wrap-break-word whitespace-pre-wrap md:text-xl lg:text-2xl"
                >
                  {{ memberData.user.name }}
                </span>
              </div>
            </div>
            <ElIconButton
              class="my-5 w-full md:hidden"
              :icon-button-prop="{
                name: '登出',
                icon: 'ri-arrow-right-s-line',
                paddingRight: '5px',
              }"
              @click="logout"
            />
          </div>

          <div class="col-span-8 mb-[30px] md:col-span-12 md:mb-0">
            <div
              class="text-secondary text-card-title flex w-full items-center justify-end pr-5 pb-4 md:hidden md:pr-0"
            >
              <!--            TODO: 後端會員等級資料建置後，將以下程式碼取消註解-->

              <!--                          <div v-for="(item, index) in memberData.memberLevel" :key="index">-->
              <!--                            <div class="flex items-center justify-center" v-if="item.now">-->
              <!--                              <img :src="item.img" alt="" class="my-1 mr-2 w-[20px]" />-->
              <!--                              <p class="mr-2">{{ item.name }}</p>-->
              <!--                            </div>-->
              <!--                          </div>-->
              <ElPopover trigger="click" placement="bottom" :width="300">
                <template #reference>
                  <div class="flex cursor-pointer flex-col items-start justify-start">
                    <div class="m-1 flex items-start justify-start">
                      <img src="/img/buy_points.svg" alt="" class="mr-2 w-[20px]" />
                      <p>{{ helpers.largeNumberComma(memberData.buyPoint) }}</p>
                    </div>
                    <div class="m-1 flex items-start justify-start">
                      <img src="/img/gift_points.svg" alt="" class="mr-2 w-[20px]" />
                      <p>{{ helpers.largeNumberComma(memberData.giftPoint) }}</p>
                    </div>
                  </div>
                </template>
                <template #default>
                  <div class="flex flex-col items-start justify-start">
                    <div class="m-1 flex items-start justify-start">
                      <img src="/img/buy_points.svg" alt="" class="mr-2 w-[20px]" />
                      <p class="font-bold">
                        現金點數<span class="text-secondary px-2">{{
                          helpers.withComma(memberData.buyPoint)
                        }}</span>
                      </p>
                    </div>
                    <div class="m-1 flex items-start justify-start">
                      <img src="/img/gift_points.svg" alt="" class="mr-2 w-[20px]" />
                      <p class="font-bold">
                        紅利點數<span class="text-secondary px-2">{{
                          helpers.withComma(memberData.giftPoint)
                        }}</span>
                      </p>
                    </div>
                  </div>
                </template>
              </ElPopover>
            </div>
            <div class="flex w-full items-center justify-end md:justify-start md:px-0 md:py-5">
              <slot name="postDataInSideBar"></slot>
            </div>

            <div
              class="border-light-gray flex hidden w-full flex-col items-center justify-center md:block md:border-t-2 md:py-5 md:pt-[30px]"
            >
              <!--            TODO: 後端會員等級資料建置後，將以下程式碼取消註解-->

              <!--                            <div v-for="(item, index) in memberData.memberLevel" :key="index">-->
              <!--                              <div class="flex items-center justify-start" v-if="item.now">-->
              <!--                                <p class="text-page-subtitle mr-2">{{ item.name }}</p>-->
              <!--                                <img :src="item.img" alt="" class="my-1 mr-2 w-[20px]" />-->
              <!--                              </div>-->
              <!--                            </div>-->
              <ElPopover trigger="click" placement="bottom" :width="300">
                <template #reference>
                  <div class="cursor-pointer">
                    <div class="mb-4 flex items-center justify-start">
                      <p class="text-page-subtitle inline-block pr-2 font-bold">現金點數</p>
                      <img src="/img/buy_points.svg" alt="" class="mr-5 inline-block w-[20px]" />
                      <p class="text-secondary text-page-subtitle inline-block">
                        {{ helpers.largeNumberComma(memberData.buyPoint) }}
                      </p>
                    </div>
                    <div class="flex items-center justify-start">
                      <p class="text-page-subtitle inline-block pr-2 font-bold">紅利點數</p>
                      <img src="/img/gift_points.svg" alt="" class="mr-5 inline-block w-[20px]" />
                      <p class="text-secondary text-page-subtitle inline-block">
                        {{ helpers.largeNumberComma(memberData.giftPoint) }}
                      </p>
                    </div>
                  </div>
                </template>
                <template #default>
                  <div class="mb-4 flex items-center justify-start">
                    <p class="text-page-subtitle inline-block pr-2 font-bold">現金點數</p>
                    <img src="/img/buy_points.svg" alt="" class="mr-5 inline-block w-[20px]" />
                    <p class="text-secondary text-page-subtitle inline-block">
                      {{ helpers.withComma(memberData.buyPoint) }}
                    </p>
                  </div>
                  <div class="flex items-center justify-start">
                    <p class="text-page-subtitle inline-block pr-2 font-bold">紅利點數</p>
                    <img src="/img/gift_points.svg" alt="" class="mr-5 inline-block w-[20px]" />
                    <p class="text-secondary text-page-subtitle inline-block">
                      {{ helpers.withComma(memberData.giftPoint) }}
                    </p>
                  </div>
                </template>
              </ElPopover>
            </div>
          </div>
        </div>
        <div class="mb-5 w-full md:hidden">
          <ElInfoMenu :info-menu-prop="memberMenuData" @info-menu-emit="emitMemberMenu" />
        </div>
        <div class="hidden w-full md:block">
          <div class="flex flex-col items-center justify-start">
            <div
              class="flex w-full flex-col items-center justify-start"
              :class="item.allow ? '' : 'disable-execution'"
              v-for="(item, index) in memberMenuData.menu.menuItem"
              :key="index"
            >
              <a :href="item.route" class="border-light-gray w-full cursor-pointer border-b-2 py-5">
                <span class="text-page-subtitle">
                  <i class="pr-2 text-2xl font-normal" :class="item.icon" />
                  {{ item.name }}
                </span>
              </a>
            </div>
            <ElInfoButton class="my-[30px] mr-5 hidden w-full md:block" @click="logout">
              <span class="flex items-center justify-center">
                <span class="pr-2">登出</span>
                <i class="ri-arrow-right-s-line text-button-icon text-white" />
              </span>
            </ElInfoButton>
          </div>
        </div>
      </div>
      <div class="col-span-12 flex flex-col items-start justify-start md:col-span-7 lg:col-span-8">
        <slot name="pageContent"></slot>
      </div>
    </div>
  </div>
</template>
