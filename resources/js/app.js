import './bootstrap';
import "../assets/theme/core/index";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

import { createApp } from 'vue';
import axios from './plugins/axios';

import registerAdminComponents from './adminComponents';
import registerAppComponents from './appComponents.js';
import registerGlobalComponents from './globalComponents.js';
import registerAuthComponents from './authComponents.js';

import helperMixin from "./mixins/helperMixin.js";
import bitrixHelperMixin from "./mixins/bitrixHelperMixin.js";

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const app = createApp({});

app.use(VueSweetalert2);
app.component('VueDatePicker', VueDatePicker);

registerAdminComponents(app);
registerAppComponents(app);
registerGlobalComponents(app);
registerAuthComponents(app);

app.mixin(helperMixin);
app.mixin(bitrixHelperMixin);
app.provide('axios', axios); // Make axios available for injection in components

// Make axios available globally
window.axios = axios;

app.mount('#app');
