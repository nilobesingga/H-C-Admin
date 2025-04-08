<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="show_bank_transactions_modal">
        <div class="modal-content top-[5%] lg:max-w-[1200px] max-h-[88vh]">
            <div class="modal-header bg-cresco_red">
                <h3 class="text-xl font-bold tracking-tight capitalize modal-title">{{ bankDetails.companyName }} - {{ bankDetails.bankName }} Transactions</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross"></i>
                </button>
            </div>

            <div class="overflow-y-auto modal-body">
                <h3 class="mb-5 text-lg font-semibold tracking-tight text-black">{{ dateRangePickerText }}</h3>

                <div data-accordion="true" data-accordion-expand-all="false">
                    <template v-for="(bank, index) in banks" :key="`bank_${index}`">
                        <div :class="['accordion-item', {'active' : index === 0}]"
                             data-accordion-item="true"
                             :aria-expanded="index === 0 ? 'true' : 'false'"
                             :id="`accordion_item_${index + 1}`"
                        >
                            <button class="p-4 text-black bg-white accordion-toggle group" :data-accordion-toggle="`#accordion_content_${index + 1}`">
                                <span class="text-base font-semibold text-neutral-800 accordion-title">{{ bank.bankName }} - {{ bank.bankCurrency }}</span>
                                <i class="block ki-outline ki-plus text-brand-active text-2sm accordion-active:hidden"></i>
                                <i class="hidden ki-outline ki-minus text-brand-active text-2sm accordion-active:block"></i>
                            </button>

                            <div class="bg-white accordion-content" :id="`accordion_content_${index + 1}`" :class="{ hidden: index !== 0 }">
                                <!-- table -->
                                <div class="relative flex-grow overflow-auto reports-table-container max-h-[60vh]">
                                    <table class="table w-full text-xs align-middle table-fixed c-table table-border">
                                        <thead>
                                            <tr class="tracking-tight text-center">
                                                <th class="sticky top-0 w-10">#</th>
                                                <th class="sticky top-0 w-[100px]">Transaction Date</th>
                                                <th class="sticky top-0 w-[350px] text-left">Transaction No.</th>
                                                <th class="sticky top-0 w-[100px] text-right">Deposit</th>
                                                <th class="sticky top-0 w-[100px] text-right">Withdrawal</th>
                                                <th class="sticky top-0 w-[100px]"></th>
                                                <th class="sticky top-0 w-[100px]"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-xs tracking-tight text-center">
                                            <tr class="transition-all duration-300 text-neutral-800">
                                                <td colspan="5"></td>
                                                <td>Opening</td>
                                                <td class="text-right text-black">
                                                    <span>{{ formatAmount(bank.openingBalance) }}</span>&nbsp;
                                                    <span class="font-bold">{{ bank.bankCurrency }}</span>
                                                </td>
                                            </tr>
                                            <template v-if="bank.bankDetails.length > 0">
                                                <tr v-for="(transaction, index) in bank.bankDetails" class="transition-all duration-300 text-neutral-800">
                                                    <td>{{ ++index }}</td>
                                                    <td>{{ formatBitrixDate(transaction.DateRemit) }}</td>
                                                    <td class="text-left">
                                                        <div>{{ transaction.Comments }}</div>
                                                        <div>
                                                            <a class="btn btn-link !text-black hover:!text-brand-active" :href="getURL(transaction)" target="_blank">{{ transaction.transactionNo }}</a>
                                                        </div>
                                                    </td>
                                                    <td class="text-right">{{ formatAmount(transaction.DepositAmt) }}</td>
                                                    <td class="text-right">{{ formatAmount(transaction.WithdrawalAmt) }}</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr class="h-full table-no-data-available">
                                                    <td class="text-center text-red-400 !border-none h-full">
                                                        <div class="flex flex-col items-center justify-center w-full h-full py-4">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-2 size-8">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                            No data available
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr class="transition-all duration-300 text-neutral-800">
                                                <td colspan="5"></td>
                                                <td>Closing</td>
                                                <td class="text-right text-black">
                                                    <span>{{ formatAmount(bank.closingBalance) }}</span>&nbsp;
                                                    <span class="font-bold">{{ bank.bankCurrency }}</span>
                                                </td>
                                            </tr>
                                            <tr class="transition-all duration-300 text-neutral-800">
                                                <td colspan="5"></td>
                                                <td>Reporting Amount</td>
                                                <td class="text-right text-black">
                                                    <span>{{ formatAmount(bank.reportingAmount) }}</span>&nbsp;
                                                    <span class="font-bold">{{ bank.reportCurrency }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="mt-3 mb-3 mr-2 text-xs text-right">Exchange Rate: 1 {{ bank.bankCurrency }} to {{ currency }} = {{ formatAmount(bank.reportCurrencyRate) }}</div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {DateTime} from "luxon";

export default {
    name: "view-bank-transactions-modal",
    props: ['bitrix_sage_company_mapping'],
    data(){
        return {
            banks: [],
            bankDetails: {},
        }
    },
    methods: {
        showTransactions(banks){
            //filter banks with no transactions
            this.banks = banks.filter(bank => bank.bankDetails != null);

            //loop through banks and get opening and closing balance
            this.banks.forEach(bank => {
                bank.sageBankCode = bank.sageBankCode.trim()

                bank.openingBalance = this.getBalance(bank.bankDetails, "Opening");
                bank.closingBalance = this.getBalance(bank.bankDetails, "Closing");

                bank.reportingAmount = bank.reportingAmount;


                bank.totalDebit = bank.bankDetails
                    .filter(transaction => transaction.SourceApp != "Closing")
                    .reduce((total, transaction) => total + transaction.DepositAmt, 0);
            });

            //remove opening and closing transactions from bank details
            this.banks.forEach(bank => {
                bank.bankDetails = bank.bankDetails.filter(transaction => transaction.SourceApp != "Opening" && transaction.SourceApp != "Closing");
            });

            let details = {
                companyName: banks.length > 0 ? banks[0].companyName : '',
                bankName: banks.length > 0 ? banks[0].bankName : '',
                sageCompanyCode: banks.length > 0 ? banks[0].sageCompanyCode : '',
                type: banks.length > 0 ? banks[0].type == 'C' ? 'Cash' : banks[0].type == 'I' ? 'Invested' : 'Reserved' : '',
            }
            this.bankDetails = details;
        },
        getBalance(bankTransactions, type) {
            var openOrCloseTransaction = bankTransactions.filter(transaction => transaction.SourceApp === type);
            if (openOrCloseTransaction.length > 0) {
                return openOrCloseTransaction[0].transactionAmount;
            } else {
                return 0;
            }
        },
        getURL(transaction){
            // Sales Invoices
            if(transaction.SourceApp === 'AR - Receipt'){
                return `${window.location.origin}/reports/sales-invoices?search=${transaction.transactionNo}&date=${DateTime.fromFormat(transaction.tranDate,'dd/MM/yyyy').toFormat("dd.MM.yyyy")}`
            }
            // Cash Request
            if(transaction.SourceApp === 'AP - Misc. Payment'){
                return `${window.location.origin}/reports/cash-requests?search=${transaction.transactionNo}&date=${DateTime.fromFormat(transaction.tranDate,'dd/MM/yyyy').toFormat("dd.MM.yyyy")}`
            }
            // Purchase Invoices
            if(transaction.SourceApp === 'AP - Payment'){
                return `${window.location.origin}/reports/purchase-invoices?search=${transaction.transactionNo}&date=${DateTime.fromFormat(transaction.tranDate,'dd/MM/yyyy').toFormat("dd.MM.yyyy")}`
            }

            if(transaction.SourceApp === 'BK'){
                return `${window.location.origin}/reports/bank-transfers?search=${transaction.transactionNo}&date=${DateTime.fromFormat(transaction.tranDate,'dd/MM/yyyy').toFormat("dd.MM.yyyy")}`
            }
        },
    },
    computed: {
        dateRangePickerText(){
            const dateRange = JSON.parse(localStorage.getItem('dateRange'));
            const formattedStart = DateTime.fromISO(dateRange[0]).toFormat("d MMMM yyyy");
            const formattedEnd = DateTime.fromISO(dateRange[1]).toFormat("d MMMM yyyy");
            return `${formattedStart} to ${formattedEnd}`;
        },
    },
    mounted() {
        const accordionElements = document.querySelectorAll('[data-accordion="true"]');

        accordionElements.forEach((accordion) => {
            if (!window.KTAccordion.getInstance(accordion)) {
                new window.KTAccordion(accordion);
            }
        });
    }
}
</script>
