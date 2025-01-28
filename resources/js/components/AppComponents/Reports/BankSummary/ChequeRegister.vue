<template>
    <div class="container-fluid">
        <reports-filters-component
            @get-data="getData"
        />
        <div class="grid gap-5 lg:gap-7.5 cheque-register">
            <!-- Filters Section -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex flex-grow gap-2">
                    <!-- Category Filter -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="select select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                            v-model="filters.category_id"
                            @change="getData"
                        >
                            <option value="" selected>Filter by Category</option>
                            <option v-for="obj in page_data.bitrix_list_categories" :key="obj.id" :value="obj.bitrix_category_id">
                                {{ obj.bitrix_category_name }}
                            </option>
                        </select>
                    </div>
                    <!-- Dynamic Filters -->
                    <div v-for="filter in page_filters" :key="filter.key" class="flex flex-shrink-0">
                        <select
                            class="select select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                            v-model="filters[filter.key]"
                        >
                            <option value="" selected>Filter by {{ filter.name }}</option>
                            <option v-for="(value, key) in filter.values" :value="key" :key="key">{{ value }}</option>
                        </select>
                    </div>
                </div>
                <!-- Search Input -->
                <div class="flex flex-grow">
                    <div class="relative w-full">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 left-3 transform -translate-y-1/2"></i>
                        <input
                            class="input input-sm ps-8 w-full text-black bg-inherit"
                            placeholder="Search"
                            type="text"
                            v-model="filters.search"
                        />
                    </div>
                </div>
                <!-- Warning Filter -->
                <div class="flex flex-shrink-0">
                    <button
                        :class="['btn btn-icon btn-sm relative px-3', filters.is_warning ? 'btn-warning text-white' : 'btn-light']"
                        @click="filters.is_warning = !filters.is_warning"
                    >
                        <i class="ki-filled ki-information-1"></i>
                        <span class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ warningCount }}</span>
                    </button>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow overflow-auto reports-table-container">
                <table class="w-full table table-border align-middle text-xs">
                    <thead>
                        <tr class="bg-black text-gray-900 font-medium text-center">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[50px]">Id</th>
                            <th class="sticky top-0 w-[100px]">Category</th>
                            <th class="sticky top-0 w-[100px]">Status</th>
                            <th class="sticky top-0 w-[100px]">Cheque Number</th>
                            <th class="sticky top-0 w-[100px]">Cheque Date</th>
                            <th class="sticky top-0 w-[150px]">Issued To</th>
                            <th class="sticky top-0 w-[100px] text-right">Amount</th>
                            <th class="sticky top-0 w-[130px]">Bank</th>
                            <th class="sticky top-0 w-[150px] text-left">Description</th>
                            <th class="sticky top-0 w-[50px]">Files</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs text-gray-700 leading-custom-normal">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="odd:bg-white even:bg-slate-100">
                            <td>{{ index + 1 }}</td>
                            <td><a target="_blank" class="btn btn-link" :href="'https://crm.cresco.ae/services/lists/106/element/0/' + obj.id  + '/?list_section_id='">{{ obj.id }}</a></td>
                            <td>{{ obj.category }}</td>
                            <td>{{ obj.status }}</td>
                            <td>{{ obj.cheque_number }}</td>
                            <td>
                                <div :class="calculateChequeDateCondition(obj.cheque_date)">{{ formatDate(obj.cheque_date) }}</div>
                            </td>
                            <td>{{ obj.issue_to }}</td>
                            <td class="text-right">
                                <span>{{ formatAmount(obj.amount) }}</span>&nbsp;
                                <span class="font-bold text-black">{{ obj.currency }}</span>
                            </td>
                            <td><a target="_blank" class="btn btn-link" :href="'https://crm.cresco.ae/services/lists/39/element/0/' + obj.bank_id  + '/?list_section_id='">{{ obj.bank_name }}</a> </td>
                            <td class="text-left">{{ obj.description }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline btn-primary" :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${obj.cheque_upload}&action=download&ncc=1`">Download</a>
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="7" class="text-black font-bold">Totals per currency</td>
                            <td colspan="1" class="text-right">
                                <div v-for="(amount, currency) in groupedByCurrency">{{ formatAmount(amount) }} <span class="font-bold text-black">{{ currency }} </span></div>
                            </td>
                        </tr>
                        <tr class="table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-red-400">No data available</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="loading" class="data-loading absolute inset-0 bg-gray-300 bg-opacity-100 flex items-center justify-center z-50 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                    <span class="mr-2">Total as per reporting currency ({{ currency }}):</span>
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
    name: "cheque-register",
    props: ['page_data'],
    data(){
        return {
            data: [],
            loading: false,
            filters:{
                date: null,
                category_id: "",
                status: "",
                search: "",
                is_warning: false,
            },
            page_filters: [
                {
                    key: "status",
                    name: "Status",
                    field_id: "PROPERTY_970",
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
            const endpoint = 'lists.field.get';
            for (const filter of this.page_filters) {
                try {
                    const requestData = {
                        IBLOCK_TYPE_ID: this.page_data.bitrix_list.bitrix_iblock_type,
                        IBLOCK_ID: this.page_data.bitrix_list.bitrix_iblock_id,
                        FIELD_ID: filter.field_id
                    };
                    const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                    filter.values = response.result.L.DISPLAY_VALUES_FORM;

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
                action: "getChequeRegister",
                categories: JSON.stringify(this.filters.category_id === "" ? this.page_data.bitrix_list_categories.map((obj) => obj.bitrix_category_id) : [this.filters.category_id]),
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
        isWarning(item) {
            if(item){
                return this.calculateChequeDateCondition(item.cheque_date);
            }
        },
        calculateChequeDateCondition(chequeDate) {
            const now = DateTime.now();
            const chequeDateTime = DateTime.fromSQL(chequeDate);

            const workingDaysDifference = this.calculateWorkingDays(now, chequeDateTime);
            console.log(workingDaysDifference, chequeDate)

            if (workingDaysDifference === 2) {
                return 'badge badge-warning';
            } else if (workingDaysDifference > 2 && workingDaysDifference <= 5) {
                return 'badge badge-dark';
            } else if (workingDaysDifference < 2) {
                return 'badge badge-danger';
            }
            return null;
        },
        calculateWorkingDays(startDate, endDate) {
            let count = 0;
            let currentDate = startDate;

            // Loop over the dates and count only Monday to Friday (working days)
            while (currentDate <= endDate) {
                const dayOfWeek = currentDate.weekday;
                // Only count weekdays (Monday: 1, Friday: 5)
                if (dayOfWeek >= 1 && dayOfWeek <= 5) {
                    count++;
                }
                // Move to the next day
                currentDate = currentDate.plus({ days: 1 });
            }
            return count;
        },
    },
    computed:{
        filteredData() {
            const urlParams = Object.fromEntries(new URLSearchParams(window.location.search).entries());
            const section = urlParams.section || 'cheque-register-outgoing';
            const searchTerm = this.filters.search?.toLowerCase() || '';

            return this.data.filter(item => {
                // Exclude items where the status is 'cancelled' or 'completed'
                if (['cancelled', 'completed'].includes(item.status?.toLowerCase())) {
                    return false;
                }
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.id, item.account_name, item.account_number, item.amount,
                    item.bank_name, item.category, item.cheque_number,
                    item.description, item.issue_to, item.status, item.type
                ].some(field => field?.toLowerCase().includes(searchTerm));
                // Filter by type
                const matchesType = section === 'cheque-register-outgoing' ? item.type === 'Outgoing' : item.type === 'Incoming'
                // Filter by status
                const matchesStatus = this.filters.status ? item.status_id === this.filters.status : true;
                // Filter by warning
                const matchesWarning = this.filters.is_warning ? this.isWarning(item) : true;
                // Return true only if all filters match
                return matchesSearch && matchesType && matchesStatus && matchesWarning;
            });
        },
        groupedByCurrency() {
            const groupedByCurrency = _.groupBy(this.filteredData, 'currency');
            const summedByCurrency = _.mapValues(groupedByCurrency, (group) =>
                _.reduce(group, (sum, transaction) => sum + parseFloat(transaction.amount), 0)
            );

            return summedByCurrency;
        },
        warningCount() {
            return this.filteredData.filter(item => this.isWarning(item)).length;
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
}
</script>
<style scoped>
</style>
