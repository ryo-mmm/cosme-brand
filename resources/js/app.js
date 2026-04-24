import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import SkinDiagnosis from './components/SkinDiagnosis.vue';

// Mount Vue app on diagnosis page if the mount point exists
const diagnosisEl = document.getElementById('skin-diagnosis-app');
if (diagnosisEl) {
    const app = createApp(SkinDiagnosis);
    app.use(createPinia());
    app.mount('#skin-diagnosis-app');
}
