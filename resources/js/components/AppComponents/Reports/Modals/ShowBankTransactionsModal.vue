<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="show_bank_transactions_modal">
        <div class="modal-content top-[5%] lg:max-w-[1500px]">
            <div class="modal-header bg-cresco_red">
                <h3 class="modal-title capitalize text-white">{{ bankDetails.companyName }} - {{ bankDetails.bankName }} Transactions</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross"></i>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="text-black text-center mb-5">{{ dateRangePickerText }}</h3>
                <div data-accordion="true" data-accordion-expand-all="false">
                    <template v-for="(bank, index) in banks" :key="`bank_${index}`">
                        <div :class="['accordion-item border-b border-b-gray-200', {'active' : index === 0}]"
                             data-accordion-item="true"
                             :aria-expanded="index === 0 ? 'true' : 'false'"
                             :id="`accordion_item_${index + 1}`"
                        >
                            <button class="accordion-toggle group bg-cresco_red text-white p-3" :data-accordion-toggle="`#accordion_content_${index + 1}`">
                                <span class="text-base text-white font-medium">{{ bank.bankName }} - {{ bank.bankCurrency }}</span>
                                <i class="ki-outline ki-plus text-white text-2sm accordion-active:hidden block"></i>
                                <i class="ki-outline ki-minus text-white text-2sm accordion-active:block hidden"></i>
                            </button>
                            <div class="accordion-content" :id="`accordion_content_${index + 1}`" :class="{ hidden: index !== 0 }">
                                <!-- table -->
                                <div class="relative flex-grow overflow-auto reports-table-container px-4">
                                    <table class="w-full table table-border align-middle table-fixed">
                                        <thead>
                                            <tr class="bg-black text-gray-900 font-medium text-center">
                                                <th class="sticky top-0 w-10">#</th>
                                                <th class="sticky top-0 w-[120px]">Transaction Date</th>
                                                <th class="sticky top-0 w-[300px] text-left">Transaction No.</th>
                                                <th class="sticky top-0 w-[150px] text-right">Deposit</th>
                                                <th class="sticky top-0 w-[150px] text-right">Withdrawal</th>
                                                <th class="sticky top-0 w-[100px]"></th>
                                                <th class="sticky top-0 w-[100px]"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center text-gray-700 leading-custom-normal">
                                            <tr class="odd:bg-white even:bg-slate-100">
                                                <td colspan="5"></td>
                                                <td>Opening</td>
                                                <td class="text-right text-black">
                                                    <span>{{ formatAmount(bank.openingBalance) }}</span>&nbsp;
                                                    <span class="font-bold">{{ bank.bankCurrency }}</span>
                                                </td>
                                            </tr>
                                            <template v-if="bank.bankDetails.length > 0">
                                                <tr v-for="(transaction, index) in bank.bankDetails" class="odd:bg-white even:bg-slate-100">
                                                    <td>{{ ++index }}</td>
                                                    <td>{{ formatBitrixDate(transaction.DateRemit) }}</td>
                                                    <td class="text-left">
                                                        <div>{{ transaction.Comments }}</div>
                                                        <div>
                                                            <a class="btn btn-link" :href="getURL(transaction)" target="_blank">{{ transaction.transactionNo }}</a>
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
                                                    <td class="text-center text-red-400">No data available</td>
                                                </tr>
                                            </template>
                                            <tr class="odd:bg-white even:bg-slate-100">
                                                <td colspan="5"></td>
                                                <td>Closing</td>
                                                <td class="text-right text-black">
                                                    <span>{{ formatAmount(bank.closingBalance) }}</span>&nbsp;
                                                    <span class="font-bold">{{ bank.bankCurrency }}</span>
                                                </td>
                                            </tr>
                                            <tr class="odd:bg-white even:bg-slate-100">
                                                <td colspan="5"></td>
                                                <td>Reporting Amount</td>
                                                <td class="text-right text-black">
                                                    <span>{{ formatAmount(bank.reportingAmount) }}</span>&nbsp;
                                                    <span class="font-bold">{{ bank.reportCurrency }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-xs text-right mt-3 mb-3">Exchange Rate: 1 {{ bank.bankCurrency }} to {{ currency }} = {{ formatAmount(bank.reportCurrencyRate) }}</div>
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


<style scoped>

</style>
