<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import '@/../scss/main.scss';
import * as date from '@/Libs/date.js';
import { chartPalette } from '@/Libs/chartPalette';

const readerSatisfaction = ref(null);
const readingProgress = ref(null);
const readingTime = ref(null);
const bookRank = ref(null);
const chartInstances = {
  readerSatisfaction: null,
  readingProgress: null,
  readingTime: null,
  bookRank: null,
};
let themeObserver;

const props = defineProps({
  readerBehaviorProp: [Array, Object],
});

const emit = defineEmits(['readerBehaviorEmit']);

const emitReaderBehaviorData = () => {
  emit('readerBehaviorEmit', 'readerBehavior元件傳出資料');
};

const chartData = ref({
  timePeriod: [],
  satisfaction: [],
  readingProgress: [],
  readingTime: [],
  bookFinishedRank: [],
});

const sumValues = (values) => {
  if (!Array.isArray(values)) {
    return 0;
  }

  return values.reduce((sum, value) => sum + Number(value ?? 0), 0);
};

const finishedRankLabels = computed(() => {
  return chartData.value.bookFinishedRank.map((item) => String(item?.name ?? ''));
});

const finishedRankValues = computed(() => {
  return chartData.value.bookFinishedRank.map((item) => Number(item?.quantity ?? 0));
});

const hasSatisfactionData = computed(() => sumValues(chartData.value.satisfaction) > 0);
const hasProgressData = computed(() => sumValues(chartData.value.readingProgress) > 0);
const hasReadingTimeData = computed(() => sumValues(chartData.value.readingTime) > 0);
const hasFinishedRankData = computed(() => sumValues(finishedRankValues.value) > 0);

const changeChartData = (data) => {
  if (!data) {
    return;
  }

  const periodMap = {
    thisMonth: () => date.weeks,
    lastThreeMonths: () => date.getRecentMonths(3),
    lastTwelveMonths: () => date.getRecentMonths(12),
  };

  if (!periodMap[data.timePeriod]) {
    return;
  }

  chartData.value.timePeriod = periodMap[data.timePeriod]() || [];
  chartData.value.satisfaction = data.satisfaction?.[data.timePeriod]?.detail ?? [];
  chartData.value.readingProgress = data.readingProgress?.[data.timePeriod]?.detail ?? [];
  chartData.value.readingTime = data.readingTime?.[data.timePeriod]?.detail ?? [];
  chartData.value.bookFinishedRank = data.bookFinishedRank?.[data.timePeriod] ?? [];
};

const palette = chartPalette;

const destroyCharts = () => {
  Object.values(chartInstances).forEach((chart) => chart?.destroy());
};

const barOptions = (colors, yTickFormatter) => ({
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
        callback(value, index) {
          const label = String(this.getLabelForValue(index) ?? '');

          return label.length > 12 ? `${label.slice(0, 12)}…` : label;
        },
      },
      grid: { display: false },
    },
    y: {
      beginAtZero: true,
      grid: { color: colors.grid },
      ticks: {
        color: colors.subtext,
        callback(value) {
          return yTickFormatter ? yTickFormatter(value) : value;
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
          const total = values.reduce((sum, value) => sum + Number(value ?? 0), 0);
          const rawValue = Number(context.raw ?? 0);
          const percentage = total > 0 ? ((rawValue / total) * 100).toFixed(1) : 0;

          return `${context.label} 人數：${rawValue.toLocaleString()} 人（${percentage}%）`;
        },
      },
    },
  },
});

const doughnutColors = (colors) => {
  return [colors.bar, colors.border, '#fbbf24', '#22d3ee', '#f472b6'];
};

const renderCharts = () => {
  if (!readerSatisfaction.value || !readingProgress.value || !readingTime.value || !bookRank.value) {
    return;
  }

  const theme = palette();
  const segmentColors = doughnutColors(theme);

  if (!chartInstances.readerSatisfaction) {
    chartInstances.readerSatisfaction = new Chart(readerSatisfaction.value, {
      type: 'doughnut',
      data: {
        labels: ['一星', '二星', '三星', '四星', '五星'],
        datasets: [
          {
            label: '人數',
            data: chartData.value.satisfaction,
            backgroundColor: segmentColors,
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.readerSatisfaction.data.datasets[0].data = chartData.value.satisfaction;
    chartInstances.readerSatisfaction.data.datasets[0].backgroundColor = segmentColors;
    chartInstances.readerSatisfaction.options = doughnutOptions(theme);
    chartInstances.readerSatisfaction.update();
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
            backgroundColor: segmentColors,
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.readingProgress.data.datasets[0].data = chartData.value.readingProgress;
    chartInstances.readingProgress.data.datasets[0].backgroundColor = segmentColors;
    chartInstances.readingProgress.options = doughnutOptions(theme);
    chartInstances.readingProgress.update();
  }

  if (!chartInstances.readingTime) {
    chartInstances.readingTime = new Chart(readingTime.value, {
      type: 'bar',
      data: {
        labels: chartData.value.timePeriod,
        datasets: [
          {
            label: '閱讀時數(小時)',
            data: chartData.value.readingTime,
            backgroundColor: theme.bar,
            borderRadius: 0,
            maxBarThickness: 40,
          },
        ],
      },
      options: barOptions(theme, (value) => Number(value ?? 0).toLocaleString()),
    });
  } else {
    chartInstances.readingTime.data.labels = chartData.value.timePeriod;
    chartInstances.readingTime.data.datasets[0].data = chartData.value.readingTime;
    chartInstances.readingTime.data.datasets[0].backgroundColor = theme.bar;
    chartInstances.readingTime.options = barOptions(theme, (value) => Number(value ?? 0).toLocaleString());
    chartInstances.readingTime.update();
  }

  if (!chartInstances.bookRank) {
    chartInstances.bookRank = new Chart(bookRank.value, {
      type: 'bar',
      data: {
        labels: finishedRankLabels.value,
        datasets: [
          {
            label: '閱讀完成人數',
            data: finishedRankValues.value,
            backgroundColor: theme.border,
            borderRadius: 0,
            maxBarThickness: 40,
          },
        ],
      },
      options: barOptions(theme, (value) => Number(value ?? 0).toLocaleString()),
    });
  } else {
    chartInstances.bookRank.data.labels = finishedRankLabels.value;
    chartInstances.bookRank.data.datasets[0].data = finishedRankValues.value;
    chartInstances.bookRank.data.datasets[0].backgroundColor = theme.border;
    chartInstances.bookRank.options = barOptions(theme, (value) => Number(value ?? 0).toLocaleString());
    chartInstances.bookRank.update();
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
  () => props.readerBehaviorProp,
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
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">讀者滿意度分析</p>
      <canvas ref="readerSatisfaction" class="w-full"></canvas>
      <p v-if="!hasSatisfactionData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">閱讀進度人數佔比</p>
      <canvas ref="readingProgress" class="w-full"></canvas>
      <p v-if="!hasProgressData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">讀者總閱讀時數</p>
      <canvas ref="readingTime" class="w-full"></canvas>
      <p v-if="!hasReadingTimeData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">閱讀已完成書籍前五名</p>
      <canvas ref="bookRank" class="w-full"></canvas>
      <p v-if="!hasFinishedRankData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
    </div>
  </div>
</template>
