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
                <div class="flex flex-grow gap-2">
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
                <!-- All Overdue Invoices -->
                <div class="flex flex-shrink-0 ml-5">
                    <label class="form-label flex items-center gap-2.5">
                        <input class="checkbox checkbox-sm" name="check" type="checkbox" value="1" v-model="filters.is_all_overdue" @change="getData" />All Overdue
                    </label>
                </div>

            </div>
            <!-- table -->
            <div class="relative flex-grow h-full overflow-auto border shadow-md reports-table-container border-brand">
                <table class="table w-full text-xs align-middle table-fixed c-table table-border" :class="filteredData.length === 0 ? 'h-full' : ''">
                    <thead>
                    <tr class="tracking-tight text-center">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[50px]">Id</th>
                            <th class="sticky top-0 w-[150px] text-left">Company</th>
                            <th class="sticky top-0 w-[100px]">Contact</th>
                            <th class="sticky top-0 w-[80px] text-right">Amount</th>
                            <th class="sticky top-0 w-[80px]">Status</th>
                            <th class="sticky top-0 w-[200px] text-left">CRM</th>
                            <th class="sticky top-0 w-[70px]">Invoice Date</th>
                            <th class="sticky top-0 w-[70px]">Due Date</th>
                            <th class="sticky top-0 w-[70px]">Date Paid</th>
                            <th class="sticky top-0 w-[110px] text-left">Sage Reference</th>
                            <th class="sticky top-0 w-[80px]">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="h-full text-xs tracking-tight text-center">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-neutral-800">
                            <td>{{ index + 1 }}</td>
                            <td><a target="_blank" class="btn btn-link !text-neutral-800 hover:!text-brand-active" :href="`https://crm.cresco.ae/crm/invoice/show/${obj.id}/`">{{ obj.id }}</a></td>
                            <td class="text-left"><a target="_blank" class="btn btn-link !text-neutral-800 hover:!text-brand-active" :href="`https://crm.cresco.ae/crm/company/details/${obj.company_id}/`">{{ obj.company }}</a></td>
                            <td class="text-left"><a target="_blank" class="btn btn-link !text-neutral-800 hover:!text-brand-active" :href="`https://crm.cresco.ae/crm/contact/details/${obj.contact_id}/`">{{ obj.contact }}</a></td>
                            <td class="text-right"><span>{{ formatAmount(obj.price) }}</span> <strong class="font-bold text-black">{{ obj.currency }}</strong></td>
                            <td>{{ obj.status }}</td>
                            <td class="text-left">
                                <div>
                                    <span class="font-bold text-black">Deal: </span>
                                    <a target="_blank" class="btn btn-link !text-neutral-800 hover:!text-brand-active" :href="`https://crm.cresco.ae/crm/deal/details/${obj.deal_id}/`">{{ obj.deal }}</a>
                                </div>
                                <div>
                                    <span class="font-bold text-black">Quote: </span>
                                    <a class="btn btn-link !text-neutral-800 hover:!text-brand-active" target="_blank" :href="`https://crm.cresco.ae/crm/quote/show/${obj.quote_id}/`">View</a>
                                </div>
                            </td>
                            <td><div :class="isNotBooked(obj) ? 'badge badge-warning' : ''">{{ formatDate(obj.date_bill)  }}</div></td>
                            <td>
                                <div :class="isOverDueDate(obj) ? 'mb-1 badge badge-warning' : ''">{{ formatDate(obj.date_pay_before) }}</div>
                                <div v-if="isOverDueDate(obj)" class="text-red-900">Over {{ noOfdaysDue(obj) }} days</div>
                            </td>
                            <td>{{ formatDate(obj.date_payed) }}</td>
                            <td class="text-left">
                                <div v-if="obj.sage_invoice_number">
                                    <span>Invoice #:</span>
                                    <span>{{ obj.sage_invoice_number }}</span>
                                </div>
                                <div v-if="obj.pay_voucher_num">
                                    <span>Payment #:</span>
                                    <span>{{ obj.pay_voucher_num }}</span>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <button class="block w-full mb-1 secondary-btn"
                                v-if="obj.status == 'Booked in SAGE' && obj.payment_status == null"
                                data-modal-toggle="#process_purchase_invoice_details_modal"
                                @click="openModal(obj)">
                                    Send Invoice
                                </button>
                                <button class="block w-full mb-1 secondary-btn"
                                v-else-if="obj.payment_status != null && obj.payment_status != 'completed'"
                                data-modal-toggle="#process_purchase_invoice_details_modal"
                                data-toggle="tooltip" :title="obj.payment_status"
                                @click="openModal(obj)">
                                    Send Remider
                                </button>
                                <button class="block w-full mb-1 secondary-btn"
                                v-else-if="obj.payment_status != null && obj.payment_status == 'completed'"
                                data-modal-toggle="#process_purchase_invoice_details_modal"
                                data-toggle="tooltip" :title="obj.payment_status"
                                @click="openModal(obj)">
                                    View Payment
                                </button>
                                <div v-if="obj.payment_status != null" class="mb-1 text-white capitalize badge" :class="
                                {'bg-blue-500': obj.payment_status === 'initiated',
                                'bg-green-500': obj.payment_status === 'completed',
                                'bg-yellow-500': obj.payment_status === 'pending',
                                'bg-red-500': obj.payment_status === 'canceled',
                                'bg-orange-500': obj.payment_status === 'failed',
                                'bg-orange-500': !['initiated','completed','pending','canceled','failed'].includes(obj.payment_status)
                                }
                                "
                                data-toggle="tooltip" :title="obj.counter"
                                >{{ obj.payment_status }} <i class="ml-1 text-xs ki-outline ki-information-1"></i>
                                </div>
                            </td>
                        </tr>
                        <tr v-show="filteredData.length > 0">
                            <td colspan="4" class="text-sm font-bold text-center text-black">Total per currency</td>
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

    <!-- View Purchase Invoice Details Modal -->
    <process-purchase-invoice-details-modal
        :obj_id="obj_id"
        :obj_data="obj"
        :page_data="page_data"
        v-if="is_process_purchase_invoice_details_modal"
        @closeModal="closeModal"
    />

</template>

<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "sales-invoices",
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
                is_warning: false,
                is_all_overdue: false,
            },
            page_filters: [
                {
                    key: "status",
                    name: "Status",
                    field_id: "",
                    values: {}
                },
                {
                    key: "charge_to_account",
                    name: "Charge to Account",
                    field_id: "UF_CRM_66E151D80CA90",
                    values: {}
                },
            ],
            totalAsPerReportingCurrency: 0,
            is_process_purchase_invoice_details_modal: false,
            obj_id: null,
            payment : []
        }
    },
    methods: {
        async getData() {
            this.loading = true;
            try {
                await this.fetchFiltersValuesFromBitrix();
                await this.getPaymentStatus();
                await this.getPageData();
            } finally {
                this.loading = false;
            }
        },
        async fetchFiltersValuesFromBitrix() {
            const bitrixUserId = this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;
            for (const filter of this.page_filters) {
                try {
                    if(filter.key === 'status'){
                        filter.values = {
                            "P": "Paid",
                            "PP": "Partially Paid",
                            "B": "Booked",
                            "NB": "Not Booked",
                            "C": "Cancelled"
                        }
                    }
                    else {
                        const endpoint = 'crm.invoice.userfield.list';
                        // Pre-format the filter to match Bitrix24 API's expected payload
                        const requestData = {};
                        Object.entries({ FIELD_NAME: filter.field_id }).forEach(([key, value]) => {
                            requestData[`filter[${key}]`] = value;
                        });
                        const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                        filter.values = response.result[0].LIST.reduce((acc, item) => {
                            acc[item.ID] = item.VALUE;
                            return acc;
                        }, {});
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
                action: "getSalesInvoices",
                categories: JSON.stringify(this.filters.category_id === "" ? this.page_data.bitrix_list_categories.map((obj) => obj.bitrix_category_id) : [this.filters.category_id]),
                sage_companies: JSON.stringify(this.filters.sage_company_id === "" ? this.page_data.bitrix_list_sage_companies.map((obj) => obj.bitrix_sage_company_id) : [this.filters.sage_company_id]),
                is_all_overdue: this.filters.is_all_overdue
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                this.data = response.result;
                if(this.payment.length > 0){
                    this.data = this.data.map((item) => {
                        const paymentStatus = this.payment.find((payment) => payment.invoice_id == item.id);
                        return { ...item,
                            payment_status: ((paymentStatus) ? paymentStatus.status : null) ,
                            payment_completed_at : ((paymentStatus) ? paymentStatus.payment_completed_at : null),
                            counter : ((paymentStatus) ? paymentStatus.counter : null)
                        };
                    });
                }
                // console.log("data", this.data);
                await this.calculateTotalAsPerReportingCurrency();
            } catch (error) {
                this.loading = false
            }
        },
        async calculateTotalAsPerReportingCurrency(){
            this.totalAsPerReportingCurrency = await this.calculateTotalInBaseCurrency(this.groupedByCurrency)
        },
        isWarning(item, today){
            const dueDate = DateTime.fromSQL(item.date_pay_before)
            const invoiceDate = DateTime.fromSQL(item.date_bill);
            return ((item.status_id === "2" || item.status_id === "N") && dueDate < today) ||
                ((item.status_id === "N" || item.status_id === "S") && invoiceDate < today);
        },
        isOverDueDate(item){
            const today = DateTime.now();
            const dueDate = DateTime.fromSQL(item.date_pay_before)
            return ((item.status_id === "2" || item.status_id === "N") && dueDate < today);
        },
        isNotBooked(item){
            const today = DateTime.now();
            const invoiceDate = DateTime.fromSQL(item.date_bill);
            return ((item.status_id === "N" || item.status_id === "S") && invoiceDate < today);
        },
        noOfdaysDue(item){
            const today = DateTime.now();
            const dueDate = DateTime.fromSQL(item.date_pay_before)
            return Math.floor(today.diff(dueDate, 'days').days);
        },
        customStatusFilter(item, status){
            if (status === 'P'){
                return item.status_id === "P"
            }
            if (status === 'PP'){
                return item.status_id === "3"
            }
            if (status === 'B'){
                return item.status_id === "2"
            }
            if (status === 'NB'){
                return item.status_id === "N" || item.status_id === "S"
            }
            if (status === 'C'){
                return item.status_id === "D" || item.status_id === "1"
            }
            return true;
        },
        openModal(obj){
            console.log("openModal", obj);
            this.obj_id = obj.id;
            this.obj = obj;
            this.is_process_purchase_invoice_details_modal = true
        },
        closeModal(){
            this.is_process_purchase_invoice_details_modal = false;
            this.selected_obj = null
            this.removeModalBackdrop();
        },
        async getPaymentStatus(){
            await axios.get('payment_status')
            .then((response) => {
                this.payment = response.data.data;
            })
            .catch(error => {
                console.log(error.response)
            });
        }
    },
    computed:{
        filteredData() {
            let today = DateTime.now();
            const searchTerm = this.filters.search?.toLowerCase() || '';
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.id, item.name, item.account_number, item.bank_code,
                    item.contact, item.deal, item.order_number,
                    item.order_topic, item.pay_voucher_num, item.project_id,
                    item.quote, item.sage_invoice_number, item.to_account_name,
                    item.to_account_number, item.to_bank_name, item.to_company_name,
                    item.to_iban,
                ].some(field => field?.toLowerCase().includes(searchTerm));
                // Filter by status
                const matchesStatus = this.filters.status ? this.customStatusFilter(item, this.filters.status) : true;
                // Filter by chargeToAccount
                const matchesChargeToAccount = this.filters.charge_to_account ? item.charge_to_running_account_id === this.filters.charge_to_account : true;
                // Filter by warning
                const matchesWarning = this.filters.is_warning ? this.isWarning(item, today) : true;
                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesChargeToAccount && matchesWarning;
            });
        },
        groupedByCurrency() {
            const groupedByCurrency = _.groupBy(this.filteredData, 'currency');
            const summedByCurrency = _.mapValues(groupedByCurrency, (group) =>
                _.reduce(group, (sum, transaction) => sum + parseFloat(transaction.price), 0)
            );

            return summedByCurrency;
        },
        warningCount() {
            let today = DateTime.now();
            return this.filteredData.filter(item => this.isWarning(item, today)).length;
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
    async created() {
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
                    DateTime.now().toISODate()
                ];
                // Set dateRange on the child component via ref
                this.$nextTick(() => {
                    this.$refs.filters?.setDateRangeExternally?.(newDateRange);
                });
                startDate = parsedDate.toFormat('yyyy-MM-dd');
                endDate = parsedDate.toFormat('yyyy-MM-dd');

                // Call getPageData with formatted date
                await this.getPageData(startDate, endDate);
            }
        }
    }
}
</script>
<style scoped>

</style>
