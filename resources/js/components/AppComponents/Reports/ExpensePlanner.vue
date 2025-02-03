<template>
    <div class="container-fluid relative">
        <!-- Left Hover Area -->
        <div class="hover-area hover-area-left group">
            <button
                class="prev-week-btn opacity-0 group-hover:opacity-100"
                @click="navigateWeeks(-1)"
            >
                <i class="ki-filled ki-to-left"></i>
            </button>
        </div>
        <!-- Right Hover Area -->
        <div class="hover-area hover-area-right group">
            <button
                class="next-week-btn opacity-0 group-hover:opacity-100"
                @click="navigateWeeks(1)"
            >
                <i class="ki-filled ki-to-right"></i>
            </button>
        </div>

        <div class="grid gap-5 lg:gap-7.5 pt-6">
            <!-- filters -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm min-w-[12rem] max-w-full text-black bg-inherit" v-model="filters.category_id" @change="getData">
                        <option value="" selected>Filter by Category</option>
                        <option v-for="(obj, index) in page_data.categories" :key="index" :value="obj.category_id">
                            {{ obj.bitrix_category_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[25rem] max-w-full text-black bg-inherit" v-model="filters.sage_company_code" @change="getData">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="(obj, index) in page_data.sage_companies" :key="index" :value="obj.sage_company_code">
                            {{ obj.bitrix_sage_company_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[10rem] max-w-full text-black bg-inherit" v-model="filters.currency">
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
                    <select class="select select-sm min-w-[10rem] max-w-full text-black bg-inherit" v-model="filters.request_type">
                        <option value="" selected>Filter by Type</option>
                        <option value="cash_request">Cash Request</option>
                        <option value="purchase_invoice">Purchase Invoice</option>
                        <option value="budget_only">Budget Only</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[16rem] max-w-full text-black bg-inherit" v-model="filters.awaiting_for_exchange_rate">
                        <option value="" selected>Filter by Awaiting for Exchange Rate</option>
                        <option value="include">Include</option>
                        <option value="only">Only</option>
                    </select>
                </div>
                <div class="flex">
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3"></i>
                        <input class="input input-sm ps-8 text-black bg-inherit min-w-[25rem]" placeholder="Search" type="text" v-model="filters.search">
                    </div>
                </div>
            </div>
            <!-- content area -->
            <div class="relative flex-grow overflow-auto">
                <!-- Loading Indicator -->
                <div v-if="loading" class="data-loading absolute inset-0 bg-gray-300 bg-opacity-100 flex items-center justify-center z-50 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
                <!-- week columns -->
                <div class="flex gap-4 text-sm expense-planner-columns">
                    <!-- Each column represents a week -->
                    <div v-for="(week, index) in weekHeaders" :key="index" class="flex-1 flex flex-col bg-gray-200 p-4 rounded text-center text-sm overflow-auto">
                        <!-- Week Header -->
                        <div class="text-black font-black mb-2">Week {{ week.week_number }}</div>
                        <div class="text-black">({{ week.start_date }} to {{ week.end_date }})</div>

                        <!-- Total Amount -->
                        <div v-if="week.data.length" class="mt-4 mb-4">
                            <strong class="text-xl text-danger">{{ getWeeklyTotalWithCurrency(week.data) }}</strong>
                            <div class="mt-1">{{ getWeeklyTotalWithCurrencyConversion(week.data) }}</div>
                        </div>

                        <!-- Week Data -->
                        <div v-if="week.data.length" class="flex flex-col gap-2 overflow-y-auto h-full">
                            <div v-for="(item, itemIndex) in week.data" :key="itemIndex">
                                <!-- cash requests -->
                                <div :class="['card', (item.is_budget_only === '1937' ? 'budget-only' : 'cash-request')]" v-if="item.request_type === 'cash_request'">
                                    <div class="card-title text-left">
                                        <a class="btn btn-link" target="_blank" :href="getBitrixUrlByBlockIdAndId('105', item.id)">
                                            <div class="text-black text-lg">
                                                {{ formatAmount(item.amount) }} {{ item.currency }}
                                                <sub class="text-xs text-gray-600" v-if="item.currency !== 'USD'">({{ formatAmount(item.exchange_amount) }} USD)</sub>
                                            </div>
                                        </a>
                                    </div>
                                    <a class="btn btn-link text-left" target="_blank" :href="getBitrixProjectLink(item)">{{ item.project_name }}</a>
                                    <div class="text-black text-left mt-2">{{ item.detail_text }}</div>
                                    <div class="text-left text-xs mt-2">
                                        <span>Requested By: </span>
                                        <span class="text-black">{{ item.requested_by_name }}</span>
                                    </div>
                                    <div class="text-left text-xs">
                                        <span>Pay By: </span>
                                        <span class="text-black">{{ getPaymentMode(item.payment_mode_id) }}</span>
                                    </div>
                                    <div class="text-left mt-2">
                                        <small :class="['badge text-xs', isOverdue(item.payment_date) ? 'badge-danger' : 'badge-success']">Due: {{ formatDate(item.payment_date) }}</small>
                                        <small class="badge badge-warning text-xs ml-1" v-if="item.is_budget_only === '1937'">Budget Only</small>
                                    </div>
                                </div>
                                <!-- purchase invoices -->
                                <div class="card purchase-invoice" v-if="item.request_type === 'purchase_invoice'">
                                    <div class="card-title text-left">
                                        <a class="btn btn-link" target="_blank" :href="getBitrixUrlByBlockIdAndId(item.request_type === 'cash_request' ? '104' : '104', item.id)">
                                            <div class="text-black text-lg">
                                                <span v-if="item.remaining_balance">{{ formatAmount(item.remaining_balance) }}</span>
                                                <span v-else>{{ formatAmount(item.amount) }}</span>
                                                {{ item.currency }}
                                            </div>
                                        </a>
                                    </div>
                                    <a class="btn btn-link text-left" target="_blank" :href="getBitrixProjectLink(item)">{{ item.project_name }}</a>
                                    <div class="text-black text-left mt-2">{{ item.detail_text }}</div>
                                    <div class="text-left text-xs mt-2">
                                        <span>Requested By: </span>
                                        <span class="text-black">{{ item.requested_by_name }}</span>
                                    </div>
                                    <div class="text-left mt-2">
                                        <small class="badge badge-danger text-xs font-bold">Due: {{ formatDate(item.due_date) }}</small>
                                        <small v-if="item.sage_status && item.sage_status === '1863'" class="badge badge-success text-xs font-bold ml-1">Booked In Sage</small>
                                        <small v-else class="badge badge-warning text-xs font-bold ml-1">NOT Booked In Sage</small>
                                        <small v-if="item.status_id === '1864'" class="badge badge-warning text-xs font-bold ml-1 ">Partially Paid</small>
                                    </div>
                                </div>
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
export default {
    name: "expense-planner",
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
                currency: "",
                awaiting_for_exchange_rate: "",
                search: "",
            },
            payment_modes: [
                {
                    id: 1867,
                    name: 'Cash'
                },
                {
                    id: 1868,
                    name: 'Card'
                },
                {
                    id: 1869,
                    name: 'Bank Transfer'
                },
                {
                    id: 1870,
                    name: 'Cheque'
                }
            ],
            week_off_set: 0,
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
/* Column Styles */
.expense-planner-columns > div {
    background: linear-gradient(135deg, #fffafa, #b2b6bf); /* Subtle gradient background */
    border: 1px solid #d6d6d6; /* Light gray border */
    border-radius: 8px; /* Rounded corners */
    padding: 16px;
    transition: box-shadow 0.3s ease, background-color 0.3s ease; /* Smooth hover effect */
}

.expense-planner-columns > div:hover {
    background: linear-gradient(135deg, #ffffff, #f5f5f5); /* Slightly brighter background on hover */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Soft shadow */
}

/* Card Styles */
.card {
    padding: 16px;
    border-radius: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle card shadow */
    margin-bottom: 12px; /* Space between cards */
}

.card:hover {
    transform: translateY(-5px); /* Lift the card slightly on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Stronger shadow on hover */
}

/* Cash Request Card */
.card.cash-request {
    background-color: #ffffff; /* White background for cash requests */
    border: 2px solid #e5e5e5; /* Subtle border */
}
.card.budget-only {
    background-color: #ebca92; /* White background for cash requests */
    border: 2px solid #e5e5e5; /* Subtle border */
}

/* Purchase Invoice Card */
.card.purchase-invoice {
    background-color: #bae9f8; /* Light blue background */
    border: 2px solid #cde4f6; /* Matching border */
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
    padding: 0px 4px;
    font-size: 8px;
    font-weight: 500;
    border-radius: 4px;
    margin-top: 8px;
}

.badge.due {
    background-color: #ffebea; /* Light red background */
    color: #941301; /* Red text */
}
.badge-warning{
    background-color: yellow;
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
    width: 40px;
    z-index: 1000;
    background-color: transparent; /* Default transparent */
    transition: background-color 0.3s ease; /* Smooth transition for hover effect */
}

.hover-area-left {
    left: 45px;
}

.hover-area-right {
    right: 0;
}

/* Change background color on hover */


/* Navigation Buttons */
.prev-week-btn, .next-week-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(255, 255, 255, 0.8); /* Subtle opacity */
    border: 1px solid #d6d6d6;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: opacity 0.3s, background-color 0.3s, box-shadow 0.3s;
    opacity: 0.5; /* Subtle visibility */
}

/* Buttons fully visible on hover */
.group:hover .prev-week-btn,
.group:hover .next-week-btn {
    opacity: 1;
}

/* Button hover effect */
.prev-week-btn:hover, .next-week-btn:hover {
    background-color: rgba(255, 255, 255, 1); /* Fully opaque on hover */
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    opacity: 1; /* Full visibility on hover */
}
</style>




