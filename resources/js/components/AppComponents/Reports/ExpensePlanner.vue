<template>
    <div class="container-fluid px-3">
        <!-- Left Hover Area -->
        <div class="hover-area hover-area-left group transition-all duration-500">
            <button
                class="prev-week-btn group-hover:w-16 group-hover:h-16 group-hover:bg-black"
                @click="navigateWeeks(-1)"
            >
                <i class="ki-solid ki-to-left text-black group-hover:text-white"></i>
            </button>
        </div>
        <!-- Right Hover Area -->
        <div class="hover-area hover-area-right group transition-all duration-500">
            <button
                class="next-week-btn group-hover:w-16 group-hover:h-16 group-hover:right-0 group-hover:bg-black"
                @click="navigateWeeks(1)"
            >
                <i class="ki-solid ki-to-right text-black group-hover:text-white"></i>
            </button>
        </div>

        <div class="grid gap-2 pt-2">
            <!-- filters -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex">
                    <select class="select select-sm select-input w-48" v-model="filters.category_id" @change="getData">
                        <option value="" selected>Filter by Category</option>
                        <option v-for="(obj, index) in page_data.categories" :key="index" :value="obj.category_id">
                            {{ obj.bitrix_category_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm select-input w-96" v-model="filters.sage_company_code" @change="getData">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="(obj, index) in page_data.sage_companies" :key="index" :value="obj.sage_company_code">
                            {{ obj.bitrix_sage_company_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm select-input w-40" v-model="filters.currency">
                        <option value="" selected>Filter by Currency</option>
                        <option value="USD">USD</option>
                        <option value="AED">AED</option>
                        <option value="AUD">AUD</option>
                        <option value="CNY">CNY</option>
                        <option value="GBP">GBP</option>
                        <option value="EUR">EUR</option>
                        <option value="CHF">CHF</option>
                        <option value="PHP">PHP</option>
                        <option value="INR">INR</option>
                        <option value="SCR">SCR</option>
                        <option value="CRC">CRC</option>
                        <option value="BRL">BRL</option>
                        <option value="RUB">RUB</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm select-input w-40" v-model="filters.request_type">
                        <option value="" selected>Filter by Type</option>
                        <option value="cash_request">Cash Request</option>
                        <option value="purchase_invoice">Purchase Invoice</option>
                        <option value="budget_only">Budget Only</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm select-input w-64" v-model="filters.awaiting_for_exchange_rate">
                        <option value="" selected>Filter by Awaiting for Exchange Rate</option>
                        <option value="include">Include</option>
                        <option value="only">Only</option>
                    </select>
                </div>
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
            </div>
            <!-- content area -->
            <div class="relative flex-grow overflow-auto">
                <!-- Loading Indicator -->
                <div v-if="loading" class="data-loading absolute inset-0 bg-neutral-100 flex items-center justify-center z-100 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm text-brand-active">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
                <!-- week columns -->
                <div class="flex gap-3 text-sm expense-planner-columns">
                    <!-- Each column represents a week -->
                    <div v-for="(week, index) in weekHeaders" :key="index" class="flex-1 flex flex-col bg-neutral-200 p-4 rounded text-center text-sm overflow-auto overflow-y-hidden group">
                        <!-- Week Header -->
                        <div class="text-black text-2xl tracking-tight font-bold">Week {{ week.week_number }}</div>
                        <div class="text-neutral-700 text-xs">{{ week.start_date }} &mdash; {{ week.end_date }}</div>

                        <!-- Total Amount -->
                        <div v-if="week.data.length" class="mt-4 mb-4">
                            <strong class="text-xl text-red-700">{{ getWeeklyTotalWithCurrency(week.data) }}</strong>
                            <div class="mt-1 text-xs text-neutral-700">{{ getWeeklyTotalWithCurrencyConversion(week.data) }}</div>
                        </div>

                        <!-- Full Access -->
                        <div v-if="page_data.permission === 'full_access'" class="h-[68vh]">
                            <draggable
                                tag="template"
                                group="data"
                                :list="week.data"
                                item-key="id"
                                class="flex flex-col gap-2 overflow-y-auto h-full border-t border-neutral-200 pt-4 group-hover:border-black"
                                @change="onDragUpdateColumn($event, week)"
                                :animation="200"
                            >
                                <template #item="{ element: item, index: itemIndex }">
                                    <div :key="itemIndex">
                                        <!-- cash requests -->
                                        <div :class="['card', (item.is_budget_only === '1937' ? 'budget-only' : 'cash-request')]" v-if="item.request_type === 'cash_request'">
                                            <div class="card-title text-left flex w-full flex-col">
                                                <div class="flex justify-between items-center">
                                                    <a class="btn btn-link !text-black hover:!text-brand-active text-lg" target="_blank" :href="getBitrixUrlByBlockIdAndId('105', item.id)">
                                                        {{ formatAmount(item.amount) }} {{ item.currency }}
                                                    </a>
                                                    <!-- awaiting for exchange rate -->
                                                    <i class="ki-filled ki-timer text-orange-400" v-if="item.awaiting_for_exchange_rate === 'Yes'"></i>
                                                </div>
                                                <sub class="text-xs text-neutral-600 mb-2 -mt-1" v-if="item.currency !== 'USD'">({{ formatAmount(item.exchange_amount) }} USD)</sub>
                                            </div>
                                            <a class="btn btn-link text-left !text-neutral-800" target="_blank" :href="getBitrixProjectLink(item)">{{ item.project_name }}</a>
                                            <div class="text-neutral-700 text-left">{{ item.detail_text }}</div>
                                            <div class="text-left text-xs text-neutral-700 mt-4">
                                                <span>Requested By: </span>
                                                <span class="text-neutral-800">{{ item.requested_by_name }}</span>
                                            </div>
                                            <div class="text-left text-xs text-neutral-700">
                                                <span>Pay By: </span>
                                                <span class="text-neutral-800">{{ getPaymentMode(item.payment_mode_id) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-2">
                                                <div class="text-left">
                                                    <small :class="['badge text-xs', isOverdue(item.payment_date) ? 'badge-danger' : 'badge-success']">Due: {{ formatDate(item.payment_date) }}</small>
                                                    <small class="badge badge-warning text-xs ml-1" v-if="item.is_budget_only === '1937'">Budget Only</small>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center mt-3">
                                                <div>
                                                    <button
                                                        data-modal-toggle="#cash_request_update_form_modal"
                                                        class="btn btn-xs btn-light"
                                                        @click="openModal('update', item)"
                                                    >
                                                        Update
                                                    </button>
                                                    <button
                                                        data-modal-toggle="#cash_request_release_fund_form_modal"
                                                        class="btn btn-xs btn-light ml-1"
                                                        @click="openModal('release_fund', item)"
                                                    >
                                                        Release Funds
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- purchase invoices -->
                                        <div class="card purchase-invoice" v-if="item.request_type === 'purchase_invoice'">
                                            <div class="card-title text-left flex">
                                                <a class="btn btn-link !text-black hover:!text-brand-active " target="_blank" :href="getBitrixUrlByBlockIdAndId(item.request_type === 'cash_request' ? '104' : '104', item.id)">
                                                    <div class="text-lg">
                                                        <span v-if="item.remaining_balance">{{ formatAmount(item.remaining_balance) }}</span>
                                                        <span v-else>{{ formatAmount(item.amount) }}</span>
                                                        {{ item.currency }}
                                                    </div>
                                                </a>
                                            </div>
                                            <a class="btn btn-link text-left !text-neutral-800" target="_blank" :href="getBitrixProjectLink(item)">{{ item.project_name }}</a>
                                            <div class="text-neutral-700 text-left mt-2">{{ item.detail_text }}</div>
                                            <div class="text-left text-xs text-neutral-700 mt-4">
                                                <span>Requested By: </span>
                                                <span class="text-neutral-800">{{ item.requested_by_name }}</span>
                                            </div>
                                            <div class="text-left mt-2">
                                                <small :class="['badge text-xs', isOverdue(item.due_date) ? 'badge-danger' : 'badge-success']">Due: {{ formatDate(item.due_date) }}</small>
                                                <small v-if="item.sage_status && item.sage_status === '1863'" class="badge badge-info text-xs font-bold ml-1">Booked In Sage</small>
                                                <small v-else class="badge badge-warning text-xs font-bold ml-1">NOT Booked In Sage</small>
                                                <small v-if="item.status_id === '1864'" class="badge badge-warning text-xs font-bold ml-1 ">Partially Paid</small>
                                            </div>
                                            <div class="flex justify-between items-center mt-3" v-if="item.preview_url">
                                                <a
                                                    class="btn btn-xs btn-light"
                                                    target="_blank"
                                                    :href="item.preview_url"
                                                >
                                                    Preview Invoice
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Empty column is still draggable -->
                                <template #footer>
                                    <div v-if="week.data.length === 0" class="text-center text-red-400 mt-4 flex justify-center items-center">
                                        No data available
                                    </div>
                                </template>
                            </draggable>
                        </div>
                        <!-- View Only -->
                        <div v-if="page_data.permission === 'view_only'" class="h-full">
                            <!-- Week Data -->
                            <div v-if="week.data.length" class="flex flex-col gap-2 overflow-y-auto h-[68vh] border-t border-neutral-200 pt-4 group-hover:border-black">
                                <div v-for="(item, itemIndex) in week.data" :key="itemIndex">
                                    <!-- cash requests -->
                                    <div :class="['card', (item.is_budget_only === '1937' ? 'budget-only' : 'cash-request')]" v-if="item.request_type === 'cash_request'">
                                        <div class="card-title text-left flex w-full flex-col">
                                            <div class="flex justify-between items-center">
                                                <a class="btn btn-link !text-black hover:!text-brand-active text-lg" target="_blank" :href="getBitrixUrlByBlockIdAndId('105', item.id)">
                                                    {{ formatAmount(item.amount) }} {{ item.currency }}
                                                </a>
                                                <!-- awaiting for exchange rate -->
                                                <i class="ki-filled ki-timer text-orange-400" v-if="item.awaiting_for_exchange_rate === 'Yes'"></i>
                                            </div>
                                            <sub class="text-xs text-neutral-600 mb-2 -mt-1" v-if="item.currency !== 'USD'">({{ formatAmount(item.exchange_amount) }} USD)</sub>
                                        </div>
                                        <a class="btn btn-link text-left !text-neutral-800" target="_blank" :href="getBitrixProjectLink(item)">{{ item.project_name }}</a>
                                        <div class="text-neutral-700 text-left">{{ item.detail_text }}</div>
                                        <div class="text-left text-xs text-neutral-700 mt-4">
                                            <span>Requested By: </span>
                                            <span class="text-neutral-800">{{ item.requested_by_name }}</span>
                                        </div>
                                        <div class="text-left text-xs text-neutral-700">
                                            <span>Pay By: </span>
                                            <span class="text-neutral-800">{{ getPaymentMode(item.payment_mode_id) }}</span>
                                        </div>
                                        <div class="text-left mt-2">
                                            <small :class="['badge text-xs', isOverdue(item.payment_date) ? 'badge-danger' : 'badge-success']">Due: {{ formatDate(item.payment_date) }}</small>
                                            <small class="badge badge-warning text-xs ml-1" v-if="item.is_budget_only === '1937'">Budget Only</small>
                                        </div>
                                    </div>
                                    <!-- purchase invoices -->
                                    <div class="card purchase-invoice" v-if="item.request_type === 'purchase_invoice'">
                                        <div class="card-title text-left flex">
                                            <a class="btn btn-link !text-black hover:!text-brand-active " target="_blank" :href="getBitrixUrlByBlockIdAndId(item.request_type === 'cash_request' ? '104' : '104', item.id)">
                                                <div class="text-lg">
                                                    <span v-if="item.remaining_balance">{{ formatAmount(item.remaining_balance) }}</span>
                                                    <span v-else>{{ formatAmount(item.amount) }}</span>
                                                    {{ item.currency }}
                                                </div>
                                            </a>
                                        </div>
                                        <a class="btn btn-link text-left !text-neutral-800" target="_blank" :href="getBitrixProjectLink(item)">{{ item.project_name }}</a>
                                        <div class="text-neutral-700 text-left mt-2">{{ item.detail_text }}</div>
                                        <div class="text-left text-xs text-neutral-700 mt-4">
                                            <span>Requested By: </span>
                                            <span class="text-neutral-800">{{ item.requested_by_name }}</span>
                                        </div>
                                        <div class="text-left mt-2">
                                            <small :class="['badge text-xs', isOverdue(item.due_date) ? 'badge-danger' : 'badge-success']">Due: {{ formatDate(item.due_date) }}</small>
                                            <small v-if="item.sage_status && item.sage_status === '1863'" class="badge badge-info text-xs font-bold ml-1">Booked In Sage</small>
                                            <small v-else class="badge badge-warning text-xs font-bold ml-1">NOT Booked In Sage</small>
                                            <small v-if="item.status_id === '1864'" class="badge badge-warning text-xs font-bold ml-1 ">Partially Paid</small>
                                        </div>
                                        <div class="flex justify-between items-center mt-3" v-if="item.preview_url">
                                            <a
                                                class="btn btn-xs btn-light"
                                                target="_blank"
                                                :href="item.preview_url"
                                            >
                                                Preview Invoice
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center text-red-400 mt-4 flex justify-center items-center h-full">No data available</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cash Request Update -->
    <cash-request-update-form-modal
        :obj="selected_obj"
        :cash_pools="cash_pools"
        :cash_release_locations="cash_release_locations"
        v-if="is_cash_request_update"
        @closeModal="closeModal"
    />
    <!-- Cash Request Release Fund Form Modal -->
    <cash-request-release-fund-form-modal
        :obj="selected_obj"
        :payment_modes="payment_modes"
        :cash_pools="cash_pools"
        :cash_release_locations="cash_release_locations"
        :sage_companies="page_data.bitrix_list_cash_requests_sage_companies"
        v-if="is_cash_request_release_fund_form_modal"
        @closeModal="closeModal"
    />
</template>

<script>
import {DateTime} from "luxon";
import _ from "lodash";
import qs from 'qs';
import draggable from 'vuedraggable'

export default {
    name: "expense-planner",
    props: ['page_data'],
    components: {
        draggable,
    },
    data(){
        return {
            data: [],
            loading: false,
            filters: {
                date: null,
                category_id: "",
                sage_company_code: "",
                request_type: "",
                currency: "",
                awaiting_for_exchange_rate: "",
                search: "",
            },
            payment_modes: [],
            cash_pools: [],
            cash_release_locations: [],
            week_off_set: 0,
            selected_obj: null,
            modal_type: null,
            is_cash_request_release_fund_form_modal: false,
            is_cash_request_update: false,
        }
    },
    methods: {
        async getData(){
            try {
                this.loading = true;
                this.data = [];

                // Fetch payment modes
                await this.fetchPaymentModesFromBitrix();
                await this.fetchCashPoolsFromBitrix();
                await this.fetchCashReleaseLocationsFromBitrix();
                // Fetch cash requests and purchase invoices in parallel
                const [cashRequests, purchaseInvoices] = await Promise.all([
                    this.getCashRequestsData(),
                    this.getPurchaseInvoicesData()
                ]);

                this.data = [...cashRequests, ...purchaseInvoices];

                // Extract unique currencies (excluding USD)
                const uniqueCurrencies = [...new Set(this.data.map(item => item.currency))].filter(currency => currency !== "USD");
                if (uniqueCurrencies.length > 0) {
                    // Fetch exchange rates for all unique currencies
                    const exchangeRates = await this.getExchangeRatesByCurrencies(uniqueCurrencies, "USD");

                    // Map exchange rates for quick lookup
                    const rateMap = exchangeRates.reduce((acc, { sourceCurrency, rate }) => {
                        acc[sourceCurrency] = rate;
                        return acc;
                    }, {});

                    // Update each item's exchange_amount
                    this.data.forEach(item => {
                        const rate = rateMap[item.currency];
                        item.exchange_amount = rate ? Number(item.amount) * rate : Number(item.amount); // Use USD amount if no rate found
                    });
                } else {
                    // If all currencies are already in USD
                    this.data.forEach(item => {
                        item.exchange_amount = Number(item.amount);
                    });
                }
            } catch (error) {
                this.loading = false;
            } finally {
                this.loading = false;
            }
        },
        async fetchPaymentModesFromBitrix() {
            const bitrixUserId = this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;
            try {
                const endpoint = 'lists.field.get';
                const requestData = {
                    IBLOCK_TYPE_ID: "bitrix_processes",
                    IBLOCK_ID: 105,
                    FIELD_ID: "PROPERTY_1088"
                };
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response && response.result && response.result.L && response.result.L.DISPLAY_VALUES_FORM) {
                    this.payment_modes = Object.entries(response.result.L.DISPLAY_VALUES_FORM).map(([id, name]) => ({
                        id,
                        name
                    }));
                } else {
                    this.payment_modes = [];
                }
            } catch (error) {
                console.error(`Error fetching filter data for payment mode list:`, error);
            }
        },
        async fetchCashPoolsFromBitrix() {
            const bitrixUserId = this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;
            try {
                const endpoint = 'lists.field.get';
                const requestData = {
                    IBLOCK_TYPE_ID: "bitrix_processes",
                    IBLOCK_ID: 105,
                    FIELD_ID: "PROPERTY_1231"
                };
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response && response.result && response.result.L && response.result.L.DISPLAY_VALUES_FORM) {
                    this.cash_pools = Object.entries(response.result.L.DISPLAY_VALUES_FORM).map(([id, name]) => ({
                        id,
                        name
                    }));
                } else {
                    this.cash_pools = [];
                }
            } catch (error) {
                console.error(`Error fetching filter data for cash pools list:`, error);
            }
        },
        async fetchCashReleaseLocationsFromBitrix() {
            const bitrixUserId = this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;
            try {
                const endpoint = 'lists.field.get';
                const requestData = {
                    IBLOCK_TYPE_ID: "bitrix_processes",
                    IBLOCK_ID: 105,
                    FIELD_ID: "PROPERTY_954"
                };
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response && response.result && response.result.L && response.result.L.DISPLAY_VALUES_FORM) {
                    this.cash_release_locations = Object.entries(response.result.L.DISPLAY_VALUES_FORM).map(([id, name]) => ({
                        id,
                        name
                    }));
                } else {
                    this.cash_release_locations = [];
                }
            } catch (error) {
                console.error(`Error fetching filter data for cash release locations list:`, error);
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
                    // .filter(item => item.status_id === "1652" || item.status_id === "1655" || item.status_id === "1687")
                    .filter(item => item.status_id === "1652" || item.status_id === "1687")
                    .map(item => ({
                        ...item,
                        request_type: "cash_request",
                        week_date: item.funds_available_date != null ? item.funds_available_date : item.payment_date,
                    }));
            } catch (error) {
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
                return [];
            }
        },
        calculateDateRange(){
            const now = DateTime.now().plus({ weeks: this.week_off_set * 5 });
            const startOfWeek = now.startOf("week");
            const endOfWeek = now.endOf("week");

            // Calculate overall start and end for 5 weeks
            const overallStartDate = startOfWeek.toISODate();
            const overallEndDate = endOfWeek.plus({ weeks: 4 }).toISODate();

            return [overallStartDate, overallEndDate]
        },
        getWeeklyTotalWithCurrency(data) {
            if (this.filters.currency === ''){
                let total = data.reduce((total, item) => total + (isNaN(item.exchange_amount) ? 0 : Number(item.exchange_amount)), 0)
                return `${this.formatAmount(total)} USD`
            }
            else {
                let total = data.reduce((total, item) => total + (isNaN(item.amount) ? 0 : Number(item.amount)), 0)
                return `${this.formatAmount(total)} ${this.filters.currency}`
            }
        },
        getWeeklyTotalWithCurrencyConversion(data) {
            if (this.filters.currency === '' || this.filters.currency === 'USD'){
                let total = data.reduce((total, item) => total + (isNaN(item.exchange_amount) ? 0 : Number(item.exchange_amount)), 0)
                let convertedAmount = total * 3.6725
                return `${this.formatAmount(convertedAmount)} AED`
            }
            else {
                let total = data.reduce((total, item) => total + (isNaN(item.exchange_amount) ? 0 : Number(item.exchange_amount)), 0)
                return `${this.formatAmount(total)} USD`
            }
        },
        getPaymentMode(paymentModeId) {
            let paymentMode = this.payment_modes.find(obj => obj.id === paymentModeId);
            if (paymentMode) {
                return paymentMode.name;
            } else {
                return "Cash";
            }
        },
        isOverdue(date) {
            var today = DateTime.now();
            var dueDate = DateTime.fromSQL(date);

            return today > dueDate;
        },
        navigateWeeks(direction) {
            this.week_off_set += direction;
            this.getData();
        },
        onDragUpdateColumn(event, week) {
            if (event.added && event.added.element){
                if (event.added && event.added.element.request_type === 'cash_request') {
                    this.updateCashRequestFundReleaseDate(event.added.element, week)
                } else if (event.added && event.added.element.request_type === 'purchase_invoice') {
                    this.updatePurchaseInvoicePaymentSchedule(event.added.element, week)
                }
            }
        },
        async updateCashRequestFundReleaseDate(item, newWeek){
            try {
                let itemId = item.id;
                const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
                const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
                const endpoint = 'lists.element.update';
                const requestData = qs.stringify({
                    IBLOCK_TYPE_ID: 'bitrix_processes',
                    IBLOCK_ID: '105',
                    ELEMENT_ID: item.id,
                    fields: {
                        'NAME': item.name,
                        'DETAIL_TEXT': item.detail_text,
                        // Company
                        'PROPERTY_938': item.company_id,
                        // SAGE Company
                        'PROPERTY_951': item.sage_company_id,
                        // VATable
                        'PROPERTY_1236': item.vatable_id,
                        // Amount (including VAT)
                        'PROPERTY_939': item.cash_amount,
                        // Awaiting for Exchange Rate
                        'PROPERTY_1249': item.awaiting_for_exchange_rate_id,
                        // Cash Pool
                        'PROPERTY_1231': item.cash_pool_id,
                        // Cash Release Location
                        'PROPERTY_954': item.cash_release_location_id,
                        // Invoice Number
                        'PROPERTY_1241': item.invoice_number,
                        // Payment Date
                        'PROPERTY_940': item.payment_date,
                        // Payment Mode
                        'PROPERTY_1088': item.payment_mode_id,
                        // Budget only
                        'PROPERTY_1160': item.budget_only_id,
                        // Charge extra to client
                        'PROPERTY_1215': item.charge_extra_to_client_id,
                        // Charge To Running Account
                        'PROPERTY_1243': item.charge_to_running_account_id,
                        // Accountant
                        'PROPERTY_941': item.accountant_id,
                        // Project
                        'PROPERTY_942': item.project_id,
                        // Supplier
                        'PROPERTY_1234': item.supplier_id,
                        // Link to CRM
                        'PROPERTY_1214': item.linked_contact_id,
                        // Pay To Running Account
                        'PROPERTY_1251': item.pay_to_running_account_id,
                        // Doc for Bank
                        'PROPERTY_1247': item.doc_for_bank,
                        // Other Documents
                        'PROPERTY_1210': item.other_documents,
                        // Requested By
                        'CREATED_BY': item.created_by,
                        // Amount Given
                        'PROPERTY_944': item.amount_given,
                        // Company Link
                        'PROPERTY_945': item.company_link_id,
                        // Funds Available Date
                        'PROPERTY_946': DateTime.fromFormat(newWeek.start_date, "dd MMM yyyy").toSQLDate(),
                        // Cash Release Receipt
                        'PROPERTY_1059': item.cash_release_receipt_id,
                        // Receipt
                        'PROPERTY_948': { itemId: [item.receipt_id] },
                        // Receipt Preview Link
                        'PROPERTY_949': item.receipt_preview_link,
                        // Amount Returned
                        'PROPERTY_950': item.amount_returned,
                        // Status
                        'PROPERTY_943': item.status_id,
                        //  Bank Transfer Id
                        'PROPERTY_1222': item.bitrix_bank_transfer_id,
                        //  Modified By
                        'MODIFIED_BY': this.page_data.user.bitrix_user_id,
                        //  Released By
                        'PROPERTY_1071': item.released_by,
                        //  Release Date
                        'PROPERTY_1073': item.released_date,
                        //  Offer Generated
                        'PROPERTY_1224': item.has_offer_generated,
                    }
                });

                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if(response){
                    this.successToast('Fund released date updated successfully')
                    item.week_date = DateTime.fromFormat(newWeek.start_date, "dd MMM yyyy").toISODate();
                }
            } catch (error) {
                this.errorToast('Error updating fund release date')
            }
        },
        async updatePurchaseInvoicePaymentSchedule(item, newWeek){
            try {
                let itemId = item.id;
                const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
                const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
                const endpoint = 'lists.element.update';
                const requestData = qs.stringify({
                    IBLOCK_TYPE_ID: 'bitrix_processes',
                    IBLOCK_ID: '104',
                    ELEMENT_ID: item.id,
                    fields: {
                        'NAME': item.name,
                        'DETAIL_TEXT': item.detail_text,
                        // Company
                        'PROPERTY_925': item.company_id,
                        // SAGE Company
                        'PROPERTY_933': item.sage_company_id,
                        // Invoice Date
                        'PROPERTY_926': item.invoice_date,
                        // Invoice Number
                        'PROPERTY_953': item.invoice_number,
                        // Due Date
                        'PROPERTY_934': item.due_date,
                        // VATable
                        'PROPERTY_1171': item.vatable_id,
                        // Total Invoice Amount (including VAT)
                        'PROPERTY_927': item.invoice_amount,
                        // Charge extra to client
                        'PROPERTY_1219': item.charge_extra_to_client,
                        // Charge To Running Account
                        'PROPERTY_1242': item.charge_to_running_account_id,
                        // Remaining Balance
                        'PROPERTY_1180': item.remaining_balance,
                        // Supplier
                        'PROPERTY_928': item.supplier_id,
                        // Project
                        'PROPERTY_929': item.project_id,
                        // Link to Contact
                        'PROPERTY_1230': item.linked_to_contact,
                        // Pay To Running Account
                        'PROPERTY_1250': item.pay_to_running_account_id,
                        // Invoice Document
                        'PROPERTY_930': {itemId: [item.invoice_document_id]},
                        // Doc for Bank
                        'PROPERTY_1246': {itemId: [item.doc_for_bank]},
                        // Other Documents
                        'PROPERTY_1208': {itemId: [item.other_documents]},
                        // Accountant
                        'PROPERTY_935': item.accountant_id,
                        // Preview URL
                        'PROPERTY_936': item.preview_url,
                        // Company Link
                        'PROPERTY_937': item.company_link,
                        // Transfer Document
                        'PROPERTY_952': {itemId: [item.transfer_document_id]},
                        // Payment Schedule Date
                        'PROPERTY_955': DateTime.fromFormat(newWeek.start_date, "dd MMM yyyy").toSQLDate(),
                        // SAGE Status
                        'PROPERTY_1081': item.sage_status,
                        // SAGE Batch Id
                        'PROPERTY_1170': item.sage_batch_id,
                        // Sage Bank Transfer
                        'PROPERTY_1258': item.sage_bank_transfer,
                        // Payment Reference Id
                        'PROPERTY_1184': item.payment_reference_id,
                        // Payment Date
                        'PROPERTY_1185': item.sage_payment_date,
                        // Bank Transfer Id
                        'PROPERTY_1195': item.bitrix_bank_transfer_id,
                        // Status
                        'PROPERTY_932': item.status_id,
                        // CREATED_BY
                        'CREATED_BY': item.created_by,
                        //  Modified By
                        'MODIFIED_BY': this.page_data.user.bitrix_user_id,
                    }
                });

                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if(response){
                    this.successToast('Purchase Invoice payment schedule date updated successfully')
                    item.week_date = DateTime.fromFormat(newWeek.start_date, "dd MMM yyyy").toISODate();
                }
            } catch (error) {
                this.errorToast('Error updating Purchase Invoice payment schedule date')
            }
        },
        openModal(type, obj){
            this.selected_obj = obj;
            this.modal_type = type
            if(type === 'release_fund'){
                this.is_cash_request_release_fund_form_modal = true
            }
            if(type === 'update'){
                this.is_cash_request_update = true
            }
        },
        closeModal(){
            this.getData();
            this.is_cash_request_release_fund_form_modal = false;
            this.is_cash_request_update = false;
            this.selected_obj = null
            this.removeModalBackdrop();
        },
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch =
                    (item.name && item.name.toLowerCase().includes(this.filters.search.toLowerCase())) ||
                    (item.id && item.id.includes(this.filters.search)) ||
                    (item.detail_text && item.detail_text.toLowerCase().includes(this.filters.search)) ||
                    (item.cash_amount && item.cash_amount.includes(this.filters.search)) ||
                    (item.project_name && item.project_name.toLowerCase().includes(this.filters.search.toLowerCase()));

                // Filter by type
                const matchesType = this.filters.request_type ? (this.filters.request_type === 'budget_only' ? item.is_budget_only === '1937' : item.request_type === this.filters.request_type) : true;

                // Filter by currency
                const matchesCurrency = this.filters.currency ? item.currency === this.filters.currency : true;

                // Filter by Awaiting for Exchange Rate
                const matchesAwaitingForExchangeRate = this.filters.awaiting_for_exchange_rate === 'only' ? item.awaiting_for_exchange_rate_id === '2268' : true;

                // Return true only if all filters match
                return matchesSearch && matchesType && matchesCurrency && matchesAwaitingForExchangeRate;
            });
        },
        // Dynamically filter and group data by week
        weekHeaders() {
            const today = DateTime.now().plus({ weeks: this.week_off_set * 5 });;
            const currentWeekStart = today.startOf("week");

            // Generate 5 week headers based on the current offset
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
                    // week.data = filteredGroupedData[week.week_number];

                    // Sort data by due_date (purchase_invoice) or payment_date (cash_request)
                    week.data = filteredGroupedData[week.week_number].sort((a, b) => {
                        const dateA = a.request_type === "purchase_invoice"
                            ? (a.due_date ? DateTime.fromISO(a.due_date) : null)
                            : (a.payment_date ? DateTime.fromISO(a.payment_date) : null);

                        const dateB = b.request_type === "purchase_invoice"
                            ? (b.due_date ? DateTime.fromISO(b.due_date) : null)
                            : (b.payment_date ? DateTime.fromISO(b.payment_date) : null);

                        if (!dateA) return 1; // Move null/undefined dates to the end
                        if (!dateB) return -1;
                        return dateA - dateB; // Ascending order
                    });
                }
            });

            return weeks;
        },
    },
    created() {
        this.sharedState.bitrix_user_id = this.page_data.user.bitrix_user_id;
        this.sharedState.bitrix_webhook_token = this.page_data.user.bitrix_webhook_token;
    },
    mounted() {
        this.getData();
    }
}
</script>

<style scoped>
/* Column Styles */
.expense-planner-columns > div {
    /* background: linear-gradient(135deg, #fffafa, #b2b6bf); /* Subtle gradient background */
    background: #fff;
    border: 1px solid #e5e5e5; /* Light gray border */
    border-radius: 0; /* Rounded corners */
    padding: 20px 0 0;
    transition: all 0.3s ease; /* Smooth hover effect */
}

.expense-planner-columns > div:hover {
    background: #FFF;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12); /* Soft shadow */
    border-color: #000;
}

/* Card Styles */
.card {
    padding: 16px 16px;
    margin: 0 16px 4px;
    border-radius: 0;
    transition: all 0.2s ease-in-out;
    box-shadow: none;
    border:none;
    border-left: 2px solid transparent;
}

/*
.card:hover {
    box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.12);  Stronger shadow on hover
}
*/
/* Cash Request Card */
.card.cash-request {
    background-color: #f7f7f7; /* White background for cash requests */
    border-left: 3px solid #cbcfda;
}
.card.cash-request:hover {
    border-left: 3px solid #777;
}

/* Budget Only Card */
.card.budget-only {
    background-color: rgb(255, 246, 227); /* White background for cash requests */
    border-left: 3px solid rgb(255, 217, 151); /* Subtle border */
}
.card.budget-only:hover {
    border-left: 3px solid rgb(231, 187, 109);
}

/* Purchase Invoice Card */
.card.purchase-invoice {
    background-color: rgb(232, 247, 255); /* Light blue background */
    border-left: 3px solid rgb(196, 229, 255); /* Matching border */
}
.card.purchase-invoice:hover {
    border-left: 3px solid rgb(148, 199, 239);
}

/* Card Content */
.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #2d2d2d; /* Dark text */
    margin-bottom: 8px;
}

.card-link {
    color: #1e88e5; /* Modern blue for links */
    text-decoration: underline;
    font-weight: 500;
}

.card-text {
    font-size: 14px;
    color: #555; /* Subtle gray for detail text */
}

.badge {
    display: inline-block;
    padding: 1px 7px;
    font-size: 11px;
    font-weight: 500;
    border-radius: 40px;
    margin-top: 8px;
    letter-spacing: -0.5px;
}

.badge-danger {
    background-color: #ef4444;
    color: #FFF;
}
.badge-warning{
    background-color: rgb(250 204 21 / 0.5);
    color: black;
}
.badge-info{
    background-color: rgb(193, 238, 255);
    color: black;
}

/* Ensure borders are not hidden */
.expense-planner-columns > div:hover {
    outline: 1px solid transparent; /* Prevent border from collapsing */
    z-index: 1; /* Bring the column above others on hover */
}
/* Hover Areas */
.hover-area {
    position: fixed;
    top: 0;
    bottom: 0;
    width: 14px;
    z-index: 1000;
    background-color: transparent; /* Default transparent */
    transition: background-color 0.3s ease; /* Smooth transition for hover effect */
}

.hover-area-left {
    left: 0;
}

.hover-area-right {
    right:-2px;
}

/* Change background color on hover */


/* Navigation Buttons */
.prev-week-btn, .next-week-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0); /* Subtle opacity */
    width: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0);
    cursor: pointer;
    transition: opacity 0.3s, background-color 0.3s, box-shadow 0.3s;
}

/* Buttons fully visible on hover */
.group:hover .prev-week-btn,
.group:hover .next-week-btn {
    opacity: 1;
}

/* Button hover effect */
.prev-week-btn:hover, .next-week-btn:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}
</style>
