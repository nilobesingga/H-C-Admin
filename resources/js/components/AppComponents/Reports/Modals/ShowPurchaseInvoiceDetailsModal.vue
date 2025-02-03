<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="show_bank_transfer_details_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-black">Bank Transfer Details</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross"></i>
                </button>
            </div>
            <div class="modal-body relative h-full overflow-auto">
                <!-- Loading Spinner -->
                <div v-if="loading" class="absolute inset-0 bg-gray-300 bg-opacity-50 flex items-center justify-center z-50">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
                <!-- Modal Content -->
                <div v-else>
                    <div class="flex-center">
                        <a class="btn btn-link text-3xl" target="_blank" :href="`https://crm.cresco.ae/services/lists/99/element/0/${obj.id}/?list_section_id=`">
                            <span>{{ formatAmount(obj.amount) }}</span>&nbsp;
                            <span>{{ obj.currency }}</span>
                        </a>
                    </div>
                    <div class="flex-center">{{ obj.status_text }}</div>
                    <div class="flex-center">{{ formatDate(obj.transfer_date) }}</div>
                    <!-- Bank Details -->
                    <div class="text-sm text-black mt-5">Bank Details</div>
                    <div class="content-box">{{ obj.preview_text }}</div>

                    <!-- Purpose of Transfer -->
                    <div class="text-sm text-black mt-5">Purpose of Transfer</div>
                    <div class="content-box">{{ obj.detail_text }}</div>

                    <!-- Documents -->
                    <div class="text-sm text-black mt-5">Documents</div>
                    <div v-if="obj.swift_copy_array.length || obj.transfer_documents.length" class="content-box">
                        <a v-for="(documentId, index) in obj.swift_copy_array" :key="'swift_' + index"
                           class="btn btn-sm btn-outline btn-primary mb-1" target="_blank"
                           :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1`">
                            <i class="ki-filled ki-file-down"></i>
                            <span>Swift doc {{ index + 1 }}</span>
                        </a>
                        <a v-for="(documentId, index) in obj.transfer_documents" :key="'transfer_' + index"
                           class="btn btn-sm btn-outline btn-primary mb-1" target="_blank"
                           :href="`https://crm.cresco.ae/bitrix/tools/disk/uf.php?attachedId=${documentId}&action=download&ncc=1`">
                            <i class="ki-filled ki-file-down"></i>
                            <span>Transfer doc {{ index + 1 }}</span>
                        </a>
                    </div>
                    <div v-else>No Documents</div>

                    <!-- Project -->
                    <div class="text-sm text-black mt-5">Project</div>
                    <div class="content-box">
                        <a class="btn btn-link" target="_blank" :href="getBitrixProjectLink(obj)">{{ obj.project_name }}</a>
                    </div>
                    <!-- Bank Transfer -->
                    <div class="text-sm text-black mt-5">Bank Transfer</div>
                    <div class="content-box">
                        <a class="btn btn-link" target="_blank" :href="bankTransferLink">View Bank Transfer</a>
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
    name: "show-purchase-invoice-details-modal",
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
        console.log(this.obj_id);
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
