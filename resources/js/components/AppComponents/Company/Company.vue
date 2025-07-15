<template>
<div class="z-0 w-full min-h-screen p-6">
    <!-- Loading Overlay -->
    <loading-overlay :is-visible="isLoading" message="Switching company..." />

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">
        <!-- <i class="mr-3 ki-duotone ki-arrow-right-left">
            <span class="path1"></span>
            <span class="path2"></span>
        </i> -->
        {{ company_data.name }}</h1>
        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span>Company Profile:</span>
            <a href="#" class="flex items-center gap-1 font-bold underline hover:text-blue-800">
                Download <i class="fas fa-download"></i>
            </a>
            <a href="#" class="flex items-center gap-1 font-bold underline hover:text-blue-800">
                Print <i class="fas fa-print"></i>
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="flex flex-col gap-8 p-8 mb-3 border border-black rounded-lg md:flex-row">
        <!-- Left: Logo and Info -->
        <div class="flex flex-col items-center md:items-start md:w-1/7">
            <template v-if="company_data.logo">
                <img :src="company_data.logo" alt="Company Icon" class="object-contain w-20 h-20 border-2 border-red-200 rounded-full">
            </template>
            <template v-else>
                <span class="flex items-center justify-center flex-shrink-0 w-20 h-20 text-3xl font-bold text-gray-500 border-2 border-red-200 rounded-full">{{ company_data.name ? company_data.name.substring(0,2).toUpperCase() : '--' }}</span>
            </template>
            <!-- <img :src="company_data.logo" :alt="company_data.name + ' Logo'" class="w-24 h-24 mb-6 border-4 border-white rounded-full shadow" /> -->
        </div>
        <!-- Middle: Details -->
        <div class="flex-1">
            <div class="grid w-full grid-cols-6 mb-4 text-sm text-gray-600">
                <div>
                    <div class="text-xs text-gray-600">Status</div>
                    <div class="text-base font-semibold text-green-700">{{ company_data.status }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Incorporation Date</div>
                    <div class="text-base font-semibold text-gray-900">{{ company_data.incorporation_date }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">License Number</div>
                    <div class="flex items-center gap-2 text-base font-semibold text-gray-900">
                        {{ company_data.license_number }}
                        <i class="text-xs text-gray-800 cursor-pointer fas fa-copy" @click="copyToClipboard(company_data.license_number)"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-600">Authority</div>
                    <div class="text-base font-semibold text-gray-900">{{ company_data.authority }}</div>
                </div>
                <div class="col-span-2 mx-4">
                    <div class="text-xs text-gray-600">Organization Type</div>
                    <div class="text-base font-semibold text-gray-900">{{ company_data.organization_type }}</div>
                </div>
            </div>
            <hr class="my-4 border-gray-300" />
            <!-- Company info section with Activity and Annual Turnover -->
            <div class="grid grid-cols-3 gap-8">
                <!-- Company Info Section -->
                <div class="relative col-span-2 py-6 border border-gray-200 rounded-lg">
                    <!-- Edit/Save/Cancel Buttons -->
                    <div class="absolute z-10 right-4 top-2">
                        <button v-if="!isEditingCompanyInfo" @click="editCompanyInfo" class="flex items-center justify-center rounded-lg secondary-btn action-button edit-button btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            Edit
                        </button>
                        <div v-else class="flex items-center gap-2 -mt-1">
                            <button @click="saveCompanyInfo" :disabled="isLoading" class="z-10 action-button text-white bg-[#D67A40] rounded-lg hover:bg-orange-600 btn-sm flex items-center justify-center">
                                <svg v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ isLoading ? 'Saving...' : 'Save' }}
                            </button>
                            <button @click="cancelEditCompanyInfo" class="flex items-center justify-center rounded-lg btn-sm secondary-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </button>
                        </div>
                    </div>
                    <!-- Display/Edit Mode -->
                    <div v-if="!isEditingCompanyInfo">
                        <div class="flex-1">
                            <h3 class="mb-2 text-xs font-semibold tracking-wide text-gray-600">Company Activity</h3>
                            <p class="text-sm font-medium leading-relaxed text-gray-800">
                                {{ company_data.company_activity || 'Not Available' }}
                            </p>
                        </div>
                        <hr class="my-4 border-gray-300" />
                        <!-- Contact Information -->
                        <div class="grid w-full grid-cols-1 text-sm text-gray-600 md:grid-cols-4 gap-y-4">
                            <div class="pr-6">
                                <div class="mb-1 text-xs text-gray-600">Website</div>
                                <div class="mb-2 text-sm font-semibold text-gray-900">
                                    <a v-if="company_data.website" :href="company_data.website" target="_blank" class="text-blue-500">{{ company_data.website }}</a>
                                    <span v-else>Not Available</span>
                                </div>
                                <div class="mb-1 text-xs text-gray-600">Contact No.</div>
                                <div class="text-sm font-semibold text-gray-900">{{ company_data.contact_no || 'Not Available' }}</div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs text-gray-600">Email</div>
                                <div class="text-sm font-semibold text-gray-900">{{ company_data.email || 'Not Available' }}</div>
                            </div>
                            <div class="col-span-2">
                                <div class="mb-1 text-xs text-gray-600">Registered Address</div>
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ company_data.address || 'Not Available' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="form-field">
                            <label class="form-label">Company Activity</label>
                            <textarea v-model="editedCompanyInfo.company_activity" class="form-input address-input" rows="2"></textarea>
                        </div>
                        <div class="grid w-full grid-cols-1 md:grid-cols-4 gap-y-4">
                            <div class="pr-6 form-field">
                                <label class="form-label">Website</label>
                                <input type="text" v-model="editedCompanyInfo.website" class="form-input" />
                                <label class="mt-2 form-label">Contact No.</label>
                                <input type="text" v-model="editedCompanyInfo.contact_no" class="form-input" />
                            </div>
                            <div class="mr-6 form-field">
                                <label class="form-label">Email</label>
                                <input type="email" v-model="editedCompanyInfo.email" class="form-input" />
                            </div>
                            <div class="col-span-2 form-field">
                                <label class="form-label">Registered Address</label>
                                <textarea v-model="editedCompanyInfo.address" class="form-input address-input" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4D Pie Chart Section -->
                <div class="flex flex-col items-center justify-center h-full max-h-64">
                    <div class="flex flex-col items-center justify-center h-full text-center rounded-lg">
                        <div class="flex flex-col items-center justify-center flex-1 h-48" style="width:420px; height:420px; display:flex; align-items:center; justify-content:center;">
                            <canvas id="requestsPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tab Navigation -->
    <div class="mb-8 border-b border-gray-600">
        <div class="flex items-center justify-between">
            <div class="flex">
                <button @click="activeTab = 'company_documents'" :class="['px-6 py-4 font-medium text-gray-500 border-b-4 transition-colors',
                        activeTab === 'company_documents' ? 'border-orange-500 text-gray-800 font-semibold' : 'border-transparent hover:text-gray-600']">
                    Company Documents
                </button>
                <button @click="activeTab = 'stakeholders'" :class="['px-6 py-4 font-medium text-gray-500 border-b-4 transition-colors',
                        activeTab === 'stakeholders' ? 'border-orange-500 text-gray-800 font-semibold' : 'border-transparent hover:text-gray-600']">
                    Stakeholders
                </button>
                <button @click="activeTab = 'accounting'" :class="['px-6 py-4 font-medium text-gray-500 border-b-4 transition-colors',
                        activeTab === 'accounting' ? 'border-orange-500 text-gray-800 font-semibold' : 'border-transparent hover:text-gray-600']">
                    Accounting
                </button>
                <button @click="activeTab = 'bank_accounts'" :class="['px-6 py-4 font-medium text-gray-500 border-b-4 transition-colors',
                        activeTab === 'bank_accounts' ? 'border-orange-500 text-gray-800 font-semibold' : 'border-transparent hover:text-gray-600']">
                    Bank Accounts
                </button>
            </div>
            <div class="relative">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search"
                    class="py-2 pl-10 pr-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @input="handleSearch"
                />
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button class="absolute inset-y-0 right-0 flex items-center pr-3" @click="clearSearch">
                    <svg v-if="searchQuery" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 12a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Tab Content -->
    <CompanyDocumentsTab v-if="activeTab === 'company_documents'" :documents="filteredDocuments" />
    <StakeholdersTab v-else-if="activeTab === 'stakeholders'" :documents="filteredStakeholders" />
    <AccountingTab v-else-if="activeTab === 'accounting'" :documents="filteredAccounting"/>
    <BankAccountsTab v-else-if="activeTab === 'bank_accounts'" :banks="filteredBankAccounts" />

    <!-- New Requests Section -->
    <RequestForms
        @document-request-submitted="handleDocumentRequestSubmitted"
        @change-request-submitted="handleChangeRequestSubmitted"
        @request-success="handleRequestSuccess"
        :custom-document-categories="documentCategories"
        :custom-change-categories="changeCategories"
        :company-id="company_id"
        :contact-id="page_data.user.bitrix_contact_id"
    />
</div>
</template>

<script>
import crescoLogo from '/public/storage/images/logos/CRESCO_icon.png'
import CompanyDocumentsTab from './Tabs/CompanyDocumentsTab.vue'
import StakeholdersTab from './Tabs/StakeholdersTab.vue'
import AccountingTab from './Tabs/AccountingTab.vue'
import BankAccountsTab from './Tabs/BankAccountsTab.vue'
import RequestForms from '../Shared/RequestForms.vue'
import LoadingOverlay from '../Shared/LoadingOverlay.vue'
// Import Swiper and its styles
import Swiper from 'swiper'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import Chart from 'chart.js/auto'

export default {
    name: 'Company',
    components: {
        CompanyDocumentsTab,
        StakeholdersTab,
        AccountingTab,
        BankAccountsTab,
        RequestForms,
        LoadingOverlay
    },
    props: {
        page_data: { type: Object , required: true },
        company_data : { type: Object , required: true },
        company_id : { type : Number }
    },
    data() {
        return {
            logoSrc: crescoLogo,
            activeTab: 'company_documents',
            swiper: null, // To store the Swiper instance
            sliderImages: [
                // Using relative paths for better asset loading through Laravel's asset pipeline
                `${window.location.origin}/img/cslide/slider-1.png`,
                `${window.location.origin}/img/cslide/slider-2.png`,
                `${window.location.origin}/img/cslide/slider-3.png`
            ],
            isLoading: false,
            searchQuery: '', // Added search query
            documentRequest: {
                category: '',
                details: '',
                file: null
            },
            changeRequest: {
                category: '',
                details: '',
                file: null
            },
            documentCategories: [
                'Certified Documents',
                'Notarised Documents',
                'Apostilled Documents',
                'Legalized Documents',
                'Others',
            ],
            changeCategories: [
                "Change Owner",
                "Change Capital",
                "Change Director / Manager / Secretary",
                "Change UBO",
                "Change Registered Agent",
                "Bank Account",
                "Name Change",
                "Others",
            ],
            isLoading: false, // Loading state for the overlay
            requestsPieChart: null, // Store chart instance
            isEditingCompanyInfo: false,
            editedCompanyInfo: {},
        }
    },
    created() {
        // Initialize company data from props
        if (this.page_data && this.company_data) {
            console.log('Company component created with page_data:', this.company_data);
            this.isLoading = true;
            // Short delay for consistent UX when loading directly vs switching
            setTimeout(() => {
                this.company_data = this.company_data;
                this.isLoading = false;
            }, 300);
        }
    },
    methods: {
        getCompanyLogo() {
            // Return the company logo from the company data
            if (this.company_data && this.company_data.logo) {
                return this.company_data.logo.startsWith('http')
                    ? this.company_data.logo
                    : `${window.location.origin}/${this.company_data.logo}`;
            }
            // Fallback to default logo
            return this.logoSrc;
        },
        copyToClipboard(text) {
            navigator.clipboard.writeText(text);
        },
        copyToClipboard(text) {
            // Create a temporary input element
            const el = document.createElement('textarea');
            // Set its value to the text that needs to be copied
            el.value = text;
            // Make it readonly to avoid tampering
            el.setAttribute('readonly', '');
            // Set its position to be outside the screen
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            // Add it to the document
            document.body.appendChild(el);
            // Select the text in the element
            el.select();
            // Copy the selected text to clipboard
            document.execCommand('copy');
            // Remove the temporary element
            document.body.removeChild(el);

            // Show a notification
            this.successToast('Copied to clipboard: ' + text);

        },

        getRandomGrowth() {
            // Generate a random growth percentage between 1 and 20
            return (1 + Math.random() * 19).toFixed(2);
        },

        sendDocumentRequest() {
            // Handle document request submission
            console.log('Sending document request:', this.documentRequest);
            // Here you would typically make an API call to submit the request
            // Reset form after successful submission
            this.documentRequest = {
                category: '',
                details: '',
                file: null
            };
            // Show success message
            this.successToast('Document request submitted successfully!');
        },

        sendChangeRequest() {
            // Handle change request submission
            console.log('Sending change request:', this.changeRequest);
            // Here you would typically make an API call to submit the request
            // Reset form after successful submission
            this.changeRequest = {
                category: '',
                details: '',
                file: null
            };
            // Show success message
            this.successToast('Change request submitted successfully!');
        },

        handleDocumentRequestSubmitted(requestData) {
            // Handle document request submission from component
            console.log('Company component received document request:', requestData);
            // Here you would typically make an API call to submit the request
        },

        handleChangeRequestSubmitted(requestData) {
            // Handle change request submission from component
            console.log('Company component received change request:', requestData);
            // Here you would typically make an API call to submit the request
        },

        handleRequestSuccess(requestType) {
            // Display success message based on request type
            const message = (requestType === 'document_request')
                ? 'Document request submitted successfully!'
                : 'Change request submitted successfully!';
            this.successToast(message);
        },

        handleFileAttachment(requestType, event) {
            const file = event.target.files[0];
            if (file) {
                if (requestType === 'document_request') {
                    this.documentRequest.file = file;
                } else {
                    this.changeRequest.file = file;
                }
            }
        },

        initSwiper() {
            // Initialize the Swiper instance for image slides
            if (this.$refs.swiperContainer) {
                this.swiper = new Swiper(this.$refs.swiperContainer, {
                    loop: true,
                    slidesPerView: 1,
                    spaceBetween: 0,
                    centeredSlides: true,
                    grabCursor: true,
                    watchOverflow: false, // Disable overflow watching to allow content to overflow
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                        dynamicBullets: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                        hideOnClick: false,
                    },
                    autoplay: {
                        delay: 5000, // 5 seconds delay between slides
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    effect: 'slide',
                    speed: 800,
                    on: {
                        init: () => {
                            console.log('Swiper initialized successfully');
                        }
                    }
                });
            }
        },

        initRequestsPieChart() {
            const ctx = document.getElementById('requestsPieChart');
            if (!ctx) return;
            // Example data, replace with real data as needed
            const data = {
                labels: ['Pending Requests', 'Priority Tasks'],
                datasets: [{
                    data: [12, 5], // Replace with real values
                    backgroundColor: [
                        '#f59e42', // Pending
                        '#ea4719', // Priority
                    ],
                    borderWidth: 2
                }]
            };
            if (this.requestsPieChart) {
                this.requestsPieChart.destroy();
            }
            this.requestsPieChart = new Chart(ctx, {
                type: 'pie', // Use pie chart instead of doughnut
                data,
                options: {
                    plugins: {
                        legend: { display: true, position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.parsed}`;
                                }
                            }
                        }
                    }
                }
            });
        },

        formatAmount(amount) {
            // Format amount to currency with commas
            if (!amount) return '0';
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'AED',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        // Search functionality methods
        handleSearch() {
            // Debounce search to improve performance
            clearTimeout(this._searchTimeout);
            this._searchTimeout = setTimeout(() => {
                console.log('Searching for:', this.searchQuery);
                // The actual filtering is handled by computed properties
            }, 300);
        },

        clearSearch() {
            this.searchQuery = '';
            console.log('Search cleared');
        },

        editCompanyInfo() {
            this.editedCompanyInfo = {
                company_activity: this.company_data.company_activity || '',
                website: this.company_data.website || '',
                contact_no: this.company_data.contact_no || '',
                email: this.company_data.email || '',
                address: this.company_data.address || ''
            };
            this.isEditingCompanyInfo = true;
        },
        saveCompanyInfo() {
            this.isLoading = true;
            // Simulate API call
            setTimeout(() => {
                this.company_data.company_activity = this.editedCompanyInfo.company_activity;
                this.company_data.website = this.editedCompanyInfo.website;
                this.company_data.contact_no = this.editedCompanyInfo.contact_no;
                this.company_data.email = this.editedCompanyInfo.email;
                this.company_data.address = this.editedCompanyInfo.address;
                this.isEditingCompanyInfo = false;
                this.isLoading = false;
                this.successToast && this.successToast('Company info updated!');
            }, 800);
        },
        cancelEditCompanyInfo() {
            this.isEditingCompanyInfo = false;
        },
    },
    computed: {
        // Define computed properties for filtered data based on the search query
        filteredDocuments() {
            if (!this.searchQuery || !this.company_data.documents) return this.company_data.documents;

            return this.company_data.documents.filter(doc => {
                return (
                    (doc.name && doc.name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (doc.type && doc.type.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (doc.status && doc.status.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            });
        },

        filteredStakeholders() {
            if (!this.searchQuery || !this.company_data.relation) return this.company_data.relation;

            return this.company_data.relation.filter(stakeholder => {
                return (
                    (stakeholder.name && stakeholder.name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (stakeholder.role && stakeholder.role.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (stakeholder.position && stakeholder.position.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            });
        },

        filteredAccounting() {
            if (!this.searchQuery || !this.company_data.documents) return this.company_data.documents;

            // Filter accounting documents - assuming financial documents have a type or category
            return this.company_data.documents.filter(doc => {
                return (
                    (doc.name && doc.name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (doc.type && doc.type.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (doc.category && doc.category.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            });
        },

        filteredBankAccounts() {
            if (!this.searchQuery || !this.company_data.bank) return this.company_data.bank;

            return this.company_data.bank.filter(bank => {
                return (
                    (bank.name && bank.name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (bank.account_number && bank.account_number.includes(this.searchQuery)) ||
                    (bank.account_type && bank.account_type.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (bank.currency && bank.currency.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            });
        }
    },
    watch: {
        // Watch for changes in page_data to update the company data
        'company_data': {
            handler(newValue) {
                if (newValue) {
                    // Show loading state
                    this.isLoading = true;

                    // Simulate a brief loading period for better UX
                    setTimeout(() => {
                        this.company_data = newValue;
                        this.isLoading = false;
                    }, 300);
                }
            },
            deep: true
        },

        // Clear search when changing tabs
        activeTab() {
            this.searchQuery = '';
        }
    },
    mounted() {
        // Initialize Swiper
        this.$nextTick(() => {
            this.initSwiper();
            setTimeout(() => this.initRequestsPieChart(), 500); // Ensure DOM is ready
        });
    },

    beforeUnmount() {
        // Destroy Swiper instance to prevent memory leaks
        if (this.swiper) {
            this.swiper.destroy();
        }
        if (this.requestsPieChart) {
            this.requestsPieChart.destroy();
        }
    },
    created() {
        // Initialize company data from props
        if (this.page_data && this.company_data) {
            this.isLoading = true;
            // Short delay for consistent UX when loading directly vs switching
            setTimeout(() => {
                this.isLoading = false;
            }, 300);
        }
    },
}
</script>

<style scoped>
/* Custom Swiper styling to allow overflow and match New Requests width */
.swiper {
    position: relative;
    width: 100%;
    margin: 0 auto;
    /* max-width: 100%; */
    border-radius: 0.5rem;
    overflow: hidden;
}

/* Animation for company data transition */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.text-3xl, .text-base, .grid-cols-2, .grid-cols-3 {
    animation: fadeIn 0.5s ease-out;
}

.swiper-button-prev,
.swiper-button-next {
    color: #333 !important;
    transition: all 0.3s ease;
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
    color: #000 !important;
    transform: scale(1.2);
}

.swiper-pagination-bullet-active {
    background-color: #333 !important;
}

.swiper-slide {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    transition: transform 300ms ease;
    overflow: visible;
    z-index: 1;
    perspective: 1000px;
}

.swiper-slide-active {
    z-index: 2;
}

/* Style for image slides */
.swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    margin: 0 auto;
    transform-origin: center;
    transition: all 0.3s ease-out;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 0.25rem;
}

/* Add hover effect for image slides */
.swiper-slide:hover img {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

/* Search input styling */
.search-highlight {
    background-color: rgba(255, 213, 86, 0.4);
    padding: 2px;
    border-radius: 2px;
}

/* Search animation */
@keyframes searchPulse {
    0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
    70% { box-shadow: 0 0 0 5px rgba(59, 130, 246, 0); }
    100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
}

input[type="text"]:focus {
    animation: searchPulse 1.5s infinite;
}

.form-field {
  position: relative;
  margin-bottom: 8px;
}

.form-label {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 4px;
  font-weight: 500;
}

.form-input {
  width: 100%;
  padding: 8px 8px 8px 8px;
  font-size: 0.875rem;
  font-weight: 500;
  color: #1f2937;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background-color: #f9fafb;
  transition: all 0.2s ease;
}

.form-input:hover {
  border-color: #9ca3af;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(59, 130, 246, 0.5);
  background-color: #fff;
}

.address-input {
  padding-top: 8px;
  resize: none;
}

/* @media (max-width: 640px) {
    .swiper {
        height: 280px;
    }

    .swiper-button-prev,
    .swiper-button-next {
        transform: scale(0.8);
    }
} */
</style>
