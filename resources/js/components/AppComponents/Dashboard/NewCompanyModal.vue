<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-black/50" @click="close"></div>

    <!-- Modal Content -->
    <div class="relative w-full max-w-2xl p-6 mx-4 bg-white rounded-lg shadow-xl" @click="closeDropdowns">
      <!-- Modal Header -->
      <div class="mb-6 text-start">
        <h1 class="text-xl text-gray-900">Set-up a new company</h1>
        <!-- Progress Bar -->
        <div class="flex w-full mt-4 mb-6">
          <div class="flex-1 h-1.5 bg-orange-500"></div>
          <div class="flex-1 h-1.5 bg-gray-200"></div>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="space-y-6">
        <div>
          <label class="block mb-2 text-base font-medium text-gray-700">Give us a short description about the business you want to open.</label>
          <textarea
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"
            rows="4"
            placeholder="Tell us more in details."
            v-model="description"
          ></textarea>
        </div>

        <div class="relative">
          <div
            @click="toggleTimeframeDropdown"
            data-dropdown="timeframe"
            class="flex items-center justify-between p-4 rounded-lg cursor-pointer bg-gray-50"
          >
            <span class="text-base font-medium text-gray-700">{{ timeframeSelected ? timeframe : 'How soon would you like to open your company?' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500" :class="{'rotate-180': showTimeframeDropdown}">
              <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
          </div>

          <!-- Dropdown Options -->
          <div
            v-if="showTimeframeDropdown"
            class="absolute z-10 w-full mt-2 overflow-hidden bg-white border border-gray-200 rounded-lg shadow-lg"
          >
            <div
              v-for="(option, index) in timeframeOptions"
              :key="index"
              @click="selectTimeframe(option, $event)"
              class="p-4 cursor-pointer hover:bg-gray-50"
              :class="{'bg-gray-50': timeframe === option}"
            >
              {{ option }}
            </div>
          </div>
        </div>

        <div class="relative">
          <div
            @click="toggleLanguageDropdown"
            data-dropdown="language"
            class="flex items-center justify-between p-4 rounded-lg cursor-pointer bg-gray-50"
          >
            <span class="text-base font-medium text-gray-700">{{ languageSelected ? language : 'What is your preferred language?' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500" :class="{'rotate-180': showLanguageDropdown}">
              <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
          </div>

          <!-- Dropdown Options -->
          <div
            v-if="showLanguageDropdown"
            class="absolute z-10 w-full mt-2 overflow-hidden bg-white border border-gray-200 rounded-lg shadow-lg"
          >
            <div
              v-for="(option, index) in languageOptions"
              :key="index"
              @click="selectLanguage(option, $event)"
              class="p-4 cursor-pointer hover:bg-gray-50"
              :class="{'bg-gray-50': language === option}"
            >
              {{ option }}
            </div>
          </div>
        </div>

        <div>
          <h3 class="mb-3 text-base font-medium text-gray-700">How would you like to be contacted?</h3>
          <div class="flex flex-wrap gap-3">
            <button
              @click="selectContactMethod('none')"
              :class="['px-4 py-2 text-sm font-medium border rounded-lg hover:bg-gray-100',
                contactMethod === 'none' ? 'border-gray-400' : 'bg-[#EAECF3] border-gray-300'
              ]"
            >
              No preference
            </button>
            <button
              @click="selectContactMethod('call')"
              :class="['flex items-center px-4 py-2 text-sm font-medium border rounded-lg hover:bg-gray-100',
                contactMethod === 'call' ? 'border-gray-400' : 'bg-[#EAECF3] border-gray-300'
              ]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
              </svg>
              Call
            </button>
            <button
              @click="selectContactMethod('email')"
              :class="['flex items-center px-4 py-2 text-sm font-medium border rounded-lg hover:bg-gray-100',
                contactMethod === 'email' ? 'border-gray-400' : 'bg-[#EAECF3] border-gray-300'
              ]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
              </svg>
              E-mail
            </button>
            <button
              @click="selectContactMethod('video')"
              :class="['flex items-center px-4 py-2 text-sm font-medium border rounded-lg hover:bg-gray-100',
                contactMethod === 'video' ? 'border-gray-400' : 'bg-[#EAECF3] border-gray-300'
              ]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
              Online Video Meeting
            </button>
            <button
              @click="selectContactMethod('in-person')"
              :class="['flex items-center px-4 py-2 text-sm font-medium border rounded-lg hover:bg-gray-100',
                contactMethod === 'in-person' ? 'border-gray-400' : 'bg-[#EAECF3] border-gray-300'
              ]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
              </svg>
              In-person Meeting
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-between mt-8">
        <button
          class="px-8 py-3 font-medium text-gray-700 border border-gray-900 rounded-lg w-52 hover:bg-gray-100"
          @click="close"
          :disabled="isLoading"
        >
          Cancel
        </button>
        <button
          class="px-8 py-3 font-medium w-96 text-white bg-[#D67A40] rounded-lg hover:bg-orange-600 flex items-center justify-center"
          @click="submitRequest"
          :disabled="isLoading"
        >
          <svg v-if="isLoading" class="w-5 h-5 mr-3 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isLoading ? 'Submitting...' : 'Submit Request' }}
        </button>
      </div>

      <!-- Close Button -->
      <button
        class="absolute text-gray-500 top-2 right-2 hover:text-gray-700"
        @click="close"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'NewCompanyModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    contactId: {
      type: Number,
    }
  },
  data() {
    return {
      description: '',
      timeframe: '',
      timeframeSelected: false,
      showTimeframeDropdown: false,
      timeframeOptions: [
        'As soon as possible',
        'Within 1 month',
        'Within 3 months',
        'Within 6 months',
        'Not sure yet'
      ],
      language: '',
      languageSelected: false,
      showLanguageDropdown: false,
      languageOptions: [
        'English',
        'Arabic',
        'French',
        'German',
        'Hindi',
        'Spanish',
        'Other'
      ],
      contactMethod: '',
      isLoading: false
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    closeDropdowns(event) {
      // Skip if the click is on the dropdown toggle itself
      const isTimeframeDropdownToggle = event.target.closest('[data-dropdown="timeframe"]');
      const isLanguageDropdownToggle = event.target.closest('[data-dropdown="language"]');

      if (!isTimeframeDropdownToggle && !isLanguageDropdownToggle) {
        this.showTimeframeDropdown = false;
        this.showLanguageDropdown = false;
      }
    },
    selectContactMethod(method) {
      this.contactMethod = method;
    },
    toggleTimeframeDropdown(event) {
      event.stopPropagation();
      this.showTimeframeDropdown = !this.showTimeframeDropdown;
      if (this.showTimeframeDropdown) {
        this.showLanguageDropdown = false;
      }
    },
    selectTimeframe(option, event) {
      if (event) event.stopPropagation();
      this.timeframe = option;
      this.timeframeSelected = true;
      this.showTimeframeDropdown = false;
    },
    toggleLanguageDropdown(event) {
      event.stopPropagation();
      this.showLanguageDropdown = !this.showLanguageDropdown;
      if (this.showLanguageDropdown) {
        this.showTimeframeDropdown = false;
      }
    },
    selectLanguage(option, event) {
      if (event) event.stopPropagation();
      this.language = option;
      this.languageSelected = true;
      this.showLanguageDropdown = false;
    },
    submitRequest() {
      // Validation before submission
      if (!this.description || !this.timeframe || !this.language) {
        alert('Please fill out all required fields.');
        return;
      }

      // Data to be sent to the API
      const requestData = {
        contact_id: this.contactId, // Optional contact ID
        description: this.description,
        timeframe: this.timeframe,
        language: this.language,
        contact_method: this.contactMethod
      };

      // If we have a contact ID in the future, add it here
      // requestData.contact_id = this.contactId;

      // Show loading state
      this.isLoading = true;

      // Make the API call
      axios.post('/api/request/company-setup', requestData)
        .then(response => {
          console.log('Company setup request submitted successfully:', response.data);
          // You can emit an event for successful submission
        //   this.$emit('success', response.data.data);
            this.successToast('Your request has been submitted successfully! We will get back to you shortly.');
          // Close the modal after submission
          this.close();
        })
        .catch(error => {
          console.error('Error submitting company setup request:', error);
          // Handle different error scenarios
          if (error.response) {
            // The request was made and the server responded with a status code
            // that falls out of the range of 2xx
            console.error(error.response.data);
            alert(error.response.data.message || 'An error occurred while submitting your request. Please try again.');
          } else if (error.request) {
            // The request was made but no response was received
            alert('No response received from server. Please check your internet connection and try again.');
          } else {
            // Something happened in setting up the request that triggered an Error
            alert('An error occurred while preparing your request. Please try again.');
          }
        })
        .finally(() => {
          // Hide loading state
          this.isLoading = false;
        });
    }
  }
}
</script>

<style scoped>
/* Add any modal-specific styles here */
.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.2s ease-in-out;
}

svg {
  transition: transform 0.2s ease-in-out;
}

/* Dropdown animation */
@keyframes dropdownIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.absolute {
  animation: dropdownIn 0.2s ease-out forwards;
}
</style>
