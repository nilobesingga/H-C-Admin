<template>
    <div class="container-fluid px-3">
        <!-- Page Header -->
        <div class="py-7">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-black font-bold text-3xl tracking-tight">
                    {{ page_data.title }}
                </div>

                <div class="flex items-center gap-4 flex-wrap w-full justify-end">
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
<!--                    <div class="flex">-->
<!--                        <button-->
<!--                            class="main-btn !bg-white !border !py-2 !px-5 !min-w-[120px] !text-sm focus:!border-tec-active"-->
<!--                            :disabled="loading"-->
<!--                            @click="openModal('add')"-->
<!--                            data-modal-toggle="#module_form_modal"-->
<!--                        >-->
<!--                            Add Module-->
<!--                        </button>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="relative flex-grow overflow-auto table-container shadow-md border border-tec">
            <!-- Table -->
            <table class="w-full table table-main table-border align-middle text-xs table-fixed">
                <thead>
                    <tr class="text-left tracking-tight">
                        <th class="sticky top-0 w-10">#</th>
                        <th class="sticky top-0 w-10">Order No</th>
                        <th class="sticky top-0 w-[150px]">Name</th>
                        <th class="sticky top-0 w-[150px]">Parent</th>
                        <th class="sticky top-0 w-[100px] text-left">Slug</th>
                        <th class="sticky top-0 w-[250px]">Route</th>
                        <th class="sticky top-0 w-[100px]">Icon</th>
<!--                        <th class="sticky top-0 w-10">Action</th>-->
                    </tr>
                </thead>
                <draggable
                    v-model="data"
                    tag="tbody"
                    item-key="id"
                    @change="updateOrder"
                >
                    <template #item="{ element }">
                        <tr class="cursor-move group transition-all duration-300 text-neutral-800">
                            <td scope="row">{{ element.id }}</td>
                            <td>{{ element.order }}</td>
                            <td>{{ element.name }}</td>
                            <td>{{ element.parent ? element.parent.name : null }}</td>
                            <td>{{ element.slug }}</td>
                            <td>{{ element.route }}</td>
                            <td>{{ element.icon }}</td>
<!--                            <td class="text-end">-->
<!--                                <button-->
<!--                                    @click="openModal('edit', obj.id)"-->
<!--                                    data-modal-toggle="#user_form_modal"-->
<!--                                    class="secondary-btn mb-1 block w-full focus:!border-tec-active"-->
<!--                                >-->
<!--                                    Edit-->
<!--                                </button>-->
<!--                            </td>-->
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
export default {
    name: "modules",
    props: ['page_data'],
    components: {
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
