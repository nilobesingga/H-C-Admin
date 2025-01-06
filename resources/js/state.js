import { reactive } from 'vue';

export const sharedState = reactive({
    currency: localStorage.getItem('currency') || 'AED',
});
