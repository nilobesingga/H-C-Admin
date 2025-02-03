<template>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="pb-6">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-black font-black">
                    <div>{{ page_data.title }}</div>
                </div>
                <div class="flex items-center gap-4 flex-wrap w-full justify-end">
                    <div class="flex">
                        <div class="relative">
                            <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3"></i>
                            <input class="input input-sm ps-8 text-black bg-inherit min-w-[20rem]" placeholder="Search" type="text" v-model="filters.search">
                        </div>
                    </div>
                    <div class="flex">
                        <button
                            class="btn btn-sm btn-outline btn-primary"
                            :disabled="loading"
                            @click="openModal('add')"
                            data-modal-toggle="#module_form_modal"
                        >
                            <i class="ki-filled ki-plus-squared"></i>
                            <span>Add Module</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="relative flex-grow overflow-auto table-container">
            <!-- Table -->
            <table class="w-full table table-main table-border align-middle text-xs table-fixed">
                <thead>
                    <tr class="bg-black text-gray-900 font-medium text-center">
                        <th class="sticky top-0 w-10">#</th>
                        <th class="sticky top-0 w-10">Order No</th>
                        <th class="sticky top-0 w-[150px]">Name</th>
                        <th class="sticky top-0 w-[150px]">Parent</th>
                        <th class="sticky top-0 w-[50px] text-left">Slug</th>
                        <th class="sticky top-0 w-[200px]">Route</th>
                        <th class="sticky top-0 w-10"></th>
                    </tr>
                </thead>
                <draggable
                    v-model="data"
                    tag="tbody"
                    item-key="id"
                    @change="updateOrder"
                >
                    <template #item="{ element }">
                        <tr class="odd:bg-white even:bg-slate-100 hover:bg-gray-300 cursor-move">
                            <td scope="row">{{ element.id }}</td>
                            <td>{{ element.order }}</td>
                            <td>{{ element.name }}</td>
                            <td>{{ element.parent ? element.parent.name : null }}</td>
                            <td>{{ element.slug }}</td>
                            <td>{{ element.route }}</td>
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
                    </template>
                </draggable>
                <tr class="table-no-data-available" v-if="data.length === 0">
                    <td class="text-center text-red-400">No data available</td>
                </tr>
            </table>
        </div>
        <!-- footer-->
        <div class="flex items-center justify-between">
            <div class="text-xs">
                <span>Showing {{ data.length }} records</span>
            </div>
        </div>
    </div>
    <module-form-modal
        :modules="data"
        :obj_id="obj_id"
        :modal_type="modal_type"
        v-if="is_form_modal"
        @closeModal="closeModal"
    />
</template>

<script>
import draggable from 'vuedraggable'
import ModuleFormModal from "./ModuleFormModal.vue";
export default {
    name: "modules",
    props: ['page_data'],
    components: {
        ModuleFormModal,
        draggable,
    },
    data() {
        return {
            data: [],
            loading: false,
            filters: {
                search: null,
            },
            obj_id: null,
            is_form_modal: false,
            modal_type: null,
            crud_loading: false,
        }
    },
    methods: {
        getData(){
            this.loading = true
            this.data = []
            axios({
                url: `/admin/settings/modules/get-data`,
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
        updateOrder(){
            this.data.forEach((item, index) => item.order = index + 1 )

            const payload = this.data.map(item => ({
                id: item.id,
                order: item.order
            }));

            this.crud_loading = true
            axios({
                url: `/admin/settings/modules/update`,
                method: 'POST',
                data: payload
            }).then(response => {
                this.successToast(response.data.message);
            }).catch(error => {
                if (error.status === 500){
                    this.errorToast(error.response.data.message)
                }
            }).finally(() => {
                this.crud_loading = false;
            })
        }
    },
    mounted() {
        this.getData();
    }
}
</script>

<style scoped>

</style>
