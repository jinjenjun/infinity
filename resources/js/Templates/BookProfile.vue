<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import '@/../scss/main.scss';
import * as helpers from '@/Libs/helpers.js';
import * as date from '@/Libs/date.js';
import { chartPalette } from '@/Libs/chartPalette';
import ElInfoButton from '@/Components/ElInfoButton.vue';

const salesTrend = ref(null);
const visitor = ref(null);
const readingProgress = ref(null);
const gender = ref(null);
const age = ref(null);
const searchKeyword = ref('');
const chartInstances = {
  salesTrend: null,
  visitor: null,
  readingProgress: null,
  gender: null,
  age: null,
};
let themeObserver;

const props = defineProps({
  bookProfileProp: [Array, Object],
  searchResults: {
    type: Array,
    default: () => [],
  },
  searchLoading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['bookProfileEmit', 'bookProfileSelect']);

const defaultSpecification = () => ({
  name: '',
  isbn: '',
  author: [],
});

const chartData = ref({
  specification: defaultSpecification(),
  salesVolume: [],
  readingTime: [],
  timePeriod: [],
  visitor: [],
  readingProgress: [],
  gender: [],
  age: [],
});

const onInputSanitize = (event) => {
  searchKeyword.value = helpers.sanitizeLooseInput(event.target.value).trim();
};

const sumValues = (values) => {
  if (!Array.isArray(values)) {
    return 0;
  }

  const total = values.reduce((sum, value) => sum + Number(value ?? 0), 0);

  return Number.isFinite(total) ? total : 0;
};

const totalReadingHours = computed(() => sumValues(chartData.value.readingTime));
const hasBookProfile = computed(() => {
  return Boolean(chartData.value.specification?.name || chartData.value.specification?.isbn);
});
const hasSalesTrendData = computed(() => {
  return sumValues(chartData.value.salesVolume) > 0 || sumValues(chartData.value.readingTime) > 0;
});
const hasVisitorData = computed(() => sumValues(chartData.value.visitor) > 0);
const hasReadingProgressData = computed(() => sumValues(chartData.value.readingProgress) > 0);
const hasGenderData = computed(() => sumValues(chartData.value.gender) > 0);
const hasAgeData = computed(() => sumValues(chartData.value.age) > 0);

const resolvePeriodMap = () => ({
  thisMonth: () => date.weeks,
  lastThreeMonths: () => date.getRecentMonths(3),
  lastTwelveMonths: () => date.getRecentMonths(12),
});

const resetChartData = (timePeriod = 'lastThreeMonths') => {
  const periodMap = resolvePeriodMap();

  chartData.value = {
    specification: defaultSpecification(),
    salesVolume: [],
    readingTime: [],
    timePeriod: periodMap[timePeriod] ? periodMap[timePeriod]() : [],
    visitor: [],
    readingProgress: [],
    gender: [],
    age: [],
  };
};

const changeChartData = (data) => {
  if (!data) {
    resetChartData();
    return;
  }

  const periodMap = resolvePeriodMap();
  const selectedPeriod = data.timePeriod;

  if (!periodMap[selectedPeriod]) {
    resetChartData();
    return;
  }

  const targetBook = data.profile?.[0];
  if (!targetBook) {
    resetChartData(selectedPeriod);
    return;
  }

  const specification = targetBook.specification ?? {};

  chartData.value.timePeriod = periodMap[selectedPeriod]() || [];
  chartData.value.specification = {
    name: String(specification?.name ?? ''),
    isbn: String(specification?.isbn ?? ''),
    author: Array.isArray(specification?.author)
      ? specification.author.map((author) => String(author ?? '')).filter((author) => author !== '')
      : [],
  };
  chartData.value.salesVolume = targetBook.salesVolume?.[selectedPeriod]?.detail ?? [];
  chartData.value.readingTime = targetBook.readingTime?.[selectedPeriod]?.detail ?? [];
  chartData.value.visitor = targetBook.visitor?.[selectedPeriod]?.detail ?? [];
  chartData.value.readingProgress = targetBook.readingProgress?.[selectedPeriod]?.detail ?? [];
  chartData.value.gender = targetBook.gender?.[selectedPeriod]?.detail ?? [];
  chartData.value.age = targetBook.age?.[selectedPeriod]?.detail ?? [];
};

const searchBook = () => {
  emit('bookProfileEmit', searchKeyword.value);
  searchKeyword.value = '';
};

const selectBook = (book) => {
  emit('bookProfileSelect', book);
  searchKeyword.value = '';
};

const palette = chartPalette;

const destroyCharts = () => {
  Object.values(chartInstances).forEach((chart) => chart?.destroy());
};

const trendOptions = (colors) => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        color: colors.subtext,
      },
    },
    tooltip: {
      backgroundColor: colors.background,
      borderColor: colors.subtext,
      borderWidth: 1,
      titleColor: colors.text,
      bodyColor: colors.text,
    },
  },
  interaction: { mode: 'index', intersect: false },
  scales: {
    x: {
      grid: { display: false },
      ticks: { color: colors.subtext },
    },
    y: {
      beginAtZero: true,
      title: { display: true, text: '銷售量(本)', color: colors.subtext },
      ticks: { color: colors.subtext },
      grid: { color: colors.grid },
    },
    y1: {
      beginAtZero: true,
      position: 'right',
      grid: { drawOnChartArea: false },
      title: { display: true, text: '閱讀時數(小時)', color: colors.subtext },
      ticks: { color: colors.subtext },
    },
  },
});

const barOptions = (colors) => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      onClick: () => {},
      position: 'bottom',
      labels: {
        color: colors.subtext,
      },
    },
    tooltip: {
      backgroundColor: colors.background,
      borderColor: colors.subtext,
      borderWidth: 1,
      titleColor: colors.text,
      bodyColor: colors.text,
    },
  },
  scales: {
    x: {
      ticks: {
        autoSkip: false,
        maxRotation: 45,
        minRotation: 30,
        color: colors.subtext,
      },
      grid: { display: false },
    },
    y: {
      beginAtZero: true,
      grid: { color: colors.grid },
      ticks: { color: colors.subtext },
    },
  },
});

const doughnutOptions = (colors) => ({
  responsive: true,
  maintainAspectRatio: false,
  cutout: '55%',
  plugins: {
    legend: {
      position: 'bottom',
      onClick: () => {},
      labels: {
        color: colors.subtext,
      },
    },
    tooltip: {
      backgroundColor: colors.background,
      borderColor: colors.subtext,
      borderWidth: 1,
      titleColor: colors.text,
      bodyColor: colors.text,
      callbacks: {
        label(context) {
          const values = Array.isArray(context.dataset.data) ? context.dataset.data : [];
          const total = values.reduce((sum, value) => sum + Number(value ?? 0), 0);
          const rawValue = Number(context.raw ?? 0);
          const percentage = total > 0 ? ((rawValue / total) * 100).toFixed(1) : 0;

          return `${context.label} 人數：${rawValue.toLocaleString()} 人（${percentage}%）`;
        },
      },
    },
  },
});

const renderCharts = () => {
  if (!salesTrend.value || !visitor.value || !readingProgress.value || !gender.value || !age.value) {
    return;
  }

  const theme = palette();

  if (!chartInstances.salesTrend) {
    chartInstances.salesTrend = new Chart(salesTrend.value, {
      data: {
        labels: chartData.value.timePeriod,
        datasets: [
          {
            type: 'bar',
            label: '銷售量(本)',
            data: chartData.value.salesVolume,
            backgroundColor: theme.barBackground,
            borderColor: theme.bar,
            borderWidth: 2,
            yAxisID: 'y',
          },
          {
            type: 'line',
            label: '閱讀時數(小時)',
            data: chartData.value.readingTime,
            borderColor: theme.border,
            backgroundColor: theme.borderBackground,
            tension: 0.3,
            fill: false,
            yAxisID: 'y1',
          },
        ],
      },
      options: trendOptions(theme),
    });
  } else {
    chartInstances.salesTrend.data.labels = chartData.value.timePeriod;
    chartInstances.salesTrend.data.datasets[0].data = chartData.value.salesVolume;
    chartInstances.salesTrend.data.datasets[0].backgroundColor = theme.barBackground;
    chartInstances.salesTrend.data.datasets[0].borderColor = theme.bar;
    chartInstances.salesTrend.data.datasets[1].data = chartData.value.readingTime;
    chartInstances.salesTrend.data.datasets[1].borderColor = theme.border;
    chartInstances.salesTrend.data.datasets[1].backgroundColor = theme.borderBackground;
    chartInstances.salesTrend.options = trendOptions(theme);
    chartInstances.salesTrend.update();
  }

  if (!chartInstances.visitor) {
    chartInstances.visitor = new Chart(visitor.value, {
      type: 'bar',
      data: {
        labels: chartData.value.timePeriod,
        datasets: [
          {
            label: '人數(人)',
            data: chartData.value.visitor,
            backgroundColor: theme.border,
            borderRadius: 0,
            maxBarThickness: 40,
          },
        ],
      },
      options: barOptions(theme),
    });
  } else {
    chartInstances.visitor.data.labels = chartData.value.timePeriod;
    chartInstances.visitor.data.datasets[0].data = chartData.value.visitor;
    chartInstances.visitor.data.datasets[0].backgroundColor = theme.border;
    chartInstances.visitor.options = barOptions(theme);
    chartInstances.visitor.update();
  }

  if (!chartInstances.readingProgress) {
    chartInstances.readingProgress = new Chart(readingProgress.value, {
      type: 'doughnut',
      data: {
        labels: ['進度0%', '進度25%', '進度50%', '進度75%', '進度100%'],
        datasets: [
          {
            label: '人數',
            data: chartData.value.readingProgress,
            backgroundColor: [theme.bar, theme.border, '#fbbf24', '#22d3ee', '#f472b6'],
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.readingProgress.data.datasets[0].data = chartData.value.readingProgress;
    chartInstances.readingProgress.data.datasets[0].backgroundColor = [
      theme.bar,
      theme.border,
      '#fbbf24',
      '#22d3ee',
      '#f472b6',
    ];
    chartInstances.readingProgress.options = doughnutOptions(theme);
    chartInstances.readingProgress.update();
  }

  if (!chartInstances.gender) {
    chartInstances.gender = new Chart(gender.value, {
      type: 'doughnut',
      data: {
        labels: ['男性', '女性'],
        datasets: [
          {
            label: '人數',
            data: chartData.value.gender,
            backgroundColor: [theme.bar, '#f472b6'],
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.gender.data.datasets[0].data = chartData.value.gender;
    chartInstances.gender.data.datasets[0].backgroundColor = [theme.bar, '#f472b6'];
    chartInstances.gender.options = doughnutOptions(theme);
    chartInstances.gender.update();
  }

  if (!chartInstances.age) {
    chartInstances.age = new Chart(age.value, {
      type: 'doughnut',
      data: {
        labels: ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'],
        datasets: [
          {
            label: '人數',
            data: chartData.value.age,
            backgroundColor: [theme.bar, theme.border, '#fbbf24', '#22d3ee', '#f472b6', '#94a3b8'],
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.age.data.datasets[0].data = chartData.value.age;
    chartInstances.age.data.datasets[0].backgroundColor = [
      theme.bar,
      theme.border,
      '#fbbf24',
      '#22d3ee',
      '#f472b6',
      '#94a3b8',
    ];
    chartInstances.age.options = doughnutOptions(theme);
    chartInstances.age.update();
  }
};

const registerThemeObserver = () => {
  themeObserver = new MutationObserver(renderCharts);
  themeObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class', 'data-theme'],
  });
};

onMounted(() => {
  renderCharts();
  registerThemeObserver();
});

onBeforeUnmount(() => {
  destroyCharts();

  if (themeObserver) {
    themeObserver.disconnect();
  }
});

watch(
  () => props.bookProfileProp,
  (newVal) => {
    changeChartData(newVal);
    renderCharts();
  },
  { immediate: true, deep: true },
);
</script>

<template>
  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-12 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full md:items-end md:justify-end">
      <div class="flex h-[60px] items-center space-x-2 md:w-[340px] md:px-5">
        <input
          :value="searchKeyword"
          @input="onInputSanitize"
          class="h-full w-full grow rounded border border-gray-300 px-2 font-bold text-gray-900 placeholder:text-[18px] placeholder:text-gray-400 focus:border-orange-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500"
          placeholder="輸入書名或ISBN"
          type="text"
        />
        <div :class="{ 'pointer-events-none opacity-50': !searchKeyword }">
          <ElInfoButton class="text-card-description w-[100px] whitespace-nowrap px-2 py-4" @click="searchBook">
            <p class="text-[18px] font-bold">搜尋</p>
          </ElInfoButton>
        </div>
      </div>
    </div>

    <div class="flex w-full md:justify-end">
      <div class="md:w-[340px] md:px-5">
        <p v-if="searchLoading" class="mt-2 text-sm text-gray-500 dark:text-gray-400">搜尋中...</p>
        <div
          v-else-if="searchResults.length"
          class="mt-2 overflow-hidden rounded border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >
          <button
            v-for="book in searchResults"
            :key="book.id"
            type="button"
            class="flex w-full items-start justify-between gap-3 px-3 py-2 text-left text-sm text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-gray-800"
            @click="selectBook(book)"
          >
            <span class="text-gray-800 dark:text-gray-100">{{ book.name }}</span>
            <span class="text-gray-500 dark:text-gray-400">{{ book.isbn }}</span>
          </button>
        </div>
      </div>
    </div>

    <div class="text-primary flex w-full flex-col items-start justify-start py-5 text-[18px] font-bold dark:text-gray-100 md:px-5">
      <hr class="border-light-gray w-full border-t-2 dark:border-gray-700" />
      <template v-if="hasBookProfile">
        <p class="mb-2 mt-5">書名：{{ chartData.specification.name }}</p>
        <p class="mb-2">
          作者:
          <span v-if="chartData.specification.author.length">{{ chartData.specification.author.join('、') }}</span>
          <span v-else>－</span>
        </p>
        <p>ISBN: {{ chartData.specification.isbn }}</p>
        <p class="mt-2">總閱讀時數：{{ totalReadingHours.toFixed(1) }} 小時</p>
      </template>
      <p v-else class="mt-5 text-sm text-gray-500 dark:text-gray-400">目前無書籍資料，請搜尋書名或 ISBN。</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-12 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">銷售與閱讀趨勢</p>
      <canvas ref="salesTrend" class="w-full"></canvas>
      <p v-if="!hasSalesTrendData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">造訪人數統計</p>
      <canvas ref="visitor" class="w-full"></canvas>
      <p v-if="!hasVisitorData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">閱讀進度人數佔比</p>
      <canvas ref="readingProgress" class="w-full"></canvas>
      <p v-if="!hasReadingProgressData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">讀者性別佔比</p>
      <canvas ref="gender" class="w-full"></canvas>
      <p v-if="!hasGenderData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
      <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">資料來源為 Google 分析</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">讀者年齡層分布</p>
      <canvas ref="age" class="w-full"></canvas>
      <p v-if="!hasAgeData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
      <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">資料來源為 Google 分析</p>
    </div>
  </div>
</template>
