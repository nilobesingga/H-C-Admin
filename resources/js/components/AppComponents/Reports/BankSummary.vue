<template>
    <div class="container-fluid">
        <!--        <div class="pb-6">-->
        <!--            <div class="flex items-center justify-between flex-wrap gap-3">-->
        <!--                <div class="flex flex-col gap-1">-->
        <!--                    <select v-model="filters.bitrix_active" name="bitrix_active" class="select select-sm min-w-[8rem] max-w-full truncate" @change="getData(false)">-->
        <!--                        <option value="">All</option>-->
        <!--                        <option value="1">Active</option>-->
        <!--                        <option value="0">In-Active</option>-->
        <!--                    </select>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <div class="grid gap-5 lg:gap-7.5">
            <div class="card min-w-full">
                <div class="flex flex-col gap-4" v-if="sageNotAccessible">
                    <div class="w-full">
                        <div class="alert alert-danger bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                            <p class="font-bold">
                                Unable to access CRESCO Sage. Follow the steps below to troubleshoot:
                            </p>
                            <ol class="list-decimal list-inside text-sm">
                                <li>
                                    Click <a class="text-blue-500 underline" target="_blank" :href="sageUrl">here</a> to verify if you have access.
                                </li>
                                <li>
                                    If the link above shows "Connection not secure or connection is not private," click show
                                    details and then proceed to the site.
                                </li>
                                <li>Reload the reports page after allowing access in the previous step.</li>
                                <li>
                                    If the issue persists, try to turn your VPN or Wi-Fi on/off and refresh the reports
                                    page.
                                </li>
                                <li>
                                    If the issue persists, try to open the reports page in incognito mode or private window
                                    (Shift+Command+N).
                                </li>
                                <li>If the issue persists, contact IT.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card-header">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex flex-col gap-1">
                            <select class="select select-sm min-w-[20rem] max-w-full truncate" v-model="selected_sage_company_code" @change="getCompanies">
                                <option value="all" selected>All</option>
                                <option v-for="obj in page_data.sage_companies_code" :key="obj.sage_company_code" :value="obj.sage_company_code">
                                    {{ obj.sage_company_code }}
                                </option>
                            </select>
                            <span class="text-xs text-gray-500">Filter by Sage Company</span>
                        </div>
                    </div>
                </div>
                <div class="card-table scrollable-x-auto">
                    <div class="scrollable-auto">
                        <table class="table-auto w-full border-collapse border border-gray-300 text-sm text-gray-700">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border border-gray-300" style="width: 200px">Country</th>
                                <template v-for="(bankByCountry, index) in groupedByCountryBanks">
                                    <th
                                        :colspan="bankByCountry.length"
                                        class="px-4 py-2 text-center border border-gray-300"
                                        :style="{ backgroundColor: bankByCountry[0].countryColor, color: 'white', fontFamily: 'sans-serif', fontWeight: 'bold' }">
                                        {{ index }}
                                    </th>
                                </template>
                                <th class="px-4 py-2 border border-gray-300"></th>
                            </tr>
                            <tr>
                                <th class="px-4 py-2 border border-gray-300" style="width: 200px">Company</th>
                                <template v-for="(bankByCountry, index) in groupedByCountryBanks">
                                    <template v-for="bank in bankByCountry">
                                        <th class="px-4 py-2 text-center border border-gray-300">{{ bank.bankName }}</th>
                                    </template>
                                </template>
                                <th class="px-4 py-2 text-center border border-gray-300">
                                    TOTALS ({{ selectedCurrency }})
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="company in companies" class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-300">{{ company.companyName }}</td>
                                <template v-for="(bankByCountry, index) in groupedByCountryBanks">
                                    <template v-for="bank in bankByCountry">
                                        <td
                                            :class="getBankClass(company.banks, bank.bankId, 'C') + ' px-4 py-2 text-right border border-gray-300 cursor-pointer'"
                                            @click="showBankTransactions(company.banks, bank.bankId, 'C')">
                                            {{ getBankAmount(company.banks, bank.bankId) | formatAmounts }}
                                        </td>
                                    </template>
                                </template>
                                <td class="px-4 py-2 text-right border border-gray-300 font-bold">
                                    {{ getTotalsPerCompany( formatTotals(company.companyId)) }}
                                </td>
                            </tr>
                            <tr class="font-bold bg-blue-100">
                                <td class="px-4 py-2 border border-gray-300">TOTAL CASH</td>
                                <template v-for="(bankByCountry, index) in groupedByCountryBanks">
                                    <template v-for="bank in bankByCountry">
                                        <td class="px-4 py-2 text-right border border-gray-300">
                                            {{  formatTotals(bank.allCash) }}
                                        </td>
                                    </template>
                                </template>
                                <td class="px-4 py-2 text-right border border-gray-300">
                                    {{ formatTotals(overAllTotalCash) }}
                                </td>
                            </tr>
                            <tr class="font-bold">
                                <td class="px-4 py-2 border border-gray-300">RESERVED</td>
                                <template v-for="(bankByCountry, index) in groupedByCountryBanks">
                                    <template v-for="bank in bankByCountry">
                                        <td class="px-4 py-2 text-right border border-gray-300">
                                            {{ formatTotals(getReservedAmountsByBank(bank)) }}
                                        </td>
                                    </template>
                                </template>
                                <td class="px-4 py-2 text-right border border-gray-300">
                                    {{ formatTotals(overAllMinimumBalance) }}
                                </td>
                            </tr>
                            <tr class="font-bold">
                                <td class="px-4 py-2 border border-gray-300">BLOCKED</td>
                                <template v-for="(bankByCountry, index) in groupedByCountryBanks">
                                    <template v-for="bank in bankByCountry">
                                        <td class="px-4 py-2 text-right border border-gray-300">
                                            {{ formatTotals(bank.allBlocked)  }}
                                        </td>
                                    </template>
                                </template>
                                <td class="px-4 py-2 text-right border border-gray-300">
                                    {{ formatTotals(overallTotalBlockedCash) }}
                                </td>
                            </tr>
                            <tr class="font-bold bg-green-100">
                                <td class="px-4 py-2 border border-gray-300">AVAILABLE CASH</td>
                                <template v-for="(bankByCountry, index) in groupedByCountryBanks">
                                    <template v-for="bank in bankByCountry">
                                        <td class="px-4 py-2 text-right border border-gray-300">
                                            {{ formatTotals(bank.availableCash) }}
                                        </td>
                                    </template>
                                </template>
                                <td class="px-4 py-2 text-right border border-gray-300">
                                    {{ formatTotals(overAllAvailableCash) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import {DateTime} from "luxon";
import {Interval} from "luxon";
import _ from 'lodash';

export default {
    name: "bank-summary",
    props: ['page_data'],
    created() {
        this.getBanks();
        this.getCompanies();
    },
    data: function () {
        return {
            selected_sage_company_code: 'all',
            loadingData: false,
            sageUrl: "https://10.0.1.17/CrescoSage/api/V1/FOBank/2/Transactions",
            sageNotAccessible: false,
            availableCash: {
                seriesData: [{
                    category: "Credit Suisse",
                    value: 1000000,
                }, {
                    category: "Barcalys",
                    value: 2000000,
                }, {
                    category: "Cite Gestion",
                    value: 3000000,
                }, {
                    category: "FAB",
                    value: 4000000,
                }, {
                    category: "Al Salam Bank",
                    value: 5000000,
                }],
                template: "#= category #: #= kendo.format('{0:P}', percentage)#",
                labelAlign: "center"
            },
            allCash: {
                seriesData: [{
                    category: "Credit Suisse",
                    value: 1000000,
                }, {
                    category: "Barcalys",
                    value: 2000000,
                }, {
                    category: "Cite Gestion",
                    value: 3000000,
                }, {
                    category: "First Abu Dhabi Bank FAB",
                    value: 4000000,
                }, {
                    category: "Al Salam Bank",
                    value: 5000000,
                }],
                template: "#= category #: #= kendo.format('{0:P}', percentage)#",
                labelAlign: "center"
            },
            wealth: {
                seriesData: [{
                    category: "Credit Suisse",
                    value: 1000000,
                }, {
                    category: "Barcalys",
                    value: 5000000,
                }, {
                    category: "Cite Gestion",
                    value: 3000000,
                }, {
                    category: "First Abu Dhabi Bank FAB",
                    value: 9000000,
                }, {
                    category: "Al Salam Bank",
                    value: 7000000,
                }],
                template: "#= category #: #= kendo.format('{0:P}', percentage)#",
                labelAlign: "center"
            },
            availableCashByBank: {
                seriesData: [],
                template: "#= category #: #= kendo.format('{0:P}', percentage)#",
            },
            allCashByBank: {
                seriesData: [],
                template: "#= category #: #= kendo.format('{0:P}', percentage)#",
            },
            wealthByBank: {
                seriesData: [],
                template: "#= category #: #= kendo.format('{0:P}', percentage)#",
            },
            companies: [],
            banks: [],
            groupedByCountryBanks: [],
            totals: {
                cashAvailable: 0,
                cashBlocked: 0,
                invAvailable: 0,
                invBlocked: 0,
                overAllTotal: 0,
            },
            overAllTotalCash: 0,
            overAllAvailableCash: 0,
            overallTotalBlockedCash: 0,
            overAllMinimumBalance: 0,
            overaAllExcludingCreditSuisse: 0,
            overaAllAvailableCashExcludingCreditSuisse: 0,
            overAllBlockedCashExcludingCreditSuisse: 0,
            companyTotals: [],
            bankCurrencies: [],
            companyCurrencies: []

        }
    },
    methods: {
        getBanks() {
            var self = this;
            axios.get("https://10.0.1.17/CrescoSage/api/v1/FOBank/2/MainBanks")
                .then(function (response) {

                    var banks = response.data.FOMainBanks;
                    var bankList = [];

                    //loop banks using lodash
                    _.forEach(banks, function (bank) {
                        let bankObj = {
                            bankId: bank.ID,
                            bankName: bank.FOBankName,
                            availableCash: 0,
                            allCash: 0,
                            wealth: 0,
                            color: self.getRandomColor(),
                            countryColor: bank.ColorCode,
                            minBalance: bank.ReservedAmt,
                            country: bank.FOBankCountry
                        }
                        bankList.push(bankObj);
                    });

                    //sort banks by bank name
                    bankList = _.sortBy(bankList, ['bankName']);

                    //group banks by country
                    var groupedBanks = _.groupBy(bankList, 'country');
                    var sortedGroupedBanks = _.chain(groupedBanks)
                        .toPairs()
                        .sortBy(([country]) => country)
                        .fromPairs()
                        .value();


                    self.banks = bankList;
                    self.groupedByCountryBanks = sortedGroupedBanks;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        getRandomColor() {
            var r = Math.floor(Math.random() * 256); // Random value between 0 and 255 for red
            var g = Math.floor(Math.random() * 256); // Random value between 0 and 255 for green
            var b = Math.floor(Math.random() * 256); // Random value between 0 and 255 for blue
            var rgbColor = `rgb(${r}, ${g}, ${b})`; // Create the RGB color string
            return rgbColor;

        },
        getRandomDarkColors() {
            const getRandomValue = () => Math.floor(Math.random() * 256); // Generates a random value between 0 and 255

            while (true) {
                const red = getRandomValue();
                const green = getRandomValue();
                const blue = getRandomValue();

                // Calculate the luminance of the color using the relative luminance formula
                const luminance = (red * 0.299 + green * 0.587 + blue * 0.114) / 255;

                if (luminance > 0.3) {
                    // If the luminance is above the threshold, generate a new color
                    continue;
                }

                return `rgb(${red}, ${green}, ${blue})`;
            }
        },
        getCompanies() {
            var self = this;
            const fromDate = "30/09/2024";
            const toDate = "31/12/2027";
            const currency = "AED";
            const user = "Felvin";
            const familyId = 1;

            const url = this.sageUrl + "?fromdate=" + fromDate + "&todate=" + toDate + "&currency=" + currency + "&user=" + user;

            self.loadingData = true;
            axios.post(url)
                .then(function (response) {
                    let filteredCompanies = response.data.companies;
                    // If selected_sage_company_code is 'all', filter using page_data.sage_companies_code
                    if (self.selected_sage_company_code === 'all') {
                        filteredCompanies = filteredCompanies.filter(company =>
                            self.page_data.sage_companies_code.some(sage => sage.sage_company_code === company.sageDBCode)
                        );
                    } else {
                        filteredCompanies = filteredCompanies.filter(company => company.sageDBCode === self.selected_sage_company_code);
                    }

                    //sort companies by company name
                    self.companies = _.sortBy(filteredCompanies, ['companyName']);
                    //self.getAvailableCashByBank();
                    self.getTotalCashPerBank();
                    self.groupBankByCurrency();
                    self.sageNotAccessible = false;


                })
                .catch(function (error) {
                    self.sageNotAccessible = true;
                    console.log(error);
                    self.loadingData = false;
                    alert("Something went wrong. Please refresh page again")

                });

        },
        getBankAmount(companyBanks, bankId) {
            //filter company banks by bankId and type
            //combine cash and reserved amounts
            var banks = companyBanks.filter(function (bank) {
                return bank.bankId == bankId && bank.type == 'C' || bank.bankId == bankId && bank.type == 'R';
            });

            if (!banks || banks.length == 0) {
                return 0;
            }

            let amount = banks.reduce(function (sum, bank) {
                return sum + bank.reportingAmount;
            }, 0);

            return amount;

            //return Math.round(amount).toLocaleString();

        },
        showBankTransactions(companyBanks, bankId, type) {
            //filter company banks by bankId and type
            var banks = companyBanks.filter(function (bank) {
                return bank.bankId == bankId && bank.type == 'C' || bank.bankId == bankId && bank.type == 'R';
            });

            this.$refs.orchidTransactions.showTransactions(banks);
        },
        showBankTransactionsByCurrency(companyBanks, currency) {
            var banks = companyBanks.filter(function (bank) {
                return bank.bankCurrency == currency;
            });

            this.$refs.orchidTransactions.showTransactions(banks);
        },
        getBankClass(banks, bankId, type) {
            //check if there is a bank that is blocked
            let bank = banks.find(function (bank) {
                return bank.isBlock && bank.type == type && bank.bankId == bankId;
            });

            let bankAmount = this.getBankAmount(banks,bankId);

            if (bank) {
                return "cash-item blocked";
            }
            else if (bankAmount < 0){
                return "warning-orange text-right";
            }
            else if (bankAmount === 0) {
                return "not-applicable-gray text-right";
            }
            else
            {
                return "cash-item";
            }


            // if(bank){
            //     if(type == "C"){
            //         return "cash-item blocked";
            //     }
            //     else{
            //         return "invested-item blocked";
            //     }
            // }else{
            //     if(type == "C"){
            //         return "cash-item";
            //     }
            //     else{
            //         return "invested-item";
            //     }
            // }


        },
        getTotalCashPerBank() {
            var total = 0;
            var totalExcludingCreditSuisse = 0;
            var totalBlocked = 0
            var self = this;
            self.availableCashByBank.seriesData = [];
            self.allCashByBank.seriesData = [];
            self.wealthByBank.seriesData = [];

            self.overAllTotalCash = 0;
            self.overallTotalBlockedCash = 0;
            self.overAllAvailableCash = 0;
            self.overAllMinimumBalance = 0;

            self.banks.forEach(function (bank) {
                bank.availableCash = 0;
                bank.allCash = 0;
                bank.wealth = 0;
                bank.allInvested = 0;
                bank.allBlocked = 0;
                total = 0;
                totalExcludingCreditSuisse = 0;
                totalBlocked = 0;

                var currentBankId = bank.bankId;

                self.companies.forEach(function (company) {

                    var cashAndReserved = company.banks.filter(function (bank) {
                        return bank.bankId == currentBankId && bank.type == 'C' || bank.bankId == currentBankId && bank.type == "R";
                    });

                    var amount = cashAndReserved.reduce(function (sum, bank) {
                        return sum + bank.reportingAmount;
                    }, 0);

                    total += amount;

                    //caculate blocked amounts
                    var blocked = cashAndReserved.filter(function (bank) {
                        return bank.isBlock;
                    });

                    var blockedAmount = blocked.reduce(function (sum, bank) {
                        return sum + bank.reportingAmount;
                    }, 0);

                    totalBlocked += blockedAmount;

                    //calculate totals without credit suisse
                    var cashAndReservedExcludingCreditSuisse = company.banks.filter(function (bank) {
                        if (bank.bankId != 1) {
                            return bank.bankId == currentBankId && bank.type == 'C' || bank.bankId == currentBankId && bank.type == "R";
                        }

                    });
                    var amountExcludingCreditSuisse = cashAndReservedExcludingCreditSuisse.reduce(function (sum, bank) {
                        return sum + bank.reportingAmount;
                    }, 0);

                    totalExcludingCreditSuisse += amountExcludingCreditSuisse;

                });

                var bankMinBalance = self.getReservedAmountsByBank(bank)

                bank.allCash = total;
                bank.allBlocked = totalBlocked;
                bank.availableCash = (total - totalBlocked) - bankMinBalance

                self.overAllTotalCash += bank.allCash;
                self.overallTotalBlockedCash += bank.allBlocked;
                self.overAllAvailableCash += bank.availableCash;
                self.overAllMinimumBalance += bankMinBalance;

                //totals without credit suisse
                self.overaAllExcludingCreditSuisse += totalExcludingCreditSuisse;
                let totalMinExcludingCreditSuisse = totalExcludingCreditSuisse - bankMinBalance;
                self.overaAllAvailableCashExcludingCreditSuisse += totalMinExcludingCreditSuisse;
                if (bank.bankId != 1) {
                    self.overAllBlockedCashExcludingCreditSuisse += bank.allBlocked;
                }


                let obj = {
                    category: bank.bankName,
                    value: bank.availableCash,
                    color: bank.color
                }
                self.availableCashByBank.seriesData.push(obj);

                obj = {
                    category: bank.bankName,
                    value: bank.allCash,
                    color: bank.color
                }
                self.allCashByBank.seriesData.push(obj);


                obj = {
                    category: bank.bankName,
                    value: bank.allCash,
                    color: bank.color
                }
                self.wealthByBank.seriesData.push(obj);


            });

            self.loadingData = false;
        },
        getReservedAmountsByBank(bank) {
            var minBalance = bank.minBalance;
            var currency = this.selectedCurrency;
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
        getTotalsPerCompany(companyId) {
            var self = this;
            //find company
            var company = self.companies.find(function (company) {
                return company.companyId == companyId;
            });

            //loop company banks
            var total = 0;
            var totalCash = 0;

            company.banks.forEach(function (companyBank) {
                total += companyBank.reportingAmount;
            });
            return total;

        },
        getTotalsPerCompanyExcludingCredit(companyId) {
            var self = this;
            //find company
            var company = self.companies.find(function (company) {
                return company.companyId == companyId;
            });

            //loop company banks
            var total = 0;
            var totalCash = 0;

            company.banks.forEach(function (companyBank) {
                if (companyBank.bankId != 1) {
                    total += companyBank.reportingAmount;
                }

            });
            return total;

        },
        groupBankByCurrency() {
            let self = this;
            self.bankCurrencies = [];
            //loop companies
            _.forEach(this.companies, function (company) {
                //loop company banks
                _.forEach(company.banks, function (bank) {

                    //check if currency is already in the array
                    var bankCurrency = _.find(self.bankCurrencies, function (currencyInfo) {
                        return currencyInfo.currency == bank.bankCurrency;
                    });

                    if (!bankCurrency) {
                        self.bankCurrencies.push({
                            currency: bank.bankCurrency,
                            total: bank.bankTotal,
                        });
                    } else {
                        bankCurrency.total += bank.bankTotal;
                    }
                });

            });


        },
        getCurrencyAmount(companyBanks, currency, companyId) {
            //filter company banks by bankId and type
            //combine cash and reserved amounts
            var banks = companyBanks.filter(function (bank) {
                return bank.bankCurrency == currency;
            });

            if (!banks || banks.length == 0) {
                return '<span class="not-applicable-gray">N/A</span>';
            }

            var amount = banks.reduce(function (sum, bank) {
                return sum + bank.bankTotal;
            }, 0);

            return Math.round(amount).toLocaleString();

        },
        formatTotals(value) {
            console.log('from formatTotals', value)
            var number = value;
            var rounded = Math.round(number);
            var formatted = (rounded === 0) ? Math.abs(rounded).toLocaleString() : rounded.toLocaleString();
            return formatted;
        },

    },
    computed: {
        selectedCurrency(){
            return "AED"
        }
    },
    filters: {
        formatTotals(value) {
            console.log('from formatTotals', value)
            var number = value;
            var rounded = Math.round(number);
            var formatted = (rounded === 0) ? Math.abs(rounded).toLocaleString() : rounded.toLocaleString();
            return formatted;
        },
    },


}
</script>
<style scoped>

</style>
