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
                        <option value="1618">Approved</option>
                        <option value="1619">Rejected</option>
                        <option value="1620">Pending for Approval</option>
                        <option value="1678">Paid</option>
                        <option value="1864">Partially Paid</option>
                        <option value="1871">Cancelled</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.charge_to_account">
                        <option value="" selected>Filter by Charge to Account</option>
                        <option value="2242">No</option>
                        <option value="2247">Alex E</option>
                        <option value="2226">Andre</option>
                        <option value="2263">BhaPun</option>
                        <option value="2227">CLP Alphabet</option>
                        <option value="2248">Dmitry</option>
                        <option value="2228">Erhan</option>
                        <option value="2229">Evaland</option>
                        <option value="2230">Farhad</option>
                        <option value="2273">Geston</option>
                        <option value="2278">Irfan</option>
                        <option value="2231">Jochen</option>
                        <option value="2276">NaBro MRF096</option>
                        <option value="2277">NaBro MRF097</option>
                        <option value="2270">Patrick</option>
                        <option value="2232">Sergei</option>
                        <option value="2260">Sid H</option>
                        <option value="2233">SS</option>
                    </select>
                </div>
                <div class="flex">
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3"></i>
                        <input class="input input-sm ps-8 text-black bg-inherit" placeholder="Search" type="text" v-model="filters.search">
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
                            <th class="sticky top-0 w-[70px]">Id</th>
                            <th class="sticky top-0 w-[100px]">Invoice Date</th>
                            <th class="sticky top-0 w-[100px]">Due Date</th>
                            <th class="sticky top-0 w-[100px]">Payment Schedule</th>
                            <th class="sticky top-0 w-[80px]">SAGE Status</th>
                            <th class="sticky top-0 w-[125px]">Status</th>
                            <th class="sticky top-0 w-[125px] text-right">Invoice Amount</th>
                            <th class="sticky top-0 w-[150px] text-left">Invoice Number</th>
                            <th class="sticky top-0 w-[150px] text-left">Project</th>
                            <th class="sticky top-0 w-[150px] text-left">Receiver</th>
                            <th class="sticky top-0 w-[70px]">Charge to Client</th>
                            <th class="sticky top-0 w-[150px] text-left whitespace-normal break-words">Request By & Remarks</th>
<!--                            <th class="sticky top-0 w-[100px]">Actions</th>-->
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs text-gray-700">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="odd:bg-white even:bg-slate-100">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/104/element/0/' + obj.id  + '/?list_section_id='">{{ obj.id }}</a></td>
                            <td>{{ formatDate(obj.invoice_date) }}</td>
                            <td>{{ formatDate(obj.due_date) }}</td>
                            <td><span v-if="obj.payment_schedule_date" class="font-bold text-black">{{ formatDate(obj.payment_schedule_date)}}</span></td>
                            <td :class="[isSageStatusWarning(obj) ? 'bg-warning' : '']">
                                <div v-if="obj.sage_status_text">{{ obj.sage_status_text }}</div>
                                <div v-if="obj.sage_batch_id" class="text-gray-600">({{ obj.sage_batch_id }})</div>
                            </td>
                            <td :class="[isBitrixStatusWarning(obj) ? 'bg-warning' : '']">
                                <div>
                                    <span> {{ obj.status_text }} </span>&nbsp
                                    <span v-if="obj.sage_payment_date"> {{ formatBitrixDate(obj.sage_payment_date) }} </span>
                                    <div v-if="obj.payment_reference_id"> {{ obj.payment_reference_id }} </div>
                                </div>
                            </td>
                            <td class="text-right">{{ formatAmount(obj.amount) }} <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td class="text-left">{{ obj.invoice_number }}</td>
                            <td class="text-left"><a class="btn btn-link" target="_blank" :href="getBitrixProjectLink(obj)">{{ obj.project_name }}</a></td>
                            <td class="text-left">{{ obj.supplier_name }}</td>
                            <td>{{ getChargeExtraToClientValue(obj.charge_extra_to_client, page_data.identifier) }}</td>
                            <td class="text-left whitespace-normal break-words">
                                <span>Requested By:</span>
                                <span class="font-bold text-black">{{ obj.requested_by_name }}</span>
                                <br><br>
                                <span class="">{{ obj.detail_text }}</span>
                            </td>
<!--                            <td>-->
<!--                                <button-->
<!--                                    @click="openModal('isCreateBankTransferFormModal', obj)" data-modal-toggle="#create_bank_transfer_form_modal" class="btn btn-sm btn-outline btn-danger">-->
<!--                                    <i class="ki-filled ki-plus-squared"></i>-->
<!--                                    <span>Create Transfer</span>-->
<!--                                </button>-->
<!--                            </td>-->
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="6" class="text-black font-bold text-center">Totals per currency</td>
                            <td colspan="2" class="text-right">
                                <div v-for="(amount, currency) in groupedByCurrency">{{ formatAmount(amount) }} <span class="font-bold text-black">{{ currency }} </span></div>
                            </td>
                        </tr>
                        <tr class="table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-red-400">No data available</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="loading" class="absolute inset-0 bg-gray-300 bg-opacity-50 flex items-center justify-center z-100 pointer-events-none">
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
            <!-- create transfer form modal -->
            <create-bank-transfer-form-modal
                :obj="selected_obj"
                :bitrix_bank_transfer_company_ids="page_data.bitrixBankTransferCompanyIds"
                v-if="is_crate_bank_transfer_form_modal"
                type="purchaseInvoice"
                @closeModal="closeModal"
            />
        </div>
    </div>
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "purchase-invoices",
    props: ['page_data'],
    data(){
        return {
            data: [],
            loading: true,
            filters:{
                date: null,
                category_id: "",
                sage_company_id: "",
                status: "",
                charge_to_account: "",
                search: "",
                is_warning: false,
            },
            selected_obj: null,
            is_crate_bank_transfer_form_modal: false,
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
                action: "getPurchaseInvoices",
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
        isWarning(item, today) {
            return (item.sage_status_text === "Not Booked In Sage" && item.status_text === "Approved") ||
                (item.status_text === "Approved" && DateTime.fromSQL(item.payment_schedule_date) <= today);
        },
        isSageStatusWarning(item){
            return item.sage_status_text === "Not Booked In Sage" && item.status_text === "Approved";
        },
        isBitrixStatusWarning(item){
            let today = DateTime.now();
            return item.status_text === "Approved" && DateTime.fromSQL(item.payment_schedule_date) <= today
        },
        openModal(modal, obj){
            this.selected_obj = obj;
            if(modal === 'isCreateBankTransferFormModal'){
                this.is_crate_bank_transfer_form_modal = true
            }
        },
        closeModal(){
            this.is_crate_bank_transfer_form_modal = false;
            this.obj_id = null
            this.removeModalBackdrop();
        }
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch =
                        (item.id && item.id.includes(this.filters.search)) ||
                        (item.sage_status_text && item.sage_status_text.includes(this.filters.search) ||
                        (item.sage_batch_id && item.sage_batch_id.includes(this.filters.search)) ||
                        (item.status_text && item.status_text.includes(this.filters.search)) ||
                        (item.payment_reference_id && item.payment_reference_id.includes(this.filters.search)) ||
                        (item.invoice_number && item.invoice_number.includes(this.filters.search)) ||
                        (item.project_name && item.project_name.includes(this.filters.search)) ||
                        (item.supplier_name && item.supplier_name.includes(this.filters.search)) ||
                        (item.detail_text && item.detail_text.includes(this.filters.search))
                        );

                // Filter by status
                const matchesStatus = this.filters.status ? item.status_id === this.filters.status : true;

                // Filter by chargeToAccount
                const matchesChargeToClient = this.filters.charge_to_account ? item.charge_to_running_account_id === this.filters.charge_to_account : true;

                // Filter by warning
                const matchesWarning = this.filters.is_warning ? this.isWarning(item, today) : true;

                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesChargeToClient && matchesWarning;
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
            let today = DateTime.now();
            return this.data.filter(item => this.isWarning(item, today)).length;
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
