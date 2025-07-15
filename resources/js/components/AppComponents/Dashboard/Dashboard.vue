<template>
  <div class="w-full min-h-screen p-6 mx-10 md:w-auto">
    <!-- Main Grid Container -->
    <div class="grid grid-cols-12 gap-6">
      <!-- Left Column (7/12) -->
      <div class="col-span-8">
        <!-- Header Section -->
        <div class="flex items-center">
          <img :src="user.profilePhoto" alt="Profile Photo" class="object-cover w-20 h-20 mr-5 border-2 border-gray-300 rounded-full shadow-sm">
          <div class="flex-grow">
            <div class="flex items-center justify-between mb-4">
              <h1 class="text-4xl font-extrabold text-gray-900">{{ user.name }} {{ user.lastName }}</h1>
              <div class="flex space-x-4 text-sm text-gray-500">
                <div class="flex items-center gap-2">
                  <span>Profile Photo:</span>
                  <button type="button" @click="downloadFile(user.profilePhoto)" class="flex items-center gap-1 text-gray-900 hover:text-blue-800">
                    <span class="font-bold underline">Download</span>
                    <i class="text-sm fas fa-download"></i>
                  </button>
                </div>
                <div class="flex items-center gap-2">
                  <span>CV / Resume:</span>
                  <button type="button" class="flex items-center gap-1 text-gray-900 hover:text-blue-800 hove:cursor-pointer" @click="downloadFile(user.resume)">
                    <span class="font-bold underline">Download</span>
                    <i class="text-sm fas fa-download"></i>
                  </button>
                </div>
              </div>
            </div>
            <hr class="border-gray-500 border-1"/>
          </div>
        </div>

        <!-- Companies Section -->
        <div class="p-6 mb-6 ml-20">
          <!-- Personal Details -->
          <div class="relative p-4 mb-10 border border-black rounded-lg">
            <!-- Edit/Save/Cancel Buttons -->
            <div class="absolute right-4 top-2">
              <button
                v-if="!isEditing"
                @click="editPersonalDetails"
                class="action-button edit-button btn-sm"
              >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
              </button>
              <div v-else class="flex items-center gap-2 -mt-1">
                <button
                  @click="savePersonalDetails"
                  :disabled="isLoading"
                  class="z-10 action-button text-white bg-[#D67A40] rounded-lg hover:bg-orange-600 btn-sm flex items-center justify-center"
                >
                  <svg v-if="isLoading" class="animate-spin mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>
                  {{ isLoading ? 'Saving...' : 'Save' }}
                </button>
                <button
                  @click="cancelEdit"
                  class="z-10 border border-black action-button btn-sm"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                  Cancel
                </button>
              </div>
            </div>

            <!-- Display Mode -->
            <div v-if="!isEditing" class="grid grid-cols-4 gap-4">
              <div>
                <p class="text-xs text-gray-500">Date of Birth</p>
                <p class="text-sm font-medium text-gray-900">{{ user.dob || 'Not Available'}}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Phone No</p>
                <p class="text-sm font-medium text-gray-900">{{ user.phone_no || 'Not Available' }}</p>
              </div>
              <div class="col-span-2">
                <p class="text-xs text-gray-500">Individual Tax No</p>
                <p class="text-sm font-medium text-gray-900">{{ user.taxNo || 'Not Available' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Nationality</p>
                <p class="text-sm font-medium text-gray-900">{{ user.nationality || 'Not Available' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Mobile No</p>
                <p class="text-sm font-medium text-gray-900">{{ user.mobile || 'Not Available' }}</p>
              </div>
              <div class="col-span-2">
                <p class="text-xs text-gray-500">Address</p>
                <p class="text-sm font-medium text-gray-900">{{ user.address || 'Not Available' }}</p>
              </div>
            </div>

            <!-- Edit Mode - More Elegant Version -->
            <div v-else class="grid grid-cols-4 gap-5 mt-1">
              <div class="form-field">
                <label class="form-label">Date of Birth</label>
                <div class="input-container">
                  <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                  <input
                    type="date"
                    v-model="editedUser.dob"
                    class="form-input"
                  />
                </div>
              </div>
              <div class="form-field">
                <label class="form-label">Phone No</label>
                <div class="input-container">
                  <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                  <input
                    type="tel"
                    v-model="editedUser.phone_no"
                    class="form-input"
                    pattern="[+][0-9]{1,4}\s[0-9\s]{5,}"
                    placeholder="+971 4 323 4567"
                  />
                </div>
              </div>
              <div class="col-span-2 form-field">
                <label class="form-label">Individual Tax No</label>
                <div class="input-container">
                  <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                  <input
                    type="text"
                    v-model="editedUser.taxNo"
                    class="form-input"
                    placeholder="Not Available"
                  />
                </div>
              </div>
              <div class="form-field">
                <label class="form-label">Nationality</label>
                <div class="input-container">
                  <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
                  </svg>
                  <input
                    type="text"
                    v-model="editedUser.nationality"
                    class="form-input"
                  />
                </div>
              </div>
              <div class="form-field">
                <label class="form-label">Mobile No</label>
                <div class="input-container">
                  <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                  </svg>
                  <input
                    type="tel"
                    v-model="editedUser.mobile"
                    class="form-input"
                    pattern="[+][0-9]{1,4}\s[0-9\s]{5,}"
                    placeholder="+971 56 784 7893"
                  />
                </div>
              </div>
              <div class="col-span-2 form-field">
                <label class="form-label">Address</label>
                <div class="input-container">
                  <svg xmlns="http://www.w3.org/2000/svg" class="input-icon address-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                  <textarea
                    v-model="editedUser.address"
                    class="form-input address-input"
                    rows="2"
                    placeholder="Enter your full address"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">My Companies</h2>
            <button
              @click="openNewCompanyModal"
              class="flex items-center overflow-hidden bg-white border border-black rounded"
            >
                <span class="px-3 text-xs font-semibold">Add Company</span>
                <i class="ki-duotone ki-plus text-semibold px-1 bg-[#313D4F] text-white text-lg font-bold"></i>
            </button>
          </div>
          <hr class="border border-black"/>
          <div v-for="company in companies" :key="company.id" class="w-full">
            <div class="px-6 py-4">
              <div class="flex items-start justify-between">
                <div class="flex items-start flex-1 space-x-4">
                  <!-- Logo -->
                  <div class="flex items-center justify-center flex-shrink-0 w-20 h-20 overflow-hidden bg-white border-2 border-red-200 rounded-full">
                    <template v-if="company.logo">
                      <img :src="company.logo" alt="Company Icon" class="object-contain w-20 h-20">
                    </template>
                    <template v-else>
                      <span class="text-3xl font-bold text-gray-500">{{ company.name ? company.name.substring(0,2).toUpperCase() : '--' }}</span>
                    </template>
                  </div>
                  <!-- Company Info -->
                  <div class="flex-1 min-w-0">
                    <h3 class="mb-2 text-xl font-semibold text-gray-900"><a :href="company.link">{{ company.name }}</a></h3>
                    <hr class="w-full mb-2 border border-gray-300"/>
                    <div class="flex items-start justify-between">
                      <div class="flex flex-col space-y-1 text-sm text-gray-600">
                        <div v-if="company.otherRoles.length === 0"><span class="text-sm font-medium">Role:</span> <span class="text-sm text-black capitalize"> Not Available</span></div>
                        <template v-for="(role, key) in company.otherRoles" :key="key">
                            <div v-if="key === 0"><span class="text-sm font-medium">Role:</span> <span class="text-sm font-medium text-black capitalize"> {{ role.name }}</span></div>
                            <div v-else class="text-sm font-medium text-black capitalize ml-9">{{ role.name }}</div>
                        </template>
                      </div>
                        <div class="flex flex-col space-y-2 w-max">
                            <div class="flex">
                                <button class="flex items-center bg-[#313D4F] rounded-l-md h-8 px-4.5 text-white text-sm font-medium focus:outline-none">
                                View Task(s)
                                </button>
                                <span class="flex items-center justify-center bg-[#FFA348] text-white text-xs font-bold rounded-r h-8 w-10">
                                {{ company.tasks }}
                                </span>
                            </div>
                            <div class="flex">
                                <button class="flex items-center bg-[#313D4F] rounded-l-md h-8 px-4 text-white text-sm font-medium focus:outline-none">
                                Payment Due
                                </button>
                                <span class="flex items-center justify-center bg-[#FFA348] text-white text-xs font-bold rounded-r h-8 w-10">
                                {{ company.payments }}
                                </span>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Right Column (5/12) -->
      <div class="col-span-4">
        <!-- KYC Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
          <div class="p-4 mb-2 bg-[#C9D2E2] rounded-lg shadow">
            <h2 class="mb-2 text-lg font-semibold text-gray-800">KYC of {{ user.name }} {{ user.lastName }}</h2>
            <div class="space-y-4">
              <div v-for="doc in kyc_documents" :key="doc.type" class="p-4 mb-2 bg-white border rounded">
                <p class="mb-2 text-sm text-gray-900"><strong>{{ doc.label }}</strong></p>
                <div class="grid grid-cols-3 gap-3">
                  <div v-for="field in doc.fields" :key="field.label">
                    <p class="text-xs text-gray-500">{{ field.label }}</p>
                    <p class="text-sm font-medium text-gray-900">
                      {{ field.value }}
                      <button v-if="field.copy" class="ml-1 text-gray-900 hover:text-gray-900" @click="copyToClipboard(field.value)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                          <path d="M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 0 1 3.75 3.75v1.875C13.5 8.161 14.34 9 15.375 9h1.875A3.75 3.75 0 0 1 21 12.75v3.375C21 17.16 20.16 18 19.125 18h-9.75A1.875 1.875 0 0 1 7.5 16.125V3.375Z" />
                          <path d="M15 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 17.25 7.5h-1.875A.375.375 0 0 1 15 7.125V5.25ZM4.875 6H6v10.125A3.375 3.375 0 0 0 9.375 19.5H16.5v1.125c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V7.875C3 6.839 3.84 6 4.875 6Z" />
                        </svg>
                      </button>
                    </p>
                  </div>
                  <div v-if="doc.download" class="flex flex-col">
                    <p class="text-xs text-gray-500">Document</p>
                    <button type="button" @click="downloadFile(doc.path)" class="flex items-center gap-1 text-xs text-blue-600 text-decoration-none text-semibold btn-text hover:text-blue-800">
                        <span class="text-sm font-semibold underline">Download <i class="ml-1 fas fa-download"></i></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Tasks Section -->
        <div class="p-6 mb-6 bg-white rounded-lg shadow">
          <h2 class="mb-1 text-lg font-semibold text-gray-700">My Tasks</h2>
          <hr class="mb-4 border"/>
          <div v-for="task in tasks" :key="task.id" :class="task.class">
            <button :class="task.buttonClass">{{ task.company }}</button>
            <p class="text-sm font-semibold">{{ task.text }}</p>
            <hr class="mb-4 border"/>
          </div>
        </div>
        <div class="p-6 mb-6 bg-white rounded-lg shadow">
          <h2 class="mb-1 text-lg font-semibold text-gray-700">My Request</h2>
          <hr class="mb-4 border"/>
          <div v-for="task in tasks" :key="task.id" :class="task.class">
            <button :class="task.buttonClass">{{ task.company }}</button>
            <p class="text-sm font-semibold">{{ task.text }}</p>
            <hr class="mb-4 border"/>
          </div>
        </div>
        <div class="mb-2 bg-white rounded-lg shadow">
          <div class="p-6 w-full bg-white transition-all duration-500 shadow-2xl h-[400px] rounded-2xl hover:shadow-3xl group">
            <swiper :options="swiperOptions" class="h-full">
              <swiper-slide v-for="(slide, idx) in slides" :key="idx">
                <img :src="slide" class="flex items-center justify-center w-full h-full overflow-hidden">
              </swiper-slide>
            </swiper>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Floating Chat Icon -->
  <a href="#"
    class="fixed bottom-0 z-20 left-4"
    aria-label="Chat"
  >
    <img
      :src="chatIcon"
      alt="Chat"
      class="w-24 h-32 p-3 rounded-lg"
    />
  </a>

  <!-- New Company Modal -->
  <new-company-modal
    :show="showNewCompanyModal"
    @close="closeNewCompanyModal"
    :contact-id="page_data.user.bitrix_contact_id"
  />

  <!-- Notification Toast -->
  <transition name="toast">
    <div
      v-if="showNotification"
      class="notification-toast"
      :class="{ 'error-notification': isNotificationError }"
    >
      <div class="notification-content">
        <svg v-if="!isNotificationError" xmlns="http://www.w3.org/2000/svg" class="notification-icon" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="notification-icon" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span>{{ notificationMessage }}</span>
        <button @click="showNotification = false" class="notification-close">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
      <div class="notification-progress">
        <div class="notification-progress-bar"></div>
      </div>
    </div>
  </transition>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/swiper-bundle.css';
import NewCompanyModal from './NewCompanyModal.vue';
import axios from 'axios';
export default {
  name: 'dashboard',
  components: {
    Swiper,
    SwiperSlide,
    NewCompanyModal
  },
  props: {
    page_data : { type: Object, required: true },
    user: { type: Object, required: true },
    companies: { type: Array, required: true },
    kyc_documents: { type: Array, required: true },
    tasks: { type: Array, required: true },
    slides: { type: Array, default: () => [] }
  },
  data() {
    return {
      chatIcon: '/storage/images/logos/chat.svg',
      showNewCompanyModal: false,
      isEditing: false,
      editedUser: null,
      showNotification: false,
      notificationMessage: '',
      isLoading: false,
      isNotificationError: false,
      swiperOptions: {
        loop: true,
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        autoplay: { delay: 3000, disableOnInteraction: false },
        effect: 'slide',
      }
    };
  },
  methods: {
    copyToClipboard(text) {
      navigator.clipboard.writeText(text);
      this.successToast(`Copied to clipboard! ${text}`);
    },
    editPersonalDetails() {
      // Clone user data to editedUser so we can revert if necessary
      this.editedUser = { ...this.user };

      // Format the date properly for the date input if needed
      if (this.editedUser.dob && !this.isValidDateFormat(this.editedUser.dob)) {
        try {
          // Try to convert a date like "12 Aug 1968" to yyyy-MM-dd format for input
          const dateObj = new Date(this.editedUser.dob);
          if (!isNaN(dateObj)) {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            this.editedUser.dob = `${year}-${month}-${day}`;
          }
        } catch (e) {
          console.error('Error formatting date:', e);
        }
      }

      this.isEditing = true;
    },
    savePersonalDetails() {
      // Set loading state
      this.isLoading = true;

      // Create a copy of the edited user to prepare for API submission
      const userData = { ...this.editedUser };
        console.log('Saving user data:', userData);
      // Format the date for API submission (if needed)
      if (userData.dob && this.isValidDateFormat(userData.dob)) {
        try {
          const dateObj = new Date(userData.dob);
          if (!isNaN(dateObj)) {
            // Format date for API (ISO format)
            userData.dob_for_api = dateObj.toISOString().split('T')[0]; // YYYY-MM-DD format

            // Format date for display
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            userData.dob = dateObj.toLocaleDateString('en-US', options);
          }
        } catch (e) {
          console.error('Error formatting date for display:', e);
        }
      }

      // Make API call to update user details
      axios.post('/api/request/personal-details', {
        contact_id: userData.contact_id,
        phone: userData.phone_no,
        birthdate: userData.dob_for_api || userData.dob,
        nationality: userData.nationality,
        taxNo: userData.taxNo,
        address: userData.address
      })
        .then(response => {
          // Update the local user object with response data
          Object.assign(this.user, response.data.data || userData);

          // Show success notification
          this.notificationMessage = response.data.message || 'Personal details updated successfully!';
          this.showNotification = true;
          setTimeout(() => {
            this.showNotification = false;
          }, 3000);

          // Emit an event to notify parent components of the update
          this.$emit('user-updated', this.user);
        })
        .catch(error => {
          // Handle errors
          console.error('Error updating user details:', error);

          // Show error notification
          this.notificationMessage = error.response?.data?.message || 'Failed to update personal details. Please try again.';
          this.showNotification = true;
          this.isNotificationError = true;
          setTimeout(() => {
            this.showNotification = false;
            this.isNotificationError = false;
          }, 3000);
        })
        .finally(() => {
          // Reset loading state and exit edit mode
          this.isLoading = false;
          this.isEditing = false;
        });
    },
    cancelEdit() {
      this.isEditing = false;
      this.editedUser = null;
    },
    openNewCompanyModal() {
      this.showNewCompanyModal = true;
    },
    closeNewCompanyModal() {
      this.showNewCompanyModal = false;
    },

    isValidDateFormat(dateStr) {
      // Check if the date is in yyyy-MM-dd format
      const regex = /^\d{4}-\d{2}-\d{2}$/;
      return regex.test(dateStr);
    },
    async downloadFile(filePath) {
        try {
            console.log('Starting download for:', filePath);
            // Show loading indicator if needed
            this.$emit('download-started');

            // Generate the full URL to the file
            const fileUrl = `${filePath}`;

            // Fetch the file as a blob
            const response = await fetch(fileUrl);

            if (!response.ok) {
            throw new Error(`Error downloading file: ${response.statusText}`);
            }

            // Convert the response to a blob
            const blob = await response.blob();

            // Create a download link and trigger the download
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;

            // Extract filename from the path
            const filename = filePath.split('/').pop();
            link.setAttribute('download', filename);

            // Append to the body, click, and clean up
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            // Signal completion
            this.$emit('download-completed');
        } catch (error) {
            console.error('Download failed:', error);
            this.$emit('download-error', error.message);

            // You can also show an error notification to the user
            // this.$notify({
            //   type: 'error',
            //   title: 'Download Failed',
            //   text: 'Unable to download the file. Please try again later.'
            // });
        }
    }
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
.grid {
  transition: all 0.3s ease-in-out;
}

/* Form field styling */
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

.input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 10px;
  width: 16px;
  height: 16px;
  color: #9ca3af;
  pointer-events: none;
}

.address-icon {
  top: 8px;
}

.form-input {
  width: 100%;
  padding: 8px 8px 8px 32px;
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

/* Button styling */
.action-button {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 6px;
  transition: all 0.2s ease;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.edit-button {
  color: #4b5563;
  background-color: #f3f4f6;
  border: 1px solid #d1d5db;
}

.edit-button:hover {
  color: #1f2937;
  background-color: #e5e7eb;
  border-color: #9ca3af;
}

.save-button {
  color: #fff;
  background-color: #10b981;
  border: 1px solid #059669;
}

.save-button:hover {
  background-color: #059669;
}

.cancel-button {
  color: #fff;
  background-color: #ef4444;
  border: 1px solid #dc2626;
}

.cancel-button:hover {
  background-color: #dc2626;
}

/* Notification Toast */
.notification-toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  max-width: 350px;
  background-color: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 50;
  border-left: 4px solid #10b981; /* Success green border */
}

.error-notification {
  border-left: 4px solid #ef4444; /* Error red border */
}

.error-notification .notification-progress-bar {
  background-color: #ef4444; /* Error red progress bar */
}

.notification-content {
  display: flex;
  align-items: center;
  padding: 12px 16px;
}

.notification-icon {
  width: 20px;
  height: 20px;
  color: #10b981;
  margin-right: 12px;
  flex-shrink: 0;
}

.notification-close {
  color: #9ca3af;
  margin-left: 12px;
  cursor: pointer;
  transition: color 0.15s ease;
}

.notification-close:hover {
  color: #4b5563;
}

.notification-progress {
  height: 3px;
  width: 100%;
  background-color: #e5e7eb;
  position: relative;
  overflow: hidden;
}

.notification-progress-bar {
  position: absolute;
  height: 100%;
  width: 100%;
  left: 0;
  background-color: #10b981;
  animation: progress 3s linear forwards;
}

@keyframes progress {
  0% {
    width: 100%;
  }
  100% {
    width: 0%;
  }
}

/* Toast Animation */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
