<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="create_cash_request_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px]" style="height: 85vh !important;">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Create New Cash Request</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body relative h-full overflow-auto">
                <!-- Loading Spinner -->
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center z-50">
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
                        <!-- Form Name Heading -->
                        <div class="text-center text-base font-semibold text-black mb-2">
                            {{ form.name }}
                        </div>
                        <!-- Basic Info Display Section -->
                        <div class="bg-white shadow rounded-md p-4 mb-4 border border-gray-200 text-sm text-black">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:divide-x sm:divide-gray-300">
                                <!-- First Column -->
                                <div>
                                    <div class="flex justify-between py-0.5">
                                        <span>Company:</span>
                                        <strong class="text-wrap">{{ form.category_name }}</strong>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>SAGE Company:</span>
                                        <span>{{ form.sage_company_name }}</span>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>VAT Status:</span>
                                        <span>{{ form.vatable }}</span>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>Amount:</span>
                                        <span>{{ formatAmount(form.amount) }} <strong>{{ form.currency }}</strong></span>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>Awaiting for Exchange Rate:</span>
                                        <span>{{ form.awaiting_for_exchange_rate }}</span>
                                    </div>
                                </div>
                                <!-- Second Column -->
                                <div class="pl-5">
                                    <div class="flex justify-between py-0.5">
                                        <span>Cash Release Location:</span>
                                        <span>{{ form.cash_release_location }}</span>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>Payment Date:</span>
                                        <span>{{ formatDate(form.payment_date) }}</span>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>Payment Mode:</span>
                                        <span>{{ form.payment_mode }}</span>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>Budget Only:</span>
                                        <span>{{ form.budget_only }}</span>
                                    </div>
                                    <hr class="my-1 border-gray-300">
                                    <div class="flex justify-between py-0.5">
                                        <span>Qashio Status:</span>
                                        <span class="capitalize" :class="obj.clearingStatus === 'cleared' ? 'badge badge-success' : obj.clearingStatus === 'pending' ? 'badge badge-warning' : ''">{{ obj.clearingStatus }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Form Section -->
                        <div class="bg-white shadow rounded-md p-4 mb-4 border border-gray-200 text-sm text-black">
                            <div class="flex gap-5">
                                <div class="w-1/2">
                                    <!-- Charge extra to client: -->
                                    <div class="mb-4 w-full gap-2.5">
                                        <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="charge_extra_to_client">Charge Extra to Client</label>
                                        <select
                                            v-model="form.charge_extra_to_client_id"
                                            class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                                            id="charge_extra_to_client"
                                        >
                                            <option
                                                v-for="(option, index) in form_fields_lists.find(f => f.key === 'charge_extra_to_client')?.values"
                                                :key="index"
                                                :value="option.key"
                                            >
                                                {{ option.value }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Charge To Running Account -->
                                    <div class="mb-4 w-full gap-2.5">
                                        <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="charge_to_running_account">Charge to Running Account</label>
                                        <select
                                            v-model="form.charge_to_running_account_id"
                                            class="select select-input select-sm px-3 pr-8 min-w-fit max-w-full text-black bg-inherit"
                                            id="charge_to_running_account"
                                        >
                                            <option
                                                v-for="option in form_fields_lists.find(f => f.key === 'charge_to_running_account')?.values"
                                                :key="option.key"
                                                :value="option.key"
                                            >
                                                {{ option.value }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Project -->
                                    <div class="mb-4 w-full gap-2.5">
                                        <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="project">Project
                                            <span class="text-danger">* <span class="form-text-error" v-if="v$.form.project.$error">Please fill out this field</span></span>
                                        </label>
                                        <v-select
                                            v-model="form.project"
                                            :options="form_data.projects"
                                            label="TITLE"
                                            :filterable="false"
                                            placeholder="Search Project"
                                            class="text-black"
                                            :class="{'has-error': v$.form.project.$error}"
                                            @search="debouncedSearch('projects', $event)"
                                            id="project"
                                        >
                                            <template #no-options>
                                                <div class="text-black">Search by project name</div>
                                            </template>
                                            <template #option="option">
                                                <div class="py-2 text-xs text-black">
                                                    <span v-if="option.TYPE === '1'" class="px-1 bg-[#1759cd] text-white">L</span>
                                                    <span v-if="option.TYPE === '2'" class="px-1 bg-[#149ac0] text-white">D</span>
                                                    <span v-if="option.TYPE === '3'" class="px-1">C</span>
                                                    <span class="ml-1">{{ option.TITLE }}</span>
                                                </div>
                                            </template>
                                            <template #selected-option="option">
                                                <div class="text-xs" v-if="form.project">
                                                    <span v-if="option.TYPE === '1'" class="px-1 bg-[#1759cd] !text-white">L</span>
                                                    <span v-if="option.TYPE === '2'" class="px-1 bg-[#149ac0] !text-white">D</span>
                                                    <span v-if="option.TYPE === '3'" class="px-1">C</span>
                                                    <span class="ml-1 text-black"> {{ form.project.TITLE }}</span>
                                                </div>
                                            </template>
                                        </v-select>
                                    </div>
                                    <!-- Receipts -->
                                    <div class="mb-4 w-full gap-2.5">
                                        <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="receipts">Receipts / Invoices
                                            <span class="text-danger">* <span class="form-text-error" v-if="v$.form.receipts.$error">Please fill out this field</span></span>
                                        </label>
                                        <input
                                            class="file-input file-input-sm !text-black mb-2"
                                            :class="v$.form.receipts.$error ? '!border-red-500' : ''"
                                            placeholder="Upload Receipts"
                                            id="receipts"
                                            type="file"
                                            multiple
                                            accept="image/*,application/pdf"
                                            @change="handleReceiptUpload($event)"
                                        >
                                        <p v-if="uploadError" class="text-red-500 text-xs mt-1">{{ uploadError }}</p>
                                        <div v-if="form.receipts?.length">
                                            <div class="flex flex-wrap gap-2">
                                                <div v-for="(receipt, index) in form.receipts" :key="index" class="flex items-center gap-2 bg-gray-100 p-2 rounded">
                                                    <a
                                                        class="block btn-xs secondary-btn other-doc-btn"
                                                        target="_blank"
                                                        :href="receipt.url"
                                                        v-if="receipt.url"
                                                    >
                                                        <span>{{ index + 1 }}. Qashio Receipt </span>
                                                    </a>
                                                    <span v-else class="block btn-xs secondary-btn other-doc-btn">{{ index + 1 }}. Uploaded File</span>
                                                    <button type="button" class="text-red-500" @click="removeReceipt(index)">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <!-- Invoice Number -->
                                    <div class="mb-4 w-full gap-2.5">
                                        <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="invoice_number">Supplier Invoice Number
                                            <span class="text-danger">* <span class="form-text-error" v-if="v$.form.invoice_number.$error">Please fill out this field</span></span>
                                        </label>
                                        <input
                                            class="input input-sm text-input"
                                            :class="v$.form.invoice_number.$error ? '!border-red-500' : ''"
                                            placeholder="Invoice Number"
                                            id="invoice_number"
                                            v-model="form.invoice_number"
                                        >
                                    </div>
                                    <!-- Supplier -->
                                    <div class="mb-4 w-full gap-2.5">
                                        <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="supplier">Supplier
                                            <span class="text-danger">* <span class="form-text-error" v-if="v$.form.supplier.$error">Please fill out this field</span></span>
                                            <span>({{ obj.merchantName }})</span>
                                        </label>
                                        <v-select
                                            v-model="form.supplier"
                                            :options="form_data.suppliers"
                                            label="TITLE"
                                            :filterable="false"
                                            placeholder="Search Supplier"
                                            class="text-black"
                                            :class="{'has-error': v$.form.supplier.$error}"
                                            @search="debouncedSearch('suppliers', $event)"
                                            id="supplier"
                                        >
                                            <template #no-options>
                                                <div class="mt-2">
                                                    <a
                                                        href="https://crm.cresco.ae/crm/company/details/0/"
                                                        class="text-blue-500 hover:underline"
                                                        target="_blank"
                                                    >
                                                        Create Supplier
                                                    </a>
                                                </div>
                                            </template>
                                            <template #option="option">
                                                <div class="py-2 text-xs text-black">
                                                    <span class="ml-1">{{ option.TITLE }}</span>
                                                </div>
                                            </template>
                                            <template #selected-option="option">
                                                <div class="text-xs" v-if="form.supplier">
                                                    <span class="ml-1 text-black"> {{ form.supplier.TITLE }}</span>
                                                </div>
                                            </template>
                                        </v-select>
                                    </div>
                                    <!-- Remarks -->
                                    <div class="mb-4 w-full gap-2.5">
                                        <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="remarks">Remarks
                                            <span class="text-danger">* <span class="form-text-error" v-if="v$.form.remarks.$error">Please fill out this field</span></span>
                                        </label>
                                        <textarea
                                            class="textarea textarea-input textarea-sm text-black"
                                            :class="v$.form.remarks.$error ? '!border-red-500' : ''"
                                            placeholder="Remarks" id="remarks"
                                            rows="5"
                                            type="text"
                                            v-model="form.remarks"
                                        >
                                    </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer justify-end">
                <div class="flex gap-4">
                    <button class="secondary-btn !text-md font-semibold !border-2 !px-10" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Cancel
                    </button>
                    <button
                        class="main-btn"
                        type="submit"
                        @click="submit"
                        :disabled="loading || crud_loading"
                    >
                        <svg v-if="crud_loading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-else>Create Cash Requisition</span>
                    </button>
                </div>
            </div>
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

export default {
    name: "qashio-create-cash-request-form-modal",
    props: ['obj', 'page_data'],
    components: { vSelect },
    data(){
        return {
            form: {
                name: null,
                category_id: null,
                category_name: null,
                sage_company_id: null,
                sage_company_name: null,
                vatable: null,
                amount: null,
                currency: null,
                invoice_number: null,
                project: null,
                supplier: null,
                remarks: null,
                receipts: [],
            },
            form_fields_lists: [
                {
                    key: "vatable",
                    name: "VATable",
                    field_id: "PROPERTY_1236",
                    values: {}
                },
                {
                    key: "awaiting_for_exchange_rate",
                    name: "Awaiting for Exchange Rate",
                    field_id: "PROPERTY_1249",
                    values: {}
                },
                {
                    key: "cash_release_location",
                    name: "Cash Release Location",
                    field_id: "PROPERTY_954",
                    values: {}
                },
                {
                    key: "payment_mode",
                    name: "Payment Mode",
                    field_id: "PROPERTY_1088",
                    values: {}
                },
                {
                    key: "budget_only",
                    name: "Budget Only",
                    field_id: "PROPERTY_1160",
                    values: {}
                },
                {
                    key: "charge_extra_to_client",
                    name: "Charge Extra to Client",
                    field_id: "PROPERTY_1215",
                    values: {}
                },
                {
                    key: "charge_to_running_account",
                    name: "Charge to Running Account",
                    field_id: "PROPERTY_1243",
                    values: {}
                },
                {
                    key: "pay_to_running_account",
                    name: "Pay to Running Account",
                    field_id: "PROPERTY_1251",
                    values: {}
                },
            ],
            form_data: {
                projects: [],
                suppliers: [],
            },
            crud_loading: false,
            loading: true,
            uploadError: null,
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
                project: {required},
                supplier: {required},
                remarks: {required},
                invoice_number: {required},
                receipts: {required},
            }
        }
    },
    methods: {
        async submit(){
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            this.crud_loading = true;

            // Prepare FormData for submission
            const formData = new FormData();
            formData.append('data', JSON.stringify({
                ...this.form,
                receipts: this.form.receipts.map(receipt => ({
                    url: receipt.url || null,
                    name: receipt.name || null,
                })),
            }));

            // Append user-uploaded files
            this.form.receipts.forEach((receipt, index) => {
                if (receipt.file) {
                    formData.append(`files[${index}]`, receipt.file);
                }
            });

            try {
                const response = await axios.post('/qashio/transaction/save', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
                if (response){
                    this.crud_loading = false;
                    this.$emit('closeModal');
                    this.successToast(response.data.message);
                }
            } catch (error) {
                this.crud_loading = false;
                this.$emit('closeModal');
                this.errorToast('Something went wrong');
            }
        },
        async searchData(type, query, isSearchFromForm = false) {
            const clearData = () => {
                if (isSearchFromForm) {
                    if (type === 'projects') this.form_data.projects = [];
                    if (type === 'suppliers') this.form_data.suppliers = [];
                }
            };

            if (!query) {
                clearData();
                return;
            }

            const bitrixUserId = this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;

            let endpoint, requestData;

            if (type === "projects") {
                endpoint = "crm.contact.search";
                requestData = { search: `%${query}%` };
            }
            if (type === "suppliers") {
                endpoint = "crm.company.list";
                requestData = {
                    filter: {
                        "%TITLE": query
                    }
                };
            }

            try {
                const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, qs.stringify(requestData));
                if (response.result) {
                    if (isSearchFromForm){
                        if (type === "projects"){
                            this.form_data.projects = response.result.filter(obj => obj.TYPE !== "3");
                        }
                        if (type === "suppliers"){
                            this.form_data.suppliers = response.result;
                        }
                    } else {
                        return response.result;
                    }
                } else {
                    clearData(); // Just in case the result is empty
                }
            } catch (error) {
                clearData(); // Also clear on error
                console.error(`Error fetching ${type}:`, error);
            }
        },
        handleReceiptUpload(event) {
            const files = Array.from(event.target.files);
            this.uploadError = null;

            // Validate file types and size
            const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            const invalidFiles = files.filter(file => !validTypes.includes(file.type) || file.size > maxSize);

            if (invalidFiles.length) {
                this.uploadError = 'Invalid file type or size. Only JPEG, PNG, PDF up to 10MB allowed.';
                return;
            }

            // Add uploaded files to receipts array
            const newReceipts = files.map(file => ({ file, name: file.name }));
            this.form.receipts = [...this.form.receipts, ...newReceipts];
            event.target.value = ''; // Clear input
        },
        removeReceipt(index) {
            this.form.receipts.splice(index, 1);
        },
        // async fetchFormFieldsListValuesFromBitrix() {
        //     const bitrixUserId = this.page_data.user.bitrix_user_id;
        //     const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;
        //     const endpoint = 'lists.field.get';
        //     for (const field of this.form_fields_lists) {
        //         try {
        //             const requestData = {
        //                 IBLOCK_TYPE_ID: this.page_data.bitrix_list.bitrix_iblock_type,
        //                 IBLOCK_ID: this.page_data.bitrix_list.bitrix_iblock_id,
        //                 FIELD_ID: field.field_id
        //             };
        //             const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
        //             field.values = response.result.L.DISPLAY_VALUES_FORM;
        //         } catch (error) {
        //             console.error(`Error fetching filter data for ${filter.key}:`, error);
        //         }
        //     }
        // },
        async fetchFormFieldsListValuesFromBitrix() {
            const bitrixUserId = this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.page_data.user.bitrix_webhook_token;
            const endpoint = 'lists.field.get';

            for (const field of this.form_fields_lists) {
                try {
                    const requestData = {
                        IBLOCK_TYPE_ID: this.page_data.bitrix_list.bitrix_iblock_type,
                        IBLOCK_ID: this.page_data.bitrix_list.bitrix_iblock_id,
                        FIELD_ID: field.field_id
                    };

                    const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData);
                    const rawValues = response?.result?.L?.DISPLAY_VALUES_FORM || {};

                    // Convert to array of { key, value }
                    let valuesArray = Object.entries(rawValues).map(([key, value]) => ({
                        key,
                        value: String(value)
                    }));

                    // Sort only for specific field
                    if (field.key === 'charge_to_running_account') {
                        const noIndex = valuesArray.findIndex(item => item.value.toLowerCase() === 'no');
                        const noItem = noIndex !== -1 ? valuesArray.splice(noIndex, 1)[0] : null;

                        valuesArray.sort((a, b) => a.value.localeCompare(b.value));

                        if (noItem) {
                            valuesArray.unshift(noItem);
                        }
                    }
                    field.values = valuesArray;

                } catch (error) {
                    console.error(`Error fetching filter data for ${field.key}:`, error);
                }
            }
        },
        assignDynamicField(key, valueKey) {
            const field = this.form_fields_lists.find(f => f.key === key);
            if (!field || !field.values) return;

            const matchedEntry = field.values.find(({ value }) => value.toLowerCase() === valueKey.toLowerCase());
            if (matchedEntry) {
                this.form[key + '_id'] = matchedEntry.key;
                this.form[key] = matchedEntry.value;
            }

        }
    },
    created() {
        this.debouncedSearch = debounce((type, query) => {
            this.searchData(type, query, true);
        }, 500);
    },
    async mounted() {
        await this.fetchFormFieldsListValuesFromBitrix();

        this.form['qashio_object'] = this.obj;

        // Initialize form fields
        const category = this.page_data.bitrix_list_categories.find(
            item => item.bitrix_category_id === this.obj.bitrix_qashio_credit_card_category_id
        );
        const cashRequisitionCategory = this.page_data.bitrix_cash_requisition_categories.find(
            item => item.category_id === category?.category_id
        );
        const sageCompany = this.page_data.bitrix_list_sage_companies.find(
            item => item.bitrix_sage_company_id === this.obj.bitrix_qashio_credit_card_sage_company_id
        );
        const cashRequisitionSageCompany = this.page_data.bitrix_cash_requisition_sage_companies.find(
            item => item.category_id === sageCompany?.category_id
        );
        if (cashRequisitionCategory) {
            this.form.category_id = cashRequisitionCategory.bitrix_category_id;
            this.form.category_name = cashRequisitionCategory.bitrix_category_name;
        }
        if (cashRequisitionSageCompany) {
            this.form.sage_company_id = cashRequisitionSageCompany.bitrix_sage_company_id;
            this.form.sage_company_name = cashRequisitionSageCompany.bitrix_sage_company_name;
        }
        // Vatable: match 'Standard Rate' to "Yes", else "No"
        this.assignDynamicField('vatable', this.obj.erpTaxRateName === 'Standard Rate' ? 'Yes' : 'No');
        // Amount
        this.form.amount = this.obj.clearingStatus === 'pending' ? this.obj.transactionAmount :  (this.obj.clearingAmount ? (parseFloat(this.obj.clearingAmount) +  parseFloat(this.obj.clearingFee)).toFixed(2) : '0.00')
        // Currency
        this.form.currency = this.obj.clearingStatus === 'pending' ? this.obj.transactionCurrency : this.obj.billingCurrency;
        // Name
        this.form.name = `Cash Request - ${this.form.amount}|${this.form.currency}`;
        // Amount Given
        this.form.amount_given = this.obj.clearingStatus === 'cleared' ? this.form.amount : '';
        // Awaiting for Exchange Rate
        this.assignDynamicField('awaiting_for_exchange_rate', (this.obj.clearingStatus === 'pending' && this.obj.transactionCurrency !== 'AED' ) ? 'Yes' : 'No');
        // Cash Release Location
        this.assignDynamicField('cash_release_location', 'Dubai');
        // Invoice Number
        this.form.invoice_number = this.obj.purchaseOrderNumber;
        // Payment Date
        this.form.payment_date =  DateTime.fromISO(this.obj.transactionTime).toFormat('yyyy-MM-dd');
        // Payment Mode
        this.assignDynamicField('payment_mode', 'Qashio');
        // Budget Only
        this.assignDynamicField('budget_only', 'No');
        // Charge extra to client
        this.assignDynamicField('charge_extra_to_client', 'No');
        // Charge To Running Account:
        this.assignDynamicField('charge_to_running_account', 'No');
        // Pay to Running Account:
        this.assignDynamicField('pay_to_running_account', 'No');
        // Initialize receipts (Qashio URLs)
        if (this.obj.receipts && Array.isArray(this.obj.receipts)) {
            this.form.receipts = this.obj.receipts.map(url => ({
                url,
                name: `Receipt_${url.split('/').pop()}`,
            }));
        }
        // Load project
        if (this.obj.memo) {
            const match = this.obj.memo.match(/[A-Z]_\d+-\d+/);
            if (match) {
                const projects = await this.searchData('projects', match[0], false);
                if (Array.isArray(projects)) {
                    this.form_data.projects = projects;
                    const priorityItem = projects.find(item => item.TYPE === "2") || projects.find(item => item.TYPE === "1");
                    this.form.project = priorityItem;
                    if (priorityItem) {
                        this.form.project_id = (priorityItem.TYPE === "2" ? "D_" : "L_") + priorityItem.ID;
                    }
                }
            }
        }
        // Load supplier
        if (this.obj.merchantName) {
            try {
                const mappingResponse = await axios.post('/qashio/merchants/get-data', {
                    'qashio_merchant_name': this.obj.merchantName,
                    'is_array': false
                });
                if (mappingResponse.data.data) {
                    const bitrixCompanyId = mappingResponse.data.data.bitrix_company_id;
                    const response = await this.callBitrixAPI('crm.company.get', this.page_data.user.bitrix_user_id, this.page_data.user.bitrix_webhook_token, { ID: bitrixCompanyId });
                    if (response.result) {
                        this.form_data.suppliers.push(response.result);
                        this.form.supplier = response.result;
                    }
                }
            } catch (error) {
                console.error('Error fetching supplier:', error);
            }
        }
        // Release Date and Released By
        if (this.obj.clearingStatus === 'cleared'){
            this.form.release_date = DateTime.now().toFormat('dd.MM.yyyy');
            this.form.release_by = this.page_data.user.user_name
        }
        // Remarks
        if(this.obj.transactionCurrency !== 'AED' && this.obj.clearingStatus === 'cleared'){
            this.form.remarks =  `Converted ${this.formatAmount(this.obj.transactionAmount)} ${this.obj.transactionCurrency} to ${this.formatAmount(parseFloat(this.obj.clearingAmount) + parseFloat(this.obj.clearingFee))} ${this.obj.billingCurrency}`;
        }

        this.loading = false;

    },
}
</script>
<style scoped>
</style>
