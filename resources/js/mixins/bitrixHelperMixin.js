import { DateTime } from 'luxon'
import { sharedState } from '../state.js'
import {end} from "@popperjs/core";
import qs from "qs";
export default {
    data() {
        return {
            sharedState
        }
    },
    methods: {
        async callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, data = {}, method = 'post', headers = {} ){
            try {
                const url = `https://crm.cresco.ae/rest/${bitrixUserId}/${bitrixWebhookToken}/${endpoint}`;
                delete axios.defaults.headers.common['X-Requested-With'];
                const bitrixAxios = axios.create({
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                });
                // Perform the API request
                const response = await axios({
                    method,
                    url,
                    data: new URLSearchParams(data)
                });
                return response.data;
            } catch (error) {
                console.error('Error calling Bitrix API:', error);
                throw error;
            }
        },
        async getExchangeRatesByCurrencies(sourceCurrencies, baseCurrency = null){
            try {
                const today = new Date().toLocaleDateString('en-GB'); // Format as DD/MM/YYYY
                const url = `https://10.0.1.17/CrescoSage/api/V1/FOBank/SAMAED/ExchangeRate`;
                const responses = await Promise.all(
                    sourceCurrencies.map(async (sourceCurrency) => {
                        try {
                            const response = await axios.get(`${url}`, {
                                params: {
                                    baseCurrency: baseCurrency ? baseCurrency : this.currency,
                                    sourceCurrency: sourceCurrency,
                                    rateDate: today,
                                },
                            });
                            return { sourceCurrency, rate: response.data.Rate, baseCurrency };
                        } catch (error) {
                            console.error(`Error fetching exchange rate for ${sourceCurrency}:`, error);
                            return { sourceCurrency, rate: null }; // Return null for failed rates
                        }
                    })
                );
                return responses;

            } catch (error) {
                console.error('Error calling Sage API:', error);
                sharedState.sageNotAccessible = true
                throw error;
            }
        },
        async calculateTotalInBaseCurrency(groupedByCurrency){
            let currenciesArray = Object.keys(groupedByCurrency)
            let exchangeRateData = await this.getExchangeRatesByCurrencies(currenciesArray)

            const exchangeRates = {};
            exchangeRateData.forEach(({ sourceCurrency, rate }) => {
                if (rate !== null) exchangeRates[sourceCurrency] = rate;
            });
            let totalInBaseCurrency = 0;

            for (const [currency, amount] of Object.entries(groupedByCurrency)) {
                if (currency === this.currency) {
                    // If the currency is already the base currency, add it directly
                    totalInBaseCurrency += amount;
                } else if (exchangeRates[currency]) {
                    // Use the fetched rate to convert to the base currency
                    const rate = exchangeRates[currency];
                    totalInBaseCurrency += amount * rate;
                } else {
                    console.warn(`Exchange rate for ${currency} not available`);
                }
            }
            return totalInBaseCurrency;
        },
        async getBitrixIBlockData(endPoint, iBlockDta, method){
            try {
                const url = `https://crm.cresco.ae/rest/${this.sharedState.bitrixUserId}/${this.sharedState.bitrixWebhookToekn}/${endPoint}`;
                // delete axios.defaults.headers.common['X-Requested-With'];
                // const bitrixAxios = axios.create({
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded',
                //     },
                // });
                // Perform the API request
                const response = await axios({
                    url,
                    method,
                    data: iBlockDta
                });
                return response.data;
            } catch (error) {
                console.error('Error calling Bitrix (getBitrixIBlockData) API:', error);
                throw error;
            }
        },
        formatDate(value)  {
            if (value) {
                return DateTime.fromSQL(value).toFormat('dd LLL yyyy')
            } else {
                return "";
            }

        },
        formatDateTime24Hours(value)  {
            if (value) {
                return DateTime.fromSQL(value).toFormat('dd LLL yyyy HH:mm')
            } else {
                return "";
            }
        },
        formatDateTime12Hours(value)  {
            if (value) {
                return DateTime.fromSQL(value).toFormat('dd LLL yyyy hh:mm a')
            } else {
                return "";
            }
        },
        formatBitrixDate(value)  {
            if (value) {
                // Check if value contains a dot
                if (value.includes('.')) {
                    return DateTime.fromFormat(value, "dd.MM.yyyy").toFormat("dd LLL yyyy");
                }
                // Check if value contains a slash
                else if (value.includes('/')) {
                    return DateTime.fromFormat(value, "dd/MM/yyyy").toFormat("dd LLL yyyy");
                }
            }
            return "";
        },
        formatAmount(value) {
            if (value) {
                let numericValue = typeof value === 'string' ? parseFloat(value) : value;
                return numericValue.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                return 0.00;
            }
        },
        getBitrixProjectLink(obj){
            if(obj && obj.project_id){
                let project_id = obj.project_id.split("_");
                if (obj.project_type === "Deal") {
                    return `https://crm.cresco.ae/crm/deal/details/${project_id[1]}/`
                } else {
                    return `https://crm.cresco.ae/crm/lead/details/${project_id[1]}/`
                }
            }
        },
        getChargeExtraToClientValue(chargeExtra, identifier) {
            if(identifier === 'reports_purchase_invoices'){
                return !chargeExtra || chargeExtra === "1993" ? "No" : "Yes"
            }
            if(identifier === 'reports_cash_reports'){
                return !chargeExtra || chargeExtra === "1991" ? "No" : "Yes"
            }

        },
        getBitrixUrlByBlockIdAndId(blockId, id) {
            return `https://crm.cresco.ae/bizproc/processes/${blockId}/element/0/${id}/?list_section_id=`;
        },
        handleNetworkError(error){
            if (error.response.data){
                console.log('error from bitrixHelperMixin.js', error.response)
            }
        },
        async getCashRequestById(id){
            const bitrixUserId = this.sharedState.bitrix_user_id;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
            const endpoint = 'lists.element.get';
            try {
               const requestData = {
                    action: "getCashRequestById",
                    id: id
                }
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    return response.result
                }
            } catch (error) {
                console.log(error.response)
            }
        },
        async getPurchaseInvoiceById(id){
            const bitrixUserId = this.sharedState.bitrix_user_id;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
            const endpoint = 'lists.element.get';
            try {
                const requestData = {
                    action: "getPurchaseInvoiceById",
                    id: id
                }
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    return response.result
                }
            } catch (error) {
                console.log(error.response)
            }
        },
        async getPurchaseInvoiceByIdBitrixFields(id){
            const bitrixUserId = this.sharedState.bitrix_user_id;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
            const endpoint = 'lists.element.get';
            try {
                const requestData = qs.stringify({
                    IBLOCK_TYPE_ID: "bitrix_processes",
                    IBLOCK_ID: 104,
                    FILTER: {
                        ID: id
                    }
                });
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    return response.result
                }
            } catch (error) {
                console.log(error.response)
            }
        },
        async getCashRequestByIdBitrixFields(id){
            const bitrixUserId = this.sharedState.bitrix_user_id;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
            const endpoint = 'lists.element.get';
            try {
                const requestData = qs.stringify({
                    IBLOCK_TYPE_ID: "bitrix_processes",
                    IBLOCK_ID: 105,
                    FILTER: {
                        ID: id
                    }
                });
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    return response.result
                }
            } catch (error) {
                console.log(error.response)
            }
        },
        async getBitrixCurrencyList(){
            try {
                const response = await this.callBitrixAPI('crm.currency.list', this.sharedState.bitrix_user_id, this.sharedState.bitrix_webhook_token, {});
                if(response.result) {
                    return response.result;
                }
            } catch (error) {
                console.error(error)
            }
        },
    },
    computed: {
        currency: {
            get() {
                return this.sharedState.currency;
            },
            set(newCurrency) {
                this.sharedState.currency = newCurrency;
                localStorage.setItem("currency", newCurrency);
            },
        },
    },
    mounted() {

    }
}
