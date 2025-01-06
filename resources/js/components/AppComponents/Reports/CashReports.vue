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
                    <select class="select select-sm min-w-[12rem] max-w-full text-black bg-inherit" v-model="filters.category_id" @change="getData">
                        <option value="" selected>Filter by Category</option>
                        <option v-for="obj in page_data.bitrix_list_categories" :key="obj.id" :value="obj.bitrix_category_id">
                            {{ obj.bitrix_category_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[10rem] max-w-full text-black bg-inherit" v-model="filters.sage_company_id" @change="getData">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="obj in page_data.bitrix_list_sage_companies" :key="obj.id" :value="obj.bitrix_sage_company_id">
                            {{ obj.bitrix_sage_company_name }}
                        </option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[10rem] max-w-full text-black bg-inherit" v-model="filters.status">
                        <option value="" selected>Filter by Status</option>
                        <option value="1651">Pending</option>
                        <option value="1652">Approved</option>
                        <option value="1653">Declined</option>
                        <option value="1655">Cash Released</option>
                        <option value="1659">Cancelled</option>
                        <option value="1687">Partial Cash Release</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[12rem] max-w-full text-black bg-inherit" v-model="filters.charge_to_client">
                        <option value="" selected>Filter by Charge to Client</option>
                        <option value="1990">Yes</option>
                        <option value="1991">No</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.payment_mode">
                        <option value="" selected>Filter by Payment Mode</option>
                        <option value="1867">Cash</option>
                        <option value="1868">Card</option>
                        <option value="1869">Bank Transfer</option>
                        <option value="1870">Cheque</option>
                    </select>
                </div>
                <div class="flex">
                    <select class="select select-sm min-w-[20rem] max-w-full text-black bg-inherit" v-model="filters.charge_to_account">
                        <option value="" selected>Filter by Charge to Account</option>
                            <option value="2243" selected="">No</option>
                            <option value="2246">Alex E</option>
                            <option value="2234">Andre</option>
                            <option value="2265">BhaPun</option>
                            <option value="2235">CLP Alphabet</option>
                            <option value="2245">Dmitry</option>
                            <option value="2236">Erhan</option>
                            <option value="2237">Evaland</option>
                            <option value="2238">Farhad</option>
                            <option value="2274">Geston</option>
                            <option value="2281">Irfan</option>
                            <option value="2239">Jochen</option>
                            <option value="2279">NaBro MRF096</option>
                            <option value="2280">NaBro MRF097</option>
                            <option value="2271">Patrick</option>
                            <option value="2324">Raed</option>
                            <option value="2240">Sergei</option>
                            <option value="2261">Sid H</option>
                            <option value="2241">SS</option>
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
            <div class="flex-grow overflow-auto reports-table-container">
                <table class="relative w-full table table-border align-middle text-xs">
                    <thead>
                        <tr class="bg-black text-gray-900 font-medium text-center">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[70px]">Id</th>
                            <th class="sticky top-0 w-[70px]">Payment Mode</th>
                            <th class="sticky top-0 w-[100px] text-right">Amount</th>
                            <th class="sticky top-0 w-[100px]">Payment Date</th>
                            <th class="sticky top-0 w-[100px]">Payment Schedule</th>
                            <th class="sticky top-0 w-[150px] text-left">Project</th>
                            <th class="sticky top-0 w-[70px]">Charge to Client</th>
                            <th class="sticky top-0 w-[200px] text-left">Request By & Remarks</th>
                            <th class="sticky top-0 w-[100px]">Status</th>
                            <th class="sticky top-0 w-[150px]">Documents</th>
                            <th class="sticky top-0 w-[125px]">Actions</th>
                            <th class="sticky top-0 w-[125px]">Sage Reference</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs text-gray-700">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="odd:bg-white even:bg-slate-100">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/105/element/0/' + obj.id  + '/?list_section_id='">{{obj.id }}</a></td>
                            <td class="text-center">{{ getPaymentMode(obj.payment_mode_id) }}</td>
                            <td class="text-right">{{ formatAmount(obj.amount) }} <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td>{{ formatDate(obj.payment_date)  }}</td>
                            <td>{{ formatDate(obj.funds_available_date) }}</td>
                            <td class="text-left">
                                <span class="font-bold text-black" v-if="obj.project_type">{{ obj.project_type }}:</span>
                                <a class="btn btn-link" target="_blank" v-if="obj.project_type" :href="getBitrixProjectLink(obj)">{{ obj.project_name }}</a>
                            </td>
                            <td class="text-center">{{ getChargeExtraToClientValue(obj.charge_extra_to_client, page_data.identifier) }}</td>
                            <td class="text-left" style="max-width: 200px; word-wrap: break-word;">
                                Requested By: <span class="font-bold text-black">{{ obj.requested_by_name }}</span><br><br>
                                <span>{{ obj.detail_text }}</span>
                            </td>
                            <td :class="isWarning(obj) ? 'bg-warning' : ''">
                                <span>{{ obj.status_text }}</span>
                                <span v-if="obj.sage_payment_date">on {{ formatBitrixDate(obj.sage_payment_date ) }}</span>
                            </td>
                            <td>
                                <a v-for="(documentId, index) in obj.document_lists"
                                   class="btn btn-sm btn-outline btn-primary mb-1" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    <i class="ki-filled ki-file-down"></i>
                                    <span>Receipt</span>
                                </a>

                                <button
                                    v-if="obj.status_id == bitrixCashRequestStatus.cashReleased || obj.status_id == bitrixCashRequestStatus.partialCashRelease"
                                    @click="downloadCashReleaseReceipt(obj)" class="btn btn-sm btn-outline btn-primary"
                                >
                                    <i class="ki-filled ki-file-down"></i>
                                    <span>Release Receipt</span>
                                </button>
                            </td>
                            <td class="text-middle">
                                <button class="btn btn-sm btn-outline btn-success" v-if="obj.bitrix_bank_transfer_id" @click="showBankTransferDetails(obj.bitrix_bank_transfer_id)">View Transfer</button>
                                <button v-if="obj.payment_mode_id === '1869' && !obj.bitrix_bank_transfer_id" class="btn btn-sm btn-outline btn-success mb-2" @click="showCreateNewBankTransferModal(obj)">Create Transfer</button>
                                <button class="btn btn-sm btn-outline btn-success mt-2" v-if="obj.charge_extra_to_client === '1990' && !obj.has_offer_generated" @click="createOffer(obj)">Create Offer For Extra Charges</button>
                            </td>
                            <td>
                                <div v-if="!obj.sage_transaction_type && !obj.sage_transaction_type_id">
                                    <a class="btn btn-sm btn-outline btn-danger mb-2"
                                       style="width: 10rem"
                                       :href="`https://10.0.1.17/CRESCOSage/AP/APInvoice?blockId=105&purchaseId=${obj.id}`" target="_blank"
                                    >
                                        Book Purchase Invoice
                                    </a>
                                    <a class="btn btn-sm btn-outline btn-danger mb-2"
                                       style="width: 10rem"
                                       :href="`https://10.0.1.17/CRESCOSage/AP/APCashRequisition?blockId=105&crId=${obj.id}`" target="_blank"
                                    >
                                        Book Misc Payment
                                    </a>
                                </div>
                                <div v-else >
                                    <div v-if="obj.sage_transaction_type === 'Purchase Invoice' && obj.sage_transaction_type_id === '2223'">
                                        <a class="btn btn-sm btn-outline btn-primary mb-2"
                                           style="width: 10rem"
                                           :href="`https://10.0.1.17/CRESCOSage/AP/APInvoice?blockId=105&purchaseId=${obj.id}`" target="_blank"
                                        >
                                            View Purchase Invoice
                                        </a>
                                    </div>
                                    <div v-else >
                                        <a class="btn btn-sm btn-outline btn-primary mb-2"
                                           style="width: 10rem"
                                           :href="`https://10.0.1.17/CRESCOSage/AP/APCashRequisition?blockId=105&crId=${obj.id}`" target="_blank"
                                        >
                                            View Misc Payment
                                        </a>
                                    </div>
                                    <!--                                <div>{{ invoice.sage_payment_reference_id }}</div>-->
                                </div>
                                <!--                            <button v-if="invoice.sage_payment_reference_id" class="btn btn-sm btn-outline-success mb-2"-->
                                <!--                                    @click="showSagePaymentReferenceModal(invoice)">View in Sage-->
                                <!--                            </button>-->
                                <!--                            <button v-else class="btn btn-sm btn-outline-danger mb-2"-->
                                <!--                                    @click="showSagePaymentReferenceModal(invoice)">Book in Sage-->
                                <!--                            </button>-->
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="3" class="text-black font-bold">Totals per currency</td>
                            <td class="text-right">
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
import bitrixHelperMixin from "../../../mixins/bitrixHelperMixin.js";
export default {
    name: "cash-reports",
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
                charge_to_client: "",
                charge_to_account: "",
                search: "",
                payment_mode: "",
                is_warning: false,
            },
            totalAsPerReportingCurrency: 0,
            bitrixCashRequestStatus: {
                pending: 1651,
                approved: 1652,
                declined: 1653,
                cashReleased: 1655,
                completed: 1656,
                cancelled: 1659,
                partialCashRelease: 1687
            },
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
                action: "getCashReports",
                categories: JSON.stringify(this.filters.category_id === "" ? this.page_data.bitrix_list_categories.map((obj) => obj.bitrix_category_id) : [this.filters.category_id]),
                sage_companies: JSON.stringify(this.filters.sage_company_id === "" ? this.page_data.bitrix_list_sage_companies.map((obj) => obj.bitrix_sage_company_id) : [this.filters.sage_company_id])
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                this.loading = false
                this.data = response.result;
                this.data.forEach((item) => {
                    item.document_lists = []
                    if (item.receipt_id){
                        item.document_lists = item.receipt_id.split(",");
                    }
                })
                await this.calculateTotalAsPerReportingCurrency();
            } catch (error) {
                this.loading = false
                this.handleNetworkError(error);
            }
        },
        async calculateTotalAsPerReportingCurrency(){
                this.totalAsPerReportingCurrency = await this.calculateTotalInBaseCurrency(this.groupedByCurrency)
            },
        isOverTwoWorkingDays(fundAvailableDate) {
            const now = DateTime.now();
            const dateCreated = DateTime.fromSQL(fundAvailableDate);

            const workingDays = this.calculateWorkingDays(dateCreated, now);

            return workingDays > 2;
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
        isWarning(item) {
            return (item.status_text === "Approved" && this.isOverTwoWorkingDays(item.funds_available_date));
        },
        getPaymentMode(paymentModeId) {
            switch (paymentModeId) {
                case "1867":
                    return "Cash";
                case "1868":
                    return "Card";
                case "1869":
                    return "Bank Transfer";
                case "1870":
                    return "Cheque";
                default:
                    return "Cash";
            }
        },
        showBankTransferDetails(){},
        showCreateNewBankTransferModal(){},
        createOffer(){},
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
                const matchesStatus = this.filters.status ? item.status_id === this.filters.status : true;
                // Filter by warning
                const matchesWarning = this.filters.is_warning ? this.isWarning(item, today) : true;
                // Filter by payment mode
                const matchesPaymentMode = this.filters.payment_mode
                    ? (this.filters.payment_mode === "1867"
                        ? item.payment_mode_id === this.filters.payment_mode || item.payment_mode_id === null
                        : item.payment_mode_id === this.filters.payment_mode)
                    : true;
                // Filter by charge to client
                const matchesChargeToClient = this.filters.charge_to_client
                    ? (this.filters.charge_to_client === "1991"
                        ? item.payment_mode_id === this.filters.payment_mode || item.payment_mode_id === null
                        : item.payment_mode_id === this.filters.payment_mode)
                    : true;
                // Filter by chargeToAccount
                const matchesChargeToAccount = this.filters.charge_to_account ? item.charge_to_running_account_id === this.filters.charge_to_account : true;
                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesChargeToClient && matchesWarning && matchesPaymentMode && matchesChargeToAccount;
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
            return this.filteredData.filter(item => {
                return item.status_text === "Approved" && this.isOverTwoWorkingDays(item.funds_available_date)
            }).length;
        }
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
