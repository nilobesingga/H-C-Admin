<template>
    <div class="mt-4">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-600 uppercase">
                                Bank Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-600 uppercase">
                                Account No.
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-600 uppercase">
                                IBAN
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-600 uppercase">
                                Swift Code
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-600 uppercase">
                                Currency
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-600 uppercase">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Loading indicator inside table -->
                        <tr v-if="loading">
                            <td colspan="6" class="px-6 py-8">
                                <div class="flex items-center justify-center">
                                    <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                                        <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Loading...
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <template v-else-if="!loading && banks.length === 0">
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">
                                    No bank accounts found.
                                </td>
                            </tr>
                        </template>
                        <tr v-else v-for="(bank , i) in banks" :key="i" :class="i % 2 === 0 ? 'bg-gray-50' : ''" class="transition-colors duration-200 hover:bg-gray-200">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                {{ bank.name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                               {{ bank.account_number }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                {{ bank.iban }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                               {{ bank.swift }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                {{ bank.currency }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                <button @click="copyToClipboard(bank.display_text['TEXT'])" class="flex items-center text-blue-600 hover:text-blue-800">
                                    Copy <i class="ml-1 fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'BankAccountsTab',
    props: {
        banks: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        copyToClipboard(text) {
            navigator.clipboard.writeText(text);
             this.successToast('Bank account details copied to clipboard!');
        },
    },
    mounted() {
        this.loading = true;
        // Simulate loading data
        setTimeout(() => {
            this.loading = false;
        }, 1000);
    },
}
</script>
