<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="show_bank_transfer_details_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px] max-h-[80vh]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Bank Transfer Details</h3>
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
                            <a class="btn btn-link text-2xl !text-black hover:!text-brand-active font-bold tracking-tight" target="_blank" :href="`https://crm.cresco.ae/services/lists/99/element/0/${obj.id}/?list_section_id=`">
                                <span>{{ formatAmount(obj.amount) }}</span>
                                <span>{{ obj.currency }}</span>
                            </a>
                            <div class="mb-4 text-sm text-neutral-500 font-normal">{{ formatDate(obj.transfer_date) }}</div>
                            <span class="px-3 py-1 text-xs rounded-full font-semibold" :class="obj.status_text === 'Completed' ? 'text-emerald-600 bg-emerald-500/10' : 'text-neutral-600 bg-neutral-500/10'">{{ obj.status_text }}</span>
                        </div>
                        <div class="content-box col-span-3 mb-4 mt-1">{{ obj.preview_text }}</div>
                        <div class="content-box col-span-2 !text-xs text-right !pr-0"><a class="secondary-btn" target="_blank" :href="bankTransferLink">View Bank Transfer</a></div>
                    </div>

                    <!-- Purpose of Transfer -->
                    <div class="grid grid-cols-7 gap-8 w-full py-4">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Purpose of Transfer</div>
                        <div class="content-box col-span-5 pl-8">{{ obj.detail_text }}</div>
                    </div>

                    <!-- Documents -->
                    <div class="grid grid-cols-7 gap-8 w-full pt-4 pb-0">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Documents</div>
                        <div v-if="obj.swift_copy_array.length || obj.transfer_documents.length" class="col-span-5 pl-8 py-4">
                            <a v-for="(documentId, index) in obj.swift_copy_array" :key="'swift_' + index"
                               class="secondary-btn mb-1" target="_blank"
                               :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1`">
                                Swift doc {{ index + 1 }}
                            </a>
                            <a v-for="(documentId, index) in obj.transfer_documents" :key="'transfer_' + index"
                               class="secondary-btn mb-1" target="_blank"
                               :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1`">
                                Transfer doc {{ index + 1 }}
                            </a>
                        </div>
                        <div v-else class="col-span-5 pl-8 py-4">No Documents</div>
                    </div>

                    <!-- Project -->
                    <div v-if="obj.project_name" class="grid grid-cols-7 mt-4 gap-8 w-full pt-4">
                        <div class="col-span-2 text-md font-semibold text-black text-right py-4">Project</div>
                        <div class="col-span-3 pl-8 py-4"><a class="btn btn-link !text-black hover:!text-brand-active" target="_blank" :href="getBitrixProjectLink(obj)">{{ obj.project_name }}</a></div>
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
    name: "view-bank-transfer-details-modal",
    props: ['obj_id'],
    data(){
        return {
            loading: false,
            obj: {
                swift_copy_array: [],
                transfer_documents: []
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
                    action: "getBankTransferById",
                    id: this.obj_id
                }
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if (response.result){
                    this.obj = response.result
                    this.obj.swift_copy_array = [];
                    this.obj.transfer_documents = [];
                    if (this.obj.swift_copy){
                        this.obj.swift_copy_array = this.obj.swift_copy ? this.obj.swift_copy.split(",") : [];
                    }
                    if (this.obj.transfer_documents_id) {
                        this.obj.transfer_documents = this.obj.transfer_documents_id ? this.obj.transfer_documents_id.split(",") : [];
                    }
                    this.loading = false
                }
            } catch (error) {
                console.log(error.response)
            }
        },
    },
    computed: {
        bankTransferLink() {
            if (!this.obj.date_create) return "#";

            const date = DateTime.fromFormat(this.obj.date_create, "yyyy-MM-dd HH:mm:ss");
            if (date.isValid) {
                const formattedDate = date.toFormat("dd.MM.yyyy");
                return `${window.location.origin}/reports/bank-transfers?id=${this.obj.id}&date=${formattedDate}`;
            } else {
                console.error("Invalid Date:", this.obj.date_create);
                return "#";
            }
        }
    },
    mounted() {
        this.getData();
    }
}
</script>
