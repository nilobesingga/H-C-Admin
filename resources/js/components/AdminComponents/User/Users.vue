<template>
    <div class="container-fluid">
        <div class="pb-6">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex flex-col gap-1">
                    <select v-model="filters.bitrix_active" name="bitrix_active" class="select select-sm min-w-[8rem] max-w-full truncate" @change="getData(false)">
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">In-Active</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="grid gap-5 lg:gap-7.5">
            <div class="card min-w-full">
                <div class="card-header">
                    <h3 class="card-title">Bitrix Users</h3>
                    <div class="flex gap-6">
                        <div class="relative">
                            <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3"></i>
                            <input class="input input-sm ps-8" placeholder="Search Users" type="text" v-model="filters.search" @input="debouncedSearch">
                        </div>
                        <button
                            class="btn btn-sm btn-outline btn-primary"
                            :disabled="loading"
                            @click="getData(true)"
                        >
                            <i class="ki-filled ki-arrows-circle"></i>
                            <span>Sync Users</span>
                        </button>
                    </div>
                </div>
                <div class="card-table scrollable-x-auto">
                    <div class="scrollable-auto">
                        <table class="table align-middle text-2sm text-gray-600 relative">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="font-medium">#</th>
                                    <th class="font-medium min-w-36">Name</th>
                                    <th class="font-medium min-w-20">Bitrix User Id</th>
                                    <th class="font-medium min-w-20">Bitrix Webhook Token</th>
                                    <th class="font-medium min-w-20">Access URL</th>
                                    <th class="min-w-16"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(obj, index) in data" :key="index">
                                    <td>{{ ++index }}</td>
                                    <td>
                                        <div class="flex items-center justify-between px-5 py-1.5 gap-1.5">
                                            <div class="flex items-center gap-2">
                                                <img alt="" class="rounded-full size-9 shrink-0" :src="obj.profile ? obj.profile.bitrix_profile_photo : null">
                                                <div class="flex flex-col">
                                                    <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="#">{{ obj.profile ? obj.profile.bitrix_name : null }}</a>
                                                    <span class="text-xs font-normal text-gray-600">{{ obj.email }}</span>
                                                </div>
                                            </div>
                                            <span v-if="obj.is_admin" class="badge badge-xs badge-primary badge-outline">Admin</span>
                                        </div>
                                    </td>
                                    <td>{{ obj.bitrix_user_id }}</td>
                                    <td>{{ obj.bitrix_webhook_token }}</td>
                                    <td>
                                        <a :href="`${appUrl}/${obj.access_token}`" class="btn btn-link">
                                            {{ `${appUrl}/login/${obj.access_token}` }}
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <div class="menu inline-flex" data-menu="true">
                                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical">
                                                    </i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <span class="menu-link" data-modal-toggle="#user_form_modal" @click="openModal('edit', obj.id)">
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
                            <div v-if="loading" class="absolute inset-0 bg-gray-300 bg-opacity-50 flex items-center justify-center z-50 pointer-events-none">
                                <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                                    <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </div>
                            </div>
                        </table>
                    </div>
                </div>
                <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                    <div class="flex items-center gap-2 order-2 md:order-1">
                        Showing {{ data.length }} records
                    </div>
                </div>
            </div>
        </div>
    </div>
    <user-form-modal
        :obj_id="obj_id"
        :modal_type="modal_type"
        v-if="is_form_modal"
        @closeModal="closeModal"
    />
</template>

<script>
import { debounce } from 'lodash';
export default {
    name: "users",
    data() {
        return {
            data: [],
            loading: false,
            filters: {
                search: null,
                bitrix_active: "1"
            },
            obj_id: null,
            is_form_modal: false,
            modal_type: null
        }
    },
    methods:{
        getData(isSync = false){
            this.loading = true
            this.data = []
            axios({
                url: `/admin/settings/users/get-data`,
                method: 'POST',
                data: {
                    filters: this.filters,
                    is_sync: isSync
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
        }
    },
    mounted() {
        this.getData(false);
    }
}
</script>

<style scoped>
.table tbody tr td{
    padding-top: unset;
    padding-bottom: unset;
}
.table-no-data-available td{
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
}
</style>
