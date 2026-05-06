<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import '@/../scss/main.scss';
import * as date from '@/Libs/date.js';
import { chartPalette } from '@/Libs/chartPalette';

const gender = ref(null);
const age = ref(null);
const device = ref(null);
const visitor = ref(null);
const country = ref(null);
const chartInstances = {
  gender: null,
  age: null,
  device: null,
  visitor: null,
  country: null,
};
let themeObserver;

const props = defineProps({
  readerInsightProp: [Array, Object],
});

const emit = defineEmits(['readerInsightEmit']);

const emitReaderInsightData = () => {
  emit('readerInsightEmit', 'readerInsight元件傳出資料');
};

const chartData = ref({
  timePeriod: [],
  visitor: [],
  gender: [],
  age: [],
  ageMale: [],
  ageFemale: [],
  device: [],
  country: [],
  countryLabels: [],
});

const sumValues = (values) => {
  if (!Array.isArray(values)) {
    return 0;
  }

  const total = values.reduce((sum, value) => sum + Number(value ?? 0), 0);

  return Number.isFinite(total) ? total : 0;
};

const useAgeFallback = computed(() => {
  const ageGenderTotal = sumValues(chartData.value.ageMale) + sumValues(chartData.value.ageFemale);

  return ageGenderTotal === 0 && sumValues(chartData.value.age) > 0;
});

const hasVisitorData = computed(() => sumValues(chartData.value.visitor) > 0);
const hasGenderData = computed(() => sumValues(chartData.value.gender) > 0);
const hasAgeData = computed(() => {
  if (useAgeFallback.value) {
    return sumValues(chartData.value.age) > 0;
  }

  return sumValues(chartData.value.ageMale) + sumValues(chartData.value.ageFemale) > 0;
});
const hasDeviceData = computed(() => sumValues(chartData.value.device) > 0);
const hasCountryData = computed(() => sumValues(chartData.value.country) > 0);

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
  chartData.value.visitor = data.visitor?.[data.timePeriod]?.detail ?? [];
  chartData.value.gender = data.gender?.[data.timePeriod]?.detail ?? [];
  chartData.value.age = data.age?.[data.timePeriod]?.detail ?? [];
  chartData.value.ageMale = data.ageGender?.[data.timePeriod]?.male ?? [];
  chartData.value.ageFemale = data.ageGender?.[data.timePeriod]?.female ?? [];
  chartData.value.device = data.device?.[data.timePeriod]?.detail ?? [];
  chartData.value.country = data.country?.[data.timePeriod]?.detail ?? [];
  chartData.value.countryLabels = data.country?.[data.timePeriod]?.labels ?? [];
};

const palette = chartPalette;

const destroyCharts = () => {
  Object.values(chartInstances).forEach((chart) => chart?.destroy());
};

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

const pieOptions = (colors) => ({
  responsive: true,
  maintainAspectRatio: false,
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

const countryPalette = (colors, count) => {
  const baseColors = [
    colors.border,
    colors.bar,
    '#f472b6',
    '#a3e635',
    '#c4b5fd',
    '#fbbf24',
    '#94a3b8',
    '#22d3ee',
  ];

  if (count <= baseColors.length) {
    return baseColors.slice(0, count);
  }

  const expanded = [...baseColors];
  while (expanded.length < count) {
    expanded.push(baseColors[expanded.length % baseColors.length]);
  }

  return expanded;
};

const renderCharts = () => {
  if (!gender.value || !age.value || !device.value || !visitor.value || !country.value) {
    return;
  }

  const theme = palette();
  const baseDoughnutColors = [theme.bar, theme.border, '#fbbf24', '#22d3ee', '#f472b6'];

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

  const ageDatasets = useAgeFallback.value
    ? [
        {
          label: '人數',
          data: chartData.value.age,
          backgroundColor: theme.border,
          maxBarThickness: 30,
        },
      ]
    : [
        {
          label: '男性',
          data: chartData.value.ageMale,
          backgroundColor: theme.bar,
          maxBarThickness: 30,
        },
        {
          label: '女性',
          data: chartData.value.ageFemale,
          backgroundColor: '#f472b6',
          maxBarThickness: 30,
        },
      ];

  if (!chartInstances.age) {
    chartInstances.age = new Chart(age.value, {
      type: 'bar',
      data: {
        labels: ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'],
        datasets: ageDatasets,
      },
      options: barOptions(theme),
    });
  } else {
    chartInstances.age.data.datasets = ageDatasets;
    chartInstances.age.options = barOptions(theme);
    chartInstances.age.update();
  }

  if (!chartInstances.device) {
    chartInstances.device = new Chart(device.value, {
      type: 'doughnut',
      data: {
        labels: ['電腦', '手機', '平板'],
        datasets: [
          {
            label: '人數',
            data: chartData.value.device,
            backgroundColor: baseDoughnutColors.slice(0, 3),
            hoverOffset: 12,
          },
        ],
      },
      options: doughnutOptions(theme),
    });
  } else {
    chartInstances.device.data.datasets[0].data = chartData.value.device;
    chartInstances.device.data.datasets[0].backgroundColor = baseDoughnutColors.slice(0, 3);
    chartInstances.device.options = doughnutOptions(theme);
    chartInstances.device.update();
  }

  const countryColors = countryPalette(theme, chartData.value.countryLabels.length);

  if (!chartInstances.country) {
    chartInstances.country = new Chart(country.value, {
      type: 'pie',
      data: {
        labels: chartData.value.countryLabels,
        datasets: [
          {
            label: '人數',
            data: chartData.value.country,
            backgroundColor: countryColors,
          },
        ],
      },
      options: pieOptions(theme),
    });
  } else {
    chartInstances.country.data.labels = chartData.value.countryLabels;
    chartInstances.country.data.datasets[0].data = chartData.value.country;
    chartInstances.country.data.datasets[0].backgroundColor = countryColors;
    chartInstances.country.options = pieOptions(theme);
    chartInstances.country.update();
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
  () => props.readerInsightProp,
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
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">造訪人數統計</p>
      <canvas ref="visitor" class="w-full"></canvas>
      <p v-if="!hasVisitorData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
      <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">資料來源為 Google 分析</p>
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

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">讀者使用裝置佔比</p>
      <canvas ref="device" class="w-full"></canvas>
      <p v-if="!hasDeviceData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
      <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">資料來源為 Google 分析</p>
    </div>
  </div>

  <div class="border-light-gray col-span-12 flex flex-col rounded border bg-white p-5 text-gray-900 shadow-sm md:col-span-6 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
    <div class="flex w-full flex-col items-center justify-center p-5" style="height: 400px">
      <p class="pb-5 text-sm font-bold text-gray-500 dark:text-gray-300">造訪國家/地區佔比</p>
      <canvas ref="country" class="w-full"></canvas>
      <p v-if="!hasCountryData" class="mt-3 text-sm text-gray-400 dark:text-gray-500">資料不足，暫無法顯示</p>
      <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">資料來源為 Google 分析</p>
    </div>
  </div>
</template>
