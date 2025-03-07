<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="cash_request_update_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Update Cash Request</h3>
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
                        <!-- Action -->
                        <div class="w-full mt-10">
                            <label class="form-label flex items-center gap-1 text-sm mb-1" for="action">Select Action
                                <span class="text-danger">* <span class="form-text-error" v-if="v$.form.action.$error">Please fill out this field</span></span>
                            </label>
                            <select
                                v-model="form.action"
                                class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                                :class="v$.form.action.$error ? '!border-red-500' : ''"
                                id="action"
                            >
                                <option value="1">Modify Cash Request Amount</option>
                                <option value="2">Cancel Request</option>
                            </select>
                        </div>
                        <div v-if="form.action === '1'">
                            <div v-if="form.status_id === '1687'">
                                <div class="form-text-error">
                                    This request has been partially paid. You can only modify unpaid requests.
                                </div>
                            </div>
                            <div v-if="form.status_id !== '1687'">
                                <div class="flex gap-5 mt-8">
                                    <div class="w-1/2">
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
                                        <!-- New Amount -->
                                        <div class="mb-4 w-full gap-2.5">
                                            <label class="form-label flex items-center gap-1 text-sm mb-1" for="new_amount">New Amount
                                                <span class="text-danger">* <span class="form-text-error" v-if="v$.form.new_amount.required.$invalid">Please fill out this field</span></span>
                                                <span class="text-danger"><span class="form-text-error" v-if="v$.form.new_amount.minValue.$invalid">Amount must be greater than 0.</span></span>
                                            </label>
                                            <input
                                                class="input text-black bg-inherit"
                                                :class="v$.form.new_amount.$error ? '!border-red-500' : ''"
                                                placeholder="New Amount"
                                                id="new_amount"
                                                type="number"
                                                v-model="form.new_amount"
                                            >
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
                                            >
                                                <option value="2268">Yes</option>
                                                <option value="2269">No</option>
                                            </select>
                                            <div v-if="form.awaiting_for_exchange_rate_id === '2268'" class="form-text-error">To Release Funds Awaiting for Exchange rate must be No.</div>
                                        </div>
                                    </div>
                                    <div class="w-1/2">
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
                                        <!-- Currency -->
                                        <div class="mb-4 w-full gap-2.5">
                                            <label class="form-label flex items-center gap-1 text-sm mb-1" for="currency">Currency
                                                <span class="text-danger">* <span class="form-text-error" v-if="v$.form.currency.$error">Please fill out this field</span></span>
                                            </label>
                                            <select v-model="form.currency" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" :class="v$.form.currency.$error ? '!border-red-500' : ''" id="currency">
                                                <option value="">Select Currency</option>
                                                <option value="USD">US Dollar</option>
                                                <option value="AED">UAE Dirham</option>
                                                <option value="AUD">Australian Dollar</option>
                                                <option value="CNY">China Yuan Renminbi</option>
                                                <option value="GBP">Pound Sterling</option>
                                                <option value="HKD">Hong Kong Dollar</option>
                                                <option value="SGD">Singapore Dollar</option>
                                                <option value="THB">Thai Baht</option>
                                                <option value="EUR">Euro</option>
                                                <option value="CHF">Swiss Franc</option>
                                                <option value="PHP">Philippine Peso</option>
                                                <option value="INR">Indian Rupee</option>
                                                <option value="SCR">Seychelles Rupee</option>
                                                <option value="CRC">Costa Rican Coln</option>
                                                <option value="BRL">Brazilian Real</option>
                                            </select>
                                        </div>
                                        <!-- Budget Only -->
                                        <div class="mb-4 w-full gap-2.5">
                                            <label class="form-label flex items-center gap-1 text-sm mb-1" for="budget_only_id">Budget Only
                                            </label>
                                            <select v-model="form.budget_only_id" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" id="budget_only_id">
                                                <option value="">N/A</option>
                                                <option value="1937">Yes</option>
                                                <option value="1938">No</option>
                                            </select>
                                        </div>
                                        <!-- Reason -->
                                        <div class="mb-4 w-full gap-2.5">
                                            <label class="form-label flex items-center gap-1 text-sm mb-1" for="reason">Reason for changing the amount
                                                <span class="text-danger">* <span class="form-text-error" v-if="v$.form.reason.$error">Please fill out this field</span></span>
                                            </label>
                                            <input
                                                class="input text-black bg-inherit"
                                                :class="v$.form.reason.$error ? '!border-red-500' : ''"
                                                placeholder="Reason for changing the amount"
                                                id="reason"
                                                type="text"
                                                v-model="form.reason"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.action === '2'">

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
                        <span v-if="!crud_loading">Save</span>
                        <span v-else>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { useVuelidate } from '@vuelidate/core'
import { required, minValue } from '@vuelidate/validators'
import qs from 'qs';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import _, { debounce } from 'lodash';
import { sharedState } from "../../../../state.js";

export default {
    name: "cash-request-update-form-modal",
    props: ['obj', 'payment_modes', 'cash_pools', 'cash_release_locations'],
    components: { vSelect },
    data(){
        return {
            form: {
                action: null,
                new_amount: null,
                payment_mode_id: null,
                cash_pool_id: '',
                cash_release_location_id: null,
                currency: null,
                awaiting_for_exchange_rate_id: null,
                reason: '',
                budget_only_id: '',
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
                action: { required },
                new_amount: { required, minValue: minValue(1) },
                payment_mode_id: { required },
                cash_release_location_id: { required },
                currency: { required },
                awaiting_for_exchange_rate_id: { required },
                reason: { required },
                ...(this.form.payment_mode_id === '1867' && {
                    cash_pool_id: { required },
                }),
            }
        }
    },
    methods: {
        async submit(){
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            this.crud_loading = true;

            let amountCurrency = null;
            let name = null;

            amountCurrency = _.round(this.form.new_amount, 2) + "|" + this.form.currency;
            name = 'Cash Request - ' + amountCurrency;

            // Name
            this.bitrix_obj.NAME = name;
            // Amount (including VAT)
            // this.bitrix_obj.PROPERTY_939 = amountCurrency;
            // Amount Given
            this.bitrix_obj.PROPERTY_944 = this.form.amount;
            // Cash Pool
            this.bitrix_obj.PROPERTY_1231 = this.form.cash_pool_id;
            // Cash Release Location
            this.bitrix_obj.PROPERTY_954 = this.form.cash_release_location_id;
            //  Modified By
            this.bitrix_obj.MODIFIED_BY = this.sharedState.bitrix_user_id;
            //  Awaiting for exchange rate
            this.bitrix_obj.PROPERTY_1249 = this.form.awaiting_for_exchange_rate_id;
            //  Budget Only
            this.bitrix_obj.PROPERTY_1160 = this.form.budget_only_id;

            const requestData = qs.stringify({
                IBLOCK_TYPE_ID: 'bitrix_processes',
                IBLOCK_ID: '105',
                ELEMENT_ID: this.obj.id,
                FIELDS: this.bitrix_obj
            });

            try {
                const response = await this.callBitrixAPI('lists.element.update', this.sharedState.bitrix_user_id, this.sharedState.bitrix_webhook_token, requestData);
                if(response.result) {

                    // await this.notifyRequestUpdated();

                    this.successToast('Cash Request updated successfully')
                    this.$emit('closeModal')

                }
            } catch (error) {
                console.error(error)
                this.crud_loading = false;
                this.$emit('closeModal')
                this.errorToast('Something went wrong. Please contact IT')
            }
        },
        notifyRequestUpdated(){

        }
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
            this.form.action = '1';
            this.form.new_amount = 0;
            this.form.awaiting_for_exchange_rate_id = "2269";
            this.form.reason = "";
            let response = await this.getCashRequestByIdBitrixFields(this.obj.id)
            if(response && response.length > 0){
                this.bitrix_obj = response[0];
            }
        }
    },
}
</script>
