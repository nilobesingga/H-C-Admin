<template>
    <div class="container-fluid">
        <reports-filters-component
            @get-data="getData"
        />
        <div class="grid gap-5 lg:gap-7.5">
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
                    <!-- Sage Company Filter -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="select select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                            v-model="filters.sage_company_id"
                            @change="getData"
                        >
                            <option value="" selected>Filter by Sage Company</option>
                            <option v-for="obj in page_data.bitrix_list_sage_companies" :key="obj.id" :value="obj.bitrix_sage_company_id">
                                {{ obj.bitrix_sage_company_name }}
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
                <table class="w-full table table-border align-middle text-xs table-fixed">
                    <thead>
                        <tr class="bg-black text-gray-900 font-medium text-center">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[70px]">Id</th>
                            <th class="sticky top-0 w-[100px]">Invoice Date</th>
                            <th class="sticky top-0 w-[100px]">Due Date</th>
                            <th class="sticky top-0 w-[100px]">Payment Schedule</th>
                            <th class="sticky top-0 w-[100px]">SAGE Status</th>
                            <th class="sticky top-0 w-[125px]">Status</th>
                            <th class="sticky top-0 w-[125px] text-right">Invoice Amount</th>
                            <th class="sticky top-0 w-[125px] text-left">Invoice Number</th>
                            <th class="sticky top-0 w-[150px] text-left">Project</th>
                            <th class="sticky top-0 w-[150px] text-left">Receiver</th>
                            <th class="sticky top-0 w-[70px]">Charge to Client</th>
                            <th class="sticky top-0 w-[130px] text-left whitespace-normal break-words">Request By & Remarks</th>
                            <th class="sticky top-0 w-[100px]">Documents</th>
                            <th class="sticky top-0 w-[120px]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs text-gray-700">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="odd:bg-white even:bg-slate-100">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/104/element/0/' + obj.id  + '/?list_section_id='">{{ obj.id }}</a></td>
                            <td>{{ formatDate(obj.invoice_date) }}</td>
                            <td>{{ formatDate(obj.due_date) }}</td>
                            <td><span v-if="obj.payment_schedule_date" class="font-bold text-black">{{ formatDate(obj.payment_schedule_date)}}</span></td>
                            <td>
                                <div :class="[isSageStatusWarning(obj) ? 'badge badge-warning' : '']">
                                    <div v-if="obj.sage_status_text">{{ obj.sage_status_text }}</div>
                                    <div v-if="obj.sage_batch_id" class="text-gray-600">({{ obj.sage_batch_id }})</div>
                                </div>
                            </td>
                            <td>
                                <div :class="[isBitrixStatusWarning(obj) ? 'badge badge-warning' : '']">
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
                            <td>
                                <a v-for="(documentId, index) in obj.document_list"
                                   class="btn btn-sm btn-outline btn-primary mb-1" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1'`"
                                >
                                    <i class="ki-filled ki-file-down"></i>
                                    <span>Doc {{ ++index }}</span>
                                </a>
                            </td>
                            <td>
                                <button
                                    @click="openModal('showBankTransferDetailsModal', obj)"
                                    data-modal-toggle="#show_bank_transfer_details_modal"
                                    class="btn btn-sm btn-outline btn-primary"
                                    v-if="obj.bitrix_bank_transfer_id"
                                >
                                    <i class="ki-filled ki-eye"></i>
                                    <span>Transfer</span>
                                </button>
                                <button
                                    @click="openModal('isCreateBankTransferFormModal', obj)"
                                    data-modal-toggle="#create_bank_transfer_form_modal"
                                    class="btn btn-sm btn-outline btn-danger"
                                    v-if="page_data.permission === 'full_access' && (!obj.bitrix_bank_transfer_id && obj.status_id !== '1619' && obj.status_id !== '1620' && obj.status_id !== '1871' && obj.sage_status_text === 'Booked In Sage')"
                                >
                                    <i class="ki-filled ki-plus-squared"></i>
                                    <span>Transfer</span>
                                </button>
                                <a
                                    class="btn btn-sm btn-outline btn-success"
                                    target="_blank"
                                    :href="`https://10.0.1.17/CRESCOSage/AP/APInvoice?blockId=104&purchaseId=${obj.id}`"
                                    v-if="page_data.permission === 'full_access' && (obj.sage_status !== '1863' && obj.status_id !== '1619' && obj.status_id !== '1620')"
                                >
                                    <i class="ki-filled ki-plus-squared"></i>
                                    <span>Book In Sage</span>
                                </a>
                            </td>
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
                <div v-if="loading" class="data-loading absolute inset-0 bg-gray-300 bg-opacity-50 flex items-center justify-center z-100 pointer-events-none">
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
    <!-- show bank transfer detail modal -->
    <show-bank-transfer-details-modal
        :obj_id="selected_obj.bitrix_bank_transfer_id"
        v-if="is_show_bank_transfer_details_modal"
        @closeModal="closeModal"
    />
    <!-- create transfer form modal -->
    <create-bank-transfer-form-modal
        :obj="selected_obj"
        :bitrix_bank_transfer_company_ids="page_data.bitrix_bank_transfer_company_ids"
        v-if="is_create_bank_transfer_form_modal"
        type="purchaseInvoice"
        @closeModal="closeModal"
    />
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
            filters: {
                date: null,
                category_id: "",
                sage_company_id: "",
                status: "",
                charge_to_account: "",
                search: "",
                is_warning: false,
            },
            page_filters: [
                {
                    key: "status",
                    name: "Status",
                    field_id: "PROPERTY_932",
                    values: {}
                },
                {
                    key: "charge_to_account",
                    name: "Charge to Account",
                    field_id: "PROPERTY_1242",
                    values: {}
                },
            ],
            selected_obj: null,
            is_show_bank_transfer_details_modal: false,
            is_create_bank_transfer_form_modal: false,
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
                this.data = response.result;
                _.forEach(this.data, function(item){
                    item.document_list = []
                    if (item.invoice_document_id){
                        item.document_list = item.invoice_document_id.split(",");
                    }
                })
                await this.calculateTotalAsPerReportingCurrency();
            } catch (error) {
                if (error.status === 500){
                    this.errorToast('Something went wrong! Please refresh the page or contact support if this keeps happening.')
                }
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
        openModal(type, obj){
            this.selected_obj = obj;
            if(type === 'showBankTransferDetailsModal'){
                this.is_show_bank_transfer_details_modal = true
            }
            if(type === 'isCreateBankTransferFormModal'){
                this.is_create_bank_transfer_form_modal = true
            }
        },
        closeModal(){
            this.is_show_bank_transfer_details_modal = false;
            this.is_create_bank_transfer_form_modal = false;
            this.selected_obj = null
            this.removeModalBackdrop();
        },
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            const searchTerm = this.filters.search?.toLowerCase() || '';

            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.id, item.sage_status_text, item.sage_batch_id, item.status_text,
                    item.payment_reference_id, item.invoice_number, item.project_name,
                    item.supplier_name, item.detail_text,
                ].some(field => field?.toLowerCase().includes(searchTerm));
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
            return this.filteredData.filter(item => this.isWarning(item, today)).length;
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
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get("search")){
            this.filters.search = urlParams.get("search");
        }
        this.sharedState.bitrix_user_id = this.page_data.user.bitrix_user_id;
        this.sharedState.bitrix_webhook_token = this.page_data.user.bitrix_webhook_token;
    }
}
</script>
<style scoped>

</style>
