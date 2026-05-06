<script setup>
import { defineAsyncComponent, onMounted, ref } from 'vue';
import '@/../scss/main.scss';

const ReaderOverview = defineAsyncComponent(() => import('@/Templates/ReaderOverview.vue'));
const SalesAnalysis = defineAsyncComponent(() => import('@/Templates/SalesAnalysis.vue'));
const ReaderBehavior = defineAsyncComponent(() => import('@/Templates/ReaderBehavior.vue'));
const ReaderInsight = defineAsyncComponent(() => import('@/Templates/ReaderInsight.vue'));
const BookProfile = defineAsyncComponent(() => import('@/Templates/BookProfile.vue'));

const props = defineProps({
  useRealData: {
    type: Boolean,
    default: false,
  },
  totalReadingHours: {
    type: Number,
    default: 0,
  },
  topBooks: {
    type: Array,
    default: () => [],
  },
  bookReadingTotals: {
    type: Array,
    default: () => [],
  },
  overviewData: {
    type: Object,
    default: () => ({}),
  },
  analysisData: {
    type: Object,
    default: () => ({}),
  },
  behaviorData: {
    type: Object,
    default: () => ({}),
  },
  readerInsight: {
    type: Object,
    default: () => ({}),
  },
  bookProfile: {
    type: Object,
    default: () => ({}),
  },
  canSelectCompany: {
    type: Boolean,
    default: false,
  },
  companyOptions: {
    type: Object,
    default: () => ({}),
  },
  companyId: {
    type: Number,
    default: null,
  },
  livewireId: {
    type: String,
    default: null,
  },
  overviewTotals: {
    type: Object,
    default: () => ({}),
  },
});

const readerOverviewData = ref({});
const salesAnalysisData = ref({});
const readerBehaviorData = ref({});
const readerInsightData = ref({});
const bookProfileData = ref({
  profile: [],
  timePeriod: '',
});
const bookSearchResults = ref([]);
const bookSearchLoading = ref(false);
const categorySelector = ref('全覽概況');
const categoryOption = ref([]);
const dateSelector = ref('近三個月');
const dateOption = ref([
  { label: '本月分析', value: 'thisMonth' },
  { label: '近三個月', value: 'lastThreeMonths' },
  { label: '近一年', value: 'lastTwelveMonths' },
]);
const companySelector = ref(null);
const companyOption = ref([]);

const tagList = ref([]);
const categoryList = ref([
  { name: '全覽概況', value: 'overview' },
  { name: '銷售分析', value: 'analysis' },
  { name: '閱讀行為', value: 'behavior' },
  { name: '讀者洞察', value: 'insight' },
  { name: '書籍詳情', value: 'profile' },
]);
const categoryMap = ref({
  overview: true,
  analysis: false,
  behavior: false,
  insight: false,
  profile: false,
});
const categoryStorageKey = 'sales-chart-active-category';
let mockPropsPromise = null;

const cloneData = (value, fallback = {}) => {
  try {
    return JSON.parse(JSON.stringify(value ?? fallback));
  } catch (error) {
    return JSON.parse(JSON.stringify(fallback));
  }
};

const getMockProps = async () => {
  if (!mockPropsPromise) {
    mockPropsPromise = import('@/../../public/public-doc').then((module) => module.unlock.sales.mockProps());
  }

  const mockProps = await mockPropsPromise;

  return cloneData(mockProps, {});
};

const assignTimePeriod = (state, period) => {
  if (!state.value || typeof state.value !== 'object') {
    state.value = {};
  }

  state.value.timePeriod = period;
};

const loadStoredCategory = () => {
  try {
    return sessionStorage.getItem(categoryStorageKey);
  } catch (error) {
    return null;
  }
};

const storeCategory = (value) => {
  try {
    sessionStorage.setItem(categoryStorageKey, value);
  } catch (error) {
    // ignore
  }
};

const normalizeCompanyOptions = () => {
  if (!props.canSelectCompany || !props.companyOptions) return [];

  return Object.entries(props.companyOptions).map(([value, label]) => ({
    value: Number(value),
    label: String(label),
  }));
};

const resolveCompanySelector = () => {
  companyOption.value = normalizeCompanyOptions();
  if (companyOption.value.length === 0) {
    companySelector.value = null;
    return;
  }

  const resolvedId = Number(props.companyId);
  if (Number.isFinite(resolvedId)) {
    const target = companyOption.value.find((option) => option.value === resolvedId);
    if (target) {
      companySelector.value = resolvedId;
      return;
    }
  }

  companySelector.value = companyOption.value[0].value;
};

const emitCompanySelector = (value) => {
  if (!props.canSelectCompany || !props.livewireId) return;
  const resolvedValue = Number(value);
  if (!Number.isFinite(resolvedValue)) return;

  const livewire = window.Livewire?.find(props.livewireId);
  livewire?.set('companyId', resolvedValue);
};

const resolveLivewire = () => {
  if (!props.livewireId) return null;

  return window.Livewire?.find(props.livewireId) ?? null;
};

const requestBookProfile = async (book) => {
  const livewire = resolveLivewire();
  if (!livewire) return null;

  const profile = await livewire.call('loadBookProfile', book.id);
  if (!profile || Object.keys(profile).length === 0) {
    return null;
  }

  return profile;
};

const handleBookProfileSelect = async (book) => {
  if (!book) return;

  bookSearchLoading.value = true;
  try {
    const profile = await requestBookProfile(book);
    if (!profile) {
      ElNotification.warning({
        title: '查無此書',
        message: '請重新輸入正確資訊',
        offset: 100,
      });
      return;
    }

    bookProfileData.value.profile = [profile];
    bookSearchResults.value = [];
  } finally {
    bookSearchLoading.value = false;
  }
};

const emitReaderOverview = (data) => {
  return data;
};

const emitSalesAnalysis = (data) => {
  return data;
};

const emitReaderBehavior = (data) => {
  return data;
};

const emitReaderInsight = (data) => {
  return data;
};

const emitBookProfile = async (data) => {
  if (!data) return;

  const keyword = data.trim();

  if (props.useRealData) {
    const livewire = resolveLivewire();
    if (!livewire) return;

    bookSearchLoading.value = true;
    try {
      const results = await livewire.call('searchBooks', keyword);
      const normalizedResults = Array.isArray(results) ? results : [];
      bookSearchResults.value = normalizedResults;
      const resultCount = normalizedResults.length;

      if (resultCount === 1) {
        await handleBookProfileSelect(normalizedResults[0]);
      }

      if (resultCount === 0) {
        ElNotification.warning({
          title: '查無此書',
          message: '請重新輸入正確資訊',
          offset: 100,
        });
      }
    } finally {
      bookSearchLoading.value = false;
    }

    return;
  }

  const { profile: mockProfile = [] } = await getMockProps();
  const targetBook = mockProfile.find(
    (book) => book.specification.isbn === keyword || book.specification.name.includes(keyword),
  );

  if (targetBook) {
    bookProfileData.value.profile = [targetBook];
  } else {
    ElNotification.warning({
      title: '查無此書',
      message: '請重新輸入正確資訊',
      offset: 100,
    });
  }
};

const generateInitData = async () => {
  const defaultPeriod = 'lastThreeMonths';

  tagList.value = categoryList.value?.map(({ name, value }) => ({ name, value }));
  categoryOption.value = categoryList.value?.map(({ name, value }) => ({
    label: name,
    value,
  }));

  if (props.useRealData) {
    readerOverviewData.value = cloneData(props.overviewData, {});
    salesAnalysisData.value = cloneData(props.analysisData, {});
    readerBehaviorData.value = cloneData(props.behaviorData, {});
    readerInsightData.value = cloneData(props.readerInsight, {});

    const resolvedBookProfile =
      props.bookProfile && Object.keys(props.bookProfile).length > 0
        ? cloneData(props.bookProfile, {})
        : null;

    bookProfileData.value.profile = resolvedBookProfile ? [resolvedBookProfile] : [];
  } else {
    const { overview, analysis, behavior, insight, profile } = await getMockProps();

    readerOverviewData.value = cloneData(overview, {});
    salesAnalysisData.value = cloneData(analysis, {});
    readerBehaviorData.value = cloneData(behavior, {});
    readerInsightData.value = cloneData(insight, {});
    bookProfileData.value.profile = profile?.[0] ? [cloneData(profile[0], {})] : [];
  }

  assignTimePeriod(readerOverviewData, defaultPeriod);
  assignTimePeriod(salesAnalysisData, defaultPeriod);
  assignTimePeriod(readerBehaviorData, defaultPeriod);
  assignTimePeriod(readerInsightData, defaultPeriod);
  bookProfileData.value.timePeriod = defaultPeriod;

  resolveCompanySelector();

  const storedCategory = loadStoredCategory();
  if (storedCategory && categoryMap.value[storedCategory] !== undefined) {
    changePageData(storedCategory);
  }
};

const changePageData = (data) => {
  const targetItem =
    typeof data === 'object' ? data : categoryList.value?.find((item) => item?.value === data);

  if (!targetItem) return;

  categorySelector.value = targetItem.name;

  Object.keys(categoryMap.value).forEach((key) => {
    categoryMap.value[key] = key === targetItem.value;
  });

  storeCategory(targetItem.value);
};

const emitDateSelector = (data) => {
  const targetItem = dateOption?.value.find((item) => item?.value === data);
  if (!targetItem) return;

  const { value, label } = targetItem;

  [readerOverviewData, salesAnalysisData, readerBehaviorData, readerInsightData, bookProfileData].forEach(
    (state) => {
      if (!state.value || typeof state.value !== 'object') {
        return;
      }

      state.value.timePeriod = value;
    },
  );

  dateSelector.value = label;
};

onMounted(() => {
  generateInitData();
});
</script>

<template>
  <div class="default-page min-h-screen rounded bg-white dark:bg-gray-950">
    <div class="mx-auto grid max-w-screen-2xl grid-cols-12 gap-6 px-4 py-5 md:px-10">
      <div class="col-span-12 flex w-full items-center justify-center">
        <div class="flex items-center justify-center overflow-hidden lg:w-[1000px]">
          <ElScrollbar always class="hidden lg:block">
            <div class="flex w-full min-w-max gap-4 whitespace-nowrap">
              <div
                v-for="(item, index) in tagList"
                :key="index"
                class="text-card-title inline-block cursor-pointer rounded px-4 py-2"
                :class="
                  item.name === categorySelector
                    ? 'bg-secondary text-white'
                    : 'bg-light-brown text-primary dark:bg-gray-800 dark:text-gray-100'
                "
                @click="changePageData(item)"
              >
                {{ item.name }}
              </div>
            </div>
          </ElScrollbar>
        </div>
      </div>
    </div>

    <div
      class="custom-page blur-transition grids mx-auto flex max-w-screen-2xl grid-cols-12 items-center justify-between gap-6 px-4 py-2 text-gray-900 md:px-10 lg:justify-end dark:text-gray-100"
    >
      <ElSelect
        v-model="categorySelector"
        placeholder="全覽概況"
        class="custom-select-small lg:!hidden"
        popper-class="sales-chart-select-popper"
      >
        <ElOption
          v-for="item in categoryOption"
          :key="item.value"
          :label="item.label"
          :value="item.value"
          :class="{
            'text-secondary font-bold': categorySelector === item.label,
            'text-primary dark:text-gray-100': categorySelector !== item.label,
          }"
          class="my-2 text-[18px]!"
          @click="changePageData(item.value)"
        />
      </ElSelect>
      <ElSelect
        v-if="canSelectCompany && categoryMap.insight"
        v-model="companySelector"
        placeholder="公司"
        class="custom-select-small font-bold"
        popper-class="sales-chart-select-popper"
      >
        <ElOption
          v-for="item in companyOption"
          :key="item.value"
          :label="item.label"
          :value="item.value"
          class="my-2 text-[18px]!"
          @click="emitCompanySelector(item.value)"
        />
      </ElSelect>
      <ElSelect
        v-model="dateSelector"
        placeholder="近三個月"
        class="custom-select-small font-bold"
        popper-class="sales-chart-select-popper"
      >
        <ElOption
          v-for="item in dateOption"
          :key="item.value"
          :label="item.label"
          :value="item.value"
          :class="{
            'text-secondary font-bold': dateSelector === item.label,
            'text-primary dark:text-gray-100': dateSelector !== item.label,
          }"
          class="my-2 text-[18px]!"
          @click="emitDateSelector(item.value)"
        />
      </ElSelect>
    </div>

    <div
      class="blur-transition mx-auto grid max-w-screen-2xl grid-cols-12 gap-6 px-4 py-10 md:px-10"
      :class="{ hidden: !categoryMap.overview }"
    >
      <ReaderOverview :reader-overview-prop="readerOverviewData" @reader-overview-emit="emitReaderOverview" />
    </div>
    <div
      class="blur-transition mx-auto grid max-w-screen-2xl grid-cols-12 gap-6 px-4 py-10 md:px-10"
      :class="{ hidden: !categoryMap.analysis }"
    >
      <SalesAnalysis :sales-analysis-prop="salesAnalysisData" @sales-analysis-emit="emitSalesAnalysis" />
    </div>
    <div
      class="blur-transition mx-auto grid max-w-screen-2xl grid-cols-12 gap-6 px-4 py-10 md:px-10"
      :class="{ hidden: !categoryMap.behavior }"
    >
      <ReaderBehavior :reader-behavior-prop="readerBehaviorData" @reader-behavior-emit="emitReaderBehavior" />
    </div>
    <div
      class="blur-transition mx-auto grid max-w-screen-2xl grid-cols-12 gap-6 px-4 py-10 md:px-10"
      :class="{ hidden: !categoryMap.insight }"
    >
      <ReaderInsight :reader-insight-prop="readerInsightData" @reader-insight-emit="emitReaderInsight" />
    </div>
    <div
      class="blur-transition mx-auto grid max-w-screen-2xl grid-cols-12 gap-6 px-4 py-10 md:px-10"
      :class="{ hidden: !categoryMap.profile }"
    >
      <BookProfile
        :book-profile-prop="bookProfileData"
        :search-results="bookSearchResults"
        :search-loading="bookSearchLoading"
        @book-profile-emit="emitBookProfile"
        @book-profile-select="handleBookProfileSelect"
      />
    </div>
  </div>
</template>
