<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="module_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[1000px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize">{{ modal_type }} Module</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="name" class="form-label required">Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="formData.name"
                                class="form-control"
                                placeholder="Name"
                                required
                            />
                        </div>
                        <div class="form-group">
                            <label for="parent" class="form-label required">Parent</label>
                            <input
                                type="text"
                                id="parent"
                                v-model="formData.parent_id"
                                class="form-control"
                                placeholder="Select Parent"
                                required
                            />
                        </div>
                        <div class="form-group">
                            <label for="rotue" class="form-label">Route</label>
                            <input
                                type="text"
                                id="parent"
                                v-model="formData.rotue"
                                class="form-control"
                                placeholder="Select Route"
                            />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-end">
                <div class="flex gap-4">
                    <button class="btn btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Cancel
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="save"
                        :disabled="crud_loading"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "module-form-modal",
    props: ['obj_id', 'modal_type'],
    data(){
        return {
            obj: null,
            form: {
                name: null,
                parent_id: null,
                route: null,
            },
            form_data: {
                modules: [],
            },
            crud_loading: false,
            loading: false
        }
    },
}
</script>
<style scoped>

</style>
