<template>
    <div class="z-0 px-3 container-fluid">
        <!-- Page Header -->
        <div class="py-7">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-3xl font-bold tracking-tight text-black">
                    {{ page_data.title }}
                </div>

                <!-- Actions & Filters -->
                <div class="flex flex-wrap items-center justify-end w-full gap-4">
                    <!-- Filters -->
                    <div class="flex max-w-full min-w-[8rem]">
                        <select v-model="filters.bitrix_active" name="bitrix_active" class="select select-input" @change="getData(false)">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">In-Active</option>
                        </select>
                    </div>
                    <div class="flex min-w-96">
                        <div class="relative w-full">
                            <i class="absolute leading-none text-black transform -translate-y-1/2 ki-outline ki-magnifier text-md top-1/2 left-3"></i>
                            <input
                                class="input input-sm text-input !ps-8"
                                placeholder="Search"
                                type="text"
                                v-model="filters.search"
                            />
                        </div>
                    </div>
                    <!-- Add User Button -->
                    <button
                        class="h-10 px-5 text-sm font-semibold rounded shadow text-dark btn-secondary hover:bg-gray/90 focus:outline-none focus:ring-2 focus:ring-tec-active/50"
                        @click="openModal('add_user')"
                        type="button"
                    >
                        + Add User
                    </button>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="relative z-0 flex-grow overflow-auto border shadow-md table-container border-tec">
            <!-- Table -->
            <table class="table w-full text-xs align-middle table-fixed table-main table-border">
                <thead>
                    <tr class="tracking-tight text-left">
                        <th class="sticky top-0 w-10 text-center">#</th>
                        <th class="sticky top-0 w-[200px] text-left">Name</th>
                        <th class="sticky top-0 w-[70px] text-center">User Name</th>
                        <!-- <th class="sticky top-0 w-[70px] text-center">Bitrix User Id</th> -->
                        <!-- <th class="sticky top-0 w-[100px] text-center">Bitrix Webhook Token</th> -->
                        <th class="sticky top-0 w-[350px] text-center">Access URL</th>
                        <!-- <th class="sticky top-0 w-[200px]">Modules</th> -->
                        <!-- <th class="sticky top-0 w-[300px]">Categories</th> -->
                        <th class="sticky top-0 w-[50px] text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs tracking-tight text-center">
                    <tr v-for="(obj, index) in data" :key="index" class="transition-all duration-300 group text-neutral-800">
                        <td>{{ ++index }}</td>
                        <td class="text-left">
                            <a :href="`https://crm.cresco.ae/crm/contact/details/${obj.bitrix_contact_id}/`" target="_blank" class="hover:text-primary-active">
                                <div class="flex items-center justify-between py-1.5 gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <img alt="" class="rounded-full transition-all duration-300 border border-white shadow-lg ring-1 ring-black group-hover:!ring-tec-active group-hover:!shadow-tec-active/30 size-9 shrink-0" :src="obj.profile ? 'storage/'+obj.profile.photo: 'storage/images/logos/CRESCO_icon.png'" />
                                        <div class="flex flex-col">
                                            <div class="mb-px text-sm font-semibold text-gray-900">{{ obj.profile ? obj.profile.name : null}}</div>
                                            <span class="text-xs font-normal text-gray-600 group-hover:!text-tec-active">{{ obj.email }}</span>
                                        </div>
                                    </div>
                                    <span v-if="obj.is_admin" class="badge badge-xs !shadow-md !shadow-tec-active/30 !bg-tec-active/80 !text-white !border-tec-active badge-outline">Admin</span>
                                </div>
                            </a>
                        </td>
                        <td>{{ obj.user_name }}</td>
                        <!-- <td>{{ obj.bitrix_user_id }}</td> -->
                        <!-- <td>{{ obj.bitrix_webhook_token }}</td> -->
                        <td><a :href="`${appUrl}/login/${obj.access_token}`" class="btn btn-link transition-all duration-300 !text-neutral-800 hover:!text-tec-active" target="_blank">{{ `${appUrl}/login/${obj.access_token}` }}</a></td>
                        <!-- <td class="text-left">
                            <span class="group-hover:!bg-tec-active/10 mr-1 mb-1 !text-neutral-800 badge badge-sm bg-transparent transition-all duration-300" v-for="(category, index) in obj.modules" :key="index">{{ category.name }}</span>
                        </td>
                        <td class="text-left">
                            <span class="group-hover:!bg-tec-active/30 mr-1 mb-1 !text-neutral-800 badge badge-sm bg-transparent transition-all duration-300" v-for="(category, index) in obj.categories" :key="index">{{ category.name }}</span>
                        </td> -->
                        <td class="text-end">
                            <button
                                @click="openModal('acl', obj.id)"
                                data-modal-toggle="#user_acl_modal"
                                class="secondary-btn mb-1 block w-full focus:!border-tec-active"
                            >
                                Module Access
                            </button>
                            <button
                                @click="openModal('update_password', obj.id)"
                                data-modal-toggle="#user_update_password_form_modal"
                                class="secondary-btn mb-1 block w-full focus:!border-tec-active"
                            >
                                Change Password
                            </button>
                        </td>
                    </tr>
                    <tr class="table-no-data-available" v-if="data.length === 0">
                        <td class="text-center text-red-400">No data available</td>
                    </tr>
                </tbody>
            </table>
            <div v-if="loading" class="absolute inset-0 z-50 flex items-center justify-center bg-gray-300 bg-opacity-100 pointer-events-none">
                <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-gray-500 bg-white border border-gray-200 rounded-md shadow-default">
                    <svg class="w-5 h-5 -ml-1 text-gray-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
    <user-create-form-modal
        :obj_id="obj_id"
        v-if="is_create_user_form_modal"
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
            is_create_user_form_modal: false,
            is_update_password_form_modal: false,
            modal_type: null
        }
    },
    methods:{
        getData(isSync = false){
            this.loading = true
            this.data = []
            axios.post('/api/users/get-data', {
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
            // Add User modal logic placeholder
            if (modalType === 'add_user') {
                this.is_create_user_form_modal = true;
            }
        },
        closeModal(){
            this.is_acl_modal = false;
            this.is_create_user_form_modal = false;
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
