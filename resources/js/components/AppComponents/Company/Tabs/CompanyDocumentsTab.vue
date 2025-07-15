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
        <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Dynamically render document groups -->
            <div v-for="(group, typeKey) in groupedDocuments" :key="typeKey" class="overflow-hidden rounded-md bg-[#EAECF3]">
                <div class="p-3 text-center bg-gray-400">
                    <h3 class="text-sm font-semibold text-gray-900">{{ getDocumentTypeLabel(typeKey) }}</h3>
                </div>
                <div class="p-4 overflow-hidden">
                    <div v-if="group.length > 0">
                        <div v-for="(doc, i ) in group" :key="doc.id" class="flex items-center justify-between gap-2 py-3">
                            <span class="text-sm text-gray-800 truncate" :title="doc.filename">{{ i + 1 }}. Document</span>
                            <a :href="doc.url" class="flex items-center flex-shrink-0 text-sm text-blue-500 underline whitespace-nowrap" target="_blank" download>
                                Download <i class="ml-1 fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                    <div v-else class="flex items-center justify-between py-3">
                        <span class="text-gray-500">No files uploaded</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CompanyDocumentsTab',
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
                {   key : "cert_incorporation",  label :  "Certificate of Incorporation" },
                {   key : "cert_incumbency",     label :  "Certificate of Incumbency" },
                {   key : "yearly_license",      label :  "Yearly License" },
                {   key : "memorandum",          label :  "Memorandum of Association" },
                {   key : "ubo_registration",    label :  "UBO Portal Registration" },
                // {   key : "tax_document",        label :  "Tax Document" },
                // {   key : "vat_document",        label :  "VAT Document" }
            ]
        };
    },
    computed: {
        groupedDocuments() {
            return this.groupDocumentsByType(this.documents);
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
        }
    }
}
</script>

<style scoped>
/* Improve the display of long filenames */
.truncate {
  max-width: 500px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Loading animation is handled by Tailwind's animate-spin class */

@media (max-width: 768px) {
  .truncate {
    max-width: 150px;
  }
}

@media (max-width: 640px) {
  .truncate {
    max-width: 120px;
  }
}
</style>
