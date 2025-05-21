<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="create_cash_request_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Create New Cash Request</h3>
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
                        <div class="flex gap-5 mt-8">
<!--                            <div class="w-1/2">-->
<!--                                &lt;!&ndash; Transfer from Account &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="transfer_from_account">Transfer from Account-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.transfer_from_account.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <v-select-->
<!--                                        v-model="form.transfer_from_account"-->
<!--                                        :options="form_data.banks"-->
<!--                                        label="NAME"-->
<!--                                        :filterable="false"-->
<!--                                        placeholder="Search Account Name"-->
<!--                                        class="text-black"-->
<!--                                        :class="{'has-error': v$.form.transfer_from_account.$error}"-->
<!--                                        @search="debouncedSearch('banks', $event)"-->
<!--                                        id="transfer_from_account"-->
<!--                                    >-->
<!--                                        <template #no-options>-->
<!--                                            <div class="text-black">Search by account name</div>-->
<!--                                        </template>-->
<!--                                        <template #option="option">-->
<!--                                            <div class="p-2 text-xs text-black">-->
<!--                                                <div>{{ option.NAME }} - <span v-if="option.PROPERTY_156"> {{ Object.values(option.PROPERTY_156)[0]}}</span></div>-->
<!--                                                <div class="font-bold" v-if="option.PROPERTY_158">{{ Object.values(option.PROPERTY_158)[0]}}</div>-->
<!--                                                <div v-if="option.PROPERTY_159">{{ Object.values(option.PROPERTY_159)[0]}}</div>-->
<!--                                                <div v-if="option.PROPERTY_166">{{ Object.values(option.PROPERTY_166)[0]}}</div>-->
<!--                                            </div>-->
<!--                                        </template>-->
<!--                                        <template #selected-option="option">-->
<!--                                            <div>-->
<!--                                                <span v-if="form.transfer_from_account.PROPERTY_158"> {{ Object.values(form.transfer_from_account.PROPERTY_158)[0]}}</span>-->
<!--                                                <span v-else>{{option.NAME}}</span>-->
<!--                                            </div>-->
<!--                                        </template>-->
<!--                                    </v-select>-->
<!--                                    <div v-if="form.transfer_from_account" class="bg-white border-l-2 border-brand-active shadow-sm text-sm p-4 mt-2">-->
<!--                                        <div class="text-md mb-2">Transfer From:</div>-->
<!--                                        <div class="text-sm">  {{ form.transfer_from_account.NAME }} - <span v-if="form.transfer_from_account.PROPERTY_156"> {{ Object.values(form.transfer_from_account.PROPERTY_156)[0]}}</span></div>-->
<!--                                        <div class="text-sm" v-if="form.transfer_from_account.PROPERTY_158"> {{ Object.values(form.transfer_from_account.PROPERTY_158)[0]}}</div>-->
<!--                                        <div class="text-sm" v-if="form.transfer_from_account.PROPERTY_159"> {{ Object.values(form.transfer_from_account.PROPERTY_159)[0]}}</div>-->
<!--                                        <div class="text-sm" v-if="form.transfer_from_account.PROPERTY_166"> {{ Object.values(form.transfer_from_account.PROPERTY_166)[0]}}</div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Transfer Amount &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="amount">Transfer Amount-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.amount.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <input class="input text-black bg-inherit" :class="v$.form.amount.$error ? '!border-red-500' : ''" placeholder="Transfer Amount" id="amount" type="text" v-model="form.amount">-->
<!--                                </div>-->
<!--                                &lt;!&ndash;Bank Charge &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="amount">Bank Charge-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.bank_charge.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <input class="input text-black bg-inherit" :class="v$.form.bank_charge.$error ? '!border-red-500' : ''" placeholder="Bank Charge" id="bank_charge" type="text" v-model="form.bank_charge">-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Reference Number &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="invoice_number">Reference Number</label>-->
<!--                                    <input class="input input-sm text-black bg-inherit" placeholder="Reference Number" id="invoice_number" type="text" v-model="form.invoice_number">-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Project &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="project">Project-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.project.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <v-select-->
<!--                                        v-model="form.project"-->
<!--                                        :options="form_data.projects"-->
<!--                                        label="TITLE"-->
<!--                                        :filterable="false"-->
<!--                                        placeholder="Search Project Name"-->
<!--                                        class="text-black"-->
<!--                                        :class="{'has-error': v$.form.project.$error}"-->
<!--                                        @search="debouncedSearch('projects', $event)"-->
<!--                                        id="project"-->
<!--                                    >-->
<!--                                        <template #no-options>-->
<!--                                            <div class="text-black">Search by project name</div>-->
<!--                                        </template>-->
<!--                                        <template #option="option">-->
<!--                                            <div class="py-2 text-xs text-black">-->
<!--                                                <span v-if="option.TYPE === '1'" class="px-1 bg-[#1759cd] text-white">L</span>-->
<!--                                                <span v-if="option.TYPE === '2'" class="px-1 bg-[#149ac0] text-white">D</span>-->
<!--                                                <span v-if="option.TYPE === '3'" class="px-1">C</span>-->
<!--                                                <span class="ml-1">{{ option.TITLE }}</span>-->
<!--                                            </div>-->
<!--                                        </template>-->
<!--                                        <template #selected-option="option">-->
<!--                                            <div class="text-xs text-black" v-if="form.project">-->
<!--                                                <span v-if="option.TYPE === '1'" class="px-1 bg-[#1759cd] text-white">L</span>-->
<!--                                                <span v-if="option.TYPE === '2'" class="px-1 bg-[#149ac0] text-white">D</span>-->
<!--                                                <span v-if="option.TYPE === '3'" class="px-1">C</span>-->
<!--                                                <span class="ml-1"> {{ form.project.TITLE }}</span>-->
<!--                                            </div>-->
<!--                                        </template>-->
<!--                                    </v-select>-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Transfer Document &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="transfer_document">Transfer Document-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.transfer_document.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <input class="file-input file-input-sm" :class="v$.form.transfer_document.$error ? '!border-red-500' : ''" placeholder="Transfer Document" id="transfer_document" type="file" @change="uploadDocument($event, 'transfer_document')">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="w-1/2">-->
<!--                                &lt;!&ndash; Transfer To Account &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="transfer_to_account">Transfer To Account-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.transfer_to_account.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <v-select-->
<!--                                        v-model="form.transfer_to_account"-->
<!--                                        :options="form_data.banks"-->
<!--                                        label="NAME"-->
<!--                                        :filterable="false"-->
<!--                                        placeholder="Search Account Name"-->
<!--                                        class="text-black"-->
<!--                                        :class="{'has-error': v$.form.transfer_to_account.$error}"-->
<!--                                        @search="debouncedSearch('banks', $event)"-->
<!--                                        id="transfer_to_account"-->
<!--                                    >-->
<!--                                        <template #no-options>-->
<!--                                            <div class="text-black">Search by account name</div>-->
<!--                                        </template>-->
<!--                                        <template #option="option">-->
<!--                                            <div class="p-2 text-xs text-black">-->
<!--                                                <div>{{ option.NAME }} - <span v-if="option.PROPERTY_156"> {{ Object.values(option.PROPERTY_156)[0]}}</span></div>-->
<!--                                                <div class="font-bold" v-if="option.PROPERTY_158">{{ Object.values(option.PROPERTY_158)[0]}}</div>-->
<!--                                                <div v-if="option.PROPERTY_159">{{ Object.values(option.PROPERTY_159)[0]}}</div>-->
<!--                                                <div v-if="option.PROPERTY_166">{{ Object.values(option.PROPERTY_166)[0]}}</div>-->
<!--                                            </div>-->
<!--                                        </template>-->
<!--                                        <template #selected-option="option">-->
<!--                                            <div>-->
<!--                                                <span v-if="form.transfer_to_account.PROPERTY_158"> {{ Object.values(form.transfer_to_account.PROPERTY_158)[0]}}</span>-->
<!--                                                <span v-else>{{option.NAME}}</span>-->
<!--                                            </div>-->
<!--                                        </template>-->
<!--                                    </v-select>-->
<!--                                    <div v-if="form.transfer_to_account" class="bg-white border-l-2 border-brand-active shadow-sm text-sm p-4 mt-2">-->
<!--                                        <div class="text-md mb-2">Transfer to:</div>-->
<!--                                        <div class="text-sm">  {{ form.transfer_to_account.NAME }} - <span v-if="form.transfer_to_account.PROPERTY_156"> {{ Object.values(form.transfer_to_account.PROPERTY_156)[0]}}</span></div>-->
<!--                                        <div class="text-sm" v-if="form.transfer_to_account.PROPERTY_158"> {{ Object.values(form.transfer_to_account.PROPERTY_158)[0]}}</div>-->
<!--                                        <div class="text-sm" v-if="form.transfer_to_account.PROPERTY_159"> {{ Object.values(form.transfer_to_account.PROPERTY_159)[0]}}</div>-->
<!--                                        <div class="text-sm" v-if="form.transfer_to_account.PROPERTY_166"> {{ Object.values(form.transfer_to_account.PROPERTY_166)[0]}}</div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Transfer Currency &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="currency">Transfer Currency-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.currency.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <select v-model="form.currency" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" :class="v$.form.currency.$error ? '!border-red-500' : ''" id="currency">-->
<!--                                        <option v-for="(obj, index) in currencies" :key="index" :value="obj.CURRENCY">{{ obj.FULL_NAME }}</option>-->
<!--                                    </select>-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Bank Charge Currency &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="currency">Bank Charge Currency-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.bank_charge_currency.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <select v-model="form.bank_charge_currency" class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit" :class="v$.form.bank_charge_currency.$error ? '!border-red-500' : ''" id="bank_charge_currency">-->
<!--                                        <option v-for="(obj, index) in currencies" :key="index" :value="obj.CURRENCY">{{ obj.FULL_NAME }}</option>-->
<!--                                    </select>-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Purpose of Transfer &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="detail_text">Purpose of Transfer-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.detail_text.$error">Please fill out this field.</span></span>-->
<!--                                    </label>-->
<!--                                    <textarea class="textarea textarea-input textarea-sm text-black" :class="v$.form.detail_text.$error ? '!border-red-500' : ''" style="height:127px!important" rows="4" placeholder="Purpose of Transfer" id="detail_text" type="text" v-model="form.detail_text"></textarea>-->
<!--                                </div>-->
<!--                                &lt;!&ndash; Supporting Document  &ndash;&gt;-->
<!--                                <div class="mb-4 w-full gap-2.5">-->
<!--                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="supporting_document">Supporting Document-->
<!--                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.supporting_document.$error">Please fill out this field</span></span>-->
<!--                                    </label>-->
<!--                                    <input class="file-input file-input-sm !text-black" :class="v$.form.supporting_document.$error ? '!border-red-500' : ''" placeholder="Supporting Document " id="supporting_document" type="file" @change="uploadDocument($event, 'supporting_document')">-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                    </form>
                </div>
            </div>
<!--            <div class="modal-footer justify-end">-->
<!--                <div class="flex gap-4">-->
<!--                    <button class="secondary-btn !text-md font-semibold !border-2 !px-10" data-modal-dismiss="true" @click="$emit('closeModal')">-->
<!--                        Cancel-->
<!--                    </button>-->
<!--                    <button-->
<!--                        class="main-btn"-->
<!--                        type="submit"-->
<!--                        @click="submit"-->
<!--                        :disabled="loading || crud_loading"-->
<!--                    >-->
<!--                        <svg v-if="crud_loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">-->
<!--                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>-->
<!--                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>-->
<!--                        </svg>-->
<!--                        <span v-else>Create Bank Transfer</span>-->
<!--                    </button>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</template>
<script>
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'
import qs from 'qs';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { debounce } from 'lodash';
import { DateTime } from "luxon";
import { sharedState } from "../../../state.js";

export default {
    name: "create-cash-request-form-modal",
    props: ['obj'],
    components: { vSelect },
    data(){
        return {
            form: {
                transfer_from_account: null,
                transfer_to_account: null,
                amount: '',
                bank_charge: 0,
                bank_charge_currency : null,
                transfer_document: null,
                invoice_number: '',
                currency: '',
                detail_text: '',
                supporting_document: null,
                project: null,
            },
            form_data: {
                banks: [],
                projects: [],
            },
            crud_loading: false,
            loading: false,
            debouncedSearch: null,
            bitrix_obj: {},
            currencies : []
        }
    },
    setup() {
        const v$ = useVuelidate();
        return { v$ };
    },
    validations () {
        return {
            form: {

            }
        }
    },
    methods: {
        async submit(){
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            this.crud_loading = true;

            let fields = {
                'NAME': 'Outgoing Bank Transfer',
                'PROPERTY_875': this.form.transfer_from_account.ID,
                'PROPERTY_876': this.form.transfer_to_account.ID,
                'PROPERTY_872': this.form.amount + "|" + this.form.currency,
                // 'PROPERTY_1275': this.form.bank_charge + "|" + this.form.bank_charge_currency,
                'PROPERTY_877': this.form.invoice_number,
                'DETAIL_TEXT': this.form.detail_text,
                'PROPERTY_887': 1532, //Transfer status
                'PROPERTY_901': `${this.form.project.TYPE === '1' ? 'L_' : this.form.project.TYPE === '2' ? 'D_' : this.form.project.TYPE === '3' ? 'C' : ''}${this.form.project.ID}`,
                'PROPERTY_1228': DateTime.now().toFormat('dd.MM.yyyy')
            }
            if (Number(this.form.bank_charge) > 0) {
                fields['PROPERTY_1275'] = `${Number(this.form.bank_charge)}|${this.form.bank_charge_currency}`;
            }

            if(this.type === "purchaseInvoice"){
                fields['PROPERTY_1080'] = this.bitrix_bank_transfer_company_ids.find(
                    (item) => item.purchase_invoice_company_id === this.obj.company_id
                )?.bank_transfer_company_id,

                fields['PROPERTY_1194'] = this.obj.id;
            }
            else if (this.type === "cashRequest"){
                fields['PROPERTY_1080'] = this.bitrix_bank_transfer_company_ids.find(
                    (item) => item.cash_request_company_id === this.obj.company_id
                )?.bank_transfer_company_id,

                fields['PROPERTY_1223'] = this.obj.id;

            }
            let transferElem = document.getElementById('transfer_document');
            let supportingElem = document.getElementById('supporting_document');

            if(transferElem.files.length !== 0){
                //upload transfer document
                const transferDoc = await this.uploadDocumentToBitrixDrive(transferElem);
                fields['PROPERTY_892'] = {
                    'n0': 'n' + transferDoc.ID
                };
            }
            if(supportingElem.files.length !== 0){
                //upload supporting document
                const supportingDoc = await this.uploadDocumentToBitrixDrive(supportingElem);
                fields['PROPERTY_878'] = {
                    'n0': 'n' + supportingDoc.ID
                };
            }
            this.infoToast('Uploading documents. This may take some time. Please wait.')
            const bitrixUserId = this.sharedState.bitrix_user_id ? this.sharedState.bitrix_user_id : null;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token ? this.sharedState.bitrix_webhook_token : null;
            const endpoint = 'lists.element.add';
            const requestData = qs.stringify({
                IBLOCK_TYPE_ID: 'lists',
                IBLOCK_ID: '99',
                ELEMENT_CODE: 'element1_' + Date.now(),
                FIELDS: fields
            });
            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                if(response.result){
                    let bankTransferId = response.result;
                    let iBlockId = 0;
                    if(this.type === "purchaseInvoice"){
                        //update purchase invoice
                        this.bitrix_obj.PROPERTY_1195 = bankTransferId;
                        this.bitrix_obj.PROPERTY_929 = `${this.form.project.TYPE === '1' ? 'L_' : this.form.project.TYPE === '2' ? 'D_' : this.form.project.TYPE === '3' ? 'C' : ''}${this.form.project.ID}`;
                        iBlockId = '104';
                    }
                    else if (this.type === "cashRequest"){
                        this.bitrix_obj.PROPERTY_1222 = bankTransferId;
                        this.bitrix_obj.PROPERTY_942 = `${this.form.project.TYPE === '1' ? 'L_' : this.form.project.TYPE === '2' ? 'D_' : this.form.project.TYPE === '3' ? 'C' : ''}${this.form.project.ID}`;
                        iBlockId = '105';
                    }

                    const toBeUpdateEndPoint = 'lists.element.update'
                    const toBeUpdateData = qs.stringify({
                        IBLOCK_TYPE_ID: 'bitrix_processes',
                        IBLOCK_ID: iBlockId,
                        ELEMENT_ID: this.obj.id,
                        FIELDS: this.bitrix_obj
                    })
                    const toBeUpdateResponse = await this.callBitrixAPI(toBeUpdateEndPoint, bitrixUserId, bitrixWebhookToken, toBeUpdateData);

                    if(toBeUpdateResponse.result){
                        this.crud_loading = false;
                        this.successToast('Bank transfer successfully created')
                        this.$emit('closeModal', true)
                    }
                }
            } catch (error) {
                console.error(error)
                this.crud_loading = false;
                this.$emit('closeModal')
                this.errorToast('Something went wrong')
            }
        },
        async searchData(type, query) {
            if (query) {
                const bitrixUserId = this.sharedState.bitrix_user_id;
                const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
                let endpoint, requestData;

                if (type === "banks") {
                    endpoint = "lists.element.get";
                    requestData = {
                        IBLOCK_TYPE_ID: "lists",
                        IBLOCK_ID: 39,
                        FILTER: { PROPERTY_158: `%${query}%`, PROPERTY_175: "226" },
                    };
                } else if (type === "projects") {
                    endpoint = "crm.contact.search";
                    requestData = { search: `%${query}%` };
                }

                try {
                    const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, qs.stringify(requestData));
                    if (response.result) {
                        if (type === "banks"){
                            this.form_data.banks = [];
                            this.form_data.banks = response.result;
                        }
                        if (type === "projects"){
                            this.form_data.projects = [];
                            this.form_data.projects = response.result.filter(obj => {
                                return obj.TYPE !== "3"
                            });
                        }
                    }
                } catch (error) {
                    console.error(`Error fetching ${type}:`, error);
                }
            }
        },
        async getProjectById(projectId){
            if (projectId){
                const bitrixUserId = this.sharedState.bitrix_user_id;
                const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
                const endpoint = "crm.contact.search";
                const requestData = {
                    id: `%${projectId.split("_")[1]}%`,
                };
                try {
                    const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, qs.stringify(requestData));
                    if (response.result) {
                        this.form.project = response.result[0]
                    }
                } catch (error) {
                    console.error("Error fetching project:", error);
                }
            }
        },
        uploadDocument(event, fieldName) {
            const file = event.target.files[0];
            if (!file) return;

            if (fieldName === 'transfer_document') {
                this.form.transfer_document = file;
            } else if (fieldName === 'supporting_document') {
                this.form.supporting_document = file;
            }
        },
        async uploadDocumentToBitrixDrive(fileInput){
            let fileName = "Transfer Document"
            if (fileInput.files.length > 0) {
                fileName = fileInput.files[0].name;

            }
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(fileInput.files[0]);
                reader.onload = async () => {
                    const base64File = reader.result.split(',')[1];
                    const bitrixUserId = this.sharedState.bitrix_user_id;
                    const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
                    const endpoint = 'disk.storage.uploadFile';
                    try {
                        const requestData = qs.stringify({
                            id: 490,
                            data: {
                                'NAME': fileName
                            },
                            fileContent: base64File,
                            generateUniqueName: true,
                        });
                        const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                        resolve(response.result);
                    } catch (error) {
                        this.errorToast('Unable to upload file')
                        reject(0);
                    }
                }
            })
        },
    },
    created() {
        this.debouncedSearch = debounce((type, query) => {
            this.searchData(type, query);
        }, 500);
    },
    async mounted() {
        this.form = this.obj;
    },
}
</script>
