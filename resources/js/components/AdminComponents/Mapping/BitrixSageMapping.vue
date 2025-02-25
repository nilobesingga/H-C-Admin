<template>
    <div class="container-fluid px-3">
        <!-- Page Header -->
        <div class="py-7">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-black font-bold text-3xl tracking-tight">
                    {{ page_data.title }}
                </div>

                <!-- Filters -->
                <div class="flex items-center gap-4 flex-wrap w-full justify-end">
                    <div class="flex max-w-full min-w-[12rem]">
                        <select
                            class="select select-input"
                            v-model="filters.bitrix_list_id"
                            @change="getData"
                        >
                            <option value="" selected>Filter by Bitrix List</option>
                            <option v-for="obj in page_data.bitrix_lists" :key="obj.id" :value="obj.id">
                                {{ obj.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex max-w-full min-w-[12rem]">
                        <select
                            class="select select-input"
                            v-model="filters.category_id"
                            @change="getData"
                        >
                            <option value="" selected>Filter by Category</option>
                            <option v-for="obj in page_data.categories" :key="obj.id" :value="obj.id">
                                {{ obj.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex">
                        <button
                            class="main-btn !bg-white !border !py-2 !px-5 !min-w-[120px] !text-sm focus:!border-tec-active"
                            :disabled="loading"
                            @click="openModal('add')"
                            data-modal-toggle="#bitrix_sage_mapping_form_modal"
                        >
                            Add Mapping
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table -->
        <div class="relative flex-grow overflow-auto table-container shadow-md border border-tec h-full">
            <!-- Table -->
            <table class="w-full table table-main table-border align-middle text-xs table-fixed h-full">
                <thead>
                    <tr class="text-left tracking-tight">
                        <th class="sticky top-0 w-10">#</th>
                        <th class="sticky top-0 w-[80px] text-left">Bitrix List Name</th>
                        <th class="sticky top-0 w-[80px] text-left">Category Name</th>
                        <th class="sticky top-0 w-[100px]">Sage Company Code</th>
                        <th class="sticky top-0 w-[100px]">Bitrix Sage Company Id</th>
                        <th class="sticky top-0 w-[150px] text-left">Bitrix Sage Company Name</th>
                        <th class="sticky top-0 w-[100px]">Bitrix Category Id</th>
                        <th class="sticky top-0 w-[100px] text-left">Bitrix Category Name</th>
                        <th class="sticky top-0 w-10"></th>
                    </tr>
                </thead>
                <tbody class="text-center text-xs tracking-tight h-full">
                    <tr v-for="(obj, index) in data" :key="index" class="group transition-all duration-300 text-neutral-800">
                        <td>{{ ++index }}</td>
                        <td class="text-black text-left">{{ obj.bitrix_list }}</td>
                        <td class="text-black text-left">{{ obj.category }}</td>
                        <td class="text-black">{{ obj.sage_company_code }}</td>
                        <td class="text-black">{{ obj.bitrix_sage_company_id }}</td>
                        <td class="text-black text-left">{{ obj.bitrix_sage_company_name }}</td>
                        <td class="text-black">{{ obj.bitrix_category_id }}</td>
                        <td class="text-black text-left">{{ obj.bitrix_category_name }}</td>
                        <td class="text-end">
                            <button
                                @click="openModal('edit', obj.id)"
                                data-modal-toggle="#bitrix_sage_mapping_form_modal"
                                class="secondary-btn mb-1 block w-full focus:!border-tec-active"
                            >
                                Edit
                            </button>
                        </td>
                    </tr>
                    <tr class="table-no-data-available h-full" v-if="data.length === 0">
                        <td class="text-center text-md text-red-400 !border-none h-full">
                            <div class="flex flex-col h-full w-full items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 mb-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                No data available
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="loading" class="absolute inset-0 bg-gray-300 bg-opacity-100 flex items-center justify-center z-50 pointer-events-none">
                <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                    <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                </div>
            </div>
        </div>
        <!-- footer -->
        <div class="flex items-center justify-between">
            <div class="text-xs">
                <span>Showing {{ data.length }} records</span>
            </div>
        </div>
    </div>
    <bitrix-sage-mapping-form-modal
        :categories="page_data.categories"
        :bitrix_lists="page_data.bitrix_lists"
        :obj_id="obj_id"
        :modal_type="modal_type"
        v-if="is_form_modal"
        @closeModal="closeModal"
    />
</template>

<script>
import {debounce} from "lodash";

export default {
    name: "bitrix-sage-mapping",
    props: ['page_data'],
    data() {
        return {
            data: [],
            loading: false,
            filters: {
                category_id: "",
                bitrix_list_id: "",
            },
            obj_id: null,
            is_form_modal: false,
            modal_type: null
        }
    },
    methods: {
        getData(){
            this.loading = true
            this.data = []
            axios({
                url: `/admin/settings/bitrix-sage-mapping/get-data`,
                method: 'POST',
                data: {
                    filters: this.filters,
                }
            }).then(response => {
                this.data = response.data;
            }).catch(error => {
                console.log(error)
            }).finally(() => {
                this.loading = false;
            })
        },
        debouncedSearch: debounce(function(){
            this.getData(false);
        }, 500),
        openModal(modalType, objId){
            this.is_form_modal = true;
            this.modal_type = modalType;
            modalType === 'edit' ? this.obj_id = objId : this.obj_id = null
        },
        closeModal(){
            this.is_form_modal = false;
            this.modal_type = null;
            this.obj_id = null
            this.removeModalBackdrop();
            this.getData();
        },
    },
    mounted() {
        this.getData();
    }
}
</script>

<style scoped>

</style>
