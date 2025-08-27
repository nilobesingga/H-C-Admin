<!-- /Applications/MAMP/htdocs/hensley-admin/resources/js/components/GlobalComponents/RequestNotification.vue -->
<template>
<div class="notification-container">
    <!-- Debug indicator -->
    <div class="toasts-container" :data-toast-count="notifications.length">
        <TransitionGroup name="toast">
            <div v-for="toast in notifications" :key="toast.id" class="toast" :class="[toast.type, { hiding: toast.hiding }]" :data-toast-id="toast.id">
                <i :class="['fas', getToastIcon(toast.type)]"></i>
                <div class="toast-content">
                    <div class="toast-message">{{ toast.message }}</div>
                    <div class="toast-timestamp" v-if="toast.timestamp">
                        {{ new Date(toast.timestamp).toLocaleTimeString() }}
                    </div>
                    <div class="notification-actions" v-if="toast.transType">
                        <button class="btn-allow" @click="handleAction(toast.id, 'approved')">
                            <i class="fas fa-thumbs-up"></i> Approve
                        </button>
                        <button class="btn-reject" @click="handleAction(toast.id, 'declined')">
                            <i class="fas fa-times"></i> Decline
                        </button>
                    </div>
                </div>
                <button class="rounded-full toast-close hover:bg-gray-700" @click="closeToast(toast.id)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </TransitionGroup>
    </div>
</div>
</template>

<script>
export default {
    name: 'RequestNotification',
    data() {
        return {
            notifications: [],
            toasts: [],
            toastId: 0,
            transType: false
        };
    },
    mounted() {
        this.initializeEcho();

        this.showNotificationHandler = (event) => this.handleNewNotification(event.detail);
        this.showToastHandler = (event) => this.successToast(event.detail.message);

        window.addEventListener('show-notification', this.showNotificationHandler);
        window.addEventListener('show-toast', this.showToastHandler);
    },

    beforeUnmount() {
        // Clean up event listeners
        window.removeEventListener('show-notification', this.showNotificationHandler);
        window.removeEventListener('show-toast', this.showToastHandler);

        // Clean up Echo listeners
        if (window.Echo) {
            window.Echo.leave('requests');
            window.Echo.leave('company-setup');
            window.Echo.leave('client-request');
        }
    },
    methods: {
        initializeEcho() {
            const initAttempt = (retryCount = 0) => {
                if (retryCount > 10) return;

                if (!window.Echo) {
                    setTimeout(() => initAttempt(retryCount + 1), 1000);
                    return;
                }

                this.setupEchoListeners();
            };

            initAttempt();
        },

        setupEchoListeners() {
            const connection = window.Echo.connector.pusher.connection;

            connection.bind('state_change', (states) => {
                if (states.current === 'connected') {
                    this.subscribeToChannel();
                }
            });

            connection.bind('error', (err) => {
                console.error('[RequestNotification] Connection error:', err);
            });

            this.subscribeToChannel();
        },

        subscribeToChannel() {
            if (!window.Echo) return;

            // Subscribe to requests channel
            const requestsChannel = window.Echo.channel('requests');
            requestsChannel
                .listen('.request.created', (event) => {
                    this.transType = true;
                    this.processEvent(event, 'change_request');
                })
                .error((error) => {
                    console.error('[RequestNotification] Requests channel error:', error);
                });

            // Subscribe to company setup channel
            const companySetupChannel = window.Echo.channel('company-setup');
            companySetupChannel
                .listen('.company-setup.created', (event) => {
                    this.transType = false;
                    this.processEvent(event, 'company_setup');
                })
                .error((error) => {
                    console.error('[RequestNotification] Company setup channel error:', error);
                });

            const clientChangeChannel = window.Echo.channel('client-request');
            clientChangeChannel
                .listen('.client.request.created', (event) => {
                    this.transType = false;
                    this.processEvent(event, 'client_request');
                })
                .error((error) => {
                    console.error('[RequestNotification] Client request channel error:', error);
                });
        },

        getToastIcon(type) {
            return {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            } [type] || 'fa-check-circle';
        },

        getNotificationIcon(notification) {
            // Choose icon based on request category or type
            const category = (notification.category || '').toLowerCase();
            if (category.includes('change')) return 'fa-exchange-alt';
            if (category.includes('urgent')) return 'fa-exclamation-circle';
            return 'fa-file-alt';
        },

        viewRequest(notification) {
            // Emit event for parent component to handle navigation
            this.$emit('view-request', notification);
            // You can also handle navigation directly if needed
            // window.location.href = `/requests/${notification.id}`;
        },

        // Removed duplicate event listener setup

        processEvent(event, type) {
            console.log(`[RequestNotification] Processing event of type: ${type}`, event);
            // For company setup events, the data is directly in the event.data
            //   const eventData = type === 'company_setup' ? event : (event.request || event);
            const existingNotification = this.notifications.find(n => n.id === event.id);

            if (!existingNotification) {
                this.handleIncomingRequestEvent(event, type);
            }
        },

        handleIncomingRequestEvent(event, type) {
            try {
                const eventData = event;
                if (!eventData || !eventData.id) {
                    throw new Error('Invalid event data received');
                }

                let notificationData = {
                    id: eventData.id,
                    type: 'success',
                    created_at: eventData.created_at || new Date().toISOString(),
                    metadata: {
                        requestId: eventData.id,
                        type: type
                    }
                };

                switch (type) {
                    case 'change_request':
                        notificationData = {
                            ...notificationData,
                            title: `New ${eventData.category}`,
                            message: `${eventData.request_no} - ${eventData.description || 'No details provided'} - Requested by ${eventData.created_by || 'Unknown'}`,
                            category: eventData.category,
                            details: eventData.description || 'No details provided',
                            created_by: eventData.created_by,
                            type: 'change_request',
                            metadata: {
                                ...notificationData.metadata,
                                company_id: eventData.company_id,
                                contact_id: eventData.contact_id
                            }
                        };
                        break;

                    case 'company_setup':
                        notificationData = {
                            ...notificationData,
                            title: 'New Company Setup Request',
                            message: `${eventData.description || 'No description provided'}`,
                            category: 'Company Setup',
                            details: `Language: ${eventData.language}, Contact Method: ${eventData.contact_method}`,
                            type: 'company_setup',
                            metadata: {
                                ...notificationData.metadata,
                                contact_id: eventData.contact_id,
                                timeframe: eventData.timeframe,
                                language: eventData.language,
                                contact_method: eventData.contact_method
                            }
                        };
                        break;

                    case 'client-request':
                        notificationData = {
                            ...notificationData,
                            title: 'New Client Request',
                            message: `Change From : ${eventData.current_value} -> To: ${eventData.proposed_value}`,
                            type: 'client-request',
                            metadata: {
                                ...notificationData.metadata,
                                contact_id: eventData.contact_id
                            }
                        };
                        break;
                }
                this.handleNewNotification(notificationData);
            } catch (error) {
                console.error('[RequestNotification] Error processing event:', error);
            }
        },

        formatTime(date) {
            return new Date(date).toLocaleString();
        },

        handleNewNotification(notification) {
            this.notifications.unshift({
                ...notification,
                show: true,
                message: notification.message || `New ${notification.category} request received`,
                transType: notification.type === 'company_setup' ? false : true,
                type: 'success',
                timestamp: new Date().toISOString()
            });

            // Show toast
            this.successToast(`New ${notification.category} request received`);
        },

        successToast(message) {
            const id = ++this.toastId;

            this.toasts.push({
                id,
                message,
                type: 'success',
                timestamp: new Date().toISOString()
            });
        },

        closeToast(id) {
            const toast = this.notifications.find(t => t.id === id);
            if (toast) {
                toast.hiding = true;
                setTimeout(() => {
                    this.notifications = this.notifications.filter(t => t.id !== id);
                }, 300);
            }
        },

        async handleAction(id, action) {
            await axios.post(`/api/requests/${id}`, {
                    status: action
                })
                .then(response => {
                    const request = this.notifications.find(n => n.id === id);
                    if (!request) {
                        console.error(`[RequestNotification] Request with ID ${id} not found`);
                        return;
                    }
                    // Update the request status in the notification
                    request.transType = false;
                    this.successToast('Request status updated successfully');
                })
                .catch(error => {
                    this.errorToast('Failed to update request status');
                    // Revert the status change
                    request.status = action;
                })
                .finally(() => {
                    this.isLoading = false;
                });
            // Emit the action event with the toast id and action type
            this.$emit('toast-action', {
                id,
                action
            });
            // Close the toast after action
            this.closeToast(id);

            //   setTimeout(() => {
            //     const toast = this.toasts.find(t => t.id === id);
            //     if (toast) {
            //       toast.hiding = true;
            //       setTimeout(() => {
            //         this.toasts = this.toasts.filter(t => t.id !== id);
            //       }, 300);
            //     }
            //   }, 5000);
        }
    }
};
</script>

<style scoped>
/* Debug indicator */
.debug-indicator {
    position: fixed;
    top: 0;
    right: 0;
    background: #2196F3;
    color: white;
    padding: 4px 8px;
    font-size: 12px;
    z-index: 99999;
    border-bottom-left-radius: 4px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.debug-status {
    display: flex;
    gap: 8px;
    margin-top: 4px;
    font-size: 10px;
}

.debug-status span {
    padding: 2px 6px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.2);
}

.debug-status span.active {
    background: #4CAF50;
}

/* Styling */
.notification-container {
    position: relative;
    width: 100%;
}

.toasts-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999999;
    /* Increased z-index */
    pointer-events: none;
    /* background: rgba(255, 0, 0, 0.1); Debug background */
    padding: 10px;
    min-width: 200px;
    min-height: 50px;
    /* border: 1px dashed rgba(255, 0, 0, 0.3); Debug border */
}

.toast {
    padding: 16px;
    margin-bottom: 12px;
    border-radius: 8px;
    color: #000;
    display: flex;
    align-items: flex-start;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    min-width: 300px;
    max-width: 400px;
    pointer-events: auto;
    transition: all 0.3s ease;
    opacity: 1;
    transform: translateX(0);
}

.toast-enter-active {
    animation: slideIn 0.3s ease-out;
}

.toast-leave-active {
    animation: fadeOut 0.3s ease-out;
}

.toast-content {
    flex: 1;
    margin-left: 12px;
}

.toast-message {
    font-weight: 500;
    margin-bottom: 4px;
}

.toast-timestamp {
    font-size: 0.75rem;
    opacity: 0.9;
}

.toast.success {
    background-color: #D1FAE5;
    border-left: 4px solid #059669;
}

.toast i {
    margin-right: 8px;
}

.toast-close {
    background: none;
    border: none;
    color: red;
    opacity: 0.7;
    cursor: pointer;
    padding: 4px;
    text-align: center;
    margin: -8px -8px -8px 8px;
    border-radius: 20px;
    transition: opacity 0.2s ease;
}

.toast-close:hover {
    opacity: 1;
}

.notification-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    padding-top: 8px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-allow,
.btn-reject {
    padding: 6px 12px;
    border-radius: 4px;
    border: none;
    font-size: 0.875rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    color: white;
}

.btn-allow {
    background-color: #059669;
}

.btn-allow:hover {
    background-color: #047857;
}

.btn-reject {
    background-color: red;
}

.btn-reject:hover {
    background-color: #dc2626;
}

/* .notifications {
  max-height: 400px;
  overflow-y: auto;
} */

.notification {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.3s ease-out;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.notification-title {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.notification-time {
    font-size: 0.875rem;
    color: #6b7280;
}

.notification-body {
    color: #4b5563;
}

.notification-details {
    margin-top: 8px;
    font-size: 0.875rem;
    color: #6b7280;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 4px;
}

.detail-item i {
    color: #9ca3af;
    width: 16px;
}

.notification-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 12px;
    padding-top: 8px;
    border-top: 1px solid #e5e7eb;
}

.action-button {
    padding: 6px 12px;
    border-radius: 4px;
    border: none;
    font-size: 0.875rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.view-button {
    background-color: #3b82f6;
    color: white;
}

.view-button:hover {
    background-color: #2563eb;
}

.close-button {
    background-color: #e5e7eb;
    color: #4b5563;
}

.close-button:hover {
    background-color: #d1d5db;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.hiding {
    animation: fadeOut 0.3s ease-out forwards;
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }

    to {
        opacity: 0;
        transform: translateX(100%);
    }
}
</style>
