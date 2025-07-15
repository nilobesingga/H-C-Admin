<template>
    <div class="mt-2">
         <!-- Loading indicator similar to BankAccountsTab -->
        <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </div>
        </div>

        <!-- Actual documents -->
        <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Accounting Documents -->
            <div>
                <div class="p-3 text-sm font-semibold text-center bg-gray-400 rounded-t-lg">
                    Accounting Documents
                </div>
                <div class="space-y-0 bg-white">
                    <div v-for="(year, index) in accountingYears" :key="year"
                         :class="['flex items-center justify-between p-4 mx-3', index % 2 === 1 ? 'bg-gray-50' : '',
                                 index !== accountingYears.length - 1 ? 'border-b' : '']">
                        <span class="text-sm font-semibold text-gray-900">{{ year }} Accounting Documents</span>
                        <a v-if="isAccountingFileAvailable(year)" :href="accountingDocuments[year][0].url"
                           class="flex items-center text-sm text-blue-600 underline hover:text-blue-800" target="_blank" download>
                            Download <i class="ml-1 fas fa-download"></i>
                        </a>
                        <span v-else class="text-sm text-gray-500">Not available</span>
                    </div>
                </div>
            </div>

            <!-- Right Side Documents -->
            <div class="space-y-6">
                <!-- Tax Documents -->
                <div>
                    <div class="p-3 text-sm font-semibold text-center bg-gray-400 rounded-t-lg">
                        Tax Documents
                    </div>
                    <div class="bg-white">
                        <div v-if="filteredDocuments.tax_document && filteredDocuments.tax_document.length > 0">
                            <div v-for="doc in filteredDocuments.tax_document" :key="doc.id"
                                 class="flex items-center justify-between p-4">
                                <span class="text-gray-900">{{ new Date().getFullYear() }} Tax Document</span>
                                <a :href="doc.url" class="flex items-center text-sm text-blue-600 underline hover:text-blue-800" target="_blank" download>
                                    Download <i class="ml-1 fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                        <div v-else class="flex items-center justify-between p-4">
                            <span class="text-gray-500">No tax documents available</span>
                        </div>
                    </div>
                </div>

                <!-- VAT Documents -->
                <div>
                    <div class="p-3 text-sm font-semibold text-center bg-gray-400 rounded-t-lg">
                        VAT Documents
                    </div>
                    <div class="bg-white">
                        <div v-if="filteredDocuments.vat_document && filteredDocuments.vat_document.length > 0">
                            <div v-for="doc in filteredDocuments.vat_document" :key="doc.id"
                                 class="flex items-center justify-between p-4">
                                <span class="text-gray-900">{{ new Date().getFullYear() }} VAT Document</span>
                                <a :href="doc.url" class="flex items-center text-sm text-blue-600 underline hover:text-blue-800" target="_blank" download>
                                    Download <i class="ml-1 fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                        <div v-else class="flex items-center justify-between p-4">
                            <span class="text-gray-500">No VAT documents available</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AccountingTab',
    props: {
        documents: {
            type: Array,
            required: true,
            default: () => []
        }
    },
    data() {
        return {
            loading: true,
            documentTypes: [
                {   key : "accounting", label :  "Accounting Documents" },
                {   key : "tax_document",   label :  "Tax Document" },
                {   key : "vat_document",   label :  "VAT Document" }
            ]
        }
    },
    computed: {
        groupedDocuments() {
            return this.groupDocumentsByType(this.documents);
        },
        accountingDocuments() {
            // Get accounting documents and organize them by year
            const accountingDocs = {};
            const currentYear = new Date().getFullYear();

            // Initialize years (from current year down to 7 years ago)
            for (let i = 0; i < 7; i++) {
                const year = currentYear - i;
                accountingDocs[year] = [];
            }

            // Find accounting documents and organize by year
            if (this.groupedDocuments.accounting) {
                this.groupedDocuments.accounting.forEach(doc => {
                    // Extract year from filename or metadata (assuming format contains year)
                    const yearMatch = doc.filename.match(/\b(20\d{2})\b/) ||
                                     (doc.year ? [null, doc.year.toString()] : null);

                    if (yearMatch && accountingDocs[yearMatch[1]]) {
                        accountingDocs[yearMatch[1]].push(doc);
                    }
                });
            }

            return accountingDocs;
        },
        filteredDocuments() {
            const filtered = {};
            Object.keys(this.groupedDocuments).forEach(key => {
                if (key !== 'accounting') {
                    filtered[key] = this.groupedDocuments[key];
                }
            });
            return filtered;
        },
        accountingYears() {
            const currentYear = new Date().getFullYear();
            const years = [];

            // Generate years from current year down to 7 years ago
            for (let i = 0; i < 7; i++) {
                years.push(currentYear - i);
            }

            return years;
        }
    },
    mounted() {
        // Simulate loading delay (remove this in production and use actual loading state)
        setTimeout(() => {
            this.loading = false;
        }, 1500);
    },
    methods: {
        groupDocumentsByType(documents) {
            const groups = {};

            // Initialize all document types with empty arrays
            this.documentTypes.forEach(type => {
                groups[type.key] = [];
            });

            // Group documents by their type
            if (documents && documents.length) {
                documents.forEach(doc => {
                    if (doc.type && groups.hasOwnProperty(doc.type)) {
                        groups[doc.type].push(doc);
                    }
                });
            }

            return groups;
        },
        getDocumentTypeLabel(typeKey) {
            const typeObj = this.documentTypes.find(type => type.key === typeKey);
            return typeObj ? typeObj.label : this.formatDocumentType(typeKey);
        },
        formatDocumentType(type) {
            if (!type) return '';

            // Convert snake_case to Title Case with spaces
            return type
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        },
        isAccountingFileAvailable(year) {
            // Check if there are documents available for this year
            return this.accountingDocuments[year] && this.accountingDocuments[year].length > 0;
        }
    }
}
</script>

<style scoped>
.bg-gray-100 {
    background-color: #f3f4f6;
}
</style>
