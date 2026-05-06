<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import '@/../scss/main.scss';
import * as helpers from '@/Libs/helpers';
import * as date from '@/Libs/date.js';
import { chartPalette } from '@/Libs/chartPalette';

const incomeTrend = ref(null);
const quantityTrend = ref(null);
const marketingEvent = ref(null);
const bookCategory = ref(null);
const bookComposition = ref(null);
const chartInstances = {
  income: null,
  quantity: null,
  marketing: null,
  category: null,
  composition: null,
};
let themeObserver;

const props = defineProps({
  salesAnalysisProp: [Array, Object],
});

const emit = defineEmits(['salesAnalysisEmit']);

const emitSalesAnalysisData = () => {
  emit('salesAnalysisEmit', 'salesAnalysis元件傳出資料');
};

const chartData = ref({
  timePeriod: [],
  revenue: [],
  salesVolume: [],
  bookSalesRank: [],
  marketingEvent: [],
  bookCategory: [],
  bookCategoryLabels: ['社會科學', '文學連載', '生活風格', '藝術設計', '其它'],
  bookSet: [],
});

const sumValues = (values) => {
  if (!Array.isArray(values)) {
    return 0;
  }

  return values.reduce((sum, value) => sum + Number(value ?? 0), 0);
};

const bookTableData = computed(() => {
  return chartData.value.bookSalesRank.map((item) => ({
    id: item?.id ?? '',
    name: item?.name ?? '',
    quantity: item?.quantity ?? 0,
  }));
});

const hasIncomeData = computed(() => sumValues(chartData.value.revenue) > 0);
const hasQuantityData = computed(() => sumValues(chartData.value.salesVolume) > 0);
const hasMarketingData = computed(() => sumValues(chartData.value.marketingEvent) > 0);
const hasBookCategoryData = computed(() => sumValues(chartData.value.bookCategory) > 0);
const hasBookSetData = computed(() => sumValues(chartData.value.bookSet) > 0);
const hasBookRankData = computed(() => bookTableData.value.some((item) => Number(item.quantity ?? 0) > 0));

const changeChartData = (data) => {
  if (!data) return;

  const periodMap = {
    thisMonth: () => date.weeks,
    lastThreeMonths: () => date.getRecentMonths(3),
    lastTwelveMonths: () => date.getRecentMonths(12),
  };

  if (!periodMap[data.timePeriod]) return;

  const revenue = data.revenue?.[data.timePeriod] ?? { detail: [] };
  const salesVolume = data.salesVolume?.[data.timePeriod] ?? { detail: [] };
  const bookSalesRank = data.bookSalesRank?.[data.timePeriod] ?? [];
  const marketing = data.marketingEvent?.[data.timePeriod] ?? { detail: [] };
  const category = data.bookCategory?.[data.timePeriod] ?? { detail: [], labels: [] };
  const bookSet = data.bookSet?.[data.timePeriod] ?? { detail: [] };

  chartData.value.timePeriod = periodMap[data.timePeriod]() || [];
  chartData.value.revenue = revenue.detail || [];
  chartData.value.salesVolume = salesVolume.detail || [];
  chartData.value.bookSalesRank = bookSalesRank;
  chartData.value.marketingEvent = marketing.detail || [];
  chartData.value.bookCategory = category.detail || [];
  chartData.value.bookCategoryLabels =
    category.labels && category.labels.length > 0
      ? category.labels
      : ['社會科學', '文學連載', '生活風格', '藝術設計', '其它'];
  chartData.value.bookSet = bookSet.detail || [];
};

const palette = chartPalette;

const destroyCharts = () => {
  Object.values(chartInstances).forEach((chart) => chart?.destroy());
};

const lineOptions = (colors, labelFormatter) => ({
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
      callbacks: {
        label: labelFormatter,
      },
    },
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: { color: colors.subtext },
    },
    y: {
      beginAtZero: true,
      grid: { color: colors.grid },
      ticks: {
        color: colors.subtext,
        callback(value) {
          return labelFormatter({ formattedValue: value });
        },
      },
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
          const total = values.reduce((a, b) => a + Number(b ?? 0), 0);
          const value = Number(context.raw ?? 0);
          const percentage = total ? ((value / total) * 100).toFixed(1) : 0;

          return `${context.label}: NTD ${value.toLocaleString()} (${percentage}%)`;
        },
      },
    },
  },
});

const categoryColors = (colors) => {
  return [
    colors.bar,
    colors.border,
    '#fbbf24',
    '#22d3ee',
    '#f472b6',
    '#94a3b8',
  ];
};

const bookSetColors = (colors) => {
  return [colors.bar, colors.border];
};

const renderCharts = () => {
  if (!incomeTrend.value || !quantityTrend.value || !marketingEvent.value || !bookCategory.value || !bookComposition.value) {
    return;
  }

  const theme = palette();

  if (!chartInstances.income) {
    chartInstances.income = new Chart(incomeTrend.value, {
      type: 'line',
      data: {
        labels: chartData.value.timePeriod,
        datasets: [
          {
            label: '銷售額 (NTD)',
            data: chartData.value.revenue,
            borderColor: theme.border,
            backgroundColor: theme.borderBackground,
            tension: 0.3,
          },
        ],
      },
      options: lineOptions(theme, (c) => `NTD ${Number(c.formattedValue ?? 0).toLocaleString()}`),
    });
  } else {
    chartInstances.income.data.labels = chartData.value.timePeriod;
    chartInstances.income.data.datasets[0].data = chartData.value.revenue;
    chartInstances.income.data.datasets[0].borderColor = theme.border;
    chartInstances.income.data.datasets[0].backgroundColor = theme.borderBackground;
    chartInstances.income.options = lineOptions(theme, (c) => `NTD ${Number(c.formattedValue ?? 0).toLocaleString()}`);
    chartInstances.income.update();
  }

  if (!chartInstances.quantity) {
    chartInstances.quantity = new Chart(quantityTrend.value, {
      type: 'line',
      data: {
        labels: chartData.value.timePeriod,
        datasets: [
          {
            label: '銷售量 (本)',
            data: chartData.value.salesVolume,
            borderColor: theme.border,
            backgroundColor: theme.borderBackground,
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 5,
            pointBackgroundColor: theme.border,
            pointBorderColor: theme.border,
            pointHoverRadius: 7,
          },
        ],
      },
      options: lineOptions(theme, (c) => `${Number(c.formattedValue ?? 0).toLocaleString()} 本`),
    });
  } else {
    chartInstances.quantity.data.labels = chartData.value.timePeriod;
    chartInstances.quantity.data.datasets[0].data = chartData.value.salesVolume;
    chartInstances.quantity.data.datasets[0].borderColor = theme.border;
    chartInstances.quantity.data.datasets[0].backgroundColor = theme.borderBackground;
    chartInstances.quantity.data.datasets[0].pointBackgroundColor = theme.border;
    chartInstances.quantity.data.datasets[0].pointBorderColor = theme.border;
    chartInstances.quantity.options = lineOptions(theme, (c) => `${Number(c.formattedValue ?? 0).toLocaleString()} 本`);
    chartInstances.quantity.update();
  }

  if (!chartInstances.marketing) {
    chartInstances.marketing = new Chart(marketingEvent.value, {
      type: 'line',
      data: {
        labels: chartData.value.timePeriod,
        datasets: [
          {
            label: '銷售額 (NTD)',
            data: chartData.value.marketingEvent,
            borderColor: theme.border,
            backgroundColor: theme.borderBackground,
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 5,
            pointBackgroundColor: theme.border,
            pointBorderColor: theme.border,
            pointHoverRadius: 7,
          },
        ],
      },
      options: lineOptions(theme, (c) => `NTD ${Number(c.formattedValue ?? 0).toLocaleString()}`),
    });
  } else {
    chartInstances.marketing.data.labels = chartData.value.timePeriod;
    chartInstances.marketing.data.datasets[0].data = chartData.value.marketingEvent;
    chartInstances.marketing.data.datasets[0].borderColor = theme.border;
    chartInstances.marketing.data.datasets[0].backgroundColor = theme.borderBackground;
    chartInstances.marketing.data.datasets[0].pointBackgroundColor = theme.border;
    chartInstances.marketing.data.datasets[0].pointBorderColor = theme.border;
    chartInstances.marketing.options = lineOptions(theme, (c) => `NTD ${Number(c.formattedValue ?? 0).toLocaleString()}`);
    chartInstances.marketing.update();
  }

  if (!chartInstances.category) {
    chartInstances.category = new Chart(bookCategory.value, {
      type: 'doughnut',
      data: {
        labels: chartData.value.bookCategoryLabels,
        datasets: [
          {
            label: '分類銷售額 (NTD)',
            data: chartData.value.bookCategory,
            backgroundColor: categoryColors(theme),
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.category.data.labels = chartData.value.bookCategoryLabels;
    chartInstances.category.data.datasets[0].data = chartData.value.bookCategory;
    chartInstances.category.data.datasets[0].backgroundColor = categoryColors(theme);
    chartInstances.category.options = doughnutOptions(theme);
    chartInstances.category.update();
  }

  if (!chartInstances.composition) {
    chartInstances.composition = new Chart(bookComposition.value, {
      type: 'doughnut',
      data: {
        labels: ['單書', '套書'],
        datasets: [
          {
            label: '書籍銷售額 (NTD)',
            data: chartData.value.bookSet,
            backgroundColor: bookSetColors(theme),
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.composition.data.datasets[0].data = chartData.value.bookSet;
    chartInstances.composition.data.datasets[0].backgroundColor = bookSetColors(theme);
    chartInstances.composition.options = doughnutOptions(theme);
    chartInstances.composition.update();
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
  () => props.salesAnalysisProp,
  (newVal) => {
    changeChartData(newVal);
    renderCharts();
  },
  { immediate: true, deep: true },
);
</script>

<template>
  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">銷售額分析</p>
      <canvas ref="incomeTrend" class="w-full"></canvas>
      <p v-if="!hasIncomeData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">銷售量分析</p>
      <canvas ref="quantityTrend" class="w-full"></canvas>
      <p v-if="!hasQuantityData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">行銷活動銷售額</p>
      <canvas ref="marketingEvent" class="w-full"></canvas>
      <p v-if="!hasMarketingData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <p class="border-light-gray border-b pb-[30px] text-center text-sm font-bold text-gray-500 dark:border-gray-700 dark:text-gray-300">
      Top5 熱銷書籍
    </p>
    <ElScrollbar always class="mt-5 flex items-center justify-center overflow-x-auto">
      <table class="sales-rank-table min-w-max table-auto text-sm font-bold text-gray-900 dark:text-gray-100">
        <thead class="bg-white dark:bg-gray-900">
          <tr class="text-left">
            <th class="sticky left-0 z-30 bg-white px-4 py-4 dark:bg-gray-900">名次</th>
            <th class="w-[250px] bg-white px-4 py-4 dark:bg-gray-900">書名</th>
            <th class="w-[300px] px-4 py-4">銷售量(本)</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in bookTableData"
            :key="item.id"
            :class="
              index % 2 === 0
                ? 'bg-[#efe8e5] text-gray-900'
                : 'bg-white text-gray-900'
            "
          >
            <td
              class="sticky left-0 z-20 px-4 py-4"
              :class="
                index % 2 === 0
                  ? 'bg-[#efe8e5] text-gray-900'
                  : 'bg-white text-gray-900'
              "
            >
              {{ index + 1 }}
            </td>
            <td
              :class="
                index % 2 === 0
                  ? 'bg-[#efe8e5] px-4 py-4 text-gray-900'
                  : 'bg-white px-4 py-4 text-gray-900'
              "
            >
              {{ item.name }}
            </td>
            <td class="px-4 py-4">{{ helpers.largeNumberComma(item.quantity) }}</td>
          </tr>
        </tbody>
      </table>
    </ElScrollbar>
    <p v-if="!hasBookRankData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">各書籍分類銷售額佔比</p>
      <canvas ref="bookCategory" class="w-full"></canvas>
      <p v-if="!hasBookCategoryData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">單書和套書銷售額佔比</p>
      <canvas ref="bookComposition" class="w-full"></canvas>
      <p v-if="!hasBookSetData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>
</template>
