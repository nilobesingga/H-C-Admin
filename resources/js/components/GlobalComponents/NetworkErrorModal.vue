<template>
    <div
        v-if="showNetworkErrorModal"
        class="fixed inset-0 flex items-start justify-center bg-black bg-opacity-70 z-[1050] pointer-events-none backdrop-blur-md"
        @click="preventInteraction">
        <div
            class="w-[700px] mx-auto mt-24 pointer-events-auto rounded-none ring-8 ring-red-500/30 border-2 border-black shadow-2xl bg-red-50"
            @click.stop> <!-- Prevent clicks from propagating outside the modal -->
            <!-- Modal Header -->
            <div class="px-8 py-5 bg-white ">
                <h3 class="modal-title capitalize text-red-900 text-xl font-bold tracking-tight">
                    Network Error
                </h3>
            </div>
            <!-- Modal Body -->
            <div class="px-8 py-5">
                <div class="flex flex-col gap-4 w-full">
                    <div class="text-red-700">
                        <p class="font-bold text-red-900 mb-4">
                            Follow the steps below to troubleshoot:
                        </p>
                        <ol class="list-decimal list-outside text-sm pl-4 space-y-4 marker:text-red-900 marker:font-bold">
                            <li>
                                Click <a class="text-black font-bold underline" target="_blank" :href="sageUrl">here</a> to verify if you have access.
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
            <!-- Modal Footer -->
            <div class="flex justify-end p-8 pt-0">
                <button
                    @click="handleRefresh"
                    class="main-btn"
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
