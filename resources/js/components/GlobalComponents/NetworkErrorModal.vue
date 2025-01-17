<template>
    <div
        v-if="showNetworkErrorModal"
        class="fixed inset-0 flex items-start justify-center bg-black bg-opacity-70 z-[1050] pointer-events-none"
        @click="preventInteraction">
        <div
            class="bg-white rounded-lg shadow-lg w-[1000px] mx-auto mt-10 pointer-events-auto"
            @click.stop> <!-- Prevent clicks from propagating outside the modal -->
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-red-600 flex items-center">
                    <i class="bi bi-wifi-off mr-2 text-xl"></i>Network Error
                </h3>
            </div>
            <!-- Modal Body -->
            <div class="p-4">
                <div class="flex flex-col gap-4">
                    <div class="w-full">
                        <div class="alert alert-danger bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                            <p class="font-bold">
                                Follow the steps below to troubleshoot:
                            </p>
                            <ol class="list-decimal list-inside text-sm">
                                <li>
                                    Click <a class="text-blue-500 underline" target="_blank" :href="sageUrl">here</a> to verify if you have access.
                                </li>
                                <li>
                                    If the link above shows "Connection not secure or connection is not private," click show
                                    details and then proceed to the site.
                                </li>
                                <li>Reload the reports page after allowing access in the previous step.</li>
                                <li>
                                    If the issue persists, try to turn your VPN or Wi-Fi on/off and refresh the reports
                                    page.
                                </li>
                                <li>
                                    If the issue persists, try to open the reports page in incognito mode or private window
                                    (Shift+Command+N).
                                </li>
                                <li>If the issue persists, contact IT.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
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
            showNetworkErrorModal: false,
            errorType: '',
            sageUrl: "https://10.0.1.17/CrescoSage/api/V1/FOBank/2/Transactions",
        };
    },
    methods: {
        handleNetworkError(event) {
            // Extract error type from the event details
            const {type} = event.detail || {};
            this.errorType = type || 'unknown'; // Default to 'unknown' if no type provided
            this.showNetworkErrorModal = true; // Show modal when event is dispatched
        },
        handleRefresh() {
            window.location.reload(); // Reload the page
        },
        preventInteraction(event) {
            event.preventDefault(); // Prevent any default interactions
            event.stopPropagation(); // Stop events from propagating further
        },
        disableBackgroundInteractions() {
            document.body.style.pointerEvents = 'none'; // Block all pointer events on the body
        },
        enableBackgroundInteractions() {
            document.body.style.pointerEvents = ''; // Re-enable pointer events on the body
        },
    },
    created() {
        // Listen for the network error event
        window.addEventListener('network-error', this.handleNetworkError);
    },
    mounted() {
        this.$watch(
            () => this.showNetworkErrorModal,
            (newValue) => {
                if (newValue) {
                    this.disableBackgroundInteractions(); // Disable background interactions when modal is shown
                } else {
                    this.enableBackgroundInteractions(); // Enable background interactions when modal is hidden
                }
            }
        );
    },
    beforeUnmount() {
        // Clean up the event listener
        window.removeEventListener('network-error', this.handleNetworkError);
        this.enableBackgroundInteractions(); // Ensure background interactions are restored
    },
};
</script>
