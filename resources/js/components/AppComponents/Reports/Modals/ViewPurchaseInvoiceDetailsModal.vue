<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="view_purchase_invoice_details_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px] max-h-[80vh]">
            <div class="modal-header">
                <h3 class="text-xl font-bold tracking-tight capitalize modal-title">Purchase Invoice Details</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross"></i>
                </button>
            </div>
            <div class="relative h-full overflow-auto modal-body">
                <!-- Loading Spinner -->
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center pointer-events-none data-loading bg-neutral-100 z-100">
                    <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                        <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>

                <!-- Modal Content -->
                <div v-else class="flex flex-col items-start justify-start w-full pb-5 divide-y divide-neutral-200">
                    <!-- Bank Details -->
                    <div class="grid w-full grid-cols-7 gap-8">
                        <div class="col-span-2 mt-4 font-semibold text-right text-black text-md">
                            <a class="btn btn-link text-2xl !text-black hover:!text-brand-active font-bold tracking-tight" target="_blank" :href="`https://crm.cresco.ae/bizproc/processes/104/element/0/${obj.id}/?list_section_id=`">
                                <span>{{ formatAmount(obj.amount) }}</span>
                                <span>{{ obj.currency }}</span>
                            </a>
                        </div>
                        <div class="col-span-3 mb-4 content-box">
                            <div class="mt-1 mb-3 tracking-wide uppercase text-neutral-500">Invoice Details</div>
                            <div>Number: {{ obj.invoice_number }}</div>
                            <div>Date: {{ formatDate(obj.invoice_date) }}</div>
                            <div>Due Date: {{ formatDate(obj.due_date) }}</div>
                            <div>Payment Schedule Date: {{ formatDate(obj.payment_schedule_date) }}</div>
                        </div>
                        <div class="content-box col-span-2 !text-xs text-right !pr-0"><a class="secondary-btn" target="_blank" :href="purchaseInvoiceLink">View Purchase Invoice</a></div>
                    </div>

                    <!-- Purpose of Transfer -->
                    <div class="grid w-full grid-cols-7 gap-8 py-4">
                        <div class="col-span-2 py-4 font-semibold text-right text-black text-md">Remarks</div>
                        <div class="col-span-5 py-4 pl-8 content-box">{{ obj.detail_text }}</div>
                    </div>

                    <!-- Documents -->
                    <div class="grid w-full grid-cols-7 gap-8 pt-4 pb-0">
                        <div class="col-span-2 py-4 font-semibold text-right text-black text-md">Documents</div>
                        <div v-if="obj.document_list.length" class="col-span-5 py-4 pl-8">
                            <a v-for="(documentId, index) in obj.document_list" :key="'swift_' + index"
                               class="mb-1 secondary-btn" target="_blank"
                               :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1`">
                                Invoice doc {{ index + 1 }}
                            </a>
                        </div>
                        <div v-else class="col-span-5 py-4 pl-8 text-md">No Documents</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import qs from 'qs';
import {DateTime} from "luxon";
export default {
    name: "view-purchase-invoice-details-modal",
    props: ['obj_id'],
    data(){
        return {
            loading: false,
            obj: {
                document_list: []
            },
        }
    },
    methods: {
        async getData() {
            this.loading = true;
            const bitrixUserId = this.sharedState.bitrix_user_id;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
            const endpoint = 'crm.company.reports_v2';
            try {
                const requestData = {
                    action: "getPurchaseInvoiceById",
                    id: this.obj_id
                }
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    this.obj = response.result
                    this.obj.document_list = [];
                    if (this.obj.transfer_documents_id) {
                        this.obj.document_list = this.obj.transfer_documents_id ? this.obj.transfer_documents_id.split(",") : [];
                    }
                    this.loading = false
                }
            } catch (error) {
                console.log(error.response)
            }
        },
    },
    computed: {
        purchaseInvoiceLink() {
            if (!this.obj.payment_schedule_date) return "#";
            const date = DateTime.fromFormat(this.obj.payment_schedule_date, "yyyy-MM-dd");
            if (date.isValid) {
                const formattedDate = date.toFormat("dd.MM.yyyy");
                return `${window.location.origin}/reports/purchase-invoices?id=${this.obj.id}&date=${formattedDate}`;
            } else {
                console.error("Invalid Date:", this.obj.payment_schedule_date);
                return "#";
            }
        }
    },
    mounted() {
        this.getData();
    }
}
</script>


<style scoped>
/* Utility class for centering content */
.flex-center {
    @apply flex items-center justify-center;
}
</style>
