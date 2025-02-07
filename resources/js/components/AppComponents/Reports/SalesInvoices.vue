<template>
    <div class="container-fluid px-3">
        <reports-filters-component
            @get-data="getData"
        />
        <div class="grid gap-2">
            <!-- Filters Section -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex gap-2">
                    <!-- Category Filter -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="select select-sm select-input w-48"
                            v-model="filters.category_id"
                            @change="getData"
                        >
                            <option value="" selected>Filter by Category</option>
                            <option v-for="obj in page_data.bitrix_list_categories" :key="obj.id" :value="obj.bitrix_category_id">
                                {{ obj.bitrix_category_name }}
                            </option>
                        </select>
                    </div>
                    <!-- Sage Company Filter -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="select select-sm select-input w-96"
                            v-model="filters.sage_company_id"
                            @change="getData"
                        >
                            <option value="" selected>Filter by Sage Company</option>
                            <option v-for="obj in page_data.bitrix_list_sage_companies" :key="obj.id" :value="obj.bitrix_sage_company_id">
                                {{ obj.bitrix_sage_company_name }}
                            </option>
                        </select>
                    </div>
                    <!-- Dynamic Filters -->
                    <div v-for="filter in page_filters" :key="filter.key" class="flex flex-shrink-0">
                        <select
                            class="select select-sm select-input w-56"
                            v-model="filters[filter.key]"
                        >
                            <option value="" selected>Filter by {{ filter.name }}</option>
                            <option v-for="(value, key) in filter.values" :value="key" :key="key">{{ value }}</option>
                        </select>
                    </div>
                </div>
                <!-- Search Input -->
                <div class="flex grow">
                    <div class="relative w-full">
                        <i class="ki-outline ki-magnifier leading-none text-md text-black absolute top-1/2 left-3 transform -translate-y-1/2"></i>
                        <input
                            class="input input-sm text-input !ps-8"
                            placeholder="Search"
                            type="text"
                            v-model="filters.search"
                        />
                    </div>
                </div>
                <!-- Warning Filter -->
                <div class="flex flex-shrink-0">
                    <button
                        :class="['btn btn-icon btn-sm relative px-3 h-10 !w-10 !rounded-none transition-all duration-300', filters.is_warning ? 'btn-warning text-white' : 'btn-light text-black']"
                        @click="filters.is_warning = !filters.is_warning"
                    >
                        <i class="ki-outline ki-information-1"></i>
                        <span class="absolute top-1 right-1 translate-x-1/2 -translate-y-1/2 shadow-md shadow-red-300 bg-red-500 text-white text-xs font-bold rounded-full min-h-5 min-w-5 flex items-center justify-center">{{ warningCount }}</span>
                    </button>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow overflow-auto reports-table-container shadow-md border border-brand">
                <table class="w-full c-table table table-border align-middle text-xs table-fixed">
                    <thead>
                        <tr class="text-center tracking-tight">
                            <th class="sticky top-0 w-[30px]">#</th>
                            <th class="sticky top-0 w-[50px]">Id</th>
                            <th class="sticky top-0 w-[100px] text-left">Company <i class="ki-outline ki-exit-down"></i></th>
                            <th class="sticky top-0 w-[100px] text-left">Contact</th>
                            <th class="sticky top-0 w-[80px] text-right">Amount</th>
                            <th class="sticky top-0 w-[80px]">Status</th>
                            <th class="sticky top-0 w-[320px] text-left">CRM</th>
                            <th class="sticky top-0 w-[70px]">Invoice Date</th>
                            <th class="sticky top-0 w-[70px]">Due Date</th>
                            <th class="sticky top-0 w-[70px]">Date Paid</th>
                            <th class="sticky top-0 w-[100px] text-left">Sage Reference</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs tracking-tight">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-gray-800">
                            <td>{{ index + 1 }}</td>
                            <td><a target="_blank" class="btn btn-link" :href="`https://crm.cresco.ae/crm/invoice/show/${obj.id}/`">{{ obj.id }}</a></td>
                            <td class="text-left whitespace-normal break-words group/request">
                                <a target="_blank" class="btn btn-link line-clamp-3 group-hover/request:line-clamp-none" :href="`https://crm.cresco.ae/crm/company/details/${obj.company_id}/`">{{ obj.company }}</a>
                            </td>
                            <td class="text-left"><a target="_blank" class="btn btn-link" :href="`https://crm.cresco.ae/crm/contact/details/${obj.contact_id}/`">{{ obj.contact }}</a></td>
                            <td class="text-right"><span>{{ formatAmount(obj.price) }}</span> <strong class="text-black font-bold">{{ obj.currency }}</strong></td>
                            <td><div :class="isWarning(obj) ? 'badge badge-warning' : ''">{{ obj.status }}</div></td>
                            <td class="text-left">
                                <div>
                                    <span class="text-black font-bold">Deal: </span>
                                    <a target="_blank" class="btn btn-link" :href="`https://crm.cresco.ae/crm/deal/details/${obj.deal_id}/`">{{ obj.deal }}</a>
                                </div>
                                <div>
                                    <span class="text-black font-bold">Quote: </span>
                                    <a class="btn btn-link" target="_blank" :href="`https://crm.cresco.ae/crm/quote/show/${obj.quote_id}/`">View</a>
                                </div>
                            </td>
                            <td>{{ formatDate(obj.date_bill)  }}</td>
                            <td>{{ formatDate(obj.date_pay_before) }}</td>
                            <td>{{ formatDate(obj.date_payed) }}</td>
                            <td class="text-left">
                                <div v-if="obj.sage_invoice_number">
                                    <span>Invoice #:</span>
                                    <span>{{ obj.sage_invoice_number }}</span>
                                </div>
                                <div v-if="obj.pay_voucher_num">
                                    <span>Payment #:</span>
                                    <span>{{ obj.pay_voucher_num }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0" class="!bg-brand text-white">
                            <td colspan="4" class="font-bold text-center">Totals per currency</td>
                            <td colspan="1" class="text-right">
                                <div v-for="(amount, currency) in groupedByCurrency">{{ formatAmount(amount) }} <span class="font-bold text-white">{{ currency }} </span></div>
                            </td>
                        </tr>
                        <tr class="table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-md text-red-400">No data available</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="loading" class="data-loading absolute inset-0 bg-gray-100 bg-opacity-50 flex items-center justify-center z-100 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm text-brand-active">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
            </div>
            <!-- footer -->
            <div class="flex items-center justify-between">
                <!-- Left Section: Showing Records -->
                <div class="text-xs">
                    <span>Showing {{ filteredData.length }} records</span>
                </div>
                <!-- Right Section: Total as per Reporting Currency -->
                <div class="flex items-center justify-center text-right text-dark">
                    <span class="mr-2 tracking-tight">Total as per reporting currency ({{ currency }}):</span>
                    <span class="font-black">
                        {{ formatAmount(totalAsPerReportingCurrency) }} {{ currency }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "sales-invoices",
    props: ['page_data'],
    data(){
        return {
            data: [],
            loading: false,
            filters:{
                date: null,
                category_id: "",
                sage_company_id: "",
                status: "",
                charge_to_account: "",
                search: "",
                is_warning: false,
            },
            page_filters: [
                {
                    key: "status",
                    name: "Status",
                    field_id: "",
                    values: {}
                },
                {
                    key: "charge_to_account",
                    name: "Charge to Account",
                    field_id: "UF_CRM_66E151D80CA90",
                    values: {}
                },
            ],
            totalAsPerReportingCurrency: 0,
        }
    },
    methods: {
        async getData() {
            this.loading = true;
            try {
                await this.fetchFiltersValuesFromBitrix();
                await this.getPageData();
            } finally {
                this.loading = false;
            }
        },
        async fetchFiltersValuesFromBitrix() {
            const bitrixUserId = this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;
            for (const filter of this.page_filters) {
                try {
                    if(filter.key === 'status'){
                        const endpoint = 'crm.invoice.status.list';
                        const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken);
                        filter.values = response.result.reduce((acc, item) => {
                            acc[item.STATUS_ID] = item.NAME;
                            return acc
                        }, {})
                    }
                    else {
                        const endpoint = 'crm.invoice.userfield.list';
                        // Pre-format the filter to match Bitrix24 API's expected payload
                        const requestData = {};
                        Object.entries({ FIELD_NAME: filter.field_id }).forEach(([key, value]) => {
                            requestData[`filter[${key}]`] = value;
                        });
                        const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                        filter.values = response.result[0].LIST.reduce((acc, item) => {
                            acc[item.ID] = item.VALUE;
                            return acc;
                        }, {});
                    }
                } catch (error) {
                    console.error(`Error fetching filter data for ${filter.key}:`, error);
                }
            }
        },
        async getPageData(){
            let dateRange = JSON.parse(localStorage.getItem('dateRange'));
            this.loading = true;
            this.data = [];
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                startDate: dateRange[0],
                endDate: dateRange[1],
                action: "getSalesInvoices",
                categories: JSON.stringify(this.filters.category_id === "" ? this.page_data.bitrix_list_categories.map((obj) => obj.bitrix_category_id) : [this.filters.category_id]),
                sage_companies: JSON.stringify(this.filters.sage_company_id === "" ? this.page_data.bitrix_list_sage_companies.map((obj) => obj.bitrix_sage_company_id) : [this.filters.sage_company_id])
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                this.loading = false
                this.data = response.result;
                await this.calculateTotalAsPerReportingCurrency();
            } catch (error) {
                this.loading = false
            }
        },
        async calculateTotalAsPerReportingCurrency(){
            this.totalAsPerReportingCurrency = await this.calculateTotalInBaseCurrency(this.groupedByCurrency)
        },
        isWarning(item){
            const today = new Date();
            const invoiceDate = new Date(item.date_pay_before);
            return ((item.status === "Booked in SAGE" || item.status === "New") && invoiceDate < today);
        },
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            const searchTerm = this.filters.search?.toLowerCase() || '';
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.id, item.name, item.account_number, item.bank_code,
                    item.contact, item.deal, item.order_number,
                    item.order_topic, item.pay_voucher_num, item.project_id,
                    item.quote, item.sage_invoice_number, item.to_account_name,
                    item.to_account_number, item.to_bank_name, item.to_company_name,
                    item.to_iban,
                ].some(field => field?.toLowerCase().includes(searchTerm));
                // Filter by status
                const matchesStatus = this.filters.status ? item.status_id === this.filters.status : true;
                // Filter by chargeToAccount
                const matchesChargeToAccount = this.filters.charge_to_account ? item.charge_to_running_account_id === this.filters.charge_to_account : true;
                // Filter by warning
                const matchesWarning = this.filters.is_warning ? this.isWarning(item, today) : true;
                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesChargeToAccount && matchesWarning;
            });
        },
        groupedByCurrency() {
            const groupedByCurrency = _.groupBy(this.filteredData, 'currency');
            const summedByCurrency = _.mapValues(groupedByCurrency, (group) =>
                _.reduce(group, (sum, transaction) => sum + parseFloat(transaction.price), 0)
            );

            return summedByCurrency;
        },
        warningCount() {
            let today = DateTime.now();
            return this.filteredData.filter(item => this.isWarning(item, today)).length;
        },
    },
    watch: {
        groupedByCurrency(){
            this.calculateTotalAsPerReportingCurrency();
        },
        currency() {
            this.calculateTotalAsPerReportingCurrency();
        },
    },
    created() {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get("search")){
            this.filters.search = urlParams.get("search");
        }
    }
}
</script>
<style scoped>

</style>
