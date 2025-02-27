<template>
    <div class="container-fluid px-3">
        <div class="pb-4 pt-4">
            <div class="flex items-center justify-between">
                <!-- Date Range Picker Text on the Left -->
                <div class="flex flex-col justify-start w-1/2 text-black font-bold text-2xl tracking-tight">
                    {{ dateRangePickerText }}
                </div>

                <!-- Right Side Controls -->
                <div class="flex items-center gap-2 flex-wrap w-1/2 justify-end">
                    <!-- Currency Select -->
                    <div class="flex flex-col w-24">
                        <select class="select select-input" v-model="selected_currency" @change="getData">
                            <option v-for="(currency, index) in currencies" :key="index" :value="currency">{{ currency }}</option>
                        </select>
                    </div>
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
                        />
                    </div>
                    <!-- Period Select -->
                    <div class="flex flex-col w-36">
                        <select class="select select-input" v-model="selected_period">
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
            <div class="flex flex-wrap items-center gap-2 reports-only-filters" v-if="!loading">
                <!-- Filters Section -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex">
                        <select class="select select-sm select-input w-96" v-model="filters.sage_company_code" @change="onSelectCompany">
                            <option value="" selected>Select Company</option>
                            <option v-for="obj in page_data.sage_companies_codes" :key="obj.id" :value="obj.sage_company_code">
                                {{ obj.bitrix_sage_company_name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex">
                        <div v-if="!filters.sage_company_code">
                            <span class="text-danger">Please select a company</span>
                        </div>
                        <div v-if="filters.sage_company_code">
                            <select class="select select-sm select-input w-96" v-model="filters.selected_bank_id" @change="onSelectBank">
                                <option value="" selected>Select Bank Account</option>
                                <option v-for="(obj, index) in company_banks" :key="index" :value="obj.Id">
                                    {{ obj.bankName }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow overflow-auto reports-table-container shadow-md border border-brand h-full">
                <table class="w-full c-table table table-border align-middle text-xs table-fixed">
                    <thead>
                        <tr class="text-center tracking-tight">
                            <th class="sticky top-0"></th>
                            <th class="sticky top-0">Transaction Date</th>
                            <th class="sticky top-0">Entry Date</th>
                            <th class="sticky top-0">Transaction Details</th>
                            <th class="sticky top-0">Transaction Number</th>
                            <th class="sticky top-0">Deposit</th>
                            <th class="sticky top-0">Withdrawal</th>
                            <th class="sticky top-0">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs tracking-tight h-full">
                        <tr class="transition-all duration-300 text-neutral-800 group">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="4">OPENING</td>
                            <td>
                                {{ formatAmount(selected_bank.openingBalance) }}
                                <span>{{ selected_bank.bankCurrency }}</span>
                            </td>
                        </tr>
                        <template v-if="selected_bank.bankDetails && selected_bank.bankDetails.length > 0">
                            <template v-for="(transaction, index) in selected_bank.bankDetails">
                                <tr v-if="transaction.SourceApp !== 'Opening' && transaction.SourceApp !== 'Closing'">
                                    <td>{{ ++index }}</td>
                                    <td>{{ formatDate(transaction.DateRemit) }}</td>
                                    <td>{{ formatDate(transaction.AuditDate) }}</td>
                                    <td class="text-left">{{ transaction.Comments }}</td>
                                    <td>{{ transaction.transactionNo }}</td>
                                    <td class="text-right">{{ formatAmount(transaction.DepositAmt) }} <span>{{ transaction.statemenCurrency }}</span></td>
                                    <td class="text-right">{{ formatAmount(transaction.WithdrawalAmt) }} <span>{{ transaction.statemenCurrency }}</span></td>
                                    <td class="text-right"></td>
                                </tr>
                            </template>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="8" class="text-center">No Transactions</td>
                            </tr>
                        </template>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>TOTAL</td>
                            <td></td>
                            <td>
                                {{ formatAmount(selected_bank.totalDeposit) }}
                                <span>{{ selected_bank.bankCurrency }}</span>
                            </td>
                            <td class="transaction-headings text-right">
                                {{ formatAmount(selected_bank.totalWithdrawal) }}
                                <span>{{ selected_bank.bankCurrency }}</span>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="4">CLOSING</td>
                            <td class="transaction-headings text-right">
                                {{ formatAmount(closingBalance) }}
                                <span>{{ selected_bank.bankCurrency }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="4">REPORTING AMOUNT</td>
                            <td class="text-right">
                                {{ formatAmount(selected_bank.reportingAmount) }}
                                <span>{{ selected_bank.reportCurrency }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-muted float-right mb-2" v-if="filters.selected_bank_id && !loading">
                    Exchange Rate: 1 {{ selected_bank.bankCurrency }} to {{ selected_bank.reportCurrency }} =
                    {{ selected_bank.reportCurrencyRate }}
                </div>
                <div v-if="loading" class="data-loading absolute inset-0 bg-neutral-100 flex items-center justify-center z-100 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm text-brand-active">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
            </div>
<!--            &lt;!&ndash; footer &ndash;&gt;-->
<!--            <div class="flex items-center justify-between">-->
<!--                &lt;!&ndash; Left Section: Showing Records &ndash;&gt;-->
<!--                <div class="text-xs text-neutral-700">-->
<!--                    <span>Showing {{ filteredData.length }} records</span>-->
<!--                </div>-->

<!--                &lt;!&ndash; Right Section: Total as per Reporting Currency &ndash;&gt;-->
<!--                <div class="flex items-center justify-center text-right text-neutral-800">-->
<!--                    <span class="mr-2 tracking-tight">Total as per reporting currency ({{ currency }}):</span>-->
<!--                    <span class="font-bold text-black">-->
<!--                        {{ formatAmount(totalAsPerReportingCurrency) }} {{ currency }}-->
<!--                    </span>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "bank-accounts",
    props: ['page_data'],
    data(){
        return {
            currencies: ["AED", "USD", "EUR", "GBP", "CHF", "PHP", "SCR"],
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
            selected_currency: 'AED',
            selected_period: 'last_60_days_plusplus',
            data: [],
            loading: false,
            filters:{
                sage_company_code: "",
                selected_bank_id: "",
            },
            companies: [],
            company_banks: [],
            selected_bank: {},
        }
    },
    methods: {
        async getData() {
            this.loading = true;
            try {
                await this.getCompanies();
            } finally {
                this.loading = false;
            }
        },
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
        async getCompanies(){
            const dateRange = this.selected_date_range;
            const fromdate = DateTime.fromISO(dateRange[0]).toFormat('dd/MM/yyyy');
            const todate = DateTime.fromISO(dateRange[1]).toFormat('dd/MM/yyyy');
            const sageUrl = 'https://10.0.1.17/CrescoSage/api/V1/FOBank/2/Transactions';
            const user = "Felvin";
            const url = sageUrl + "?fromdate=" + fromdate  + "&todate=" + todate + "&currency=" + this.selected_currency + "&user=" + user;

            try {
                const response = await axios.post(url)
                this.companies =  _.sortBy(response.data.companies, 'companyName');
            } catch (error){
                console.error(error)
            }
        },
        onSelectCompany(){
            let company = this.filteredCompanies.find(item => item.sageDBCode === this.filters.sage_company_code)
            this.company_banks = _.sortBy(company.banks, 'bankName')
        },
        onSelectBank(){
            if(this.filters.sage_company_code && this.filters.selected_bank_id){
                let bankInfo = this.company_banks.find(item => item.Id === this.filters.selected_bank_id)

                bankInfo.sageBankCode = bankInfo.sageBankCode.trim()

                if (bankInfo.bankDetails) {
                    bankInfo.openingBalance = this.getBalance(bankInfo.bankDetails, "Opening");
                    bankInfo.closingBalance = this.getBalance(bankInfo.bankDetails, "Closing");
                    bankInfo.totalDeposit = this.sumAmounts(bankInfo.bankDetails, "DepositAmt");
                    bankInfo.totalWithdrawal = this.sumAmounts(bankInfo.bankDetails, "WithdrawalAmt");

                    bankInfo.totalDebit = bankInfo.bankDetails
                        .filter(transaction => transaction.SourceApp !== "Closing")
                        .reduce((total, transaction) => total + transaction.DepositAmt, 0);

                    this.selected_bank = bankInfo;
                }
                //no transactions in bank account
                else {
                    bankInfo.openingBalance = 0;
                    bankInfo.closingBalance = 0;
                    bankInfo.totalDeposit = 0;
                    bankInfo.totalWithdrawal = 0;
                    bankInfo.totalDebit = 0;

                    this.selected_bank = bankInfo;
                }
            }
        },
        getBalance(bankTransactions, type) {
            let openOrCloseTransaction = bankTransactions.filter(transaction => transaction.SourceApp == type);
            if (openOrCloseTransaction.length > 0) {
                return openOrCloseTransaction[0].transactionAmount;
            } else {
                return 0;
            }
        },
        sumAmounts(bankTransactions, key) {
            let transactions = bankTransactions.filter(transaction => transaction.SourceApp !== "Opening" && transaction.SourceApp !== "Closing");
            return _.sumBy(transactions, key);
        },
        formatDate: function (value) {
            if (value) {
                return DateTime.fromFormat(value, "d/M/yyyy").toFormat('dd LLL yyyy')
            } else {
                return "";
            }
        },
        formatAmount: function (value) {
            if (value) {
                return value.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                return 0.00;
            }
        }
    },
    computed:{
        dateRangePickerText(){
            if (!this.selected_date_range[0] || !this.selected_date_range[1]) {
                return "No date selected";
            }
            const formattedStart = DateTime.fromISO(this.selected_date_range[0]).toFormat("d MMM yyyy");
            const formattedEnd = DateTime.fromISO(this.selected_date_range[1]).toFormat("d MMM yyyy");
            return `${formattedStart} - ${formattedEnd}`;
        },
        filteredCompanies() {
            const filterCodes = this.filters.sage_company_code ? [this.filters.sage_company_code] : this.page_data.sage_companies_codes.map(obj => obj.sage_company_code);
            return this.companies.filter(company => filterCodes.includes(company.sageDBCode));
        },
    },
    watch: {
        selected_period(){
            this.updateDateRangeForPeriod(this.selected_period);
        }
    },
    created() {
        this.updateDateRangeForPeriod(this.selected_period);
    },
    mounted() {
        this.getData()
    }
}
</script>
<style scoped>

</style>
