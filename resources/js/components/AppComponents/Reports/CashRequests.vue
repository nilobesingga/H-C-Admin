<template>
    <div class="px-3 container-fluid">
        <reports-filters-component
            @get-data="getData"
            ref="filters"
            class="reports-header-filters"
        />
        <div class="grid gap-2">
            <!-- Filters Section -->
            <div class="flex flex-wrap items-center gap-2 reports-only-filters">
                <div class="flex gap-2">
                    <!-- Category Filter -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="w-48 select select-sm select-input"
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
                            class="select select-sm select-input w-96"
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
                            class="select select-sm select-input w-52"
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
                        <span class="absolute flex items-center justify-center text-xs font-bold text-white translate-x-1/2 -translate-y-1/2 bg-yellow-500 rounded-full shadow-md top-1 right-1 shadow-yellow-300 min-h-5 min-w-5">{{ warningCount }}</span>
                    </button>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow h-full overflow-auto border shadow-md reports-table-container border-brand">
                <table class="table w-full text-xs align-middle table-fixed c-table table-border" :class="filteredData.length === 0 ? 'h-full' : ''">
                    <thead>
                        <tr class="font-medium text-center bg-black text-neutral-900">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[60px]">Id</th>
                            <th class="sticky top-0 w-[60px]" data-tooltip="#Payment_Mode_tooltip">
                                Mode <i class="ki-outline ki-information-2"></i>
                                <div class="transition-opacity duration-300 tooltip" id="Payment_Mode_tooltip">Payment Mode</div>
                            </th>
                            <th class="sticky top-0 w-[100px] text-right">Amount</th>
                            <th class="sticky top-0 w-[80px]">Payment Date</th>
                            <th class="sticky top-0 w-[80px]" data-tooltip="#Payment_Schedule_tooltip">
                                Schedule <i class="ki-outline ki-information-2"></i>
                                <div class="transition-opacity duration-300 tooltip" id="Payment_Schedule_tooltip">Payment Schedule</div>
                            </th>
                            <th class="sticky top-0 w-[180px] text-left">Project</th>
                            <th class="sticky top-0 w-[70px]" data-tooltip="#charge_to_client_tooltip">
                                Charge <i class="ki-outline ki-information-2"></i>
                                <div class="transition-opacity duration-300 tooltip" id="charge_to_client_tooltip">Charge to Client</div>
                            </th>
                            <th class="sticky top-0 w-[150px] text-left">Request By & Remarks <i class="ki-outline ki-exit-down"></i></th>
                            <th class="sticky top-0 w-[80px]">Status</th>
                            <th class="sticky top-0 w-[130px]">Documents</th>
                            <th class="sticky top-0 w-[130px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="h-full text-xs tracking-tight text-center">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-neutral-800">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link !text-black hover:!text-brand-active" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/105/element/0/' + obj.id  + '/?list_section_id='">{{obj.id }}</a></td>
                            <td class="text-center">{{ obj.payment_mode }}</td>
                            <td class="text-right">{{ formatAmount(obj.amount) }} <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td>{{ formatDate(obj.payment_date)  }}</td>
                            <td>{{ formatDate(obj.funds_available_date) }}</td>
                            <td class="text-left">
                                <span class="font-bold text-black" v-if="obj.project_type">{{ obj.project_type }}:</span>
                                <a class="btn btn-link !text-xs !text-black hover:!text-brand-active" target="_blank" v-if="obj.project_type" :href="getBitrixProjectLink(obj)">{{ obj.project_name }}</a>
                            </td>
                            <td class="text-center">{{ obj.charge_extra_to_client }}</td>
                            <td class="text-left break-words whitespace-normal group/request">
                                <div class="font-bold text-black">{{ obj.requested_by_name }}</div>
                                <div class="relative ">
                                    <div class="transition-all duration-500 line-clamp-2 group-hover/request:line-clamp-none">
                                        {{ obj.detail_text }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div :class="isWarning(obj) ? 'badge badge-warning' : ''">
                                    <div class="flex flex-col gap-0.5">
                                        <div>{{ obj.status_text }}</div>
                                        <div v-if="obj.sage_payment_date">{{ formatDate(obj.sage_payment_date ) }}</div>
                                        <div v-if="obj.released_date">{{ formatDate(obj.released_date ) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a
                                    v-if="obj.doc_for_bank_list && obj.doc_for_bank_list.length > 0"
                                    v-for="(documentId, index) in obj.doc_for_bank_list"
                                    class="block w-full mb-1 secondary-btn bank-doc-btn" target="_blank"
                                    :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    {{ ++index }}. Doc for Bank
                                </a>
                                <a
                                    v-if="obj.other_document_list && obj.other_document_list.length > 0"
                                    v-for="(documentId, index) in obj.other_document_list"
                                    class="block w-full mb-1 secondary-btn other-doc-btn" target="_blank"
                                    :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    {{ ++index }}. Other Doc
                                </a>
                                <a
                                    v-if="obj.cash_release_receipt_doc_list && obj.cash_release_receipt_doc_list.length > 0"
                                    v-for="(documentId, index) in obj.cash_release_receipt_doc_list"
                                    class="block w-full mb-1 secondary-btn cash-release-btn" target="_blank"
                                    :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1' + documentId + '&action=download&ncc=1`"
                                >
                                    {{ ++index }}. Cash Release receipt
                                </a>
                                <a
                                    v-if="obj.receipt_list && obj.receipt_list.length > 0"
                                    v-for="(documentId, index) in obj.receipt_list"
                                   class="block w-full mb-1 secondary-btn" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1`"
                                >
                                    {{ ++index }}. Receipt
                                </a>
                                <button
                                    v-if="obj.status_id == 1655 || obj.status_id == 1687"
                                    @click="downloadCashReleaseReceipt(obj)"
                                    class="block w-full mb-1 secondary-btn"
                                >
                                    Release Receipt
                                </button>
                            </td>
                            <td>
                                <button
                                    @click="openModal('view_bank_transfer', obj)"
                                    data-modal-toggle="#show_bank_transfer_details_modal"
                                    class="block w-full mb-1 secondary-btn view-transfer-btn"
                                    v-if="obj.bitrix_bank_transfer_id"
                                >
                                    View Transfer
                                </button>
                                <button
                                    @click="openModal('create_bank_transfer', obj)"
                                    data-modal-toggle="#create_bank_transfer_form_modal"
                                    class="block w-full mb-1 secondary-btn create-transfer-btn"
                                    v-if="page_data.permission === 'full_access' && obj.payment_mode_id === '1869' && !obj.bitrix_bank_transfer_id"
                                >
                                    Create Transfer
                                </button>
                                <button class="mt-2 btn btn-sm btn-outline btn-success" v-if="obj.charge_extra_to_client === '1990' && !obj.has_offer_generated" @click="createOffer(obj)">Create Offer For Extra Charges</button>
                                <div v-if="page_data.permission === 'full_access'">
                                    <div v-if="!obj.sage_transaction_type && !obj.sage_transaction_type_id">
                                        <a class="block w-full mb-1 secondary-btn"
                                           :href="`https://10.0.1.17/CRESCOSage/AP/APCashRequisition?blockId=105&crId=${obj.id}`" target="_blank"
                                        >
                                            Book Misc Payment
                                        </a>
                                        <a class="block w-full mb-1 secondary-btn"
                                           :href="`https://10.0.1.17/CRESCOSage/AP/APBankEntry?blockId=105&crId=${obj.id}`" target="_blank"
                                        >
                                            Bank Entry
                                        </a>
                                    </div>
                                    <div v-else>
                                        <div v-if="obj.sage_transaction_type === 'Bank Entry' && obj.sage_transaction_type_id === '2354'">
                                            <a class="block w-full mb-1 secondary-btn"
                                               :href="`https://10.0.1.17/CRESCOSage/AP/APBankEntry?blockId=105&crId=${obj.id}`" target="_blank"
                                            >
                                                View Bank Entry
                                            </a>
                                        </div>
                                        <div v-else>
                                            <a class="block w-full mb-1 secondary-btn view-transfer-btn"
                                               :href="`https://10.0.1.17/CRESCOSage/AP/APCashRequisition?blockId=105&crId=${obj.id}`" target="_blank"
                                            >
                                                View Misc Payment
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="3" class="text-sm font-bold text-center text-black">Total per currency</td>
                            <td colspan="1" class="text-right text-neutral-800">
                                <div v-for="(amount, currency) in groupedByCurrency">{{ formatAmount(amount) }} <span class="font-bold text-black">{{ currency }} </span></div>
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

                <!-- Right Section: Total as per Reporting Currency -->
                <div class="flex items-center justify-center text-right text-dark">
                    <span class="mr-2 tracking-tight">Total as per reporting currency ({{ currency }}):</span>
                    <span class="font-black">
                        {{ formatAmount(totalAsPerReportingCurrency) }} {{ currency }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- show bank transfer detail modal -->
    <view-bank-transfer-details-modal
        :obj_id="selected_obj.bitrix_bank_transfer_id"
        v-if="is_view_bank_transfer_details_modal"
        @closeModal="closeModal"
    />
    <!-- create transfer form modal -->
    <create-bank-transfer-form-modal
        :obj="selected_obj"
        :bitrix_bank_transfer_company_ids="page_data.bitrix_bank_transfer_company_ids"
        v-if="is_create_bank_transfer_form_modal"
        type="cashRequest"
        @closeModal="closeModal"
    />
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "cash-requests",
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
                payment_mode: "",
                charge_to_client: "",
                charge_to_account: "",
                search: "",
                is_warning: false,
            },
            page_filters: [
                {
                    key: "status",
                    name: "Status",
                    field_id: "PROPERTY_943",
                    values: {}
                },
                {
                    key: "payment_mode",
                    name: "Payment Mode",
                    field_id: "PROPERTY_1088",
                    values: {}
                },
                {
                    key: "charge_to_client",
                    name: "Charge to Client",
                    field_id: "PROPERTY_1215",
                    values: {}
                },
                {
                    key: "charge_to_account",
                    name: "Charge to Account",
                    field_id: "PROPERTY_1243",
                    values: {}
                },
            ],
            totalAsPerReportingCurrency: 0,
            active_status_filter_ids : [1651, 1652, 1653, 1655, 1659, 1687],
            selected_obj: null,
            is_view_bank_transfer_details_modal: false,
            is_create_bank_transfer_form_modal: false,
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
                    if (filter.key === "status"){
                        // Filter values based on allowed IDs
                        filter.values = Object.entries(response.result.L.DISPLAY_VALUES_FORM)
                            .filter(([key]) => this.active_status_filter_ids.includes(parseInt(key))) // Keep only allowed IDs
                            .reduce((obj, [key, value]) => {
                                obj[key] = value;
                                return obj;
                            }, {});
                    }
                    else {
                        filter.values = response.result.L.DISPLAY_VALUES_FORM;
                    }

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
                action: "getCashReports",
                categories: JSON.stringify(this.filters.category_id === "" ? this.page_data.bitrix_list_categories.map((obj) => obj.bitrix_category_id) : [this.filters.category_id]),
                sage_companies: JSON.stringify(this.filters.sage_company_id === "" ? this.page_data.bitrix_list_sage_companies.map((obj) => obj.bitrix_sage_company_id) : [this.filters.sage_company_id])
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                this.data = response.result;
                this.data.forEach((item) => {
                    item.doc_for_bank_list = [];
                    item.other_document_list = [];
                    item.cash_release_receipt_doc_list = [];
                    item.receipt_list = [];

                    if (item.doc_for_bank){
                        item.doc_for_bank_list = item.doc_for_bank.split(",");
                    }
                    if (item.other_documents){
                        item.other_document_list = item.other_documents.split(",");
                    }
                    if (item.cash_release_receipt_docs){
                        item.cash_release_receipt_doc_list = item.cash_release_receipt_docs.split(",");
                    }
                    if (item.receipt_id){
                        item.receipt_list = item.receipt_id.split(",");
                    }

                    if(item.supplier_id || item.supplier_crm_type || item.supplier_name) {
                        console.log('supplier_id', item.supplier_id)
                        console.log('supplier_crm_type', item.supplier_crm_type)
                        console.log('supplier_name', item.supplier_name)
                    }
                    if (item.linked_contact_id){
                        console.log('linked_contact_id', item.linked_contact_id)
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
        showBankTransferDetails(){},
        showCreateNewBankTransferModal(){},
        createOffer(){},
        openModal(type, obj){
            this.selected_obj = obj;
            if(type === 'view_bank_transfer'){
                this.is_view_bank_transfer_details_modal = true
            }
            if(type === 'create_bank_transfer'){
                this.is_create_bank_transfer_form_modal = true
            }
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
        downloadCashReleaseReceipt(item){
            var fileName = `${item.amount_given} | ${item.currency} - ${item.requested_by_name} - Cash Request Receipt.pdf`;

            axios({
                url: `/cash-request/download-released-receipt`,
                method: 'POST',
                responseType: 'arraybuffer',
                data: {
                    'requestId': item.id,
                    'requestCreateDate': DateTime.fromSQL(item.date_create).toFormat('dd LLL yyyy'),
                    'requestPaymentDate': DateTime.fromISO(item.payment_date).toFormat('dd LLL yyyy'),
                    'releaseDate': DateTime.fromISO(item.released_date).toFormat('dd LLL yyyy'),
                    'requestedBy': item.requested_by_name,
                    'releasedBy': item.released_by, //can be any employee not just accountant
                    'project': item.project_name,
                    'company': item.company_name,
                    'remarks': item.detail_text,
                    'amountReceived': item.amount_given,
                    'currency': item.currency,
                    'cashReleaseType': item.status_id == 1687 ? 2 : 1, // Partial cash released
                    'balance': item.amount
                },
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/pdf'
                },
            }).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();
            }).catch(error => {
                this.errorToast(error.response.data.message)
            })
        },
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            const searchTerm = this.filters.search?.toLowerCase() || '';
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.id, item.amount, item.name, item.detail_text,
                    item.project_id, item.sage_payment_reference_id, item.company_name,
                    item.status_text, item.requested_by_name, item.cash_release_location,
                    item.project_name,
                ].some(field => field?.toLowerCase().includes(searchTerm));
                // Filter by status
                const matchesStatus = this.filters.status ? item.status_id === this.filters.status : true;
                // Filter by warning
                const matchesWarning = this.filters.is_warning ? this.isWarning(item, today) : true;
                // Filter by payment mode
                const matchesPaymentMode = this.filters.payment_mode ? item.payment_mode_id === this.filters.payment_mode : true
                // Filter by charge to client
                const matchesChargeToClient = this.filters.charge_to_client ? item.charge_extra_to_client_id === this.filters.charge_to_client : true
                // Filter by chargeToAccount
                const matchesChargeToAccount = this.filters.charge_to_account ? item.charge_to_running_account_id === this.filters.charge_to_account : true;

                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesWarning && matchesPaymentMode && matchesChargeToClient  && matchesChargeToAccount;
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
    created() {
        let startDate = null;
        let endDate = null;
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get("search")){
            this.filters.search = urlParams.get("search");
        }

        if (urlParams.get('date')) {
            const parsedDate = DateTime.fromFormat(urlParams.get('date'), 'dd.MM.yyyy');
            if (parsedDate.isValid) {
                let newDateRange = [
                    parsedDate.startOf('month').toISODate(),
                    parsedDate.endOf('month').toISODate(),
                ];
                // Set dateRange on the child component via ref
                this.$nextTick(() => {
                    this.$refs.filters?.setDateRangeExternally?.(newDateRange);
                });
                startDate = parsedDate.toFormat('yyyy-MM-dd');
                endDate = parsedDate.toFormat('yyyy-MM-dd');

                // Call getPageData with formatted date
                this.getPageData(startDate, endDate);
            }
        }
        this.sharedState.bitrix_user_id = this.page_data.user.bitrix_user_id;
        this.sharedState.bitrix_webhook_token = this.page_data.user.bitrix_webhook_token;
    }
}
</script>
<style scoped>

</style>
