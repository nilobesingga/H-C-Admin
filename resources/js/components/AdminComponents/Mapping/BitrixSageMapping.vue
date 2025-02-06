<template>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="pb-6">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-black font-black">
                    <div>{{ page_data.title }}</div>
                </div>
                <!-- Filters -->
                <div class="flex items-center gap-4 flex-wrap w-full justify-end">
                    <div class="flex max-w-full min-w-[10rem]">
                        <select
                            class="select select-sm max-w-full truncate"
                            v-model="filters.bitrix_list_id"
                            @change="getData"
                        >
                            <option value="" selected>Filter by Bitrix List</option>
                            <option v-for="obj in page_data.bitrix_lists" :key="obj.id" :value="obj.id">
                                {{ obj.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex max-w-full min-w-[10rem]">
                        <select
                            class="select select-sm max-w-full truncate"
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
                            class="btn btn-sm btn-outline btn-primary"
                            :disabled="loading"
                            @click="openModal('add')"
                            data-modal-toggle="#bitrix_sage_mapping_form_modal"
                        >
                            <i class="ki-filled ki-plus-squared"></i>
                            <span>Add Mapping</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table -->
        <div class="relative flex-grow overflow-auto table-container">
            <!-- Table -->
            <table class="w-full table table-main table-border align-middle text-xs table-fixed">
                <thead>
                    <tr class="bg-black text-gray-900 font-medium text-center">
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
                <tbody class="text-center text-xs text-gray-700">
                    <tr v-for="(obj, index) in data" :key="index" class="odd:bg-white even:bg-slate-100 hover:bg-gray-300">
                        <td>{{ ++index }}</td>
                        <td class="text-black text-left">{{ obj.bitrix_list }}</td>
                        <td class="text-black text-left">{{ obj.category }}</td>
                        <td class="text-black">{{ obj.sage_company_code }}</td>
                        <td class="text-black">{{ obj.bitrix_sage_company_id }}</td>
                        <td class="text-black text-left">{{ obj.bitrix_sage_company_name }}</td>
                        <td class="text-black">{{ obj.bitrix_category_id }}</td>
                        <td class="text-black text-left">{{ obj.bitrix_category_name }}</td>
                        <td class="text-end">
                            <div class="menu inline-flex" data-menu="true">
                                <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                    <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                        <i class="ki-filled ki-dots-vertical">
                                        </i>
                                    </button>
                                    <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                        <div class="menu-item">
                                            <span class="menu-link" data-modal-toggle="#bitrix_sage_mapping_form_modal" @click="openModal('edit', obj.id)">
                                                <span class="menu-icon"><i class="ki-filled ki-pencil"></i></span>
                                                <span class="menu-title">Edit</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="table-no-data-available" v-if="data.length === 0">
                        <td class="text-center text-red-400">No data available</td>
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
