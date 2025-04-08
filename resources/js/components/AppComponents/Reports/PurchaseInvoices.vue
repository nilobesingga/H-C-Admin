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
                            class="w-56 select select-sm select-input"
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
                        <tr class="tracking-tight text-center">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[60px]">Id</th>
                            <th class="sticky top-0 w-[150px] text-left">Receiver</th>
                            <th class="sticky top-0 w-[100px]">Invoice Date</th>
                            <th class="sticky top-0 w-[100px]">Due Date</th>
                            <th class="sticky top-0 w-[100px]" data-tooltip="#Payment_Schedule_tooltip">
                                Schedule <i class="ki-outline ki-information-2"></i>
                                <div class="transition-opacity duration-300 tooltip" id="Payment_Schedule_tooltip">Payment Schedule</div>
                            </th>
                            <th class="sticky top-0 w-[130px]">SAGE Status</th>
                            <th class="sticky top-0 w-[100px]">Status</th>
                            <th class="sticky top-0 w-[125px] text-right">Invoice Amount</th>
                            <th class="sticky top-0 w-[125px] text-left">Invoice Number</th>
                            <th class="sticky top-0 w-[150px] text-left">Project</th>
                            <th class="sticky top-0 w-[80px]" data-tooltip="#charge_to_client_tooltip">
                                Charge <i class="ki-outline ki-information-2"></i>
                                <div class="transition-opacity duration-300 tooltip" id="charge_to_client_tooltip">Charge to Client</div>
                            </th>
                            <th class="sticky top-0 w-[130px] text-left whitespace-normal break-words" data-tooltip="#Request_By_Remarks">
                                Request By <i class="ki-outline ki-information-2"></i> <i class="ki-outline ki-exit-down"></i>
                                <div class="transition-opacity duration-300 tooltip" id="Request_By_Remarks">Request By & Remarks</div>
                            </th>
                            <th class="sticky top-0 w-[100px]">Documents</th>
                            <th class="sticky top-0 w-[120px]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="h-full text-xs tracking-tight text-center">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-neutral-800">
                            <td>{{ ++index }}</td>
                            <td><a class="btn btn-link !text-neutral-800 hover:!text-brand-active" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/104/element/0/' + obj.id  + '/?list_section_id='">{{ obj.id }}</a></td>
                            <td class="text-left">{{ obj.supplier_name }}</td>
                            <td>{{ formatDate(obj.invoice_date) }}</td>
                            <td><div :class="[isBitrixStatusWarning(obj) ? 'badge badge-warning' : '']">{{ formatDate(obj.due_date) }}</div></td>
                            <td><span v-if="obj.payment_schedule_date" class="font-bold text-black">{{ formatDate(obj.payment_schedule_date)}}</span></td>
                            <td>
                                <div :class="[isSageStatusWarning(obj) ? 'badge badge-warning' : '']">
                                    <div v-if="obj.sage_status_text">{{ obj.sage_status_text }}</div>
                                    <div v-if="obj.sage_batch_id" class="text-neutral-600">({{ obj.sage_batch_id }})</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span> {{ obj.status_text }} </span>&nbsp
                                    <span v-if="obj.sage_payment_date"> {{ formatBitrixDate(obj.sage_payment_date) }} </span>
                                    <div v-if="obj.payment_reference_id"> {{ obj.payment_reference_id }} </div>
                                </div>
                            </td>
                            <td class="text-right">{{ formatAmount(obj.amount) }} <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td class="text-left">{{ obj.invoice_number }}</td>
                            <td class="text-left"><a class="btn btn-link !text-xs !text-neutral-800 hover:!text-brand-active" target="_blank" :href="getBitrixProjectLink(obj)">{{ obj.project_name }}</a></td>
                            <td>{{ getChargeExtraToClientValue(obj.charge_extra_to_client, page_data.identifier) }}</td>
                            <td class="text-left break-words whitespace-normal group/request">
                                <div class="font-bold text-black">{{ obj.requested_by_name }}</div>
                                <div class="line-clamp-2 group-hover/request:line-clamp-none">
                                    {{ obj.detail_text }}
                                </div>
                            </td>
                            <td>
                                <a v-for="(documentId, index) in obj.document_list"
                                   class="block mb-1 secondary-btn" target="_blank"
                                   :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1'`"
                                >
                                    Doc {{ ++index }}
                                </a>
                            </td>
                            <td>
                                <button
                                    @click="openModal('view_bank_transfer', obj)"
                                    data-modal-toggle="#show_bank_transfer_details_modal"
                                    class="block w-full mb-1 secondary-btn"
                                    v-if="obj.bitrix_bank_transfer_id"
                                >
                                    <span>View Transfer</span>
                                </button>
                                <button
                                    @click="openModal('create_bank_transfer', obj)"
                                    data-modal-toggle="#create_bank_transfer_form_modal"
                                    class="block w-full mb-1 secondary-btn"
                                    v-if="page_data.permission === 'full_access' && (!obj.bitrix_bank_transfer_id && obj.status_id !== '1619' && obj.status_id !== '1620' && obj.status_id !== '1871' && obj.sage_status_text === 'Booked In Sage')"
                                >
                                    <span>Create Transfer</span>
                                </button>
                                <a
                                    class="block w-full mb-1 secondary-btn"
                                    target="_blank"
                                    :href="`https://10.0.1.17/CRESCOSage/AP/APInvoice?blockId=104&purchaseId=${obj.id}`"
                                    v-if="page_data.permission === 'full_access' && (obj.sage_status !== '1863' && obj.status_id !== '1619' && obj.status_id !== '1620')"
                                >
                                    Book In Sage
                                </a>
                                <a
                                    class="block w-full mb-1 secondary-btn"
                                    target="_blank"
                                    :href="`https://10.0.1.17/CRESCOSage/AP/APInvoice?blockId=104&purchaseId=${obj.id}`"
                                    v-if="obj.sage_status === '1863'"
                                >
                                    View In Sage
                                </a>
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="8" class="text-sm font-bold text-center text-black">Total per currency</td>
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
            is_view_bank_transfer_details_modal: false,
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
        async getPageData(startDate = null, endDate = null){
            let dateRange = JSON.parse(localStorage.getItem('dateRange'));
            this.data = [];
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                startDate: startDate ? startDate : dateRange[0],
                endDate: endDate ? endDate : dateRange[1],
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
                let newDateRange = [
                    parsedDate.startOf('month').toISODate(),
                    DateTime.now().toISODate()
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
