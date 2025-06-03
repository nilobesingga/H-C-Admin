<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="qashio_view_logs_modal">
        <div class="modal-content top-[5%] lg:max-w-[1500px]" style="height: 85vh !important;">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">Qashio Logs</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body relative h-full overflow-auto">
                <!-- Loading Spinner -->
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center z-50">
                    <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-white">
                        <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
                <!-- Form Section -->
                <div v-else>
                    <div class="flex gap-5">
                        <div class="w-1/2">
                            <!-- Invoice Number -->
                            <div class="mb-4 w-full gap-2.5">
                                <label class="form-label flex items-center gap-1 text-sm mb-1 !text-black" for="lines">Lines</label>
                                <input
                                    class="input input-xs text-input"
                                    placeholder="Lines"
                                    id="lines"
                                    v-model="lines"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-md p-4 mb-4 border border-gray-200 text-sm text-black">
                        <ul class="list-disc" v-for="log in data">
                            <li class="font-mono text-xs">{{ log }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import { debounce } from 'lodash';

export default {
    name: "qashio-view-logs-modal",
    data(){
        return {
            data: [],
            lines: 1000,
            loading: false,
            error: null,
        }
    },
    methods: {
        getData() {
            this.loading = true
            this.error = null;
            this.data = [];
            axios({
                url: `/qashio/logs`,
                method: 'GET',
                params: {lines: this.lines}
            }).then(response => {
                console.log(response.data)
                this.data = response.data.data;
            }).catch(error => {
                this.error = error.response?.data?.message || 'Error fetching Qashio logs.';
            }).finally(() => {
                this.loading = false;
            })
        },
        debouncedSearch: debounce(function(){
            this.getData(false);
        }, 500),
    },
    watch: {
        lines: {
            handler: 'debouncedSearch',
            immediate: false
        }
    },
    mounted() {
        this.getData();
    }
}
</script>

<style scoped>

</style>
