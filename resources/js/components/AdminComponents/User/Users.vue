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
                    <div class="flex max-w-full min-w-[8rem]">
                        <select v-model="filters.bitrix_active" name="bitrix_active" class="select select-input" @change="getData(false)">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">In-Active</option>
                        </select>
                    </div>
                    <div class="flex min-w-96">
                        <div class="relative w-full">
                            <i class="ki-outline ki-magnifier leading-none text-md text-black absolute top-1/2 left-3 transform -translate-y-1/2"></i>
                            <input
                                class="input input-sm text-input !ps-8"
                                placeholder="Search"
                                type="text"
                                v-model="filters.search"
                            />
                        </div>
                    </div>
                    <div class="flex">
                        <button
                            class="main-btn !bg-white !border !py-2 !px-5 !min-w-[120px] !text-sm focus:!border-tec-active"
                            :disabled="loading"
                            @click="getData(true)"
                        >
                            Sync Users
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="relative flex-grow overflow-auto table-container shadow-md border border-tec">
            <!-- Table -->
            <table class="w-full table table-main table-border align-middle text-xs table-fixed">
                <thead>
                    <tr class="text-left tracking-tight">
                        <th class="sticky top-0 w-10 text-center">#</th>
                        <th class="sticky top-0 w-[200px] text-left">Name</th>
                        <th class="sticky top-0 w-[70px] text-center">User Name</th>
                        <th class="sticky top-0 w-[70px] text-center">Bitrix User Id</th>
                        <th class="sticky top-0 w-[100px] text-center">Bitrix Webhook Token</th>
                        <th class="sticky top-0 w-[350px] text-center">Access URL</th>
                        <th class="sticky top-0 w-[200px]">Modules</th>
                        <th class="sticky top-0 w-[300px]">Categories</th>
                        <th class="sticky top-0 w-[50px] text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-center text-xs tracking-tight">
                    <tr v-for="(obj, index) in data" :key="index" class="group transition-all duration-300 text-neutral-800">
                        <td>{{ ++index }}</td>
                        <td class="text-left">
                            <a :href="`https://crm.cresco.ae/company/personal/user/${obj.bitrix_user_id}/`" target="_blank" class="hover:text-primary-active">
                                <div class="flex items-center justify-between py-1.5 gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <img alt="" class="rounded-full transition-all duration-300 border border-white shadow-lg ring-1 ring-black group-hover:!ring-tec-active group-hover:!shadow-tec-active/30 size-9 shrink-0" :src="obj.profile ? obj.profile.bitrix_profile_photo : null">
                                        <div class="flex flex-col">
                                            <div class="text-sm font-semibold text-gray-900  mb-px">{{ obj.profile ? obj.profile.bitrix_name : null }} {{ obj.profile ? obj.profile.bitrix_last_name : null }}</div>
                                            <span class="text-xs font-normal text-gray-600 group-hover:!text-tec-active">{{ obj.email }}</span>
                                        </div>
                                    </div>
                                    <span v-if="obj.is_admin" class="badge badge-xs !shadow-md !shadow-tec-active/30 !bg-tec-active/80 !text-white !border-tec-active badge-outline">Admin</span>
                                </div>
                            </a>
                        </td>
                        <td>{{ obj.user_name }}</td>
                        <td>{{ obj.bitrix_user_id }}</td>
                        <td>{{ obj.bitrix_webhook_token }}</td>
                        <td><a :href="`${appUrl}/login/${obj.access_token}`" class="btn btn-link transition-all duration-300 !text-neutral-800 hover:!text-tec-active" target="_blank">{{ `${appUrl}/login/${obj.access_token}` }}</a></td>
                        <td class="text-left">
                            <span class="group-hover:!bg-tec-active/10 mr-1 mb-1 !text-neutral-800 badge badge-sm bg-transparent transition-all duration-300" v-for="(category, index) in obj.modules" :key="index">{{ category.name }}</span>
                        </td>
                        <td class="text-left">
                            <span class="group-hover:!bg-tec-active/30 mr-1 mb-1 !text-neutral-800 badge badge-sm bg-transparent transition-all duration-300" v-for="(category, index) in obj.categories" :key="index">{{ category.name }}</span>
                        </td>
                        <td class="text-end">
                            <button
                                @click="openModal('acl', obj.id)"
                                data-modal-toggle="#user_acl_modal"
                                class="secondary-btn mb-1 block w-full focus:!border-tec-active"
                            >
                                ACL
                            </button>
                            <button
                                @click="openModal('update_password', obj.id)"
                                data-modal-toggle="#user_update_password_form_modal"
                                class="secondary-btn mb-1 block w-full focus:!border-tec-active"
                            >
                                Edit
                            </button>
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
    <user-acl-modal
        :obj_id="obj_id"
        v-if="is_acl_modal"
        @closeModal="closeModal"
    />
    <user-update-password-form-modal
        :obj_id="obj_id"
        v-if="is_update_password_form_modal"
        @closeModal="closeModal"
    />
</template>

<script>
import { debounce } from 'lodash';
export default {
    name: "users",
    props: ['page_data'],
    data() {
        return {
            data: [],
            loading: false,
            filters: {
                search: null,
                bitrix_active: "1"
            },
            obj_id: null,
            is_acl_modal: false,
            is_update_password_form_modal: false,
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
                if (isSync){
                    this.successToast('User sync successfully')
                }
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
            this.obj_id = objId
            this.modal_type = modalType;
            if(modalType === 'acl'){
                this.is_acl_modal = true;
            }
            if (modalType === 'update_password'){
                this.is_update_password_form_modal = true
            }
        },
        closeModal(){
            this.is_acl_modal = false;
            this.is_update_password_form_modal = false
            this.modal_type = null;
            this.obj_id = null
            this.removeModalBackdrop();
            this.getData();
        }
    },
    watch: {
        'filters.search': {
            handler: 'debouncedSearch',
            immediate: false
        }
    },
    mounted() {
        this.getData(false);
    }
}
</script>

<style scoped>

</style>
