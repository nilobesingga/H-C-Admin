<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="process_purchase_invoice_details_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px] max-h-[80vh]">
            <div class="modal-header">
                <h3 class="text-xl font-bold tracking-tight capitalize modal-title">Sales Invoice</h3>
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
                <div v-else class="w-full pb-2 divide-y divide-neutral-200">
                    <!-- Bank Details -->
                    <form @submit.prevent="submit">
                        <div class="w-full">
                            <div class="p-6 bg-white rounded-lg shadow-md">
                                <!-- Left Column -->
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <a target="_blank" class="btn btn-link text-2xl !text-black hover:!text-brand-active font-bold tracking-tight" :href="`https://crm.cresco.ae/crm/company/details/${obj.company_id}/`">
                                            {{ obj.company }}
                                        </a>
                                        <a class="btn btn-link text-2xl !text-black hover:!text-brand-active font-bold tracking-tight" target="_blank" :href="`https://crm.cresco.ae/bizproc/processes/104/element/0/${obj.id}/?list_section_id=`">
                                            <span>{{ formatAmount(obj.price) }}</span>
                                            <span>{{ obj.currency }}</span>
                                        </a>
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-medium text-gray-700">Invoice Number: </span>
                                        <a class="!text-black hover:!text-brand-active" target="_blank" :href="`https://crm.cresco.ae/bizproc/processes/104/element/0/${obj.id}/?list_section_id=`">
                                            {{ obj.sage_invoice_number }}
                                        </a>
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-medium text-gray-700">Invoice Date: </span> {{ formatDate(obj.date_bill) }}
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-medium text-gray-700">Due Date: </span>
                                        <span :class="isOverDueDate(obj) ? 'text-yellow-500 font-semibold' : 'text-gray-700'">
                                            {{ formatDate(obj.date_pay_before) }}
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-medium text-gray-700">Bank Code: </span> {{ obj.bank_code }}
                                    </div>
                                    <div v-if="obj.payment_status" class="mb-2">
                                        <span class="font-medium text-gray-700">Payment Status: </span>
                                        <span
                                        class="font-semibold capitalize"
                                        :class="{
                                            'text-blue-500': obj.payment_status === 'initiated',
                                            'text-green-500': obj.payment_status === 'completed',
                                            'text-yellow-500': obj.payment_status === 'pending',
                                            'text-red-500': obj.payment_status === 'canceled',
                                            'text-orange-500': obj.payment_status === 'failed',
                                            'text-orange-500': !['initiated','completed','pending','canceled','failed'].includes(obj.payment_status)

                                        }">
                                            {{ obj.payment_status }}
                                        </span>
                                    </div>
                                    <div v-if="obj.payment_status == 'completed'" class="mb-2">
                                        <span class="font-medium text-gray-700">Payment Date: </span> {{ formatDate(obj.payment_completed_at) }}
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-medium text-gray-700">Deal: </span>
                                        <a class="!text-black hover:!text-brand-active" target="_blank" :href="`https://crm.cresco.ae/crm/deal/details/${obj.deal_id}/`">
                                            {{ obj.deal }}
                                        </a>
                                    </div>
                                    <div class="mb-4">
                                        <span class="font-medium text-gray-700">Quote: </span>
                                        <a class="!text-black hover:!text-brand-active" target="_blank" :href="`https://crm.cresco.ae/crm/quote/show/${obj.quote_id}/`">
                                            View
                                        </a>
                                    </div>
                                    <a class="block w-full mb-1 text-center secondary-btn" :href="`https://forms.cresco.ae/invoice/${obj.id}`" target="_blank">Preview & Download</a>
                                </div>


                            </div>

                            <!-- Form Section -->
                            <div class="p-6 mt-6 bg-white rounded-lg shadow-md">
                                <h4 class="mb-4 text-lg font-bold text-gray-800">Payment Info</h4>
                                <div class="mt-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2" v-if="obj.payment_status != 'completed'">
                                        <div>
                                            <label class="block mb-1 text-sm font-medium" for="contact">Contact Name</label>
                                            <input class="w-full text-black input input-sm bg-inherit" placeholder="Contact Name" id="contact" type="text" v-model="obj.contact" disabled>
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm font-medium" for="email">Contact Email <span class="text-danger">*</span></label>
                                            <v-select
                                                v-model="form.email"
                                                :options="emails"
                                                label="VALUE"
                                                :reduce="option => option.VALUE"
                                                :filterable="false"
                                                placeholder="Search Email"
                                                class="w-full text-black bg-inherit"
                                                :class="{'has-error': v$.form.email.$error}"
                                                id="email"
                                            >
                                                <template #no-options>
                                                    <div class="text-black">Search</div>
                                                </template>
                                                <template #option="option">
                                                    <div class="py-3 text-sm text-black">
                                                        <span class="ml-1">{{ option.VALUE }}</span>
                                                    </div>
                                                </template>
                                                <template #selected-option="option">
                                                    <div class="text-sm text-black" v-if="form.email">
                                                        <span class="ml-1">{{ option.VALUE }}</span>
                                                    </div>
                                                </template>
                                            </v-select>
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm font-medium" for="currency">Bank Name <span class="text-danger">*</span></label>
                                            <v-select
                                                v-model="form.bank_code"
                                                :options="banks"
                                                label="CODE"
                                                :reduce="option => option.CODE"
                                                :filterable="false"
                                                placeholder="Search Bank"
                                                class="w-full text-black bg-inherit"
                                                :class="{'has-error': v$.form.bank_code.$error}"
                                                id="bank_code"
                                                @update:model-value="changeBankCode"
                                            >
                                                <template #no-options>
                                                    <div class="text-black">Search</div>
                                                </template>
                                                <template #option="option">
                                                    <div class="py-3 text-sm text-black">
                                                        <span class="ml-1">{{ option.COMPANY_NAME + " ( " + option.CODE + " )" }}</span>
                                                    </div>
                                                </template>
                                                <template #selected-option="option">
                                                    <div class="text-sm text-black" v-if="form.bank_code">
                                                        <span class="ml-1">{{ option.COMPANY_NAME + " ( " + option.CODE + " )" }}</span>
                                                    </div>
                                                </template>
                                            </v-select>
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm font-medium" for="message">Message <span class="text-danger">*</span></label>
                                            <input class="w-full text-black input input-sm bg-inherit" :class="v$.form.message.$error ? '!border-red-500' : ''" placeholder="Message" id="message" type="text" v-model="form.message">
                                        </div>
                                        <div class="md:col-span-2">
                                            <div class="flex gap-2 mb-2">
                                                <label class="flex items-center gap-2">
                                                    <input
                                                        type="radio"
                                                        name="invoiceUploadMethod"
                                                        value="generate"
                                                        v-model="isGenerateMode"
                                                        :checked="!isGenerateMode"
                                                        @change="isGenerateMode = false"
                                                    />
                                                    <span>Auto-Generate Invoice</span>
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input
                                                        type="radio"
                                                        name="invoiceUploadMethod"
                                                        value="upload"
                                                        v-model="isGenerateMode"
                                                        :checked="isGenerateMode"
                                                        @change="isGenerateMode = true"
                                                    />
                                                    <span>Upload Invoice Manually</span>
                                                </label>
                                            </div>
                                            <div v-if="!isGenerateMode">
                                                <div class="flex items-center gap-2">
                                                    <div class="relative w-full">
                                                        <!-- Disabled Input -->
                                                        <input
                                                            class="w-full text-black pr-36 input input-sm bg-inherit"
                                                            :class="v$.form.supporting_document.$error ? '!border-red-500' : ''"
                                                            placeholder="Generate to attach invoice"
                                                            id="contact"
                                                            type="text"
                                                            v-model="form.filename"
                                                            disabled
                                                        />

                                                        <!-- Buttons inside the input field -->
                                                        <div class="absolute flex gap-1 transform -translate-y-1/2 top-1/2 right-2">
                                                            <!-- Generate Button -->
                                                            <button
                                                                class="secondary-btn"
                                                                @click="generateInvoice(0)"
                                                                :disabled="generating"
                                                            >
                                                                <svg
                                                                    v-if="generating"
                                                                    class="w-4 h-4 animate-spin"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                                                    <path
                                                                        class="opacity-75"
                                                                        fill="currentColor"
                                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291
                                                                        A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3
                                                                        7.938l3-2.647z"
                                                                    />
                                                                </svg>
                                                                <span v-else>{{ (form.filename == null) ? 'Generate' : 'Re-Generate' }}</span>
                                                            </button>

                                                            <!-- Download Button -->
                                                            <button
                                                                class="secondary-btn"
                                                                :disabled="downloading"
                                                                @click="generateInvoice(1)"
                                                            >
                                                                <svg
                                                                    v-if="downloading"
                                                                    class="w-4 h-4 animate-spin"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                                                    <path
                                                                        class="opacity-75"
                                                                        fill="currentColor"
                                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291
                                                                        A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3
                                                                        7.938l3-2.647z"
                                                                    />
                                                                </svg>
                                                                <span v-else>Download</span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                </div>
                                                <span class="text-[11px] text-red-500"><i>Note : Just click 'Generate' — we’ll create and attach the invoice for you automatically. </i></span>
                                            </div>
                                            <div v-else>
                                                <div class="flex gap-2">
                                                    <input class="w-full text-black file-input file-input-sm bg-inherit" :class="v$.form.supporting_document.$error ? '!border-red-500' : ''" placeholder="Supporting Document" id="supporting_document" type="file" @change="uploadDocument($event, 'supporting_document')">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2" v-else>
                                        <div>
                                            <label class="block mb-1 text-sm font-medium" for="contact">Contact Name</label>
                                            <input class="w-full text-black input input-sm bg-inherit" placeholder="Contact Name" id="contact" type="text" v-model="obj.contact" disabled>
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm font-medium" for="email">Contact Email</label>
                                            <input class="w-full text-black input input-sm bg-inherit" placeholder="Contact Email" id="email" type="text" v-model="form.email" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 mt-6 bg-white rounded-lg shadow-md" v-if="payment_logs.length > 0">
                                <h3 class="mb-4 text-lg font-bold">Payment History</h3>
                                <div class="overflow-auto">
                                    <div class="relative">
                                        <div
                                            v-for="(log, index) in payment_logs"
                                            :key="index"
                                            class="relative flex items-start mb-3 ml-8"
                                        >
                                            <!-- Vertical Line Connector -->
                                            <div v-if="index < payment_logs.length - 1" class="absolute h-full border-l-2 border-gray-300 border-dotted left-3 top-6"></div>

                                            <!-- Timeline Marker with Icon -->
                                            <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 font-bold text-white rounded-full shadow-md"
                                                :class="{
                                                    'bg-blue-500': log.status === 'initiated',
                                                    'bg-green-500': log.status === 'completed',
                                                    'bg-yellow-500': log.status === 'pending',
                                                    'bg-red-500': log.status === 'canceled',
                                                    'bg-orange-700': log.status === 'failed',
                                                    'bg-orange-500': !['initiated','completed','pending','canceled','failed'].includes(log.status),
                                                }">
                                                <svg v-if="log.status === 'initiated'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <svg v-else-if="log.status === 'completed'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <svg v-else-if="log.status === 'pending'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                                                </svg>
                                                <svg v-else-if="log.status === 'canceled'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <svg v-else-if="log.status === 'failed'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01" />
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                                </svg>
                                            </div>

                                            <!-- Timeline Content -->
                                            <div class="p-4 ml-4 bg-white border border-gray-300 rounded-lg shadow-md w-80">
                                                <template v-if="index === 0">
                                                <p class="flex justify-between text-sm text-gray-600">Invoice Amount: <span class="font-bold text-black text-end">{{ formatAmount(log.amount) }} {{ log.currency }}</span></p>
                                                <p class="flex justify-between text-sm text-gray-600">Service Fee: <span class="font-semibold text-gray-600 text-end">+{{ formatAmount(obj.service_charge) }} {{ log.currency }}</span></p>
                                                <hr class="bg-gray-800 border-dotted"/>
                                                <p class="flex justify-between mb-2 text-sm text-gray-600">Total Amount: <span class="font-bold text-black text-end">{{ (obj.total_amount == 0) ? formatAmount(log.amount) : formatAmount(obj.total_amount) }} {{ log.currency }}</span></p>
                                                </template>
                                                <p class="flex justify-between text-sm text-gray-600">Date: <span class="font-bold text-black text-end">{{ formatDate(log.created_at) }}</span></p>
                                                <p class="flex justify-between text-sm text-gray-600">Status: <span class="font-semibold capitalize text-end"
                                                    :class="{
                                                        'text-blue-500': log.status === 'initiated',
                                                        'text-green-500': log.status === 'completed',
                                                        'text-yellow-500': log.status === 'pending',
                                                        'text-red-500': log.status === 'canceled',
                                                        'text-orange-500': log.status === 'failed',
                                                        'text-orange-500': !['initiated','completed','pending','canceled','failed'].includes(log.status)
                                                    }">{{ log.status }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="justify-end modal-footer">
                <div class="flex gap-4" v-if="obj.payment_status != 'completed'">
                    <button class="secondary-btn !text-md font-semibold !border-2 !px-10" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Cancel
                    </button>
                    <button v-if="payment_logs.length ==0"
                        class="main-btn"
                        type="button"
                        @click="submit('Payment Invoice')"
                        :disabled="loading"
                    >
                        <svg v-if="loading" class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-else>Send Invoice Payment</span>
                    </button>
                    <button v-else
                        class="main-btn"
                        type="button"
                        @click="submit('Payment Reminder')"
                        :disabled="loading"
                    >
                        <svg v-if="loading" class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-else>Send Payment Reminder</span>
                    </button>
                </div>
                <div class="flex gap-4" v-else>
                    <button class="secondary-btn !text-md font-semibold !border-2 !px-10" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {DateTime} from "luxon";
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'
import { sharedState } from "../../../../state.js";
import axios from "axios";
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    name: "process-purchase-invoice-details-modal",
    components : {sharedState, vSelect},
    props: ['obj_id', 'obj_data','page_data'],
    data(){
        return {
            isGenerateMode: false,
            loading: false,
            obj: {},
            emails: [],
            banks: [],
            form: {
                email: null,
                supporting_document: null,
                message: null,
                bank_code: null,
                filename: null,
            },
            sageCompanyId : null,
            payment_logs: [],
            generating: false,
            downloading: false
        }
    },
    setup() {
        const v$ = useVuelidate();
        return { v$ };
    },
    validations () {
        return {
            form: {
                email: { required },
                supporting_document: { required },
                message: { required },
                bank_code: { required },
            }
        }
    },
    methods: {
        async submit(title){
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            this.loading = true;

            try {
                // Create FormData object for multipart form submission
                const formData = new FormData();
                const bitrixUserId = this.sharedState.bitrix_user_id ? this.sharedState.bitrix_user_id : this.page_data.user.bitrix_user_id;
                // Add all the text fields
                formData.append('title', title);
                formData.append('invoice_id', this.obj.id);
                formData.append('recipient_name', this.obj.contact);
                formData.append('bitrixUserId', bitrixUserId);
                formData.append('subject', "Your Invoice #" + this.obj.sage_invoice_number);
                formData.append('recipients[]', this.form.email); // Notice the array notation
                formData.append('invoice_number', this.obj.sage_invoice_number);
                formData.append('invoice_date', this.obj.date_bill);
                formData.append('due_date', this.obj.date_pay_before);
                formData.append('amount', this.obj.price);
                formData.append('currency', this.obj.currency);
                formData.append('invoice_link', `https://forms.cresco.ae/invoice/${this.obj.id}`);
                formData.append('message', this.form.message);
                formData.append('filename', this.form.filename);
                formData.append('bank_code', this.form.bank_code);
                formData.append('pdf_file', this.form.supporting_document);
                const response = await axios.post('/api/invoice-emails/send', formData);
                if (response.data.status === 'success') {
                    this.successToast(response.data.message);
                    this.loading = false;
                    this.getCurrentStatus();
                }
                else if (response.data.status === 'error') {
                    this.errorToast(response.data.message)
                    this.loading = false;
                }
                else {
                    this.errorToast('Error sending email')
                    this.loading = false;
                }
            } catch (error) {
                this.$emit('showToast', {
                    type: 'error',
                    message: error.response?.data?.message || 'Error sending email',
                });
            } finally {
                this.loading = false;
            }
        },
        isNotBooked(item){
            const today = DateTime.now();
            const invoiceDate = DateTime.fromSQL(item.date_bill);
            return ((item.status_id === "N" || item.status_id === "S") && invoiceDate < today);
        },
        isOverDueDate(item){
            const today = DateTime.now();
            const dueDate = DateTime.fromSQL(item.date_pay_before)
            return ((item.status_id === "2" || item.status_id === "N") && dueDate < today);
        },
        getContact(){
            const endpoint = 'crm.contact.get';
            const bitrixUserId = this.sharedState.bitrix_user_id ? this.sharedState.bitrix_user_id : this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token ? this.sharedState.bitrix_webhook_token : this.page_data.user.bitrix_webhook_token;
            const requestData = {
                id: this.obj.contact_id
            }
            this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData)
            .then(response => {
                if (response.result) {
                    this.emails = response.result.EMAIL;

                    this.emails.push({VALUE: 'nilo.besingga@crescotec.com'});
                    console.log(this.emails, "this.emails");
                    this.form.email = "nilo.besingga@crescotec.com";//response.result.EMAIL[0].VALUE ?? '';
                }
            })
            .catch(error => {
                console.error('Error fetching contact:', error);
            });
        },
        uploadDocument(event, field) {
            const file = event.target.files[0];
            if (file) {
                this.form[field] = file;
                this.form.filename = file.name;
            }
            console.log(this.form[field]);
        },
        async getBankList() {
            const response = await axios.get('https://10.0.1.17/CrescoSage/api/v1/Mapping/CompanyMap/' + this.obj.sage_company_id + '?mapType=2');
            if (response) {
                this.sageCompanyId = response.data.BitrixQuoteMappedId;
                await axios.get('https://10.0.1.17/CRESCOSage/api/v1/ARBank?company=' + this.sageCompanyId)
                    .then((response) => {
                        this.banks = response.data;
                    })
                    .catch(error => {
                        console.log(error.response)
                    });
            }
        },
        async getCurrentStatus() {
            await axios.get('payment_logs/' + this.obj.id)
            .then((response) => {
                if(response.data){
                    this.obj.payment_status = response.data.data.status;
                    this.obj.payment_completed_at = response.data.data.payment_completed_at;
                    this.obj.service_charge = response.data.data.service_charge;
                    this.obj.total_amount = response.data.data.total_amount;
                    this.obj.bank_code = response.data.data.bank_code ?? this.obj.bank_code;
                    this.form.bank_code = response.data.data.bank_code ?? this.form.bank_code;
                    this.form.email = response.data.data.recipient_email;

                    this.form.filename = response.data.data.filename ?? this.form.filename;
                    if (this.form.filename) {
                        // Fetch the file from storage
                         axios.get(`/api/invoice-emails/get-file/${this.form.filename}`, {
                            responseType: 'blob'
                        })
                        .then(response => {
                            // Convert the blob to a File object
                            const blob = new Blob([response.data], { type: 'application/pdf' });
                            const file = new File([blob], this.form.filename, { type: 'application/pdf' });

                            // Assign to supporting_document
                            this.form.supporting_document = file;
                        })
                        .catch(error => {
                            console.error('Error fetching file:', error);
                        });
                    }
                    //how to get uploaded base on filename laravel storage assigned to supporting_document
                    this.payment_logs = response.data.data.payment_logs.map(v => ({...v, created_at : DateTime.fromISO(v.created_at).toFormat('yyyy-MM-dd')})) ?? [];
                }
            })
            .catch(error => {
                console.log(error.response)
            });
        },

        async generateInvoice(count){
            if(count === 1){
                this.downloading = true;
            }
            else{
                this.generating = true;
            }
            await axios.post(
                'https://forms.cresco.ae/api/invoice/download/' + this.obj.id,
                // 'http://127.0.0.1:8001/api/invoice/download/' + this.obj.id,
                {
                    bank_code: this.form.bank_code
                },
                {
                    headers: {
                        // Authorization: 'Bearer Uif54oa7vciStwIuFpH7Q1cVBozqVFysrKyetb5w',
                        // Authorization: 'Bearer ua88c8LeLp4J2I1qrOFc1gGkq0iBxmkSgqS01jYe',
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    responseType: 'blob',
                }
            ).then((response) => {
                const blob = new Blob([response.data], { type: 'application/pdf' });
                if(count == 1){
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = this.obj.deal + '-invoice.pdf';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    this.downloading = false;
                }
                else{
                    const file = new File([blob], this.obj.quote + '-invoice.pdf', { type: 'application/pdf' });
                    this.form.supporting_document = file;
                    this.form.filename = this.obj.quote + '-invoice.pdf';
                    this.successToast('Invoice generated successfully');
                    this.generating = false;
                }
            }).catch((error) => {
                console.log(error.response);
                this.errorToast('Error generating invoice');
                this.generating = false;
                this.downloading = false;
            });
        },
        async changeBankCode(){
            if(this.form.bank_code){
                this.form.supporting_document = null;
                this.form.filename = null;
                await this.generateInvoice(0);
            }
        },
    },
    async mounted() {
        this.obj = this.obj_data;
        await this.getContact();
        this.form.bank_code = this.obj.bank_code;
        await this.getBankList();
        await this.getCurrentStatus();
        this.form.message = `Payment for Invoice # ${this.obj.sage_invoice_number.replace(/\s+/g, '')}`;
    }
}
</script>


<style scoped>
/* Utility class for centering content */
.flex-center {
    @apply flex items-center justify-center;
}
</style>
