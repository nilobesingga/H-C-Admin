<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="merchant_mapping_modal">
        <div class="modal-content top-[5%] lg:max-w-[1500px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Qashio / Cetrix Merchant Mapping </h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body relative h-full overflow-auto">
                <form @submit.prevent="submit">
                    <div class="flex w-full">
                        <!-- Qashio Name -->
                        <div class="mb-4 w-full gap-2.5">
                            <label class="form-label flex items-center gap-1 text-sm mb-1" for="qashio_name">Qashio Merchant Name
                                <span class="text-danger">* <span class="form-text-error" v-if="v$.form.qashio_name.$error">Please fill out this field</span></span>
                            </label>
                            <input class="input text-black bg-inherit" :class="v$.form.qashio_name.$error ? '!border-red-500' : ''" placeholder="Qashio name" id="qashio_name" type="text" v-model="form.qashio_name" tabindex="1">
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <div class="w-1/2">
                            <!-- Cetrix Company Id -->
                            <div class="mb-4 w-full gap-2.5">
                                <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_company_id">Cetrix Company Id</label>
                                <input class="input text-black bg-inherit" placeholder="Cetrix Company Id" id="bitrix_company_id" type="text" v-model="form.bitrix_company_id" tabindex="2">
                            </div>
                            <!-- Cetrix Contact Id -->
                            <div class="mb-4 w-full gap-2.5">
                                <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_contact_id">Cetrix Contact Id</label>
                                <input class="input text-black bg-inherit" placeholder="Cetrix Contact Id" id="bitrix_contact_id" type="text" v-model="form.bitrix_contact_id" tabindex="4">
                            </div>
                        </div>
                        <div class="w-1/2">
                            <!-- Cetrix Company Name -->
                            <div class="mb-4 w-full gap-2.5">
                                <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_company_name">Cetrix Company Name</label>
                                <input class="input text-black bg-inherit" placeholder="Cetrix Company Name" id="bitrix_company_name" type="text" v-model="form.bitrix_company_name" tabindex="3">
                            </div>
                            <!-- Cetrix Contact Name -->
                            <div class="mb-4 w-full gap-2.5">
                                <label class="form-label flex items-center gap-1 text-sm mb-1" for="bitrix_contact_name">Cetrix Contact Name</label>
                                <input class="input text-black bg-inherit" placeholder="Cetrix Contact Name" id="bitrix_contact_name" type="text" v-model="form.bitrix_contact_name" tabindex="5">
                            </div>
                        </div>
                    </div>
                    <div class="flex w-full justify-end">
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
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </div>
                </form>
                <!-- table -->
                <div class="relative flex-grow h-full overflow-auto border shadow-md reports-table-container border-brand mt-4">
                    <table class="table w-full text-xs align-middle table-fixed c-table table-border" :class="filteredData.length === 0 ? 'h-full' : ''">
                        <thead>
                        <tr class="font-medium text-center bg-black text-neutral-900">
                            <th class="sticky top-0 w-10">#</th>
                            <th class="sticky top-0 w-[150px]">Qashio Name</th>
                            <th class="sticky top-0 w-[100px]">Cetrix Company Id</th>
                            <th class="sticky top-0 w-[200px]">Cetrix Company Name</th>
                            <th class="sticky top-0 w-[100px]">Cetrix Contact Id</th>
                            <th class="sticky top-0 w-[200px]">Cetrix Contact Name</th>
                        </tr>
                        </thead>
                        <tbody class="h-full text-xs tracking-tight text-center">
                        <tr v-for="(obj, index) in filteredData" :key="index" class="transition-all duration-300 text-neutral-800">
                            <td>{{ ++index }}</td>
                            <td>{{ obj.bitrix_company_id }}</td>
                            <td>{{ obj.bitrix_company_name }}</td>
                            <td>{{ obj.bitrix_contact_id }}</td>
                            <td>{{ obj.bitrix_contact_name }}</td>
                        </tr>
                        <tr class="h-full table-no-data-available" v-if="filteredData.length === 0">
                            <td class="text-center text-md text-red-400 !border-none h-full">
                                <div class="flex flex-col items-center justify-center w-full h-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-4 size-10">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    No data available
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-if="loading" class="absolute inset-0 flex items-center justify-center pointer-events-none data-loading bg-neutral-100 z-100">
                        <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                            <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'
import {DateTime} from "luxon";
import _ from "lodash";

export default {
    name: "qashio-merchant-mapping-modal",
    data(){
        return {
            data: [],
            loading: false,
            crud_loading: false,
            filters: {
                search: null,
            },
            form: {
                qashio_name: null,
                bitrix_company_id: null,
                bitrix_company_name: null,
                bitrix_contact_id: null,
                bitrix_contact_name: null,
            },
        }
    },
    setup() {
        const v$ = useVuelidate();
        return { v$ };
    },
    validations () {
        return {
            form: {
                qashio_name: { required },
            }
        }
    },
    methods: {
        getData() {
            this.loading = true
            this.data = []
            axios({
                url: `/qashio/merchants/get-data`,
                method: 'GET',
            }).then(response => {
                this.data = response.data;
            }).catch(error => {
                console.log(error)
            }).finally(() => {
                this.loading = false;
            })
        },
        submit(){
            this.crud_loading = true;
            axios({
                url: `/admin/settings/user/${this.obj_id}`,
                method: 'GET',
            }).then(response => {
                this.obj = response.data.obj;
                this.form_data.modules = response.data.modules;
                this.form_data.categories = response.data.categories;
                this.form.selected_category_ids = response.data.selected_category_ids || [];
                this.form.selected_modules = response.data.selected_modules || [];
                this.buildModuleMaps();
            }).catch(error => {
                console.error('Error fetching user data:', error);
                this.errorToast('Failed to load user data');
            }).finally(() => {
                this.crud_loading = false;
            });
        },
    },
    computed:{
        filteredData() {
            const searchTerm = this.filters.search?.toLowerCase() || '';
            return this.data.filter(item => {
                // Filter by search input (case insensitive)
                const matchesSearch = [
                    item.id, item.qashio_name, item.bitrix_company_id, item.bitrix_company_name,
                    item.bitrix_contact_id, item.bitrix_contact_name,
                ].some(field => field?.toLowerCase().includes(searchTerm));

                // Return true only if all filters match
                return matchesSearch;
            });
        },
    },
    mounted() {
        this.getData();
    }
}
</script>

<style scoped>

</style>
