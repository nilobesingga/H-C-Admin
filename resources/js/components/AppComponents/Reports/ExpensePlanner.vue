<template>
    <div class="container-fluid">
        <!--  Sage Not Accessible          -->
        <div class="flex flex-col justify-start pb-6" v-if="sharedState.sageNotAccessible">
            <sage-network-error />
        </div>

        <div class="grid gap-5 lg:gap-7.5">
            <!-- filters -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.category_id" @change="getData">
                        <option value="" selected>Filter by Category</option>
                        <option v-for="(obj, index) in page_data.categories" :key="index" :value="obj.category_id">
                            {{ obj.bitrix_category_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.sage_company_code" @change="getData">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="(obj, index) in page_data.sage_companies" :key="index" :value="obj.sage_company_code">
                            {{ obj.bitrix_sage_company_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.request_type">
                        <option value="" selected>Filter by Type</option>
                        <option value="cash_request">Cash Request</option>
                        <option value="purchase_invoice">Purchase Invoice</option>
                        <option value="budget_only">Budget Only</option>
                    </select>
                </div>
            </div>
            <div class="flex-grow overflow-auto">
                <div v-if="loading" class="absolute inset-0 bg-gray-100 bg-opacity-50 flex items-center justify-center z-10">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
                <!-- week columns -->
                <div class="flex gap-6 overflow-auto text-sm expense-planner-columns">
                    <!-- Each column represents a week -->
                    <div
                        v-for="(week, index) in weekHeaders"
                        :key="index"
                        class="flex-1 flex flex-col bg-gray-100 p-4 rounded shadow min-w-[250px] text-center text-sm"
                    >
                        <!-- Week Header -->
                        <div class="text-black font-black mb-2">Week {{ week.week_number }}</div>
                        <div class="text-black">({{ week.start_date }} to {{ week.end_date }})</div>

                        <!-- Total Amount -->
                        <div v-if="week.data.length" class="text-lg text-red-700 font-bold mt-4 mb-4">{{ getTotalAmount(week.data) }} USD</div>

                        <!-- Week Data -->
                        <div v-if="week.data.length" class="flex flex-col gap-2 overflow-y-auto h-full">
                            <div
                                v-for="(item, itemIndex) in week.data"
                                :key="itemIndex"
                                :class="[
                        'p-3 rounded shadow',
                        item.request_type === 'cash_request' ? 'bg-white' : 'bg-blue-100'
                    ]"
                            >
                                <div class="text-md text-black font-bold text-left mb-2">{{ item.amount }} {{ item.currency }}</div>
                                <div class="text-left">
                                    <a class="btn btn-link" target="_blank" :href="getBitrixProjectLink(item)">{{ item.project_name }}</a>
                                </div>
                                <div class="text-left text-black">{{ item.detail_text }}</div>
                                <div class="text-left">Requested By: {{ item.requested_by_name }}</div>
                                <div class="text-left">Pay By: {{ item.payment_mode_id === "1868" ? "Card" : "Cash" }}</div>
                                <div class="float-left badge badge-danger badge-xs mt-2">Due: {{ formatDate(item.request_type === 'cash_request' ? item.payment_date : item.due_date) }}</div>
                            </div>
                        </div>
                        <div v-else class="text-center text-red-400 mt-4">No data available</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";
import bitrixHelperMixin from "../../../mixins/bitrixHelperMixin.js";
export default {
    name: "expense-planner",
    mixins: [bitrixHelperMixin],
    props: ['page_data'],
    data(){
        return {
            data: [],
            loading: false,
            filters: {
                date: null,
                category_id: "",
                sage_company_code: "",
                request_type: "",
                search: "",
            },
        }
    },
    methods: {
        async getData(){
            try {
                this.loading = true;
                this.data = [];

                // Fetch cash requests and purchase invoices in parallel
                const [cashRequests, purchaseInvoices] = await Promise.all([
                    this.getCashRequestsData(),
                    this.getPurchaseInvoicesData()
                ]);
                this.data = [...cashRequests, ...purchaseInvoices];
            } catch (error) {
                this.loading = false;
                this.handleNetworkError(error);
            } finally {
                this.loading = false;
            }
        },
        async getCashRequestsData(){
            let dateRange = this.calculateDateRange();
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                startDate: dateRange[0],
                endDate: dateRange[1],
                action: "getCashReports",
                categories: JSON.stringify(
                    this.filters.category_id === "" ?
                        this.page_data.bitrix_list_cash_requests_categories.map((obj) => obj.bitrix_category_id) :
                        [
                            this.page_data.bitrix_list_cash_requests_categories.find(
                                (obj) => obj.category_id === this.filters.category_id
                            )?.bitrix_category_id || null
                        ]
                ),
                sage_companies: JSON.stringify(
                    this.filters.sage_company_code === "" ?
                        this.page_data.bitrix_list_cash_requests_sage_companies.map((obj) => obj.bitrix_sage_company_id) :
                        [
                            this.page_data.bitrix_list_cash_requests_sage_companies.find(
                                (obj) => obj.sage_company_code === this.filters.sage_company_code
                            )?.bitrix_sage_company_id || null
                        ]
                )
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                return response.result
                    .filter(item => item.status_id === "1652" || item.status_id === "1654" || item.status_id === "1687")
                    .map(item => ({
                        ...item,
                        request_type: "cash_request",
                        week_date: item.funds_available_date != null ? item.funds_available_date : item.payment_date
                    }));
            } catch (error) {
                this.handleNetworkError(error);
                return [];
            }
        },
        async getPurchaseInvoicesData(){
            let dateRange = this.calculateDateRange();
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                startDate: dateRange[0],
                endDate: dateRange[1],
                action: "getPurchaseInvoices",
                categories: JSON.stringify(
                    this.filters.category_id === "" ?
                        this.page_data.bitrix_list_purchase_invoices_categories.map((obj) => obj.bitrix_category_id) :
                        [
                            this.page_data.bitrix_list_purchase_invoices_categories.find(
                                (obj) => obj.category_id === this.filters.category_id
                            )?.bitrix_category_id || null
                        ]
                ),
                sage_companies: JSON.stringify(
                    this.filters.sage_company_code === "" ?
                        this.page_data.bitrix_list_purchase_invoices_sage_companies.map((obj) => obj.bitrix_sage_company_id) :
                        [
                            this.page_data.bitrix_list_purchase_invoices_sage_companies.find(
                                (obj) => obj.sage_company_code === this.filters.sage_company_code
                            )?.bitrix_sage_company_id || null
                        ]
                )
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                return response.result
                    .filter(item => item.status_id === "1618" || item.status_id === "1864")
                    .map(item => ({
                        ...item,
                        request_type: "purchase_invoice",
                        week_date: item.payment_schedule_date
                    }));
            } catch (error) {
                this.handleNetworkError(error);
                return [];
            }
        },
        calculateDateRange(){
            const now = DateTime.now();
            const startOfWeek = now.startOf("week");
            const endOfWeek = now.endOf("week");

            // Calculate overall start and end for 5 weeks
            const overallStartDate = startOfWeek.toISODate();
            const overallEndDate = endOfWeek.plus({ weeks: 4 }).toISODate();

            return [overallStartDate, overallEndDate]
        },
        // Calculate total amount for a given week's data
        getTotalAmount(data) {
            return data.reduce((total, item) => total + parseFloat(item.amount || 0), 0).toFixed(2);
        },
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch =
                    (item.amount && item.amount.includes(this.filters.search));

                // Filter by type
                const matchesType = this.filters.request_type ? item.request_type === this.filters.request_type : true;

                // Return true only if all filters match
                return matchesSearch && matchesType;
            });
        },
        // Dynamically filter and group data by week
        weekHeaders() {
            const today = DateTime.now();
            const currentWeekStart = today.startOf("week");

            // Generate 5 week headers with empty data arrays
            const weeks = Array.from({ length: 5 }).map((_, i) => {
                const startDate = currentWeekStart.plus({ weeks: i });
                return {
                    week_number: startDate.weekNumber,
                    start_date: startDate.toFormat("dd MMM yyyy"),
                    end_date: startDate.endOf("week").toFormat("dd MMM yyyy"),
                    data: [], // Will be populated based on filters
                };
            });

            // Filter and group data by week number
            const filteredGroupedData = _.groupBy(this.filteredData, (item) => {
                const weekDate = DateTime.fromISO(item.week_date).startOf("week");
                return weekDate.weekNumber;
            });

            // Populate data in the relevant week headers
            weeks.forEach((week) => {
                if (filteredGroupedData[week.week_number]) {
                    week.data = filteredGroupedData[week.week_number];
                }
            });

            return weeks;
        },
    },
    created() {
        this.sharedState.bitrixUserId = this.page_data.user.bitrix_user_id;
        this.sharedState.bitrixWebhookToekn = this.page_data.user.bitrix_webhook_token;
    },
    mounted() {
        this.getData();
    }
}
</script>

<style scoped>

</style>
