<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="cash_request_release_fund_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Cash Request Release Fund</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body relative h-full overflow-auto">
                <!-- Loading Spinner -->
                <div v-if="loading" class="absolute inset-0 h-40 flex items-center justify-center z-50">
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
                    <form @submit.prevent="submit">
                        <div class="flex mt-4 mb-2 text-2xl font-bold tracking-tight text-black">{{ formatAmount(obj.amount) }} {{ obj.currency }}</div>
                        <div class="flex gap-1">
                            <span>Requested By: </span>
                            <span>{{ obj.requested_by_name }}</span>
                        </div>
                        <div class="flex gap-5 mt-8">
                            <div class="w-1/2">
                                <!-- Sage Company -->
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="sage_company">Sage Company
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.sage_company_id.$error">Please fill out this field</span></span>
                                    </label>
                                    <select v-model="form.sage_company_id" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" :class="v$.form.sage_company_id.$error ? '!border-red-500' : ''" id="sage_company">
                                        <option v-for="obj in sage_companies" :key="obj.bitrix_sage_company_id" :value="obj.bitrix_sage_company_id">{{ obj.bitrix_sage_company_name }}</option>
                                    </select>
                                </div>
                                <!-- Payment Mode -->
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="payment_mode">Payment Mode
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.payment_mode_id.$error">Please fill out this field</span></span>
                                    </label>
                                    <select v-model="form.payment_mode_id" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" :class="v$.form.payment_mode_id.$error ? '!border-red-500' : ''" id="payment_mode">
                                        <option v-for="obj in payment_modes" :key="obj.id" :value="obj.id">{{ obj.name }}</option>
                                    </select>
                                </div>
                                <!-- Cash Release Location -->
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="cash_release_location">Cash Release Location
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.cash_release_location_id.$error">Please fill out this field</span></span>
                                    </label>
                                    <select v-model="form.cash_release_location_id" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" :class="v$.form.cash_release_location_id.$error ? '!border-red-500' : ''" id="cash_release_location">
                                        <option v-for="obj in cash_release_locations" :key="obj.id" :value="obj.id">{{ obj.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="w-1/2">
                                <!-- Cash Release Type -->
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="cash_release_type">Cash Release Type
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.cash_release_type.$error">Please fill out this field</span></span>
                                    </label>
                                    <select v-model="form.cash_release_type" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" :class="v$.form.cash_release_type.$error ? '!border-red-500' : ''" id="cash_release_type">
                                        <option :value=1>Full Cash Release</option>
                                        <option :value=2>Partial Cash Release</option>
                                    </select>
                                </div>
                                <!-- Amount given to requester -->
                                <div class="mb-4 w-full gap-2.5" v-if="form.cash_release_type === 2">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="amount_released">Amount given to requester
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.amount_released.$error">Please fill out this field</span></span>
                                    </label>
                                    <input class="input text-black bg-inherit" :class="v$.form.amount_released.$error ? '!border-red-500' : ''" placeholder="Amount Released" id="amount_released" type="text" v-model="form.amount_released">
                                </div>
                                <!-- Cash Pool -->
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="cash_pool">Cash Pool
                                        <span class="text-danger" v-if="form.payment_mode_id === '1867'">* <span class="form-text-error" v-if="v$.form.cash_pool_id.$error">Please fill out this field</span></span>
                                    </label>
                                    <select v-model="form.cash_pool_id"
                                            class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                                            :class="form.payment_mode_id === '1867' && v$.form.cash_pool_id.$error ? '!border-red-500' : ''"
                                            id="cash_pool"
                                    >
                                        <option v-if="form.payment_mode_id !== '1867'" value="">N/A</option>
                                        <option v-for="obj in cash_pools" :key="obj.id" :value="obj.id">{{ obj.name }}</option>
                                    </select>
                                </div>
                                <!-- Awaiting for Exchange Rate -->
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="awaiting_for_exchange_rate">Awaiting for Exchange Rate
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.awaiting_for_exchange_rate_id.$error">Please fill out this field</span></span>
                                    </label>
                                    <select
                                        v-model="form.awaiting_for_exchange_rate_id"
                                        class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                                        :class="v$.form.awaiting_for_exchange_rate_id.$error ? '!border-red-500' : ''"
                                        id="awaiting_for_exchange_rate"
                                        disabled
                                    >
                                        <option value="2268">Yes</option>
                                        <option value="2269">No</option>
                                    </select>
                                    <div v-if="form.awaiting_for_exchange_rate_id === '2268'" class="form-text-error">To Release Funds Awaiting for Exchange rate must be No.</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer justify-end">
                <div class="flex gap-4">
                    <button
                        class="secondary-btn !text-md font-semibold !border-2 !px-10"
                        data-modal-dismiss="true"
                        @click="$emit('closeModal')"
                    >
                        Cancel
                    </button>
                    <button
                        class="main-btn focus:!border-tec-active focus:!shadow-tec-active/30"
                        type="submit"
                        @click="submit"
                        :disabled="loading || crud_loading"
                    >
                        <span v-if="!crud_loading">Release Fund</span>
                        <span v-else>Saving...</span>
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
import qs from "qs";
import _ from "lodash";

export default {
    name: "cash-request-release-fund-form-modal",
    props: ['obj', 'payment_modes', 'sage_companies', 'cash_pools', 'cash_release_locations'],
    data(){
        return {
            form: {
                sage_company_id: null,
                cash_release_type: 1,
                payment_mode_id: null,
                cash_pool_id: '',
                cash_release_location_id: null,
                amount_released: null,
                awaiting_for_exchange_rate_id: null,
            },
            bitrix_obj: {},
            crud_loading: false,
            loading: false,
        }
    },
    setup() {
        const v$ = useVuelidate();
        return { v$ };
    },
    validations () {
        return {
            form: {
                sage_company_id: { required },
                cash_release_type: { required },
                payment_mode_id: { required },
                cash_release_location_id: { required },
                awaiting_for_exchange_rate_id: { required },
                ...(this.form.payment_mode_id === '1867' && {
                    cash_pool_id: { required },
                }),
                ...(this.form.cash_release_type === 2 && {
                    amount_released: { required },
                }),
            },
        }
    },
    methods: {
        async submit(){
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            this.crud_loading = true;
            let remainingCash = null;
            let amountCurrency = null;
            let status = null;

            if(this.form.cash_release_type === 1 && this.form.amount === this.form.amount_released) {
                remainingCash = this.form.amount;
                amountCurrency = _.round(remainingCash, 2) + "|" + this.form.currency; // Full Cash Release
                status = "1655"; // set to full amount
            }
            else if(this.form.cash_release_type === 2){
                remainingCash = this.form.amount - this.form.amount_released;
                amountCurrency = _.round(remainingCash, 2) + "|" + this.form.currency;
                status = "1687"; // Partial Cash Release
            }
            this.form.remaining_cash = remainingCash;
            this.form.status_id = status;

            // Sage Company Id
            this.bitrix_obj.PROPERTY_951 = this.form.sage_company_id;
            // Awaiting for Exchange Rate
            this.bitrix_obj.PROPERTY_1249 = this.form.awaiting_for_exchange_rate_id;
            // Cash Pool
            this.bitrix_obj.PROPERTY_1231 = this.form.cash_pool_id;
            // Cash Release Location
            this.bitrix_obj.PROPERTY_954 = this.form.cash_release_location_id;
            // Payment Mode
            this.bitrix_obj.PROPERTY_1088 = this.form.payment_mode_id;
            // Amount (including VAT)
            this.bitrix_obj.PROPERTY_939 = amountCurrency;
            // Amount Given
            this.bitrix_obj.PROPERTY_944 = this.form.amount_released;
            // Status
            this.bitrix_obj.PROPERTY_943 = this.form.status_id;
            // Modified By
            this.bitrix_obj.MODIFIED_BY = this.sharedState.bitrix_user_id;
            // Released By
            this.bitrix_obj.PROPERTY_1071 = this.sharedState.bitrix_name;
            // Release Date
            this.bitrix_obj.PROPERTY_1073 = DateTime.now().toFormat('dd.MM.yyyy');


            const bitrixUserId = this.sharedState.bitrix_user_id ? this.sharedState.bitrix_user_id : null;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token ? this.sharedState.bitrix_webhook_token : null;
            const endpoint = 'lists.element.update';
            const requestData = qs.stringify({
                IBLOCK_TYPE_ID: 'bitrix_processes',
                IBLOCK_ID: '105',
                ELEMENT_ID: this.obj.id,
                FIELDS: this.bitrix_obj
            });

            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if(response.result) {

                    this.infoToast('Receipt will be downloaded shortly')

                    // create a separate record to track partial cash releases
                    if (this.form.cash_release_type === 2) {
                        await this.createPartialCashRequestRecord();
                    }

                    await this.notifyRequesterFundReleased();
                    await this.downloadCashReceipt();

                    this.successToast('Cash Request fund released')
                    this.$emit('closeModal')

                }
            } catch (error) {
                console.error(error)
                this.crud_loading = false;
                this.$emit('closeModal')
                this.errorToast('Something went wrong. Please contact IT')
            }
        },
        async createPartialCashRequestRecord(){
            const bitrixUserId = this.sharedState.bitrix_user_id ? this.sharedState.bitrix_user_id : null;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token ? this.sharedState.bitrix_webhook_token : null;
            const endpoint = 'lists.element.add';
            const requestData = qs.stringify({
                IBLOCK_TYPE_ID: 'bitrix_processes',
                IBLOCK_ID: '111',
                ELEMENT_CODE: this.form.id + "_" + Date.now(),
                FIELDS: {
                    'NAME': "Partial Cash Release - " + this.form.amount_released,
                    'PROPERTY_1076': this.form.id,
                    'PROPERTY_1077': this.form.amount_released,
                    'PROPERTY_1078': this.form.currency,
                }
            });
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
            } catch (error) {
                console.error(error)
                this.crud_loading = false;
                this.$emit('closeModal')
                this.errorToast('Something went wrong. Please contact IT')
            }
        },
        async notifyRequesterFundReleased() {
            let url = "https://crm.cresco.ae/bizproc/processes/105/element/0/" + this.form.id + "/?list_section_id=";
            let link = " <a href='" + url + "'>Click here to view details about your request</a>";
            let notifyText = '';
            let paymentMode = this.payment_modes.find(item => item.id = this.form.payment_mode_id);
            switch (paymentMode.name) {
                case "Cash":
                    notifyText = "HAS BEEN RELEASED";
                    break;
                case "Card":
                    notifyText = "HAS BEEN PAID BY CARD";
                    break;
                case "Bank Transfer":
                    notifyText = "HAS BEEN RELEASED THRU BANK TRANSFER";
                    break;
                case "Cheque":
                    notifyText = "HAS BEEN RELEASED THRU CHEQUE";
                    break;
                case "Qashio":
                    notifyText = "HAS BEEN RELEASED THRU QASHIO";
                    break;
                case "Crypto":
                    notifyText = "HAS BEEN RELEASED THRU CRYPTO";
                    break;
            }
            if (this.form.cash_release_type !== 1) {
                notifyText += " PARTIALLY"
            }
            let post_title = "YOUR REQUEST (" + this.form.amount_released + " " + this.form.currency + ") " + notifyText
            //activity stream
            const activityRequestData = qs.stringify({
                POST_TITLE: post_title,
                DEST: ["U" + this.form.created_by],
                POST_MESSAGE: "Please attach the receipt to the request once the payment has been made. " + link
            });
            await this.callBitrixAPI('log.blogpost.add', this.sharedState.bitrix_user_id, this.sharedState.bitrix_webhook_token, activityRequestData)

            //notifications
            const notificationRequestData = qs.stringify({
                to: this.form.created_by,
                message: post_title + " " + link
            });
            await this.callBitrixAPI('im.notify', this.sharedState.bitrix_user_id, this.sharedState.bitrix_webhook_token, notificationRequestData)

            // const emailRequestData = qs.stringify({
            //     POST_TITLE: post_title,
            //     DEST: ["U" + this.form.created_by],
            //     POST_MESSAGE: "Please attach the receipt to the request once the payment has been made. " + link
            // });
            // await this.callBitrixAPI('im.notify', this.sharedState.bitrix_user_id, this.sharedState.bitrix_webhook_token, activityRequestData)

            //email
            // axios({
            //     method: 'post',
            //     url: '/notify/cashrelease',
            //     data: {
            //         'requestId': request.id,
            //         'requestCreateDate': DateTime.fromSQL(request.date_create).toFormat('dd LLL yyyy'),
            //         'requestPaymentDate': DateTime.fromISO(request.payment_date).toFormat('dd LLL yyyy'),
            //         'releaseDate': DateTime.now().toFormat('dd LLL yyyy'),
            //         'requestedBy': request.requested_by_name,
            //         'releasedBy': this.accountantName,
            //         'project': request.project_name,
            //         'company': request.company_name,
            //         'remarks': request.detail_text,
            //         'amountReleased': amountReleased,
            //         'currency': request.currency,
            //         'requestedByEmail': request.requested_by_email,
            //         'accountantEmail': request.accountant_email,
            //         'cashReleaseType': this.cashReleaseType == 1 ? 'full' : 'partial',
            //         'remainingBalance': request.amount,
            //         'paymentMode': this.paymentMode ?? this.paymentMode.name
            //
            //     },
            // })
        },
        async downloadCashReceipt() {
            let fileName = this.form.amount_released + "|" + this.form.currency + " - " + this.form.requested_by_name + " - Cash Request Receipt.pdf";
            axios({
                url: '/cash-request/download-released-receipt',
                method: 'post',
                responseType: 'arraybuffer',
                data: {
                    'requestId': this.form.id,
                    'requestCreateDate': DateTime.fromSQL(this.form.date_create).toFormat('dd LLL yyyy'),
                    'requestPaymentDate': DateTime.fromISO(this.form.payment_date).toFormat('dd LLL yyyy'),
                    'releaseDate': DateTime.now().toFormat('dd LLL yyyy'),
                    'requestedBy': this.form.requested_by_name,
                    'releasedBy': this.form.accountant, //can be any employee not just accountant
                    'project': this.form.project_name,
                    'company': this.form.company_name,
                    'remarks': this.form.detail_text,
                    'amountReceived': this.form.amount_released,
                    'currency': this.form.currency,
                    'cashReleaseType': this.form.cash_release_type,
                    'balance': this.form.remaining_cash,
                    'paymentMode': this.payment_modes.find(item => item.id === this.form.payment_mode_id).name,
                },
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/pdf'
                },
            }).then(function (response) {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName); //or any other extension
                document.body.appendChild(link);
                link.click();
                link.remove();
            });
        },
    },
    watch: {
        'form.payment_mode_id'(newVal) {
            if (newVal === '1867') {
                this.form.cash_pool_id = null; // Make selection required
            } else {
                this.form.cash_pool_id = ""; // Reset to ""
            }
            this.v$.$touch(); // Ensures validation runs after change
        }
    },
    async mounted() {
        if (this.obj){
            this.form = this.obj;
            this.form.cash_release_type = 1;
            this.form.amount_released = this.obj.amount;
            this.form.awaiting_for_exchange_rate_id = 2269; // default value NO
            let response = await this.getCashRequestByIdBitrixFields(this.obj.id)
            if(response && response.length > 0){
                this.bitrix_obj = response[0];
            }
        }
    },
}
</script>
