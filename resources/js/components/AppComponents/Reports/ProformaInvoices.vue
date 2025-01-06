<template>
    <div class="container-fluid">
        <!--  Sage Not Accessible          -->
        <div class="flex flex-col justify-start pb-6" v-if="sharedState.sageNotAccessible">
            <sage-network-error />
        </div>

        <reports-filters-component
            @get-data="getData"
        />
        <div class="grid gap-5 lg:gap-7.5">
            <!-- filters -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.category_id" @change="getData">
                        <option value="" selected>Filter by Category</option>
                        <option v-for="obj in page_data.bitrix_list_categories" :key="obj.id" :value="obj.bitrix_category_id">
                            {{ obj.bitrix_category_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.sage_company_id" @change="getData">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="obj in page_data.bitrix_list_sage_companies" :key="obj.id" :value="obj.bitrix_sage_company_id">
                            {{ obj.bitrix_sage_company_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.status">
                        <option value="" selected>Filter by Status</option>
                        <option value="DRAFT">New</option>
                        <option value="SENT">Sent to client</option>
                        <option value="APPROVED">Accepted</option>
                        <option value="DECLAINED">Declined</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.charge_to_account">
                        <option value="" selected>Filter by Charge to Account</option>
                        <option value="6021">No</option>
                        <option value="6024">Alex E</option>
                        <option value="5992">Andre</option>
                        <option value="6031">BhaPun</option>
                        <option value="5993">CLP Alphabet</option>
                        <option value="6023">Dmitry</option>
                        <option value="5994">Erhan</option>
                        <option value="5995">Evaland</option>
                        <option value="5996">Farhad</option>
                        <option value="6036">Geston</option>
                        <option value="6082">Irfan</option>
                        <option value="5997">Jochen</option>
                        <option value="6080">NaBro MRF096</option>
                        <option value="6081">NaBro MRF097</option>
                        <option value="6037">Patrick</option>
                        <option value="6086">Raed</option>
                        <option value="5998">Sergei</option>
                        <option value="6027">Sid H</option>
                        <option value="5999">SS</option>
                    </select>
                </div>
                <div class="flex">
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3"></i>
                        <input class="input input-sm ps-8 text-black bg-inherit" placeholder="Search" type="text" v-model="filters.search">
                    </div>
                </div>
<!--                <div class="flex">-->
<!--                    <button :class="['btn btn-icon btn-sm relative', filters.is_warning ? 'btn-warning text-white' : 'btn-light']" @click="filters.is_warning = !filters.is_warning">-->
<!--                        <i class="ki-filled ki-information-1"></i>-->
<!--                        <span class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ warningCount }}</span>-->
<!--                    </button>-->
<!--                </div>-->
            </div>
            <!-- table -->
            <div class="flex-grow overflow-auto reports-table-container">
                <table class="relative w-full table table-border align-middle text-xs table-fixed">
                    <thead>
                        <tr class="bg-black text-gray-900 font-medium text-center">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[70px]">Id</th>
                            <th class="sticky top-0 w-[100px]">Status</th>
                            <th class="sticky top-0 w-[100px]">Expiration Date</th>
                            <th class="sticky top-0 w-[125px] text-right">Amount</th>
                            <th class="sticky top-0 w-[200px] text-left">Subject</th>
                            <th class="sticky top-0 w-[250px] text-left">CRM</th>
                            <th class="sticky top-0 w-[150px]">Payment Terms</th>
                            <th class="sticky top-0 w-[150px]">Responsible Person</th>
                            <th class="sticky top-0 w-[150px]">Charge to Account</th>
                            <th class="sticky top-0 w-[150px]">Created On</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs text-gray-700">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="odd:bg-white even:bg-slate-100">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link" target="_blank" :href="`https://crm.cresco.ae/crm/quote/show/${obj.id}/`">{{ obj.id }}</a></td>
                            <td>{{ getStatusValue(obj.status) }}</td>
                            <td>{{ formatDate(obj.expiration_date) }}</td>
                            <td class="text-right">{{ formatAmount(obj.amount) }} <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td class="text-left">{{ obj.subject }}</td>
                            <td class="text-left">
                                <div v-if="obj.deal">
                                    <span class="text-black font-bold">Deal: </span>
                                    <a target="_blank" class="btn btn-link" :href="`https://crm.cresco.ae/crm/deal/details/${obj.deal_id}/`">{{ obj.deal }}</a>
                                </div>
                                <div>
                                    <span class="text-black font-bold">Lead: </span>
                                    <a class="btn btn-link" target="_blank" :href="`https://crm.cresco.ae/crm/lead/details/${obj.lead_id}/`">{{ obj.lead }}</a>
                                </div>
                                <div>
                                    <span class="text-black font-bold">Company: </span>
                                    <a class="btn btn-link" target="_blank" :href="`https://crm.cresco.ae/crm/company/details/${obj.company_id}/`">{{ obj.company }}</a>
                                </div>
                            </td>
                            <td>{{ obj.payment_term }}</td>
                            <td>{{ obj.responsible_person }}</td>
                            <td>{{ obj.charge_to_running_account }}</td>
                            <td>{{ formatDate(obj.created_on) }}</td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="3" class="text-black font-bold text-center">Totals per currency</td>
                            <td colspan="2" class="text-right">
                                <div v-for="(amount, currency) in groupedByCurrency">{{ formatAmount(amount) }} <span class="font-bold text-black">{{ currency }} </span></div>
                            </td>
                        </tr>
                        <tr class="table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-red-400">No data available</td>
                        </tr>
                    </tbody>
                    <div v-if="loading" class="absolute inset-0 bg-gray-300 bg-opacity-50 flex items-center justify-center z-50 pointer-events-none">
                        <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                            <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </div>
                    </div>
                </table>
            </div>
            <!-- footer -->
            <div class="flex items-center justify-between">
                <!-- Left Section: Showing Records -->
                <div class="text-xs">
                    <span>Showing 5 records</span>
                </div>

                <!-- Right Section: Total as per Reporting Currency -->
<!--                <div class="flex items-center justify-center text-right text-dark">-->
<!--                    <span class="mr-2">Total as per reporting currency ({{ currency }}):</span>-->
<!--                    <span class="font-black">-->
<!--                        {{ formatAmount(totalAsPerReportingCurrency) }} {{ currency }}-->
<!--                    </span>-->
<!--                </div>-->
            </div>
        </div>
    </div>
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";
import bitrixHelperMixin from "../../../mixins/bitrixHelperMixin.js";

export default {
    name: "proforma-invoices",
    mixins: [bitrixHelperMixin],
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
                action: "getProformaInvoices",
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
                this.handleNetworkError(error);
            }
        },
        async calculateTotalAsPerReportingCurrency(){
            this.totalAsPerReportingCurrency = await this.calculateTotalInBaseCurrency(this.groupedByCurrency)
        },
        getStatusValue(status){
            switch (status){
                case "DRAFT":
                    return "New";
                case "SENT":
                    return "Sent to client";
                case "APPROVED":
                    return "Accepted";
                case "DECLAINED":
                    return "Declined"
                default:
                    return ""

            }
        },
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch =
                        (item.id && item.id.includes(this.filters.search)) ||
                        (item.subject && item.subject.includes(this.filters.search) ||
                        (item.lead && item.lead.includes(this.filters.search)) ||
                        (item.company && item.company.includes(this.filters.search)) ||
                        (item.payment_term && item.payment_term.includes(this.filters.search))
                        );

                // Filter by status
                const matchesStatus = this.filters.status ? item.status === this.filters.status : true;

                // Filter by chargeToAccount
                const matchesChargeToClient = this.filters.charge_to_account ? item.charge_to_running_account_id === this.filters.charge_to_account : true;

                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesChargeToClient;
            });
        },
        groupedByCurrency() {
            const groupedByCurrency = _.groupBy(this.filteredData, 'currency');
            const summedByCurrency = _.mapValues(groupedByCurrency, (group) =>
                _.reduce(group, (sum, transaction) => sum + parseFloat(transaction.amount), 0)
            );

            return summedByCurrency;
        },
    },
    watch: {
        currency() {
            this.calculateTotalAsPerReportingCurrency();
        },
        filteredData(){
            this.calculateTotalAsPerReportingCurrency();
        }
    },
    created() {
        this.sharedState.bitrixUserId = this.page_data.user.bitrix_user_id;
        this.sharedState.bitrixWebhookToekn = this.page_data.user.bitrix_webhook_token;
    }
}
</script>
<style scoped>

</style>
