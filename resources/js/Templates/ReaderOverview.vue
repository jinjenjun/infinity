<script setup>
import { onBeforeUnmount, onMounted, ref, watch, computed } from 'vue';
import { Chart } from 'chart.js/auto';
import '@/../scss/filament-sales-chart.scss';
import * as helpers from '@/Libs/helpers';
import * as date from '@/Libs/date';
import { chartPalette } from '@/Libs/chartPalette';

const salesTrendChart = ref(null);
const chartInstances = {
  salesTrendChart: null,
};
let themeObserver;

const props = defineProps({
  readerOverviewProp: [Array, Object],
});

const emit = defineEmits(['readerOverviewEmit']);

const emitReaderOverviewData = () => {
  emit('readerOverviewEmit', 'readerOverview元件傳出資料');
};

const chartData = ref({
  summary: {
    totalRevenue: 0,
    totalSalesVolume: 0,
    totalReadingTime: 0,
    averageCompletionRate: 0,
  },
  salesTrend: {
    salesVolume: [],
    readingTime: [],
    timePeriod: [],
  },
  bookSalesRank: [],
});

const bookTableData = computed(() => {
  return chartData.value.bookSalesRank.map((item) => ({
    id: item?.id ?? '',
    name: item?.name ?? '',
    quantity: item?.quantity ?? 0,
  }));
});

const sumValues = (values) => {
  if (!Array.isArray(values)) {
    return 0;
  }

  return values.reduce((sum, value) => sum + Number(value ?? 0), 0);
};

const hasTrendData = computed(() => {
  return (
    sumValues(chartData.value.salesTrend.salesVolume) > 0 ||
    sumValues(chartData.value.salesTrend.readingTime) > 0
  );
});

const hasBookRankData = computed(() => {
  return bookTableData.value.some((item) => Number(item.quantity ?? 0) > 0);
});

const changeChartData = (data) => {
  if (!data) return;

  const periodMap = {
    thisMonth: () => date.weeks,
    lastThreeMonths: () => date.getRecentMonths(3),
    lastTwelveMonths: () => date.getRecentMonths(12),
  };

  if (!periodMap[data.timePeriod]) return;
  const revenue = data.revenue?.[data.timePeriod] ?? { total: 0, detail: [] };
  const salesVolume = data.salesVolume?.[data.timePeriod] ?? { total: 0, detail: [] };
  const readingTime = data.readingTime?.[data.timePeriod] ?? { total: 0, detail: [] };
  const completionRate = data.completionRate?.[data.timePeriod] ?? { averageNum: 0, detail: [] };
  const bookSalesRank = data.bookSalesRank?.[data.timePeriod] ?? [];

  chartData.value.salesTrend.timePeriod = periodMap[data.timePeriod]() || [];
  chartData.value.summary.totalRevenue = revenue.total || 0;
  chartData.value.summary.totalSalesVolume = salesVolume.total || 0;
  chartData.value.salesTrend.salesVolume = salesVolume.detail || [];
  chartData.value.summary.totalReadingTime = readingTime.total || 0;
  chartData.value.salesTrend.readingTime = readingTime.detail || [];
  chartData.value.summary.averageCompletionRate = completionRate.averageNum || 0;
  chartData.value.bookSalesRank = bookSalesRank;
};

const palette = chartPalette;

const destroyCharts = () => {
  Object.values(chartInstances).forEach((chart) => chart?.destroy());
};

const barOptions = (colors) => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' },
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

const renderCharts = () => {
  if (!salesTrendChart.value) {
    return;
  }

  const theme = palette();

  if (!chartInstances.salesTrend) {
    chartInstances.salesTrend = new Chart(salesTrendChart.value, {
      data: {
        labels: chartData.value.salesTrend.timePeriod || [],
        datasets: [
          {
            type: 'bar',
            label: '銷售量(本)',
            data: chartData.value.salesTrend.salesVolume || [],
            backgroundColor: theme.barBackground,
            borderColor: theme.bar,
            borderWidth: 2,
            yAxisID: 'y',
          },
          {
            type: 'line',
            label: '閱讀時數(小時)',
            data: chartData.value.salesTrend.readingTime || [],
            borderColor: theme.border,
            backgroundColor: theme.borderBackground,
            tension: 0.3,
            fill: false,
            yAxisID: 'y1',
          },
        ],
      },
      options: barOptions(theme),
    });
  } else {
    chartInstances.salesTrend.data.labels = chartData.value.salesTrend.timePeriod || [];
    chartInstances.salesTrend.data.datasets[0].data = chartData.value.salesTrend.salesVolume || [];
    chartInstances.salesTrend.data.datasets[0].backgroundColor = theme.barBackground;
    chartInstances.salesTrend.data.datasets[0].borderColor = theme.bar;
    chartInstances.salesTrend.data.datasets[1].data = chartData.value.salesTrend.readingTime || [];
    chartInstances.salesTrend.data.datasets[1].borderColor = theme.border;
    chartInstances.salesTrend.data.datasets[1].backgroundColor = theme.borderBackground;
    chartInstances.salesTrend.options = barOptions(theme);
    chartInstances.salesTrend.update();
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
  () => props.readerOverviewProp,
  (newVal) => {
    changeChartData(newVal);
    renderCharts();
  },
  { immediate: true, deep: true },
);
</script>

<template>
  <div
    class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 lg:col-span-3 dark:border-gray-800 dark:bg-gray-900/60 dark:text-gray-100"
  >
    <div class="flex w-full flex-col items-start justify-center">
      <p class="text-sm font-bold text-gray-500 dark:text-gray-400">總銷售額</p>
      <p class="text-auction-description">
        NTD {{ helpers.largeNumberComma(chartData.summary.totalRevenue) }}
      </p>
    </div>
  </div>

  <div
    class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 lg:col-span-3 dark:border-gray-800 dark:bg-gray-900/60 dark:text-gray-100"
  >
    <div class="flex w-full flex-col items-start justify-center">
      <p class="text-sm font-bold text-gray-500 dark:text-gray-400">總銷售量</p>
      <p class="text-auction-description">
        {{ helpers.largeNumberComma(chartData.summary.totalSalesVolume) }} 本
      </p>
    </div>
  </div>

  <div
    class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 lg:col-span-3 dark:border-gray-800 dark:bg-gray-900/60 dark:text-gray-100"
  >
    <div class="flex w-full flex-col items-start justify-center">
      <p class="text-sm font-bold text-gray-500 dark:text-gray-400">總閱讀時數</p>
      <p class="text-auction-description">
        {{ helpers.largeNumberComma(chartData.summary.totalReadingTime) }} 小時
      </p>
    </div>
  </div>

  <div
    class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 lg:col-span-3 dark:border-gray-800 dark:bg-gray-900/60 dark:text-gray-100"
  >
    <div class="flex w-full flex-col items-start justify-center">
      <p class="text-sm font-bold text-gray-500 dark:text-gray-400">平均完讀率</p>
      <p class="text-auction-description">
        {{ helpers.largeNumberComma(chartData.summary.averageCompletionRate) }} %
      </p>
    </div>
  </div>

  <div
    class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 lg:col-span-8 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100"
  >
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">銷售與閱讀趨勢</p>
      <canvas ref="salesTrendChart" class="w-full"></canvas>
      <p v-if="!hasTrendData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">
        資料不足，暫無法顯示
      </p>
    </div>
  </div>

  <div
    class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 lg:col-span-4 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100"
  >
    <p class="border-light-gray mb-4 border-b-2 pb-2 text-sm font-bold text-gray-500 dark:border-gray-700 dark:text-gray-300">
      Top5 熱銷書籍
    </p>
    <ElScrollbar always class="flex items-center justify-center overflow-x-auto">
      <table class="sales-rank-table min-w-max table-auto text-sm font-bold text-gray-900 dark:text-gray-100">
        <thead class="bg-white dark:bg-gray-900">
          <tr class="text-left">
            <th class="sticky left-0 z-30 bg-white px-4 py-4 dark:bg-gray-900">名次</th>
            <th class="w-[150px] bg-white px-4 py-4 dark:bg-gray-900">書名</th>
            <th class="w-[150px] px-4 py-4">銷售量(本)</th>
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
    <p v-if="!hasBookRankData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">
      資料不足，暫無法顯示
    </p>
  </div>
</template>
