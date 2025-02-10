<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="show_bank_transactions_modal">
        <div class="modal-content top-[5%] lg:max-w-[1200px] max-h-[88vh]">
            <div class="modal-header bg-cresco_red">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">{{ bankDetails.companyName }} - {{ bankDetails.bankName }} Transactions</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross"></i>
                </button>
            </div>

            <div class="modal-body overflow-y-auto">
                <h3 class="text-black text-lg font-semibold tracking-tight mb-5">{{ dateRangePickerText }}</h3>

                <div data-accordion="true" data-accordion-expand-all="false">
                    <template v-for="(bank, index) in banks" :key="`bank_${index}`">
                        <div :class="['accordion-item', {'active' : index === 0}]"
                             data-accordion-item="true"
                             :aria-expanded="index === 0 ? 'true' : 'false'"
                             :id="`accordion_item_${index + 1}`"
                        >
                            <button class="accordion-toggle group bg-white text-black p-4" :data-accordion-toggle="`#accordion_content_${index + 1}`">
                                <span class="text-base text-neutral-800 font-semibold accordion-title">{{ bank.bankName }} - {{ bank.bankCurrency }}</span>
                                <i class="ki-outline ki-plus text-brand-active text-2sm accordion-active:hidden block"></i>
                                <i class="ki-outline ki-minus text-brand-active text-2sm accordion-active:block hidden"></i>
                            </button>

                            <div class="accordion-content bg-white" :id="`accordion_content_${index + 1}`" :class="{ hidden: index !== 0 }">
                                <!-- table -->
                                <div class="relative flex-grow overflow-auto reports-table-container max-h-[60vh]">
                                    <table class="w-full c-table table table-border align-middle text-xs table-fixed">
                                        <thead>
                                            <tr class="text-center tracking-tight">
                                                <th class="sticky top-0 w-10">#</th>
                                                <th class="sticky top-0 w-[100px]">Transaction Date</th>
                                                <th class="sticky top-0 w-[350px] text-left">Transaction No.</th>
                                                <th class="sticky top-0 w-[100px] text-right">Deposit</th>
                                                <th class="sticky top-0 w-[100px] text-right">Withdrawal</th>
                                                <th class="sticky top-0 w-[100px]"></th>
                                                <th class="sticky top-0 w-[100px]"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center text-xs tracking-tight">
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
                                                <tr class="table-no-data-available">
                                                    <td class="text-center text-md text-red-400">No data available</td>
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
                                    <div class="text-xs text-right mt-3 mb-3 mr-2">Exchange Rate: 1 {{ bank.bankCurrency }} to {{ currency }} = {{ formatAmount(bank.reportCurrencyRate) }}</div>
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
    name: "show-bank-transactions-modal",
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
            console.log(transaction.SourceApp)
            // Sales Invoices
            if(transaction.SourceApp === 'AR - Receipt'){
                return `${window.location.origin}/reports/sales-invoices?search=${transaction.transactionNo}`
            }
            // Cash Request
            if(transaction.SourceApp === 'AP - Misc. Payment'){
                return `${window.location.origin}/reports/cash-requests?search=${transaction.transactionNo}`
            }
            // Purchase Invoices
            if(transaction.SourceApp === 'AP - Payment'){
                return `${window.location.origin}/reports/purchase-invoices?search=${transaction.transactionNo}`
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
