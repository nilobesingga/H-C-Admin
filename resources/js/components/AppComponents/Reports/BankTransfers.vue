<template>
    <div class="container-fluid px-3">
        <reports-filters-component
            @get-data="getData"
        />
        <div class="grid gap-2">
            <!-- Filters Section -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex flex-grow gap-2">
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
                        :class="['btn btn-icon btn-sm relative px-3 h-10 !w-10 !rounded-none transition-all duration-300 hover:border-black', filters.is_warning ? 'bg-yellow-100 text-black border-black' : 'btn-light text-black']"
                        @click="filters.is_warning = !filters.is_warning"
                    >
                        <i class="ki-outline ki-information-1"></i>
                        <span class="absolute top-1 right-1 translate-x-1/2 -translate-y-1/2 shadow-md shadow-yellow-300 bg-yellow-500 text-white text-xs font-bold rounded-full min-h-5 min-w-5 flex items-center justify-center">{{ warningCount }}</span>
                    </button>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow overflow-auto reports-table-container shadow-md border border-brand">
                <table class="w-full c-table table table-border align-middle text-xs table-fixed">
                    <thead>
                        <tr class="text-center tracking-tight">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[50px]">Id</th>
                            <th class="sticky top-0 w-[100px] text-right">Transfer Amount</th>
                            <th class="sticky top-0 w-[150px] text-left">Transfer From <i class="ki-outline ki-exit-down"></i></th>
                            <th class="sticky top-0 w-[150px] text-left">Transfer To <i class="ki-outline ki-exit-down"></i></th>
                            <th class="sticky top-0 w-[150px] text-left">Purpose of Transfer <i class="ki-outline ki-exit-down"></i></th>
                            <th class="sticky top-0 w-[80px]">Reference No</th>
                            <th class="sticky top-0 w-[70px]">Created Date</th>
                            <th class="sticky top-0 w-[90px]" data-tooltip="#Payment_Invoice_Reference_tooltip">
                                Reference <i class="ki-outline ki-information-2"></i>
                                <div class="tooltip transition-opacity duration-300" id="Payment_Invoice_Reference_tooltip">Purchase Invoice Reference</div>
                            </th>
                            <th class="sticky top-0 w-[80px]">Transfer Status</th>
                            <th class="sticky top-0 w-[70px]">Transfer Date</th>
                            <th class="sticky top-0 w-[120px]">Transfer Documents</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs tracking-tight">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-neutral-800 group">
                            <td>{{ index + 1 }}</td>
                            <td><a target="_blank" class="btn btn-link !text-neutral-800 hover:!text-brand-active" :href="'https://crm.cresco.ae/services/lists/99/element/0/' + obj.id  + '/?list_section_id='">{{ obj.id }}</a></td>
                            <td class="text-right">{{ formatAmount(obj.amount) }} <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td class="text-left whitespace-normal break-words">
                                <div class="relative">
                                    <div class="line-clamp-3 group-hover:line-clamp-none transition-all duration-500">
                                        <div><span>Account Name:</span> <strong class="font-bold text-black">{{ obj.from_account_name }}</strong></div>
                                        <div><span>Account Number: </span><span class="text-black">{{ obj.from_account_number }}</span></div>
                                        <div><span>Bank Name: </span><span class="text-black">{{ obj.from_bank_name }}</span></div>
                                        <div><span>IBAN: </span><span class="text-black">{{ obj.from_iban }}</span></div>
                                        <div v-if="obj.from_company_name">
                                            <br>
                                            <span class="text-black">Company:</span>
                                            <span><a :href="`https://crm.cresco.ae/crm/company/details/${obj.from_company_id}/`" target="_blank" class="btn btn-link !text-neutral-800 hover:!text-brand-active">{{ obj.from_company_name }}</a></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-left whitespace-normal break-words">
                                <div class="relative">
                                    <div class="line-clamp-3 group-hover:line-clamp-none">
                                        <div><span>Account Name:</span> <strong class="font-bold text-black">{{ obj.to_account_name }}</strong></div>
                                        <div><span>Account Number: </span> <span class="text-black">{{ obj.to_account_number }}</span></div>
                                        <div><span>Bank Name:</span> <span class="text-black">{{ obj.to_bank_name }}</span></div>
                                        <div><span>IBAN:</span> <span class="text-black">{{ obj.to_iban }}</span> </div>
                                        <div v-if="obj.to_company_name">
                                            <br>

                                            <span class="text-black">Company:</span>
                                            <span><a :href="`https://crm.cresco.ae/crm/company/details/${obj.to_company_id}/`" target="_blank" class="btn btn-link !text-neutral-800 hover:!text-brand-active">{{ obj.to_company_name }}</a></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-left whitespace-normal break-words">
                                <div class="relative">
                                    <div class="line-clamp-3 group-hover:line-clamp-none">
                                        <div class="text-wrap">{{ obj.detail_text }}</div>
                                        <br>
                                        <div v-if="obj.project_id">
                                            <span>Project:</span>
                                            <span><a :href="getBitrixProjectLink(obj)" target="_blank" class="btn btn-link !text-xs !text-neutral-800 hover:!text-brand-active">{{ obj.project_name }}</a></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ obj.reference_number }}</td>
                            <td>{{ formatDate(obj.date_create) }}</td>
                            <td>
                                <button class="secondary-btn mb-1 block w-full" v-for="pid in obj.purchase_invoice_ids">View Invoice</button>
                            </td>
                            <td>
                                <div :class="isWarning(obj) ? 'badge badge-warning' : ''">{{ obj.status_text }}</div>
                            </td>
                            <td>{{ formatDate(obj.transfer_date) }}</td>
                            <td>
                                <a v-for="(documentId, index) in obj.invoice_docoment_list"
                                   class="secondary-btn mb-1 block w-full" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    Transfer Doc {{ ++index }}
                                </a>
                                <a v-for="(documentId, index) in obj.invoice_supporting_docoment_list"
                                   class="secondary-btn mb-1 block w-full" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    Doc for Bank {{ ++index }}
                                </a>
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="2" class="font-bold text-center text-sm text-black">Total per currency</td>
                            <td class="text-right text-neutral-800">
                                <div v-for="(amount, currency) in groupedByCurrency">{{ formatAmount(amount) }} <span class="font-bold text-black">{{ currency }} </span></div>
                            </td>
                        </tr>
                        <tr class="table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-md text-red-400">No data available</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="loading" class="data-loading absolute inset-0 bg-neutral-100 bg-opacity-50 flex items-center justify-center z-100 pointer-events-none">
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
                <div class="text-xs text-neutral-700">
                    <span>Showing {{ filteredData.length }} records</span>
                </div>

                <!-- Right Section: Total as per Reporting Currency -->
                <div class="flex items-center justify-center text-right text-neutral-800">
                    <span class="mr-2 tracking-tight">Total as per reporting currency ({{ currency }}):</span>
                    <span class="font-bold text-black">
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
    name: "bank-transfers",
    props: ['page_data'],
    data(){
        return {
            data: [],
            loading: false,
            filters:{
                date: null,
                category_id: "",
                transfer_status: "",
                search: "",
                is_warning: false,
            },
            page_filters: [
                {
                    key: "transfer_status",
                    name: "Transfer Status",
                    field_id: "PROPERTY_887",
                    values: {}
                }
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
        async getPageData(startDate = null, endDate = null){
            let dateRange = JSON.parse(localStorage.getItem('dateRange'));
            this.loading = true;
            this.data = [];
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                startDate: startDate ? startDate : dateRange[0],
                endDate: endDate ? endDate : dateRange[1],
                action: "getBankTransfers",
                categories: JSON.stringify(this.filters.category_id === "" ? this.page_data.bitrix_list_categories.map((obj) => obj.bitrix_category_id) : [this.filters.category_id]),
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                this.loading = false
                this.data = response.result;
                this.data.forEach((item) => {
                    item.invoice_docoment_list = [];
                    item.invoice_supporting_docoment_list = [];
                    if (item.transfer_documents_id){
                        item.invoice_docoment_list = item.transfer_documents_id.split(",");
                    }
                    if (item.supporting_documents) {
                        item.invoice_supporting_docoment_list = item.supporting_documents.split(",");
                    }
                    if(item.purchase_invoice_id){
                        item.purchase_invoice_ids = item.purchase_invoice_id.split(",");
                    }
                });
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
                return this.isOverThreeWorkingDays(item.date_create) && item.transfer_status_id === '1532';
            }
        },
        isOverThreeWorkingDays(createdDate) {
            const now = DateTime.now();
            const dateCreated = DateTime.fromSQL(createdDate);

            const workingDays = this.calculateWorkingDays(dateCreated, now);

            return workingDays > 3;
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
            const searchTerm = this.filters.search?.toLowerCase() || '';
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.id, item.name, item.detail_text, item.to_iban,
                    item.transfer_amount, item.from_bank_name, item.from_account_number,
                    item.from_company_name, item.from_iban, item.project_id,
                    item.project_name, item.status_text, item.to_account_name,
                    item.to_account_number, item.to_bank_name, item.to_company_name,
                ].some(field => field?.toLowerCase().includes(searchTerm));
                // Filter by status
                const matchesStatus = this.filters.transfer_status ? item.transfer_status_id === this.filters.transfer_status : true;
                // Filter by warning
                const matchesWarning = this.filters.is_warning ? this.isWarning(item) : true;
                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesWarning;
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
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        let startDate = null;
        let endDate = null;
        if(urlParams.get("id")){
            this.filters.search = urlParams.get("id");
        }
        if (urlParams.get('date')) {
            const parsedDate = DateTime.fromFormat(urlParams.get('date'), 'dd.MM.yyyy');
            if (parsedDate.isValid) {
                startDate = parsedDate.toFormat('yyyy-MM-dd');
                endDate = parsedDate.toFormat('yyyy-MM-dd');

                console.log(startDate, endDate);

                // Call getPageData with formatted date
                this.getPageData(startDate, endDate);
            }
        }
    }
}
</script>
<style scoped>

</style>
