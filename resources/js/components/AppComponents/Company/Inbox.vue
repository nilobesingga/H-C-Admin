<template>
    <div class="w-full min-h-screen p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900">{{ page_title }}</h1>

        </div>
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <!-- Tabs as Grouped Button -->
                <div class="mb-4 md:mb-0">
                    <h2 class="text-2xl font-extrabold text-gray-900">Notification</h2>
                    <div class="text-sm text-gray-600">
                        You've got {{ unreadCount }} {{ unreadCount === 1 ? 'notification' : 'notifications' }} to solve
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                    <!-- Search -->
                    <div class="relative flex-shrink-0 w-full mb-4 md:w-64 md:mb-0">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search..."
                            class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Notifications -->
        <div v-if="filteredTodayNotifications.length > 0" class="mb-10">
            <h2 class="mb-4 text-lg font-medium text-gray-700">Today</h2>

            <div class="space-y-4">
                <div
                    v-for="(notification, index) in filteredTodayNotifications"
                    :key="`today-${index}`"
                    class="flex items-start p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 relative group"
                >
                    <div class="flex-shrink-0 p-3 mr-4 text-orange-500 bg-orange-100 rounded-md">
                        <component :is="getIconComponent(notification.type)" class="w-6 h-6"/>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-base font-semibold text-gray-900">{{ notification.title }}</h3>
                        <p class="text-sm text-gray-600">{{ notification.description }}</p>
                    </div>
                    <div class="flex flex-col items-end ml-4">
                        <div class="flex-shrink-0 text-sm text-gray-500">
                            {{ notification.date }}
                        </div>
                        <button
                            class="px-3 py-1 mt-2 text-xs font-medium text-blue-600 bg-white border border-blue-200 rounded-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-50"
                            @click="viewNotification(notification)"
                        >
                            View
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yesterday's Notifications -->
        <div v-if="filteredYesterdayNotifications.length > 0" class="mb-10">
            <h2 class="mb-4 text-lg font-medium text-gray-700">Yesterday</h2>

            <div class="space-y-4">
                <div
                    v-for="(notification, index) in filteredYesterdayNotifications"
                    :key="`yesterday-${index}`"
                    class="flex items-start p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 relative group"
                >
                    <div class="flex-shrink-0 p-3 mr-4 text-blue-500 bg-blue-100 rounded-md">
                        <component :is="getIconComponent(notification.type)" class="w-6 h-6"/>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-base font-semibold text-gray-900">{{ notification.title }}</h3>
                        <p class="text-sm text-gray-600">{{ notification.description }}</p>
                    </div>
                    <div class="flex flex-col items-end ml-4">
                        <div class="flex-shrink-0 text-sm text-gray-500">
                            {{ notification.date }}
                        </div>
                        <button
                            class="px-3 py-1 mt-2 text-xs font-medium text-blue-600 bg-white border border-blue-200 rounded-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-50"
                            @click="viewNotification(notification)"
                        >
                            View
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous 7 Days Notifications -->
        <div v-if="filteredPreviousNotifications.length > 0" class="mb-10">
            <h2 class="mb-4 text-lg font-medium text-gray-700">Previous 7 Days</h2>

            <div class="space-y-4">
                <div
                    v-for="(notification, index) in filteredPreviousNotifications"
                    :key="`previous-${index}`"
                    class="flex items-start p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 relative group"
                >
                    <div class="flex-shrink-0 p-3 mr-4 text-green-500 bg-green-100 rounded-md">
                        <component :is="getIconComponent(notification.type)" class="w-6 h-6"/>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-base font-semibold text-gray-900">{{ notification.title }}</h3>
                        <p class="text-sm text-gray-600">{{ notification.description }}</p>
                    </div>
                    <div class="flex flex-col items-end ml-4">
                        <div class="flex-shrink-0 text-sm text-gray-500">
                            {{ notification.date }}
                        </div>
                        <button
                            class="px-3 py-1 mt-2 text-xs font-medium text-blue-600 bg-white border border-blue-200 rounded-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-50"
                            @click="viewNotification(notification)"
                        >
                            View
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!hasNotifications" class="flex flex-col items-center justify-center py-12 text-center">
            <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <h3 class="mb-1 text-lg font-medium text-gray-900">No Notifications</h3>
            <p class="max-w-md mb-4 text-gray-500">You're all caught up! There are no new notifications to display.</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Inbox',
    props: ['page_data', 'page_title'],
    data() {
        return {
            searchQuery: '',
            notifications: [
                // Today's notifications
                {
                    title: 'Document request approved',
                    description: 'Your request for Certificate of Incorporation has been approved and is ready for download.',
                    date: '13 Jun 2025',
                    type: 'document',
                    timeframe: 'today'
                },
                {
                    title: 'Payment received',
                    description: 'Payment of $1,250.00 has been received for invoice #INV-2025-0054.',
                    date: '13 Jun 2025',
                    type: 'payment',
                    timeframe: 'today'
                },
                {
                    title: 'Task completed',
                    description: 'Annual return filing task has been completed by your account manager.',
                    date: '13 Jun 2025',
                    type: 'task',
                    timeframe: 'today'
                },

                // Yesterday's notifications
                {
                    title: 'Change request needs attention',
                    description: 'Your request to change company address requires additional information.',
                    date: '12 Jun 2025',
                    type: 'attention',
                    timeframe: 'yesterday'
                },
                {
                    title: 'Invoice generated',
                    description: 'A new invoice #INV-2025-0054 has been generated for your recent services.',
                    date: '12 Jun 2025',
                    type: 'invoice',
                    timeframe: 'yesterday'
                },

                // Previous 7 days
                {
                    title: 'Document submitted',
                    description: 'Your Good Standing Certificate has been submitted to the authorities.',
                    date: '10 Jun 2025',
                    type: 'document',
                    timeframe: 'previous'
                },
                {
                    title: 'Reminder: License renewal',
                    description: 'Your company license is due for renewal in 30 days.',
                    date: '9 Jun 2025',
                    type: 'reminder',
                    timeframe: 'previous'
                },
                {
                    title: 'Company profile updated',
                    description: 'Your company profile information has been updated successfully.',
                    date: '7 Jun 2025',
                    type: 'update',
                    timeframe: 'previous'
                }
            ]
        };
    },
    computed: {
        unreadCount() {
            // For demo purposes, showing count of all notifications
            return this.notifications.length;
        },

        filteredTodayNotifications() {
            return this.filterNotifications('today');
        },

        filteredYesterdayNotifications() {
            return this.filterNotifications('yesterday');
        },

        filteredPreviousNotifications() {
            return this.filterNotifications('previous');
        },

        hasNotifications() {
            return this.filteredTodayNotifications.length > 0 ||
                   this.filteredYesterdayNotifications.length > 0 ||
                   this.filteredPreviousNotifications.length > 0;
        }
    },
    methods: {
        filterNotifications(timeframe) {
            if (!this.searchQuery) {
                return this.notifications.filter(notification => notification.timeframe === timeframe);
            }

            const query = this.searchQuery.toLowerCase();
            return this.notifications.filter(notification =>
                notification.timeframe === timeframe &&
                (notification.title.toLowerCase().includes(query) ||
                 notification.description.toLowerCase().includes(query))
            );
        },

        getIconComponent(type) {
            // Return appropriate icon component based on notification type
            switch (type) {
                case 'document':
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>`
                    };
                case 'payment':
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line><circle cx="12" cy="16" r="2"></circle></svg>`
                    };
                case 'task':
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>`
                    };
                case 'attention':
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`
                    };
                case 'invoice':
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>`
                    };
                case 'reminder':
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>`
                    };
                case 'update':
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>`
                    };
                default:
                    return {
                        template: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>`
                    };
            }
        },

        markAllAsRead() {
            // In a real application, this would mark all notifications as read
            console.log('Marking all notifications as read');
            // Would typically call an API endpoint here
        },

        viewNotification(notification) {
            console.log('Viewing notification:', notification);
            // In a real app, this might:
            // 1. Mark the notification as read
            // 2. Navigate to relevant page or open a modal with details
            // 3. Trigger any necessary API calls

            // For demo purposes, just show an alert
            alert(`Viewing details for: ${notification.title}`);
        }
    }
};
</script>

<style scoped>
/* Custom styling for notification items */
.hover\:bg-gray-50:hover {
    transition: all 0.2s ease;
}

/* Hover transitions for buttons */
.transition-opacity {
    transition: opacity 0.2s ease-in-out;
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

/* Custom icon coloring */
.text-orange-500 {
    color: #f97316;
}
.bg-orange-100 {
    background-color: #ffedd5;
}
.text-blue-500 {
    color: #3b82f6;
}
.bg-blue-100 {
    background-color: #dbeafe;
}
.text-green-500 {
    color: #22c55e;
}
.bg-green-100 {
    background-color: #dcfce7;
}
</style>
