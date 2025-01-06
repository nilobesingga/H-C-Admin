<template>
    <div
        v-if="showNetworkErrorModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 overflow-auto">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto mt-10">
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-red-600 flex items-center">
                    <i class="bi bi-wifi-off mr-2 text-xl"></i>Network Error
                </h3>
            </div>
            <div class="p-4">
                <p class="text-gray-600 text-sm">Please check your internet connection and refresh the page.</p>
            </div>
            <div class="flex justify-end p-4 border-t border-gray-200">
                <button
                    @click="handleRefresh"
                    class="btn btn-primary px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white transition duration-200"
                >
                    Refresh
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'network-error-modal',
    data() {
        return {
            showNetworkErrorModal: false, // Controls modal visibility
        };
    },
    created() {
        // Listen for the network error event
        window.addEventListener('network-error', this.showModal);
    },
    beforeUnmount() {
        // Clean up the event listener
        window.removeEventListener('network-error', this.showModal);
    },
    methods: {
        showModal() {
            this.showNetworkErrorModal = true; // Show modal when event is dispatched
        },
        handleRefresh() {
            window.location.reload(); // Reload the page
        },
    },
};
</script>
