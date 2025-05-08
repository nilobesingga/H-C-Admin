<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="view_cash_request_details_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px] max-h-[80vh]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Cash Request Details</h3>
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
                            <a class="btn btn-link text-2xl !text-black hover:!text-brand-active font-bold tracking-tight" target="_blank" :href="`https://crm.cresco.ae/bizproc/processes/105/element/0/${obj.id}/?list_section_id=`">
                                <span>{{ formatAmount(obj.amount) }}</span>
                                <span>{{ obj.currency }}</span>
                            </a>
                        </div>
                        <div class="content-box col-span-3 mb-4">
<!--                            <div class="uppercase tracking-wide text-neutral-500 mt-1 mb-3">Cash Request Details</div>-->
                            <div>Cash Pool: {{ obj.cash_pool }}</div>
                            <div>Location: {{ obj.cash_release_location }}</div>
                            <div>Payment Date: {{ formatDate(obj.payment_date) }}</div>
                            <div>Payment Mode: {{ obj.payment_mode }}</div>
                            <div>Funds Available Date: {{ formatDate(obj.funds_available_date) }}</div>
                        </div>
                        <div class="content-box col-span-2 !text-xs text-right !pr-0"><a class="secondary-btn" target="_blank" :href="cashRequestLink">View Cash Request</a></div>
                    </div>

                    <!-- Purpose of Transfer -->
                    <div class="grid grid-cols-7 gap-8 w-full py-4">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Project</div>
                        <div class="content-box col-span-5 pl-8 py-4">
                            <a class="btn btn-link !text-black hover:!text-brand-active" :href="getBitrixProjectLink(obj)" target="_blank">{{ obj.project_name }}</a>
                        </div>
                    </div>
                    <!-- Remarks -->
                    <div class="grid grid-cols-7 gap-8 w-full py-4">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Remarks</div>
                        <div class="content-box col-span-5 pl-8 py-4">{{ obj.detail_text }}</div>
                    </div>
                    <!-- Created By -->
                    <div class="grid grid-cols-7 gap-8 w-full py-4">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Created By</div>
                        <div class="content-box col-span-5 pl-8 py-4"><span class="font-semibold text-black">{{ obj.created_by_text }}</span> on {{ formatDate(obj.date_create) }}</div>
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
    name: "view-cash-request-details-modal",
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
                    action: "getCashRequestById",
                    id: this.obj_id
                }
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    this.obj = response.result
                    this.obj.doc_for_bank_list = [];
                    this.obj.other_document_list = [];
                    this.obj.cash_release_receipt_doc_list = [];
                    this.obj.receipt_list = [];

                    if (this.obj.doc_for_bank){
                        this.obj.doc_for_bank_list = this.obj.doc_for_bank.split(",");
                    }
                    if (this.obj.other_documents){
                        this.obj.other_document_list = this.obj.other_documents.split(",");
                    }
                    if (this.obj.cash_release_receipt_docs){
                        this.obj.cash_release_receipt_doc_list = this.obj.cash_release_receipt_docs.split(",");
                    }
                    if (this.obj.receipt_id){
                        this.obj.receipt_list = this.obj.receipt_id.split(",");
                    }
                    this.loading = false
                }
            } catch (error) {
                console.log(error.response)
            }
        },
    },
    computed: {
        cashRequestLink() {
            if (!this.obj.payment_date) return "#";
            const date = DateTime.fromFormat(this.obj.payment_date, "yyyy-MM-dd");
            if (date.isValid) {
                const formattedDate = date.toFormat("dd.MM.yyyy");
                return `${window.location.origin}/reports/cash-requests?search=${this.obj.id}&date=${formattedDate}`;
            } else {
                console.error("Invalid Date:", this.obj.payment_date);
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
