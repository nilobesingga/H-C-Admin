<template>
    <div class="container-fluid px-3">
        <reports-filters-component
            @get-data="getData"
            class="reports-header-filters"
        />
        <div class="grid gap-2">
            <!-- Filters Section -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm select-input w-96" v-model="filters.sage_company_code">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="obj in page_data.sage_companies_codes" :key="obj.id" :value="obj.sage_company_code">
                            {{ obj.bitrix_sage_company_name }}
                        </option>
                    </select>
                </div>
            </div>
            <!-- table -->
            <div class="relative flex-grow overflow-auto reports-table-container shadow-md border border-brand h-full">
                <table class="w-full c-table table table-border align-middle text-xs table-fixed" :class="filteredCompanies.length === 0 ? 'h-full' : ''">
                    <thead>
                        <tr class="text-center tracking-tight">
                            <th colspan="6"></th>
                            <th colspan="3">Closing Balance</th>
                            <th></th>
                        </tr>
                        <tr class="text-center tracking-tight">
                            <th class="sticky top-0 w-[200px]">Sage Company</th>
                            <th class="sticky top-0 w-[100px]">Bank</th>
                            <th class="sticky top-0 w-[50px]">Currency</th>
                            <th class="sticky top-0 w-[50px]">Frequency</th>
                            <th class="sticky top-0 w-[80px]">Last upload statement date</th>
                            <th class="sticky top-0 w-[80px]">Last updated date</th>
                            <th class="sticky top-0 w-[80px]">Download</th>
                            <th class="sticky top-0 w-[80px]">Statement</th>
                            <th class="sticky top-0 w-[80px]">Sage</th>
                            <th class="sticky top-0 w-[80px]">Difference</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-xs tracking-tight h-full">
                        <template v-for="(company, index) in filteredCompanies" :key="index">
                            <template v-for="(bank, bindex) in company.banks" :key="`bank${bindex}`">
                                <tr class="transition-all duration-300 text-neutral-800 group">
                                    <td>{{ bank.companyName }}</td>
                                    <td>{{ bank.bankName }}</td>
                                    <td>{{ bank.bankCurrency }}</td>
                                    <td>{{ getFrequency(bank.StatementFrequency) }}</td>
                                    <td>
                                        <div v-if="bank.lastUpdate">{{ formatDateRange(bank.lastUpdate.LastStatementPeriod) }}</div>
                                    </td>
                                    <td :style="checkLastUpdate(bank.lastUpdate, bank.StatementFrequency)">
                                        <div v-if="bank.lastUpdate">{{ formatDateTime(bank.lastUpdate.LastTranUpdate) }}</div>
                                    </td>
                                    <td>
                                        <a v-if="bank.lastUpdate"
                                           class="secondary-btn mb-1 block w-full" target="_blank"
                                           :href="ecapeDownloadUrl(bank.lastUpdate.FilePath)"
                                        >
                                            Download
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <div v-if="bank.lastUpdate">
                                            {{ formatAmount(bank.lastUpdate.ClosingBalAmt) }}
                                            <span class="!text-bold text-black">{{ bank.lastUpdate.Currency }}</span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        {{ bank.closing | formatAmount }} <span>{{ bank.bankCurrency }}</span>
                                    </td>
                                    <td class="text-right" :style="checkDifference(bank)">
                                        <span v-if="bank.lastUpdate">{{ formatAmount(bank.difference) }}</span>
                                        <span>{{ bank.bankCurrency }}</span>
                                    </td>
                                </tr>
                            </template>
                        </template>
<!--                        <tr v-for="(obj, index) in " :key="index" class="transition-all duration-300 text-neutral-800 group">-->
<!--                            <td>{{ index + 1 }}</td>-->
<!--                        </tr>-->
                        <tr class="table-no-data-available h-full" v-if="filteredCompanies.length === 0">
                            <td class="text-center text-md text-red-400 !border-none h-full">
                                <div class="flex flex-col h-full w-full items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 mb-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    No data available
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
        </div>
    </div>
</template>
<script>
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "bank-monitoring",
    props: ['page_data'],
    data(){
        return {
            data: [],
            loading: false,
            bank_last_update: [],
            companies: [],
            filters: {
                sage_company_code: "",
            },
            totalAsPerReportingCurrency: 0,
        }
    },
    methods: {
        async getData() {
            this.loading = true;
            try {
                await this.getBanksMonitoring();
                await this.getCompanies();
            } finally {
                this.loading = false;
            }
        },
        async getBanksMonitoring() {
            const response = await axios.get("https://10.0.1.17/CrescoSage/api/v1/xtracta/bankstatusmonitoring");
            this.bank_last_update = response.data;
        },
        async getCompanies(){
            var self = this;
            const dateRange = JSON.parse(localStorage.getItem('dateRange'));
            const fromdate = DateTime.fromISO(dateRange[0]).toFormat('dd/MM/yyyy');
            const todate = DateTime.fromISO(dateRange[1]).toFormat('dd/MM/yyyy');
            const sageUrl = 'https://10.0.1.17/CrescoSage/api/V1/FOBank/2/Transactions';
            const user = "Felvin";
            const url = sageUrl + "?fromdate=" + fromdate  + "&todate=" + todate + "&currency=" + this.currency + "&user=" + user;

            try {
                const response = await axios.post(url)
                let companies = _.sortBy(response.data.companies, 'companyName');
                _.forEach(companies, function (company) {
                    _.forEach(company.banks, function (bank) {
                        if (bank.bankDetails) {
                            let closing = bank.bankDetails.find(obj => obj.SourceApp === "Closing");
                            if (closing) {
                                bank.closing = closing.DepositAmt;
                            } else {
                                bank.closing = 0;
                            }
                        }
                        let update = self.bank_last_update.find(obj => obj.CompanyCode === bank.sageCompanyCode && obj.GLBankCode === bank.sageBankCode);
                        if (update) {
                            bank.lastUpdate = update;
                            //statement amount - sage amount
                            bank.difference = update.ClosingBalAmt - bank.closing;
                        }
                    });
                });
                self.companies = companies;
            } catch (error){
                console.error(error)
            }
        },
        getFrequency(frequencyId) {
            switch (frequencyId) {
                case 1976:
                    return "Daily";
                case 1977:
                    return "Weekly";
                case 1978:
                    return "Monthly";
                case 1979:
                    return "Quarterly";
                case 1980:
                    return "Half Year";
                case 1981:
                    return "Yearly";
                default:
                    return "";
            }
        },
        formatDateRange(dateRange){
            if (!dateRange || typeof dateRange !== 'string') {
                console.error("Invalid date range format:", dateRange);
                return "";
            }

            const inputFormat = "dd/MM/yyyy";
            const outputFormat = "dd LLL yyyy";

            // Split the dateRange by " - " to get the two dates
            const dates = dateRange.split(" - ");

            if (dates.length !== 2) {
                console.error("Date range does not contain two dates:", dateRange);
                return "";
            }

            // Parse and format each date individually
            const startDate = DateTime.fromFormat(dates[0].trim(), inputFormat);
            const endDate = DateTime.fromFormat(dates[1].trim(), inputFormat);

            // Check if both dates are valid
            if (!startDate.isValid || !endDate.isValid) {
                console.error("Invalid date range:", dateRange);
                return "";
            }

            // Return the formatted date range
            return `${startDate.toFormat(outputFormat)} - ${endDate.toFormat(outputFormat)}`;
        },
        checkLastUpdate(lastUpdate, frequencyId) {
            if (lastUpdate && lastUpdate.LastTranUpdate) {
                const today = DateTime.now();
                let lastUpdateDate = DateTime.fromFormat(lastUpdate.LastTranUpdate, "dd/LLL/yyyy HH:mm:ss");

                let weekdayCount = 0;

                // Loop from the given date to today
                while (lastUpdateDate < today) {
                    // Check if the day is not Saturday (6) or Sunday (7)
                    if (lastUpdateDate.weekday < 6) {
                        weekdayCount++;
                    }
                    // Move to the next day
                    lastUpdateDate = lastUpdateDate.plus({days: 1});
                }

                let maxDays = 0;
                let warningDays = 0;
                switch (frequencyId) {
                    //Daily
                    case 1976:
                        maxDays = 1;
                        warningDays = 1;
                        break;
                    //Weekly
                    case 1977:
                        maxDays = 5;
                        warningDays = 3;
                        break;
                    //Monthly
                    case 1978:
                        maxDays = 30;
                        warningDays = 20;
                        break;
                    //Quarterly
                    case 1979:
                        maxDays = 90;
                        warningDays = 80;
                        break;
                    //Half Year
                    case 1980:
                        maxDays = 180;
                        warningDays = 170;
                        break;
                    //Yearly
                    case 1981:
                        maxDays = 365;
                        warningDays = 170;
                        break;
                    default:
                        maxDays = 1;
                }

                if (weekdayCount > maxDays && frequencyId) {
                    return {backgroundColor: 'red', color: 'white'};
                } else if (weekdayCount > warningDays && frequencyId) {
                    return {backgroundColor: 'orange', color: 'white'};
                } else {
                    return {}; // Default style
                }
            } else {
                return {};
            }


        },
        formatDateTime(value) {
            if (value) {
                const inputFormat = "dd/MMM/yyyy HH:mm:ss"; // Adjusted input format
                const outputFormat = "dd LLL yyyy HH:mm:ss"; // Desired output format

                // Parse the date with the input format
                let dateTime = DateTime.fromFormat(value, inputFormat);

                // Check if the date is valid
                if (!dateTime.isValid) {
                    console.error("Invalid date:", value);
                    return "";
                }

                // Return formatted date
                return dateTime.toFormat(outputFormat);
            } else {
                return "";
            }
        },
        ecapeDownloadUrl(url) {
            let convertedString = "smb:" + url.replace(/\\\\/g, '\\').replace(/\\/g, '/').replace(/\/\s+/g, '/').replace(/\s+\//g, '/');
            return convertedString;
        },
        checkDifference(bank) {
            if (bank.difference || bank.difference === 0) {
                const difference = Math.abs(bank.difference);
                if (difference < 5) {
                    return {backgroundColor: 'green', color: 'white'};
                } else if (difference < 100) {
                    return {backgroundColor: 'orange', color: 'white'};
                } else if (difference >= 100) {
                    return {backgroundColor: 'red', color: 'white'};
                } else {
                    return {}; // Default style
                }
            } else {
                return {};
            }
        },
    },
    computed:{
        filteredCompanies() {
            const filterCodes = this.filters.sage_company_code ? [this.filters.sage_company_code] : this.page_data.sage_companies_codes.map(obj => obj.sage_company_code);
            return this.companies.filter(company => filterCodes.includes(company.sageDBCode));
        },
        // filteredBanks() {
        //     const filterCodes = this.filters.sage_company_code ? [this.filters.sage_company_code] : this.page_data.sage_companies_codes.map(obj => obj.sage_company_code);
        //     return this.banks.filter(bank => filterCodes.includes(bank.CompanyCode));
        // },
    },
    mounted() {
    }
}
</script>
<style scoped>

</style>
