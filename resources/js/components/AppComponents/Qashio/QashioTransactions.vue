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
                            @update:model-value="getData(false)"
                            :disabled="loading"
                        />
                    </div>
                    <!-- Period Select -->
                    <div class="flex flex-col w-36">
                        <select class="select select-input" v-model="selected_period" @change="getData(false)" :disabled="loading">
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
                    <!-- Clearing Status -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="w-48 select select-sm select-input"
                            v-model="filters.clearing_status"
                        >
                            <option value="" selected>Filter by Clearing Status</option>
                            <option value="pending">Pending</option>
                            <option value="cleared">Cleared</option>
                            <option value="reversed">Reversed</option>
                            <option value="updated">Updated</option>
                            <option value="null">Null</option>
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
            </div>
            <!-- table -->
            <div class="relative flex-grow h-full overflow-auto border shadow-md reports-table-container border-brand">
                <table class="table w-full text-xs align-middle table-fixed c-table table-border" :class="filteredData.length === 0 ? 'h-full' : ''">
                    <thead>
                    <tr class="font-medium text-center bg-black text-neutral-900">
                        <th class="sticky top-0 w-10">#</th>
                        <th class="sticky top-0 w-[60px]">Id</th>
                        <th class="sticky top-0 w-[80px]" data-tooltip="#Transaction_Amount">
                            Amount <i class="ki-outline ki-information-2"></i>
                            <div class="transition-opacity duration-300 tooltip" id="Transaction_Amount">Transaction Amount</div>
                        </th>
                        <th class="sticky top-0 w-[100px]">Payment Date</th>
                        <th class="sticky top-0 w-[80px]">Status</th>
                        <th class="sticky top-0 w-[120px]">Memo</th>
                        <th class="sticky top-0 w-[120px]">Merchant / Supplier</th>
                        <th class="sticky top-0 w-[130px]">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="h-full text-xs tracking-tight text-center">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-neutral-800">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link !text-black hover:!text-brand-active" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/105/element/0/' + obj.bitrix_cash_request_id  + '/?list_section_id='">{{obj.bitrix_cash_request_id }}</a></td>
                            <td class="text-right">{{ formatAmount(obj.transactionAmount) }} <strong class="font-bold text-black">{{ obj.transactionCurrency }}</strong></td>
                            <td>{{ formatDateTime24HoursISO(obj.transactionTime)  }}</td>
                            <td>
                                <div class="capitalize" :class="obj.clearingStatus === 'cleared' ? 'badge badge-success' : obj.clearingStatus === 'pending' ? 'badge badge-warning' : ''">
                                    {{ obj.clearingStatus  }}
                                </div>
                            </td>
                            <td>{{ obj.memo  }}</td>
                            <td>{{ obj.merchantName  }}</td>
                            <td class="text-center p-1.5">
                                <button
                                    v-if="(!obj.bitrix_cash_request_id && obj.transactionCategory === 'purchase') && (obj.clearingStatus === 'pending' || obj.clearingStatus === 'cleared')"
                                    data-modal-toggle="#create_cash_request_form_modal"
                                    class="block w-full mb-1 secondary-btn"
                                    @click="openModal(obj)"
                                >
                                    Create Request
                                </button>
                                <button
                                    v-if="(obj.bitrix_cash_request_id && obj.transactionCategory === 'purchase') && (obj.clearingStatus === 'cleared' || obj.clearingStatus === 'reversed' || obj.clearingStatus === 'updated')"
                                    @click="saveCashRequest('create', obj)"
                                    class="block w-full mb-1 secondary-btn"
                                >
                                    Update Request
                                </button>
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
    <create-cash-request-form-modal
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
                clearing_status: "",
                search: "",
            },
            is_create_cash_request_form_modal: false,
            selected_obj: null,
        }
    },
    methods: {
        async getData(isSync = false) {
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

                let fetchedData = response.data.data;
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
        closeModal(isForm = false){
            this.is_view_bank_transfer_details_modal = false;
            this.is_create_bank_transfer_form_modal = false;
            this.selected_obj = null
            this.removeModalBackdrop();
            if (isForm){
                this.getPageData()
            }
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
                    item.string_id, item.parentId, item.rrn, item.qashioId,
                ].some(field => field?.toLowerCase().includes(searchTerm));

                // Filter by Clearing Status
                const matchesStatus = this.filters.clearing_status === 'null' ? item.clearingStatus === null : this.filters.clearing_status ? item.clearingStatus === this.filters.clearing_status : true;
                // Filter by selected Category ID
                const matchesCategory = this.filters.category_id ? item.bitrix_qashio_credit_card_category_id === this.filters.category_id : true;
                // Filter by selected Sage Company ID
                const matchesSageCompany = this.filters.sage_company_id ? item.bitrix_qashio_credit_card_sage_company_id === this.filters.sage_company_id : true;

                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesCategory && matchesSageCompany;
            });
        },
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
