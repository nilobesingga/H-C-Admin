<template>
    <div class="container-fluid">
        <reports-filters-component
            @get-data="getData"
        />
        <div class="grid gap-5 lg:gap-7.5">
            <!-- filters -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm min-w-[30rem] max-w-full text-black bg-inherit" v-model="filters.category_id" @change="getData">
                        <option value="" selected>Filter by Category</option>
                        <option v-for="obj in page_data.bitrix_list_categories" :key="obj.id" :value="obj.bitrix_category_id">
                            {{ obj.bitrix_category_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[30rem] max-w-full text-black bg-inherit" v-model="filters.transfer_status">
                        <option value="" selected>Filter by Transfer Status</option>
                        <option value="1532">With Bank</option>
                        <option value="1536">Completed</option>
                        <option value="1537">Cancelled</option>
                    </select>
                </div>
                <div class="flex">
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3"></i>
                        <input class="input input-sm ps-8 text-black bg-inherit min-w-[30rem]" placeholder="Search" type="text" v-model="filters.search">
                    </div>
                </div>
                <div class="flex">
                    <button :class="['btn btn-icon btn-sm relative', filters.is_warning ? 'btn-warning text-white' : 'btn-light']" @click="filters.is_warning = !filters.is_warning">
                        <i class="ki-filled ki-information-1"></i>
                        <span class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ warningCount }}</span>
                    </button>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow overflow-auto reports-table-container">
                <table class="w-full table table-border align-middle text-xs table-fixed">
                    <thead>
                        <tr class="bg-black text-gray-900 font-medium text-center">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[50px]">Id</th>
                            <th class="sticky top-0 w-[120px] text-right">Transfer Amount</th>
                            <th class="sticky top-0 w-[150px] text-left">Transfer From</th>
                            <th class="sticky top-0 w-[150px] text-left">Transfer To</th>
                            <th class="sticky top-0 w-[150px] text-left">Purpose of Transfer</th>
                            <th class="sticky top-0 w-[100px]">Reference No</th>
                            <th class="sticky top-0 w-[100px]">Created Date</th>
                            <th class="sticky top-0 w-[110px]">Purchase Invoice Reference</th>
                            <th class="sticky top-0 w-[60px]">Transfer Status</th>
                            <th class="sticky top-0 w-[60px]">Transfer Date</th>
                            <th class="sticky top-0 w-[120px]">Transfer Documents</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs text-gray-700">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="odd:bg-white even:bg-slate-100">
                            <td>{{ index + 1 }}</td>
                            <td><a target="_blank" class="btn btn-link" :href="'https://crm.cresco.ae/services/lists/99/element/0/' + obj.id  + '/?list_section_id='">{{ obj.id }}</a></td>
                            <td class="text-right">{{ formatAmount(obj.amount) }} <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td class="text-left">
                                <div><span>Account Name:</span> <strong class="font-bold text-black">{{ obj.from_account_name }}</strong></div>
                                <div><span>Account Number: </span><span class="text-black">{{ obj.from_account_number }}</span></div>
                                <div><span>Bank Name: </span><span class="text-black">{{ obj.from_bank_name }}</span></div>
                                <div><span>IBAN: </span><span class="text-black">{{ obj.from_iban }}</span></div>
                                <br>
                                <div v-if="obj.from_company_name">
                                    <span>Company:</span>
                                    <span><a :href="`https://crm.cresco.ae/crm/company/details/${obj.from_company_id}/`" target="_blank" class="btn btn-link">{{ obj.from_company_name }}</a></span>
                                </div>
                            </td>
                            <td class="text-left">
                                <div><span>Account Name:</span> <strong class="font-bold text-black">{{ obj.to_account_name }}</strong></div>
                                <div><span>Account Number: </span> <span class="text-black">{{ obj.to_account_number }}</span></div>
                                <div><span>Bank Name:</span> <span class="text-black">{{ obj.to_bank_name }}</span></div>
                                <div><span>IBAN:</span> <span class="text-black">{{ obj.to_iban }}</span> </div>
                                <br>
                                <div v-if="obj.to_company_name">
                                    <span>Company:</span>
                                    <span><a :href="`https://crm.cresco.ae/crm/company/details/${obj.to_company_id}/`" target="_blank" class="btn btn-link">{{ obj.to_company_name }}</a></span>
                                </div>
                            </td>
                            <td class="text-left break-words">
                                <div class="text-wrap">{{ obj.detail_text }}</div>
                                <br>
                                <div v-if="obj.project_id">
                                    <span>Project:</span>
                                    <span><a :href="getBitrixProjectLink(obj)" target="_blank" class="btn btn-link">{{ obj.project_name }}</a></span>
                                </div>
                            </td>
                            <td>{{ obj.reference_number }}</td>
                            <td>{{ formatDate(obj.date_create) }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline btn-primary mb-1" v-for="pid in obj.purchase_invoice_ids">
                                    <i class="ki-filled ki-eye"></i>
                                    <span>Invoice Details</span>
                                </button>
                            </td>
                            <td :class="isWarning(obj) ? 'bg-warning' : ''">{{ obj.status_text }}</td>
                            <td>{{ formatDate(obj.transfer_date) }}</td>
                            <td>
                                <a v-for="(documentId, index) in obj.invoice_docoment_list"
                                   class="btn btn-sm btn-outline btn-primary mb-1" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    <i class="ki-filled ki-file-down"></i>
                                    <span>Transfer Doc {{ ++index }}</span>
                                </a>
                                <br>
                                <a v-for="(documentId, index) in obj.invoice_supporting_docoment_list"
                                   class="btn btn-sm btn-outline btn-primary mb-1" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    <i class="ki-filled ki-file-down"></i>
                                    <span>Doc for Bank {{ ++index }}</span>
                                </a>
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="2" class="text-black font-bold">Totals per currency</td>
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
    name: "bank-transfers",
    props: ['page_data'],
    data(){
        return {
            data: [],
            loading: false,
            filters:{
                date: null,
                category_id: "",
                sage_company_id: "",
                transfer_status: "",
                search: "",
                is_warning: false,
            },
            totalAsPerReportingCurrency: 0,
        }
    },
    methods: {
        async getData(){
            let dateRange = JSON.parse(localStorage.getItem('dateRange'));
            this.loading = true;
            this.data = [];
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                startDate: dateRange[0],
                endDate: dateRange[1],
                action: "getBankTransfers",
                categories: JSON.stringify(this.filters.category_id === "" ? this.page_data.bitrix_list_categories.map((obj) => obj.bitrix_category_id) : [this.filters.category_id]),
                sage_companies: JSON.stringify(this.filters.sage_company_id === "" ? this.page_data.bitrix_list_sage_companies.map((obj) => obj.bitrix_sage_company_id) : [this.filters.sage_company_id])
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
            let today = DateTime.now();
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch =
                    (item.searchString && item.searchString.toLowerCase().includes(this.filters.search.toLowerCase())) ||
                    (item.id && item.id.includes(this.filters.search)) ||
                    (item.payment_reference_id && item.payment_reference_id.includes(this.filters.search));

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
            return this.data.filter(item => this.isWarning(item)).length;
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
