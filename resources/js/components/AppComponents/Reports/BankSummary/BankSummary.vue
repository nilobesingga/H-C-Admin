<template>
    <div class="container-fluid px-3">
        <reports-filters-component
            @get-data="getData"
        />
        <div class="grid gap-2">
            <!-- filters -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm select-input w-96" v-model="filters.sage_company_code">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="obj in page_data.sage_companies_code" :key="obj.id" :value="obj.sage_company_code">
                            {{ obj.bitrix_sage_company_name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Bank Summary Table -->
            <div v-if="current_section === 'overview'" class="relative flex-grow overflow-auto reports-table-container shadow-md border border-brand">
                <table v-if="filteredCompanies.length > 0 && filteredBanks.length > 0" class="w-full c-table table-auto border-collapse table-border border text-center text-xs whitespace-nowrap">
                    <thead class="text-sm">
                        <!-- Country Row -->
                        <tr class="!border !border-b-0 !border-red-800">
                            <th class="!border-red-800 sticky top-0 left-0 z-50 p-1 bg-brand text-white w-[22rem]">Country</th>
                            <template v-for="(bankCounty, index) in groupedByCountryBanks">
                                <!--<th class="sticky top-0 text-white z-40" :colspan="bankCounty.length" :style="{'background-color': bankCounty[0].countryColor}">{{ index }}</th>-->
                                <th class="!border-red-800 sticky top-0 text-white z-40 c-column" :colspan="bankCounty.length">{{ index }}</th>
                            </template>
                            <th class="sticky top-0 right-0 bg-black/30 backdrop-blur-md text-white z-50 p-1 w-[22rem]" rowspan="2">Total ({{ currency }})</th>
                        </tr>
                        <!-- Company Row -->
                        <tr class="!border !border-black">
                            <th class="!border-neutral-800 !border-b !border-b-black sticky !bg-black top-[28px] left-0 z-50 text-white p-1 w-[22rem] company-head-bg">Company</th>
                            <template v-for="(bankCounty, index) in groupedByCountryBanks">
                                <template v-for="bank in bankCounty">
                                    <th class="!border-neutral-800 !border-b !border-b-black sticky !bg-black top-[28px] text-white text-center z-40 p-2 min-w-[130px] company-head-bg">{{ bank.bankName }}</th>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs tracking-tight">
                        <!-- Main Grid, Amounts -->
                        <tr v-for="company in filteredCompanies" :key="company.companyId" class="transition-all duration-300 text-neutral-800">
                            <td class="sticky left-0 text-left z-40 bg-white p-2 border border-neutral-200 w-[22rem]">{{ company.companyName }}</td>
                            <template v-for="(bankCountry, index) in groupedByCountryBanks">
                                <template v-for="bank in bankCountry">
                                    <td
                                        :class="['text-right p-2 border border-neutral-200 cursor-pointer hover:bg-black/10', getBankClass(company.banks, bank.bankId, 'C')]"
                                        @click="showBankTransactions(company.banks, bank.bankId, 'C')"
                                        :data-modal-toggle="formatAmount(getBankAmount(company.banks, bank.bankId), false) !== 'N/A' ? '#show_bank_transactions_modal' : null"
                                    >
                                        {{ formatAmount(getBankAmount(company.banks, bank.bankId), false) }}
                                    </td>
                                </template>
                            </template>
                            <td class="sticky right-0 z-40 bg-white text-right p-2 border font-bold min-w-[150px] w-[22rem]">{{ formatAmount(getTotalsPerCompany(company), false) }}</td>
                        </tr>
                        <!-- Total Cash -->
                        <tr class="">
                            <td class="text-left sticky left-0 bg-neutral-300 z-40 p-2 text-black font-bold border w-[250px]">TOTAL CASH</td>
                            <template v-for="(bankCountry, index) in groupedByCountryBanks">
                                <template v-for="bank in bankCountry">
                                    <td class="bg-neutral-300 text-black font-bold p-2 text-right border">{{ formatAmount(bank.allCash, true) }}</td>
                                </template>
                            </template>
                            <td class="sticky right-0 bg-neutral-300 text-black font-bold p-2 text-right z-40 border w-[250px]">{{ formatAmount(overAllTotalCash, true) }}</td>
                        </tr>
                        <!-- Reserved Rows -->
                        <tr>
                            <td class="text-left text-black font-bold sticky left-0 z-40 bg-white p-2 border w-[250px]">RESERVED</td>
                            <template v-for="(bankCountry, index) in groupedByCountryBanks">
                                <template v-for="bank in bankCountry">
                                    <td class="text-black font-bold p-2 text-right border">{{ formatAmount(getReservedAmountsByBank(bank), true) }}</td>
                                </template>
                            </template>
                            <td class="sticky right-0 text-black font-bold bg-white p-2 text-right z-40 border w-[250px]">{{ formatAmount(overAllMinimumBalance, true) }}</td>
                        </tr>
                        <!-- Blocked Rows -->
                        <tr>
                            <td class="text-left text-black font-bold sticky left-0 z-40 bg-white p-2 border w-[250px]">BLOCKED</td>
                            <template v-for="(bankCountry, index) in groupedByCountryBanks">
                                <template v-for="bank in bankCountry">
                                    <td class="text-black font-bold p-2 text-right border">{{ formatAmount(bank.allBlocked, true) }}</td>
                                </template>
                            </template>
                            <td class="sticky right-0 text-black font-bold bg-white p-2 text-right z-40 border w-[250px]">{{ formatAmount(overallTotalBlockedCash, true) }}</td>
                        </tr>
                        <!-- Blocked Rows -->
                        <tr>
                            <td class="text-left text-black font-bold sticky left-0 z-40 bg-white p-2 border w-[250px]">AVAILABLE CASH</td>
                            <template v-for="(bankCountry, index) in groupedByCountryBanks">
                                <template v-for="bank in bankCountry">
                                    <td class="text-black font-bold p-2 text-right border">{{ formatAmount(bank.availableCash, true) }}</td>
                                </template>
                            </template>
                            <td class="sticky right-0 text-black font-bold bg-white p-2 text-right z-40 border w-[250px]">{{ formatAmount(overAllAvailableCash, true) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>
                    <div class="text-center text-md text-red-400">No data available</div>
                </div>
                <div v-if="loading" class="data-loading absolute inset-0 bg-neutral-100 bg-opacity-50 flex items-center justify-center z-100 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm text-brand-active">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
            </div>

            <!-- Cash by Currency Table -->
            <div v-if="current_section === 'cash-by-currency'" class="relative flex-grow overflow-auto reports-table-container shadow-md border border-brand">
                <table v-if="filteredCompanies.length > 0 && groupByBankCurrency.length > 0" class="w-full c-table table-auto border-collapse table-border border text-center text-xs whitespace-nowrap">
                    <thead class="text-sm">
                        <!-- Company Row -->
                        <tr class="!border !border-b-0 !border-red-800">
                            <th class="!border-b !border-red-800 sticky top-0 left-0 z-50 text-white p-1 w-[22rem] bg-brand">Company</th>
                            <th v-for="(obj, index) in groupByBankCurrency" class="!border-b !border-red-800 sticky top-0 text-white text-center z-40 p-2 min-w-[120px] c-column">{{ obj.currency }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs tracking-tight">
                        <!-- Main Grid, Amounts -->
                        <tr v-for="company in filteredCompanies" :key="company.companyId" class="transition-all duration-300 text-neutral-800">
                            <td class="sticky left-0 text-left z-40 bg-white text-black p-2 border w-[22rem]">{{ company.companyName }}</td>
                            <template v-for="(obj, index) in groupByBankCurrency">
                                <td
                                    :class="['text-right p-2 border border-neutral-200 cursor-pointer hover:bg-black/10', {'not-applicable-gray': formatAmount(getCurrencyAmount(company.banks, obj.currency, company.companyId), false) === 'N/A'} ]"
                                    @click="showBankTransactionsByCurrency(company.banks, obj.currency)"
                                    :data-modal-toggle="formatAmount(getCurrencyAmount(company.banks, obj.currency, company.companyId), false) !== 'N/A' ? '#show_bank_transactions_modal' : null"
                                >
                                    {{ formatAmount(getCurrencyAmount(company.banks, obj.currency, company.companyId), false) }}
                                </td>
                            </template>
                        </tr>
                        <!-- Total Cash -->
                        <tr>
                            <td class="text-left sticky left-0 bg-neutral-300 z-40 p-2 text-black font-bold border w-[250px]">TOTAL CASH</td>
                            <td v-for="obj in groupByBankCurrency" class="bg-neutral-300 text-black font-bold p-2 text-right border">{{ formatAmount(obj.total, true) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>
                    <div class="text-center text-md text-red-400">No data available</div>
                </div>
                <div v-if="loading" class="data-loading absolute inset-0 bg-neutral-100 bg-opacity-50 flex items-center justify-center z-100 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm text-brand-active">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
    <show-bank-transactions-modal
        v-if="is_show_bank_transactions_modal"
        :bitrix_sage_company_mapping="page_data.bitrix_sage_company_mapping"
        ref="showBankTransactionsModal"
        @closeModal="closeModal"
    />
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "bank-summary",
    props: ['page_data'],
    data() {
        return {
            data: [],
            loading: false,
            filters: {
                sage_company_code: "",
            },
            companies: [],
            banks: [],
            overAllTotalCash: 0,
            overallTotalBlockedCash: 0,
            overAllAvailableCash: 0,
            overAllMinimumBalance: 0,
            is_show_bank_transactions_modal: false,
            current_section: null,
        }
    },
    methods: {
        async getData() {
            this.loading = true;
            try {
                await this.getCompanies();
                await this.getBanks();
                this.calculateBankTotals();
            } finally {
                this.loading = false;
            }
        },
        getBanks() {
            return axios.get('https://10.0.1.17/CrescoSage/api/v1/FOBank/2/MainBanks')
                .then(response => {
                    this.banks = response.data.FOMainBanks || [];
                })
                .catch(error => {
                    console.error("Error fetching banks:", error);
                });
        },
        getCompanies(){
            const dateRange = JSON.parse(localStorage.getItem('dateRange'));
            const fromdate = DateTime.fromISO(dateRange[0]).toFormat('dd/MM/yyyy');
            const todate = DateTime.fromISO(dateRange[1]).toFormat('dd/MM/yyyy');
            const sageUrl = 'https://10.0.1.17/CrescoSage/api/V1/FOBank/2/Transactions';
            const user = "Felvin";
            const url = sageUrl + "?fromdate=" + fromdate  + "&todate=" + todate + "&currency=" + this.currency + "&user=" + user;

            return axios.post(url)
                .then(response => {
                    const companies = response.data.companies || [];
                    this.companies = _.sortBy(companies, 'companyName');
                })
        },
        getBankAmount(companyBanks, bankId) {
            return companyBanks
                .filter((bank) => bank.bankId === bankId && ["C", "R"].includes(bank.type))
                .reduce((sum, bank) => sum + bank.reportingAmount, 0);
        },
        getCurrencyAmount(companyBanks, currency, companyId) {
            //filter company banks by bankId and type
            //combine cash and reserved amounts
            var banks = companyBanks.filter(function (bank) {
                return bank.bankCurrency === currency;
            });

            return banks.reduce(function (sum, bank) {
                return sum + bank.bankTotal;
            }, 0);
        },
        getTotalsPerCompany(company) {
            return company.banks.reduce((sum, bank) => sum + bank.reportingAmount, 0);
        },
        calculateBankTotals() {
            this.overAllTotalCash = 0;
            this.overallTotalBlockedCash = 0;
            this.overAllAvailableCash = 0;
            this.overAllMinimumBalance = 0;

            const creditSuisseBankId = 1;

            this.filteredBanks.forEach((bank) => {
                let totalCash = 0;
                let blockedCash = 0;
                let totalExcludingCreditSuisse = 0;

                this.filteredCompanies.forEach((company) => {
                    const relevantTransactions = company.banks.filter(
                        b => b.bankId === bank.bankId && (b.type === 'C' || b.type === 'R')
                    );
                    const totalAmount = relevantTransactions.reduce((sum, b) => sum + b.reportingAmount, 0);
                    const blockedAmount = relevantTransactions
                        .filter(b => b.isBlock)
                        .reduce((sum, b) => sum + b.reportingAmount, 0);

                    totalCash += totalAmount;
                    blockedCash += blockedAmount;

                    if (bank.bankId !== creditSuisseBankId) {
                        totalExcludingCreditSuisse += totalAmount;
                    }
                });

                const reservedAmount = this.getReservedAmountsByBank(bank);

                bank.allCash = totalCash;
                bank.allBlocked = blockedCash;
                bank.availableCash = totalCash - blockedCash - reservedAmount;

                this.overAllTotalCash += bank.allCash;
                this.overallTotalBlockedCash += bank.allBlocked;
                this.overAllAvailableCash += bank.availableCash;
                this.overAllMinimumBalance += reservedAmount;

            });
        },
        getReservedAmountsByBank(bank) {
            var minBalance = bank.minBalance;
            var currency = this.currency;
            switch (currency) {
                case "USD":
                    minBalance = minBalance
                    break;
                case "AED":
                    minBalance = minBalance * 3.6725;
                    break;
                case "EUR":
                    minBalance = minBalance * 0.9;
                    break;
                case "GBP":
                    minBalance = minBalance * 0.74;
                    break;
                case "CHF":
                    minBalance = minBalance * 0.952381;
                    break;
            }

            return minBalance

        },
        getRandomColor() {
            const randomValue = () => Math.floor(Math.random() * 256);
            return `rgb(${randomValue()}, ${randomValue()}, ${randomValue()})`;
        },
        getBankClass(banks, bankId, type) {
            //check if there is a bank that is blocked
            let bank = banks.find(function (bank) {
                return bank.isBlock && bank.type === type && bank.bankId === bankId;
            });

            let bankAmount = this.getBankAmount(banks,bankId);

            if (bank) {
                return "blocked";
            } else if (bankAmount < 0){
                return "bg-orange";
            } else if (bankAmount === 0) {
                return "not-applicable-gray";
            } else {
                return "cash-item";
            }
        },
        showBankTransactions(companyBanks, bankId, type){
            //filter company banks by bankId and type
            var banks = companyBanks.filter(function (bank) {
                return bank.bankId === bankId && bank.type === 'C' || bank.bankId === bankId && bank.type === 'R';
            });
            this.is_show_bank_transactions_modal = true
            this.$nextTick(() => {
                this.$refs.showBankTransactionsModal.showTransactions(banks);
            })
        },
        showBankTransactionsByCurrency(companyBanks, currency) {
            var banks = companyBanks.filter(function (bank) {
                return bank.bankCurrency === currency;
            });
            this.is_show_bank_transactions_modal = true
            this.$nextTick(() => {
                this.$refs.showBankTransactionsModal.showTransactions(banks);
            })
        },
        closeModal(){
            this.is_show_bank_transactions_modal = false;
            this.removeModalBackdrop();
        },
        formatAmount(value, isTotalAmount){
            if (value) {
                let numericValue = typeof value === 'string' ? parseFloat(value) : value;
                return numericValue.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                return isTotalAmount ? 0 : 'N/A';
            }
        }
    },
    computed: {
        filteredCompanies() {
            const filterCodes = this.filters.sage_company_code
                ? [this.filters.sage_company_code]
                : this.page_data.sage_companies_code.map(obj => obj.sage_company_code);
            return this.companies.filter(company => filterCodes.includes(company.sageDBCode));
        },
        filteredBanks(){
            if (this.filteredCompanies.length === 0) {
                return [];
            }
            const companiesBankIds = new Set(
                this.filteredCompanies.flatMap((company) => company.banks.map((bank) => bank.bankId))
            );

            return this.banks
                .filter((bank) => companiesBankIds.has(bank.ID))
                .map((bank) => ({
                    bankId: bank.ID,
                    bankName: bank.FOBankName,
                    availableCash: 0,
                    allCash: 0,
                    wealth: 0,
                    color: this.getRandomColor(),
                    countryColor: bank.ColorCode,
                    minBalance: bank.ReservedAmt,
                    country: bank.FOBankCountry,
                }));
        },
        groupedByCountryBanks(){
            return _.chain(this.filteredBanks)
                .sortBy('bankName')
                .groupBy('country')
                .toPairs()
                .sortBy(0)
                .fromPairs()
                .value();
        },
        groupByBankCurrency(){
            let bankCurrencies = []
            //loop companies
            _.forEach(this.filteredCompanies, function (company) {
                //loop company banks
                _.forEach(company.banks, function (bank) {
                    //check if currency is already in the array
                    var bankCurrency = _.find(bankCurrencies, function (currencyInfo) {
                        return currencyInfo.currency === bank.bankCurrency;
                    });

                    if (!bankCurrency) {
                        bankCurrencies.push({
                            currency: bank.bankCurrency,
                            total: bank.bankTotal,
                        });
                    } else {
                        bankCurrency.total += bank.bankTotal;
                    }
                });
            });
            return bankCurrencies;
        }
    },
    watch: {
        currency(){
            this.getData()
        },
        "filters.sage_company_code": {
            immediate: true,
            handler() {
                this.calculateBankTotals();
            },
        },
    },
    mounted() {
        const urlParams = Object.fromEntries(new URLSearchParams(window.location.search).entries());
        this.current_section = urlParams.section || 'overview';
    },
}
</script>
<style scoped>
.company-head-bg{
    background-color: #343a3f;
}
.blocked {
    background-color: rgba(255, 0, 0, 0.12)
}
.not-applicable-gray{
    color: #6c757d78;
}
.bg-orange{
    background-color: rgba(255, 165, 0, 0.2);
}
.c-column:nth-child(odd) {
    background-color: #850F00!important; /* Green for odd columns */
}

.c-column:nth-child(even) {
    background-color: #700f03 !important; /* Blue for even columns */
}
</style>
