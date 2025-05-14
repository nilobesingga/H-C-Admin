<template>
    <div class="px-3 container-fluid">
        <div class="pt-4 pb-4 reports-header-filters">
            <div class="flex items-center justify-between">
                <!-- Date Range Picker Text on the Left -->
                <div class="flex flex-col justify-start w-1/2 text-2xl font-bold tracking-tight text-black">
                    {{ dateRangePickerText }}
                </div>
                <!-- Right Side Controls -->
                <div class="flex flex-wrap items-center justify-end w-1/2 gap-2">
                    <!-- Date Picker -->
                    <div class="flex flex-col w-64">
                        <VueDatePicker
                            v-model="selected_date_range"
                            model-type="yyyy-MM-dd"
                            auto-apply
                            range
                            :multi-calendars="{ solo: true }"
                            placeholder="Select Date"
                            :enable-time-picker="false"
                            format="dd MMM yyyy"
                            :clearable="false"
                            class="rounded-none"
                            @update:model-value="getData(false)"
                            :disabled="loading"
                        />
                    </div>
                    <!-- Period Select -->
                    <div class="flex flex-col w-36">
                        <select class="select select-input" v-model="selected_period" @change="getData(false)" :disabled="loading">
                            <option v-for="(period, index) in periods" :key="index" :value="period.key">
                                {{ period.value }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid gap-2">
            <!-- Filters Section -->
            <div class="flex flex-wrap items-center gap-2 reports-only-filters">
                <div class="flex gap-2">
                    <!-- Clearing Status -->
                    <div class="flex flex-shrink-0">
                        <select
                            class="w-48 select select-sm select-input"
                            v-model="filters.clearing_status"
                        >
                            <option value="" selected>Filter by Clearing Status</option>
                            <option value="pending">Pending</option>
                            <option value="cleared">Cleared</option>
                            <option value="reversed">Reversed</option>
                            <option value="updated">Updated</option>
                            <option value="null">Null</option>
                        </select>
                    </div>
                    <!-- Transaction Category -->
                    <div class="flex flex-shrink-0 w-[230px]">
                        <select
                            class="select select-sm select-input w-[230px]"
                            v-model="filters.transaction_category"
                        >
                            <option value="" selected>Filter by Transaction Category</option>
                            <option value="purchase">Purchase</option>
                            <option value="card_loading">Card Loading</option>
                            <option value="account_verification">Account Verification</option>
                            <option value="reversal">Reversal</option>
                            <option value="deposit">Deposit</option>
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
                <div class="flex flex-shrink-0">
                    <button
                        class="main-btn !bg-white !border !py-2 !px-5 !min-w-[120px] !text-sm focus:!border-tec-active"
                        :disabled="loading"
                        @click="getPageData(true)"
                    >
                        Sync Transactions
                    </button>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow h-full overflow-auto border shadow-md reports-table-container border-brand">
                <table class="table w-full text-xs align-middle table-fixed c-table table-border" :class="filteredData.length === 0 ? 'h-full' : ''">
                    <thead>
                        <tr class="font-medium text-center bg-gray-800 text-white">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[280px]">ID</th>
                            <th class="sticky top-0 w-[150px]">Transactions</th>
                            <th class="sticky top-0 w-[150px]">Clearing</th>
                            <th class="sticky top-0 w-[180px]">Status</th>
                            <th class="sticky top-0 w-[150px]">ERP</th>
                            <th class="sticky top-0 w-[140px]">Card</th>
                            <th class="sticky top-0 w-[200px]">Other</th>
                            <th class="sticky top-0 w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs tracking-tight text-center">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 hover:bg-gray-50">
                            <td class="font-medium">{{ index + 1 }}</td>
                            <td class="text-left p-1.5">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Table ID:</span>
                                    <strong>{{ obj.id }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Qashio ID:</span>
                                    <strong>{{ obj.qashioId }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">RRN:</span>
                                    <span>{{ obj.rrn }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Parent ID:</span>
                                    <span>{{ obj.parentId }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">ID:</span>
                                    <strong>{{ obj.stringId }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="text-center text-lg mt-5"><strong>Cetrix</strong></div>
                                <div class="flex justify-between py-0.5 mt-5">
                                    <span class="font-semibold">Last Four Digits:</span>
                                    <span>{{ obj.bitrix_qashio_credit_card ? obj.bitrix_qashio_credit_card.last_four_digits : '' }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Category:</span>
                                    <span>{{ obj.bitrix_qashio_credit_card ? obj.bitrix_qashio_credit_card.category : '' }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Sage Company:</span>
                                    <span class="text-[09px]">{{ obj.bitrix_qashio_credit_card ? obj.bitrix_qashio_credit_card.sage_company : '' }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Cash Requisition Id:</span>
                                    <span><a class="btn btn-link !text-black hover:!text-brand-active" target="_blank" :href="'https://crm.cresco.ae/bizproc/processes/105/element/0/' + obj.bitrix_cash_request_id  + '/?list_section_id='">{{obj.bitrix_cash_request_id }}</a></span>
                                </div>
                                <hr class="my-1 border-gray-400">
                            </td>
                            <td class="text-left p-1.5">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Amount:</span>
                                    <strong>{{ formatAmount(obj.transactionAmount) }} {{ obj.transactionCurrency }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Fee:</span>
                                    <span>{{ obj.transactionFeeAmount }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Currency:</span>
                                    <span>{{ obj.transactionCurrency }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Time:</span>
                                    <span>{{ formatDateTime24HoursISO(obj.transactionTime) }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Status:</span>
                                    <strong>{{ obj.transactionStatus }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Description:</span>
                                    <span>{{ obj.transactionDescription }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Type:</span>
                                    <span>{{ obj.transactionType }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Category:</span>
                                    <span>{{ obj.transactionCategory }}</span>
                                </div>
                            </td>
                            <td class="text-left p-1.5">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Amount:</span>
                                    <span class="!text-primary">{{ formatAmount(obj.clearingAmount) }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Fee:</span>
                                    <span>{{ obj.clearingFee }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Status:</span>
                                    <strong :class="obj.clearingStatus === 'cleared' ? 'badge badge-success' : obj.clearingStatus === 'pending' ? 'badge badge-warning' : ''">{{ obj.clearingStatus }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">VAT:</span>
                                    <span>{{ formatAmount(obj.vatAmount) }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Cleared:</span>
                                    <span>{{ formatDateTime24HoursISO(obj.clearedAt) }}</span>
                                </div>
                            </td>
                            <td class="text-left p-1.5">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Billing Amount:</span>
                                    <strong>{{ formatAmount(obj.billingAmount) }} {{ obj.billingCurrency }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Billing Currency:</span>
                                    <span>{{ obj.billingCurrency }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Code:</span>
                                    <strong>{{ obj.status_code }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Message:</span>
                                    <span>{{ obj.messageType }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Settlement:</span>
                                    <span>{{ obj.settlementStatus }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Approval:</span>
                                    <span>{{ obj.approvalStatus }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                            </td>
                            <td class="text-left p-1.5">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Sync Status:</span>
                                    <strong>{{ obj.erpSyncStatus }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Sync Type:</span>
                                    <span>{{ obj.erpSyncType }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Supplier:</span>
                                    <span class="text-wrap">{{ obj.erpSupplierName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Supplier ID:</span>
                                    <span class="text-wrap">{{ obj.erpSupplierRemoteId }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Tax Rate:</span>
                                    <span>{{ obj.erpTaxRateName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Tax Rate ID:</span>
                                    <span>{{ obj.erpTaxRateRemoteId }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Account:</span>
                                    <span>{{ obj.erpChatOfAccountName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Account ID:</span>
                                    <span>{{ obj.erpChatOfAccountRemoteId }}</span>
                                </div>
                            </td>
                            <td class="text-left p-1.5">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Name:</span>
                                    <strong class="text-wrap">{{ obj.cardName }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Last Four:</span>
                                    <span>{{ obj.cardLastFour }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Holder:</span>
                                    <span>{{ obj.cardHolderName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Email:</span>
                                    <span class="text-wrap">{{ obj.cardHolderEmail }}</span>
                                </div>
                            </td>
                            <td class="text-left p-1.5">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Memo:</span>
                                    <strong class="text-wrap">{{ obj.memo }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Vendor TRN:</span>
                                    <span>{{ obj.vendorTrn }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">PO Number:</span>
                                    <span>{{ obj.purchaseOrderNumber }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Visible:</span>
                                    <strong>{{ obj.visible }}</strong>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Exclude Sync:</span>
                                    <span>{{ obj.excludeFromSync }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Sync Errors:</span>
                                    <span>{{ obj.syncErrors }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Segments:</span>
                                    <span>{{ obj.segments }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Merchant:</span>
                                    <span class="text-wrap text-[9px]">{{ obj.merchantName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Expense:</span>
                                    <span class="text-wrap text-[8px]">{{ obj.expenseCategoryName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Pool Account:</span>
                                    <span>{{ obj.poolAccountName }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Receipts:</span>
                                    <div class="flex flex-col gap-1 p-0">
                                        <a
                                            v-if="obj.receipts && obj.receipts.length > 0"
                                            v-for="(receipt, index) in obj.receipts"
                                            class="block btn-xs secondary-btn other-doc-btn"
                                            target="_blank"
                                            :href="receipt"
                                        >
                                            Receipt {{ index + 1 }}
                                        </a>
                                    </div>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Created:</span>
                                    <span>{{ formatDateTime24HoursISO(obj.createdAt) }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Updated:</span>
                                    <span>{{ formatDateTime24HoursISO(obj.updatedAt) }}</span>
                                </div>
                                <hr class="my-1 border-gray-400">
                                <div class="flex justify-between py-0.5">
                                    <span class="font-semibold">Line Items:</span>
                                    <span>{{ obj.lineItems }}</span>
                                </div>
                            </td>
                            <td class="text-center p-1.5">
                                <button
                                    v-if="(!obj.bitrix_cash_request_id && obj.transactionCategory === 'purchase') && (obj.clearingStatus === 'pending' || obj.clearingStatus === 'cleared')"
                                    @click="saveCashRequest('create', obj)"
                                    class="block w-full mb-1 secondary-btn"
                                >
                                    Create Request
                                </button>
                                <button
                                    v-if="(obj.bitrix_cash_request_id && obj.transactionCategory === 'purchase') && (obj.clearingStatus === 'cleared' || obj.clearingStatus === 'reversed' || obj.clearingStatus === 'updated')"
                                    @click="saveCashRequest('create', obj)"
                                    class="block w-full mb-1 secondary-btn"
                                >
                                    Update Request
                                </button>
                            </td>
                        </tr>
                        <tr class="h-full table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-md text-red-400 !border-none h-full" colspan="10">
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
            </div>
        </div>
    </div>
</template>

<script>
import {DateTime} from "luxon";
import _, {debounce} from "lodash";

export default {
    name: "qashio-transactions",
    props: ['page_data'],
    data(){
        return {
            periods: [
                { key: "this_week", value: "This week" },
                { key: "last_week", value: "Last week" },
                { key: "this_month", value: "This month" },
                { key: "last_month", value: "Last month" },
                { key: "last_month_plusplus", value: "This month ++" },
                { key: "last_60_days_plusplus", value: "Last 60 days ++" },
                { key: "this_year", value: "This Year" },
                { key: "last_year", value: "Last Year" },
            ],
            selected_period: 'last_60_days_plusplus',
            data: [],
            loading: false,
            filters: {
                from_date: null,
                to_date: null,
                clearing_status: "",
                transaction_category: "",
                search: null,
            },
            qashio_credit_cards: [],
        }
    },
    methods: {
        async getData() {
            await this.fetchQashioCreditCardsFromBitrix();
            await this.getPageData();
        },
        async fetchQashioCreditCardsFromBitrix() {
            this.loading = true;
            const bitrixUserId = this.page_data.user.bitrix_user_id ? this.page_data.user.bitrix_user_id : null;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token ? this.page_data.user.bitrix_webhook_token : null;
            const endpoint = 'crm.company.reports_v2';
            const requestData = {
                action: "getQashioCreditCards",
            }
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    this.loading = false
                    this.qashio_credit_cards = response.result
                }
            } catch (error) {
                if (error.status === 500){
                    this.errorToast('Something went wrong! Please refresh the page or contact support if this keeps happening.')
                }
            }
        },
        async getPageData(isSync = false){
            this.loading = true;
            this.filters.from_date = this.selected_date_range[0];
            this.filters.to_date = this.selected_date_range[1];
            this.data = []
            try {
                const response = await axios({
                    url: `/qashio/get-data`,
                    method: 'POST',
                    data: {
                        filters: this.filters,
                        is_sync: isSync
                    }
                });

                this.data = response.data.data;

                // Map through this.data and add matching bitrix_qashio_credit_cards
                this.data = this.data.map(item => {
                    const matchingCard = this.qashio_credit_cards.find(card =>
                        card.last_four_digits === item.cardLastFour
                    );
                    return {
                        ...item,
                        bitrix_qashio_credit_card: matchingCard || null
                    };
                });
                this.loading = false

                if (isSync) {
                    this.successToast('Qashio data sync successfully');
                }
            } catch (error) {
                this.loading = false
                if (this.appEnv === 'local') {
                    this.errorToast(error.response.data.exception);
                } else {
                    this.errorToast(error.response.data.message);
                }
                throw error; // Rethrow to handle in getData
            }
        },
        // debouncedSearch: debounce(function(){
        //     this.getData(false);
        // }, 500),
        updateDateRangeForPeriod(period) {
            const now = DateTime.now();
            let newDateRange;

            switch (period) {
                case "this_week":
                    newDateRange = [
                        now.startOf("week").toISODate(),
                        now.endOf("week").toISODate()];
                    break;
                case "last_week":
                    newDateRange = [
                        now.minus({weeks: 1}).startOf("week").toISODate(),
                        now.minus({weeks: 1}).endOf("week").toISODate()
                    ];
                    break;
                case "this_month":
                    newDateRange = [
                        now.startOf("month").toISODate(),
                        now.endOf("month").toISODate()
                    ];
                    break;
                case "last_month":
                    newDateRange = [
                        now.minus({months: 1}).startOf("month").toISODate(),
                        now.minus({months: 1}).endOf("month").toISODate()
                    ];
                    break;
                case "last_month_plusplus":
                    newDateRange = [
                        now.startOf("month").toISODate(),
                        now.plus({ years: 3 }).endOf("year").toISODate(),
                    ];
                    break;
                case "last_60_days_plusplus":
                    newDateRange = [
                        now.minus({days: 60}).startOf("day").toISODate(),
                        now.plus({ years: 3 }).endOf("year").toISODate()
                    ];
                    break;
                case "this_year":
                    newDateRange = [
                        now.startOf("year").toISODate(),
                        now.endOf("year").toISODate()
                    ];
                    break;
                case "last_year":
                    newDateRange = [
                        now.minus({years: 1}).startOf("year").toISODate(),
                        now.minus({years: 1}).endOf("year").toISODate()
                    ];
                    break;
                default:
                    newDateRange = [
                        DateTime.now().toISODate(),
                        DateTime.now().toISODate()
                    ];
            }
            this.selected_date_range = newDateRange;
        },
        saveCashRequest(type, obj){
            axios({
                url: `/qashio/transaction/save/${type}`,
                method: 'POST',
                data: obj,
            }).then(response => {
                if (response.data){
                    this.successToast(response.data.message)
                    this.getPageData();
                }
            }).catch(error => {
                console.log(error)
            })
        }
    },
    computed: {
        dateRangePickerText(){
            if (!this.selected_date_range[0] || !this.selected_date_range[1]) {
                return "No date selected";
            }
            const formattedStart = DateTime.fromISO(this.selected_date_range[0]).toFormat("d MMM yyyy");
            const formattedEnd = DateTime.fromISO(this.selected_date_range[1]).toFormat("d MMM yyyy");
            return `${formattedStart} - ${formattedEnd}`;
        },
        filteredData() {
            let today = DateTime.now();
            const searchTerm = this.filters.search?.toLowerCase() || '';
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.string_id, item.parentId, item.rrn, item.qashioId,
                ].some(field => field?.toLowerCase().includes(searchTerm));

                // Filter by Clearing Status
                const matchesStatus = this.filters.clearing_status === 'null' ? item.clearingStatus === null : this.filters.clearing_status ? item.clearingStatus === this.filters.clearing_status : true;

                // Filter by Transaction Category
                const matchesCategory = this.filters.transaction_category ? item.transactionCategory === this.filters.transaction_category : true;

                // Return true only if all filters match
                return matchesSearch && matchesStatus && matchesCategory;
            });
        },
    },
    watch: {
        selected_period(){
            this.updateDateRangeForPeriod(this.selected_period);
        },
        // 'filters.search': {
        //     handler: 'debouncedSearch',
        //     immediate: false
        // }
    },
    created() {
        this.updateDateRangeForPeriod(this.selected_period);
    },
    mounted() {
        this.getData(false);
    }
}
</script>

<style scoped>
.table {
    border-collapse: collapse;
    width: 100%;
}
.table td {
    border: 1px solid #e5e7eb;
    padding: 6px 8px;
    vertical-align: top;
}

.table-no-data-available td {
    background-color: #fef2f2;
}

.font-semibold {
    font-weight: 600;
    color: #1f2937;
}

strong {
    font-weight: 700;
    color: #111827;
}

.text-wrap {
    word-break: break-word;
    white-space: normal;
}
.badge-success {
    background-color: rgb(119, 214, 146);
    --tw-text-opacity: 1;
    color: rgb(38 38 38 / var(--tw-text-opacity, 1));
}
</style>
