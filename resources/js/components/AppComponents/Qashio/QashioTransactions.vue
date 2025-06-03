<template>
    <div class="px-3 container-fluid">
        <div class="pt-4 pb-4 reports-header-filters">
            <div class="flex items-center justify-between">
                <!-- Date Range Picker Text on the Left -->
                <div class="flex flex-col justify-start w-1/2 text-2xl font-bold tracking-tight text-black">
                    {{ dateRangePickerText }}
                </div>
                <!-- Right Side Controls -->
                <div class="flex flex-wrap items-center justify-end w-1/2 gap-2">
                    <!-- Date Picker -->
                    <div class="flex flex-col w-64">
                        <VueDatePicker
                            v-model="selected_date_range"
                            model-type="yyyy-MM-dd"
                            auto-apply
                            range
                            :multi-calendars="{ solo: true }"
                            placeholder="Select Date"
                            :enable-time-picker="false"
                            format="dd MMM yyyy"
                            :clearable="false"
                            class="rounded-none"
                            @update:model-value="getPageData(false)"
                            :disabled="loading"
                        />
                    </div>
                    <!-- Period Select -->
                    <div class="flex flex-col w-36">
                        <select class="select select-input" v-model="selected_period" @change="getPageData(false)" :disabled="loading">
                            <option v-for="(period, index) in periods" :key="index" :value="period.key">
                                {{ period.value }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid gap-2">
            <!-- Filters Section -->
            <div class="flex flex-wrap items-center gap-2 reports-only-filters">
                <div class="flex gap-2">
                    <!-- Category Filter -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="w-48 select select-sm select-input"
                            v-model="filters.category_id"
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
                        >
                            <option value="" selected>Filter by Sage Company</option>
                            <option v-for="obj in page_data.bitrix_list_sage_companies" :key="obj.id" :value="obj.bitrix_sage_company_id">
                                {{ obj.bitrix_sage_company_name }}
                            </option>
                        </select>
                    </div>
                    <!-- Qashio Credit Cards -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="select select-sm select-input w-96"
                            v-model="filters.qashio_credit_card"
                        >
                            <option value="" selected>Filter by Qashio Credit Card</option>
                            <option v-for="obj in qashio_credit_cards" :key="obj.id" :value="obj.last_four_digits">
                                {{ obj.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <!-- Search Input -->
                <div class="flex grow">
                    <div class="relative w-full">
                        <i class="absolute leading-none text-black transform -translate-y-1/2 ki-outline ki-magnifier text-md top-1/2 left-3"></i>
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
                        :class="['btn btn-icon btn-sm relative px-3 h-10 !w-10 !rounded-none transition-all duration-300 hover:border-black', filters.is_warning ? 'bg-yellow-100 text-black border-black' : 'btn-light text-black']"
                        @click="filters.is_warning = !filters.is_warning"
                    >
                        <i class="ki-outline ki-information-1"></i>
                        <span class="absolute flex items-center justify-center text-[9px] font-bold text-white translate-x-1/2 -translate-y-1/2 bg-yellow-500 rounded-full shadow-md top-1 right-1 shadow-yellow-300 min-h-5 min-w-5">{{ warningCount }}</span>
                    </button>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow h-full overflow-auto border shadow-md reports-table-container border-brand">
                <table class="table w-full text-xs align-middle table-fixed c-table table-border" :class="filteredData.length === 0 ? 'h-full' : ''">
                    <thead>
                        <tr class="font-medium text-center bg-black text-neutral-900">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[80px]">Cash Request Id</th>
                            <th class="sticky top-0 w-[80px]" data-tooltip="#Transaction_Amount">
                                Amount <i class="ki-outline ki-information-2"></i>
                                <div class="transition-opacity duration-300 tooltip" id="Transaction_Amount">Transaction Amount</div>
                            </th>
                            <th class="sticky top-0 w-[80px]">Payment Date</th>
                            <th class="sticky top-0 w-[120px]">Project</th>
                            <th class="sticky top-0 w-[120px]">Merchant / Supplier</th>
                            <th class="sticky top-0 w-[80px]">Receipts</th>
                            <th class="sticky top-0 w-[180px]">Card</th>
                            <th class="sticky top-0 w-[150px]">Clearing Detail</th>
                            <th class="sticky top-0 w-[80px]" v-if="page_data.permission === 'full_access'">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="h-full text-xs tracking-tight text-center">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-neutral-800">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link !text-black hover:!text-brand-active" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/105/element/0/' + obj.bitrix_cash_request_id  + '/?list_section_id='">{{obj.bitrix_cash_request_id }}</a></td>
                            <td class="text-right">{{ formatAmount(obj.transactionAmount) }} <strong class="font-bold text-black">{{ obj.transactionCurrency }}</strong></td>
                            <td>{{ formatDateTime24HoursISO(obj.transactionTime)  }}</td>
                            <td>{{ obj.memo  }}</td>
                            <td>{{ obj.merchantName  }}</td>
                            <td>
                                <a
                                    v-if="obj.receipts && obj.receipts.length > 0"
                                    v-for="(receipt, index) in obj.receipts"
                                    class="block btn-xs secondary-btn other-doc-btn"
                                    :class="{'mb-2': obj.receipts.length > 1}"
                                    target="_blank"
                                    :href="receipt"
                                >
                                    Receipt {{ index + 1 }}
                                </a>
                            </td>
                            <td>
                                <div class="flex justify-between py-0.5">
                                    <span>Name:</span>
                                    <strong class="text-wrap">{{ obj.cardName }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Last Four:</span>
                                    <span>{{ obj.cardLastFour }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Holder:</span>
                                    <span>{{ obj.cardHolderName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Qashio Id:</span>
                                    <span>{{ obj.qashioId }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Tax Rate:</span>
                                    <span>{{ obj.erpTaxRateName }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-between py-0.5">
                                    <span>Amount:</span>
                                    <span>{{ formatAmount(obj.clearingAmount) }} <strong class="font-bold text-black">{{ obj.billingCurrency }}</strong></span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Fee:</span>
                                    <span>{{ formatAmount(obj.clearingFee) }} <strong class="font-bold text-black">{{ obj.billingCurrency }}</strong></span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Total:</span>
                                    <span>{{ formatAmount(parseFloat(obj.clearingAmount) + parseFloat(obj.clearingFee)) }} <strong class="font-bold text-black">{{ obj.billingCurrency }}</strong></span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Cleared At:</span>
                                    <span>{{ formatDateTime24HoursISO(obj.clearedAt) }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span>Status:</span>
                                    <span class="capitalize" :class="obj.clearingStatus === 'cleared' || obj.clearingStatus === 'updated'? 'badge badge-success' : (obj.clearingStatus === 'pending' ? 'badge badge-warning' : (obj.clearingStatus === 'reversed' ? 'badge badge-danger' : ''))">{{ obj.clearingStatus  }}</span>
                                </div>
                            </td>
                            <td class="text-center p-1.5" v-if="page_data.permission === 'full_access'">
                                <button
                                    v-if="(!obj.bitrix_cash_request_id && obj.transactionCategory === 'purchase') && (obj.clearingStatus === 'pending' || obj.clearingStatus === 'cleared')"
                                    data-modal-toggle="#create_cash_request_form_modal"
                                    class="block w-full mb-1 secondary-btn"
                                    @click="openModal(obj)"
                                >
                                    Create Request
                                </button>
<!--                                <button-->
<!--                                    v-if="(obj.bitrix_cash_request_id && obj.transactionCategory === 'purchase') && (obj.clearingStatus === 'cleared' || obj.clearingStatus === 'reversed' || obj.clearingStatus === 'updated')"-->
<!--                                    @click="saveCashRequest('create', obj)"-->
<!--                                    class="block w-full mb-1 secondary-btn"-->
<!--                                >-->
<!--                                    Update Request-->
<!--                                </button>-->
                            </td>
                        </tr>
                        <tr class="h-full table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-md text-red-400 !border-none h-full">
                                <div class="flex flex-col items-center justify-center w-full h-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-4 size-10">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    No data available
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center pointer-events-none data-loading bg-neutral-100 z-100">
                    <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                        <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
            </div>
        </div>
    </div>
    <!-- create transfer form modal -->
    <qashio-create-cash-request-form-modal
        :page_data="page_data"
        :obj="selected_obj"
        v-if="is_create_cash_request_form_modal"
        @closeModal="closeModal"
    />
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "qashio-transactions",
    props: ['page_data'],
    data() {
        return {
            periods: [
                {key: "this_week", value: "This week"},
                {key: "last_week", value: "Last week"},
                {key: "this_month", value: "This month"},
                {key: "last_month", value: "Last month"},
                {key: "last_month_plusplus", value: "This month ++"},
                {key: "last_60_days_plusplus", value: "Last 60 days ++"},
                {key: "this_year", value: "This Year"},
                {key: "last_year", value: "Last Year"},
            ],
            selected_period: 'last_60_days_plusplus',
            data: [],
            loading: false,
            filters: {
                from_date: null,
                to_date: null,
                category_id: "",
                sage_company_id: "",
                qashio_credit_card: "",
                search: "",
                is_warning: false,
            },
            qashio_credit_cards: [],
            is_create_cash_request_form_modal: false,
            selected_obj: null,
        }
    },
    methods: {
        async getData() {
            await this.fetchQashioCreditCardsFromBitrix();
            await this.getPageData(false);
        },
        async fetchQashioCreditCardsFromBitrix() {
            this.loading = true;
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                action: "getQashioCreditCards",
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result) {
                    this.loading = false
                    this.qashio_credit_cards = response.result.filter(creditCard => {
                        return this.page_data.bitrix_list_categories.some(category => category.bitrix_category_id === creditCard.category_id)
                    })
                }
            } catch (error) {
                if (error.status === 500) {
                    this.errorToast('Something went wrong! Please refresh the page or contact support if this keeps happening.')
                }
            }
        },
        async getPageData(isSync = false) {
            this.loading = true;
            this.filters.from_date = this.selected_date_range[0];
            this.filters.to_date = this.selected_date_range[1];
            this.data = []
            try {
                const response = await axios({
                    url: `/qashio/get-data`,
                    method: 'POST',
                    data: {
                        filters: this.filters,
                        is_sync: isSync
                    }
                });

                let fetchedData = response.data.transactions;
                // Filter data by user categories and sage companies
                const userCategoryIds = this.page_data.bitrix_list_categories.map(cat => cat.bitrix_category_id);
                const userSageCompanyIds = this.page_data.bitrix_list_sage_companies.map(cat => cat.bitrix_sage_company_id);

                this.data = fetchedData.filter(item =>
                    item.transactionCategory === 'purchase' &&
                    userCategoryIds.includes(item.bitrix_qashio_credit_card_category_id) &&
                    userSageCompanyIds.includes(item.bitrix_qashio_credit_card_sage_company_id)
                );

                this.loading = false

            } catch (error) {
                this.loading = false
                if (this.appEnv === 'local') {
                    this.errorToast(error.response.data.exception);
                } else {
                    this.errorToast(error.response.data.message);
                }
            }
        },
        updateDateRangeForPeriod(period) {
            const now = DateTime.now();
            let newDateRange;

            switch (period) {
                case "this_week":
                    newDateRange = [
                        now.startOf("week").toISODate(),
                        now.endOf("week").toISODate()];
                    break;
                case "last_week":
                    newDateRange = [
                        now.minus({weeks: 1}).startOf("week").toISODate(),
                        now.minus({weeks: 1}).endOf("week").toISODate()
                    ];
                    break;
                case "this_month":
                    newDateRange = [
                        now.startOf("month").toISODate(),
                        now.endOf("month").toISODate()
                    ];
                    break;
                case "last_month":
                    newDateRange = [
                        now.minus({months: 1}).startOf("month").toISODate(),
                        now.minus({months: 1}).endOf("month").toISODate()
                    ];
                    break;
                case "last_month_plusplus":
                    newDateRange = [
                        now.startOf("month").toISODate(),
                        now.plus({years: 3}).endOf("year").toISODate(),
                    ];
                    break;
                case "last_60_days_plusplus":
                    newDateRange = [
                        now.minus({days: 60}).startOf("day").toISODate(),
                        now.plus({years: 3}).endOf("year").toISODate()
                    ];
                    break;
                case "this_year":
                    newDateRange = [
                        now.startOf("year").toISODate(),
                        now.endOf("year").toISODate()
                    ];
                    break;
                case "last_year":
                    newDateRange = [
                        now.minus({years: 1}).startOf("year").toISODate(),
                        now.minus({years: 1}).endOf("year").toISODate()
                    ];
                    break;
                default:
                    newDateRange = [
                        DateTime.now().toISODate(),
                        DateTime.now().toISODate()
                    ];
            }
            this.selected_date_range = newDateRange;
        },
        openModal(obj){
            this.selected_obj = obj;
            this.is_create_cash_request_form_modal = true
        },
        closeModal(){
            this.is_create_cash_request_form_modal = false;
            this.selected_obj = null
            this.removeModalBackdrop();
            this.getPageData(false);
        },
    },
    computed: {
        dateRangePickerText() {
            if (!this.selected_date_range[0] || !this.selected_date_range[1]) {
                return "No date selected";
            }
            const formattedStart = DateTime.fromISO(this.selected_date_range[0]).toFormat("d MMM yyyy");
            const formattedEnd = DateTime.fromISO(this.selected_date_range[1]).toFormat("d MMM yyyy");
            return `${formattedStart} - ${formattedEnd}`;
        },
        filteredData() {
            const searchTerm = this.filters.search?.toLowerCase() || '';
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.qashioId, item.string_id, item.parentId, item.rrn, item.bitrix_cash_request_id, item.transactionAmount, item.clearingAmount,
                    item.bitrix_qashio_credit_card_category_id, item.bitrix_qashio_credit_card_sage_company_id,
                    item.cardHolderName, item.cardLastFour, item.cardName, item.clearingAmount, item.clearingStatus,
                    item.merchantName, item.transactionCurrency, item.billingCurrency
                ].some(field => field?.toLowerCase().includes(searchTerm));

                // Filter by selected Category ID
                const matchesCategory = this.filters.category_id ? item.bitrix_qashio_credit_card_category_id === this.filters.category_id : true;
                // Filter by selected Sage Company ID
                const matchesSageCompany = this.filters.sage_company_id ? item.bitrix_qashio_credit_card_sage_company_id === this.filters.sage_company_id : true;
                // Qashio Credit Card
                const matchesCreditCard = this.filters.qashio_credit_card ? item.cardLastFour === this.filters.qashio_credit_card : true;
                // Filter by warning
                const matchesWarning = this.filters.is_warning ? !item.bitrix_cash_request_id : true;

                // Return true only if all filters match
                return matchesSearch && matchesCategory && matchesSageCompany && matchesCreditCard && matchesWarning;
            });
        },
        warningCount() {
            return this.filteredData.filter(item => {
                return  !item.bitrix_cash_request_id;
            }).length;
        }
    },
    watch: {
        selected_period() {
            this.updateDateRangeForPeriod(this.selected_period);
        },
    },
    created() {
        this.updateDateRangeForPeriod(this.selected_period);
    },
    mounted() {
        this.getData(false);
    }
}
</script>
<style scoped>

</style>
