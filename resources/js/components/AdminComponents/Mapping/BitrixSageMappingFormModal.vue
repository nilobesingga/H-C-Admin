<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="bitrix_sage_mapping_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize">{{ modal_type }} Bitrix Sage Mapping</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <form @submit.prevent="submit">
                <div class="modal-body h-full overflow-auto">
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
                    <div v-else>
                        <div class="flex gap-5">
                            <div class="w-1/2">
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="category_id">Category
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="select select-sm max-w-full truncate" id="category_id" v-model="form.category_id">
                                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                    </select>
                                    <p class="is-invalid-text" v-if="v$.form.category_id.$error">Please fill out this field.</p>
                                </div>
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="sage_company_code">Sage Company Code</label>
                                    <input class="input input-sm" id="sage_company_code" type="text" v-model="form.sage_company_code">
                                </div>
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_sage_company_name">Bitrix Sage Company Name</label>
                                    <input class="input input-sm" id="bitrix_sage_company_name" type="text" v-model="form.bitrix_sage_company_name">
                                </div>
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_category_name">Bitrix Category Name</label>
                                    <input class="input input-sm" id="bitrix_category_name" type="text" v-model="form.bitrix_category_name">
                                </div>
                            </div>
                            <div class="w-1/2">
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_list_id">Bitrix List
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="select select-sm max-w-full truncate" v-model="form.bitrix_list_id" id="bitrix_list_id">
                                        <option v-for="bitrix_list in bitrix_lists" :key="bitrix_list.id" :value="bitrix_list.id">{{ bitrix_list.name }}</option>
                                    </select>
                                    <p class="is-invalid-text" v-if="v$.form.bitrix_list_id.$error">Please fill out this field.</p>
                                </div>
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_sage_company_id">Bitrix Sage Company Id</label>
                                    <input class="input input-sm" id="bitrix_sage_company_id" type="text" v-model="form.bitrix_sage_company_id">
                                </div>
                                <div class="mb-4 w-full gap-2.5">
                                    <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_category_id">Bitrix Category id</label>
                                    <input class="input input-sm" id="bitrix_category_id" type="text" v-model="form.bitrix_category_id">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-end">
                    <div class="flex gap-4">
                        <button class="btn btn-light btn-sm" data-modal-dismiss="true" @click="$emit('closeModal')">Cancel</button>
                        <button
                            class="btn btn-primary btn-sm justify-center"
                            type="submit"
                            @click="submit"
                            :disabled="loading || crud_loading"
                        >
                            <svg v-if="crud_loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-else>Save</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'

export default {
    name: "bitrix-sage-mapping-form-modal",
    props: ['obj_id', 'modal_type', 'categories', 'bitrix_lists'],
    data(){
        return {
            obj: null,
            form: {
                category_id: null,
                bitrix_list_id: null,
                sage_company_code: null,
                bitrix_sage_company_id: null,
                bitrix_sage_company_name: null,
                bitrix_category_id: null,
                bitrix_category_name: null,
            },
            crud_loading: false,
            loading: false
        }
    },
    setup() {
        const v$ = useVuelidate();
        return { v$ };
    },
    validations () {
        return {
            form: {
                category_id: { required },
                bitrix_list_id: { required },
            }
        }
    },
    methods: {
        getData(){
            this.loading = true
            this.data = []
            axios({
                url: `/admin/settings/bitrix-sage-mapping/get-data/${this.obj_id}`,
                method: 'POST',
            }).then(response => {
                this.form = response.data;
            }).catch(error => {
                console.log(error)
            }).finally(() => {
                this.loading = false;
            })
        },
        async submit(){
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            this.crud_loading = true
            axios({
                url: `/admin/settings/bitrix-sage-mapping/save`,
                method: 'POST',
                data: {
                    form_type: this.modal_type,
                    request_data: this.form,
                }
            }).then(response => {
                this.successToast(response.data.message)
                this.$emit('closeModal');
            }).catch(error => {
                if (error.status === 500){
                    this.errorToast(error.response.data.message)
                }
            }).finally(() => {
                this.crud_loading = false;
            })
        },
    },
    mounted() {
        this.modal_type === 'edit' ? this.getData() : null
    }
}
</script>
<style scoped>

</style>
