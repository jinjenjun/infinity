import '../../css/filament-business-chart.css';
import '../../scss/filament-business-chart.scss';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import { createApp } from 'vue';
import SalesChartHomepage from '@/Templates/SalesChartHomepage.vue';

const mountEl = document.getElementById('business-chart-app');

if (mountEl) {
    createApp(SalesChartHomepage, {
        livewireId: mountEl.dataset.livewireId,
    })
        .use(ElementPlus)
        .mount(mountEl);
}
