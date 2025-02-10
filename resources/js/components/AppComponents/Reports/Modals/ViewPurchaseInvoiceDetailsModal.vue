<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="view_purchase_invoice_details_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px] max-h-[80vh]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Purchase Invoice Details</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross"></i>
                </button>
            </div>
            <div class="modal-body relative h-full overflow-auto">
                <!-- Loading Spinner -->
                <div v-if="loading" class="data-loading absolute inset-0 bg-neutral-100 flex items-center justify-center z-100 pointer-events-none">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm text-brand-active">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>

                <!-- Modal Content -->
                <div v-else class="flex flex-col justify-start items-start divide-y divide-neutral-200 w-full pb-5">
                    <!-- Bank Details -->
                    <div class="grid grid-cols-7 gap-8 w-full">
                        <div class="col-span-2 text-md font-semibold text-black text-right mt-4">

                            <a class="btn btn-link text-2xl !text-black hover:!text-brand-active font-bold tracking-tight" target="_blank" :href="`https://crm.cresco.ae/bizproc/processes/104/element/0/${obj.id}/?list_section_id=`">
                                <span>{{ formatAmount(obj.amount) }}</span>
                                <span>{{ obj.currency }}</span>
                            </a>
                        </div>
                        <div class="content-box col-span-3 !text-xs mb-4">
                            Invoice Details
                            <div>Number: {{ obj.invoice_number }}</div>
                            <div>Date: {{ formatDate(obj.invoice_date) }}</div>
                            <div>Due Date: {{ formatDate(obj.due_date) }}</div>
                            <div>Payment Schedule Date: {{ formatDate(obj.payment_schedule_date) }}</div>
                        </div>
                        <div class="content-box col-span-2 !text-xs text-right !pr-0"><a class="secondary-btn" target="_blank" :href="purchaseInvoiceLink">View Purchase Invoice</a></div>
                    </div>

                    <!-- Purpose of Transfer -->
                    <div class="grid grid-cols-7 gap-8 w-full py-4">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Remarks</div>
                        <div class="content-box col-span-3 pl-8">{{ obj.detail_text }}</div>
                    </div>

                    <!-- Documents -->
                    <div class="grid grid-cols-7 gap-8 w-full pt-4 pb-0">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Documents</div>
                        <div v-if="obj.document_list.length" class="col-span-3 pl-8 py-3.5">
                            <a v-for="(documentId, index) in obj.document_list" :key="'swift_' + index"
                               class="secondary-btn mb-1" target="_blank"
                               :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1`">
                                Invoice doc {{ index + 1 }}
                            </a>
                        </div>
                        <div v-else class="col-span-3 pl-8">No Documents</div>
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

/* Common styling for content boxes */
.content-box {
    @apply mt-3 text-sm bg-gray-200 p-3 text-black whitespace-pre-line;
}
</style>
