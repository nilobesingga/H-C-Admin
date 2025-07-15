<template>
  <div class="w-full min-h-screen p-6">
    <!-- Header with back button -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="flex items-center text-3xl font-bold text-gray-900">
        Payments
      </h1>
      <div class="hidden md:block">
        <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
          </svg>
          Export
        </button>
      </div>
    </div>

    <!-- Wallet and Pending Payment Summary Section -->
    <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
      <!-- Wallet Card -->
      <div class="p-6 transition-shadow duration-300 bg-white rounded-lg shadow hover:shadow-md">
        <h2 class="mb-2 text-lg font-medium text-gray-600">My Wallet</h2>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-4xl font-bold text-gray-800">$ 500,000.00</p>
          </div>
          <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-75 disabled:cursor-not-allowed" @click="addFunds" :disabled="isAddingFunds">
            <span v-if="!isAddingFunds">Add Funds</span>
            <span v-else class="flex items-center">
              <svg class="w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Adding...
            </span>
            <svg v-if="!isAddingFunds" class="w-4 h-4 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Pending Payment Card -->
      <div class="p-6 transition-shadow duration-300 bg-[#FFA348] rounded-lg shadow hover:shadow-md">
        <h2 class="mb-2 text-lg font-medium text-white">Pending Payment</h2>
        <div class="flex items-center justify-between">
          <p class="text-4xl font-bold text-white">$ 1,650.00</p>
          <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-orange-500 transition-colors duration-200 bg-white border border-transparent rounded-md shadow-sm hover:bg-orange-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-75 disabled:cursor-not-allowed" @click="payAll" :disabled="isProcessingAllPayment">
            <span v-if="!isProcessingAllPayment">Pay All</span>
            <span v-else class="flex items-center">
              <svg class="w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Processing...
            </span>
            <svg v-if="!isProcessingAllPayment" class="w-4 h-4 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Pending Payments Section -->
    <div class="mb-8">
      <div class="flex items-center justify-between pb-2 mb-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">Pending Payments</h2>
        <button
          @click="refreshTransactions"
          class="flex items-center text-sm text-gray-600 transition-colors duration-200 hover:text-gray-800"
          :class="{ 'animate-spin': isLoadingTransactions }"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh
        </button>
      </div>

      <div class="flex flex-col gap-6 lg:flex-row">
        <!-- Payments Table -->
        <div class="relative overflow-hidden transition-shadow rounded-lg shadow-sm bg-gray-50 lg:w-3/4 hover:shadow-md">
          <!-- Loading Indicator -->
          <div v-if="isLoadingTransactions" class="absolute inset-0 z-10 flex items-center justify-center bg-white bg-opacity-60">
            <svg class="w-10 h-10 text-gray-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>

          <!-- Table Header -->
          <div class="hidden grid-cols-12 px-4 py-3 bg-gray-100 md:grid">
            <div class="col-span-5 font-medium text-gray-700">Payment Description</div>
            <div class="flex items-center col-span-3 font-medium text-gray-700">
              Amount (USD)
              <button class="ml-1 transition-transform hover:scale-110" @click="sortPendingPayments('amount')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    :d="pendingSortField === 'amount' && pendingSortDirection === 'desc'
                       ? 'M5 15l7-7 7 7'
                       : 'M19 9l-7 7-7-7'">
                  </path>
                </svg>
              </button>
            </div>
            <div class="flex items-center col-span-4 font-medium text-gray-700">
              Due Date
              <button class="ml-1 transition-transform hover:scale-110" @click="sortPendingPayments('dueDate')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    :d="pendingSortField === 'dueDate' && pendingSortDirection === 'desc'
                       ? 'M5 15l7-7 7 7'
                       : 'M19 9l-7 7-7-7'">
                  </path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Desktop Table Body -->
          <div class="hidden md:block">
            <!-- Payment Items -->
            <div
              v-for="payment in sortedPendingPayments"
              :key="payment.id"
              class="grid items-center grid-cols-12 px-4 py-4 border-b border-gray-200 table-row-hover pending-payment-row"
              :class="{ 'transaction-item-enter-active': isAnimating }"
            >
              <div class="flex items-center col-span-5">
                <input
                  type="checkbox"
                  class="w-4 h-4 mr-3 text-blue-600 transition-transform hover:scale-110"
                  :checked="payment.selected"
                  @click="togglePaymentSelection(payment)"
                >
                <div>
                  <span class="font-medium text-gray-600">{{ payment.id }}</span>
                  <span class="ml-2 text-gray-800">{{ payment.description }}</span>
                </div>
              </div>
              <div class="col-span-3 text-gray-800">{{ payment.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</div>
              <div class="col-span-4 text-gray-800">
                <div class="flex items-center justify-between">
                  <span>{{ formatDate(payment.dueDate) }}</span>
                  <span v-if="payment.dueInDays" class="flex items-center px-2 py-1 text-xs font-medium text-orange-800 bg-orange-100 rounded due-date-warning">
                    Due in {{ payment.dueInDays }} days
                    <svg class="w-4 h-4 ml-1 text-orange-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 100-16 8 8 0 000 16zm-1-7.59V6h2v6.59l3.7 3.7-1.41 1.41L12 14.41l-3.29 3.29-1.41-1.41L11 12.59z" clip-rule="evenodd"></path>
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile Table Body -->
          <div class="md:hidden payment-table-mobile">
            <!-- Payment Items -->
            <div
              v-for="payment in sortedPendingPayments"
              :key="payment.id"
              class="table-row p-4 mb-4 border-b border-gray-200"
            >
              <div class="flex items-center justify-between mobile-table-cell">
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    class="w-4 h-4 mr-3 text-blue-600"
                    :checked="payment.selected"
                    @click="togglePaymentSelection(payment)"
                  >
                  <span class="font-medium text-gray-600">{{ payment.id }}</span>
                </div>
              </div>
              <div class="mobile-table-cell">
                <span class="font-medium">Description</span>
                <span class="text-right">{{ payment.description }}</span>
              </div>
              <div class="mobile-table-cell">
                <span class="font-medium">Amount</span>
                <span class="font-bold text-right">{{ payment.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
              </div>
              <div class="mobile-table-cell">
                <span class="font-medium">Due Date</span>
                <div class="flex items-center justify-end">
                  <span>{{ formatDate(payment.dueDate) }}</span>
                </div>
              </div>
              <div v-if="payment.dueInDays" class="p-2 mt-2 rounded-md mobile-table-cell bg-orange-50">
                <div class="flex items-center justify-center text-sm text-orange-800">
                  <svg class="w-4 h-4 mr-1 text-orange-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 100-16 8 8 0 000 16zm-1-7.59V6h2v6.59l3.7 3.7-1.41 1.41L12 14.41l-3.29 3.29-1.41-1.41L11 12.59z" clip-rule="evenodd"></path>
                  </svg>
                  Due in {{ payment.dueInDays }} days
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Summary -->
        <div class="p-6 bg-white rounded-lg shadow-sm lg:w-1/4 payment-summary-card">
          <div class="flex items-center justify-between mb-2">
            <span class="text-gray-700">Balance Amount:</span>
            <span class="font-medium text-gray-900" :class="{ 'pulse-animation': isAnimating }">$ {{ selectedPaymentsTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
          </div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <span class="text-gray-700">VAT (5%)</span>
              <button class="ml-1 text-gray-400 transition-colors hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </button>
            </div>
            <span class="text-gray-500">$ {{ vatAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
          </div>
          <div class="pt-4 mt-2 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <span class="font-medium text-gray-900">Total</span>
                <span class="ml-1 text-sm text-gray-500">(incl. VAT)</span>
              </div>
              <span class="text-xl font-bold text-gray-900" :class="{ 'pulse-animation': isAnimating }">$ {{ totalWithVat.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
            </div>
          </div>
          <div class="mt-6">
            <button
              class="flex items-center justify-center w-full px-6 py-3 text-white transition-colors duration-200 bg-gray-800 rounded-md shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-75 disabled:cursor-not-allowed"
              @click="paySelected"
              :disabled="isProcessingPayment || selectedPaymentsTotal === 0"
            >
              <span v-if="!isProcessingPayment">Pay Now</span>
              <span v-else class="flex items-center">
                <svg class="w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
              </span>
              <svg v-if="!isProcessingPayment" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- All Transactions Section -->
    <div>
      <!-- Tabs -->
      <div class="mb-6 border-b border-gray-200">
        <div class="flex">
          <button
            class="relative px-6 py-4 text-base font-medium text-gray-900 transition-colors"
            @click="activeTransactionTab = 'all'"
          >
            All Transactions
            <span
              class="absolute bottom-0 left-0 w-full h-1 transition-all duration-300 bg-orange-500"
              :class="{ 'opacity-100': activeTransactionTab === 'all', 'opacity-0': activeTransactionTab !== 'all' }"
            ></span>
          </button>
          <button
            class="relative px-6 py-4 text-base font-medium text-gray-500 transition-colors hover:text-gray-700"
            @click="activeTransactionTab = 'invoices'"
          >
            Invoices
            <span
              class="absolute bottom-0 left-0 w-full h-1 transition-all duration-300 bg-orange-500"
              :class="{ 'opacity-100': activeTransactionTab === 'invoices', 'opacity-0': activeTransactionTab !== 'invoices' }"
            ></span>
          </button>
        </div>
      </div>

      <!-- Transaction Filter Buttons -->
      <div class="flex flex-col justify-between gap-4 mb-4 md:flex-row md:items-center">
        <div class="flex space-x-2 overflow-x-auto filter-buttons-mobile md:flex-wrap md:overflow-visible">
          <button
            class="px-4 py-2 text-sm font-medium text-gray-800 transition-colors duration-200 bg-gray-100 border border-gray-200 rounded-md whitespace-nowrap"
            :class="{ 'bg-gray-800 text-white': activeStatusFilter === 'all' }"
            @click="activeStatusFilter = 'all'"
          >
            All
          </button>
          <button
            class="px-4 py-2 text-sm font-medium text-gray-800 transition-colors duration-200 bg-gray-100 border border-gray-200 rounded-md whitespace-nowrap"
            :class="{ 'bg-gray-800 text-white': activeStatusFilter === 'paid' }"
            @click="activeStatusFilter = 'paid'"
          >
            <span class="flex items-center">
              <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
              Paid
            </span>
          </button>
          <button
            class="px-4 py-2 text-sm font-medium text-gray-800 transition-colors duration-200 bg-gray-100 border border-gray-200 rounded-md whitespace-nowrap"
            :class="{ 'bg-gray-800 text-white': activeStatusFilter === 'refunded' }"
            @click="activeStatusFilter = 'refunded'"
          >
            <span class="flex items-center">
              <span class="w-2 h-2 mr-2 bg-purple-500 rounded-full"></span>
              Refunded
            </span>
          </button>
          <button
            class="px-4 py-2 text-sm font-medium text-gray-800 transition-colors duration-200 bg-gray-100 border border-gray-200 rounded-md whitespace-nowrap"
            :class="{ 'bg-gray-800 text-white': activeStatusFilter === 'pending' }"
            @click="activeStatusFilter = 'pending'"
          >
            <span class="flex items-center">
              <span class="w-2 h-2 mr-2 bg-orange-500 rounded-full"></span>
              Pending
            </span>
          </button>
          <button
            class="px-4 py-2 text-sm font-medium text-gray-800 transition-colors duration-200 bg-gray-100 border border-gray-200 rounded-md whitespace-nowrap"
            :class="{ 'bg-gray-800 text-white': activeStatusFilter === 'declined' }"
            @click="activeStatusFilter = 'declined'"
          >
            <span class="flex items-center">
              <span class="w-2 h-2 mr-2 bg-red-500 rounded-full"></span>
              Declined
            </span>
          </button>
        </div>

        <div class="flex flex-col gap-2 md:flex-row md:items-center">
          <!-- Date Filter Dropdown -->
          <div class="relative inline-block text-left">
            <div>
              <button @click="isDateFilterOpen = !isDateFilterOpen" type="button" class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                {{ dateFilterLabel }}
                <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>

            <div v-if="isDateFilterOpen" class="absolute right-0 z-10 w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
              <div class="py-1" role="menu" aria-orientation="vertical">
                <button @click="setDateFilter('all')" class="block w-full px-4 py-2 text-sm text-left text-gray-700 transition-colors duration-200 hover:bg-gray-100" role="menuitem">All time</button>
                <button @click="setDateFilter('today')" class="block w-full px-4 py-2 text-sm text-left text-gray-700 transition-colors duration-200 hover:bg-gray-100" role="menuitem">Today</button>
                <button @click="setDateFilter('week')" class="block w-full px-4 py-2 text-sm text-left text-gray-700 transition-colors duration-200 hover:bg-gray-100" role="menuitem">This week</button>
                <button @click="setDateFilter('month')" class="block w-full px-4 py-2 text-sm text-left text-gray-700 transition-colors duration-200 hover:bg-gray-100" role="menuitem">This month</button>
                <button @click="setDateFilter('quarter')" class="block w-full px-4 py-2 text-sm text-left text-gray-700 transition-colors duration-200 hover:bg-gray-100" role="menuitem">This quarter</button>
                <button @click="setDateFilter('year')" class="block w-full px-4 py-2 text-sm text-left text-gray-700 transition-colors duration-200 hover:bg-gray-100" role="menuitem">This year</button>
                <div class="my-1 border-t border-gray-100"></div>
                <button @click="isCustomDateOpen = true; isDateFilterOpen = false" class="block w-full px-4 py-2 text-sm text-left text-gray-700 transition-colors duration-200 hover:bg-gray-100" role="menuitem">Custom range...</button>
              </div>
            </div>
          </div>

          <!-- Search Bar -->
          <div class="relative">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search by ID or description"
              class="w-full py-2 pl-10 pr-4 transition-all duration-200 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <div v-if="searchQuery" class="absolute inset-y-0 right-0 flex items-center pr-3">
              <button @click="searchQuery = ''" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Transaction Table -->
      <div class="overflow-hidden transition-shadow rounded-lg shadow-sm bg-gray-50 hover:shadow-md" :class="{ 'opacity-60': isLoadingTransactions }">
        <!-- Table Header -->
        <div class="hidden grid-cols-12 px-4 py-3 bg-gray-100 md:grid">
          <div class="col-span-4 font-medium text-gray-700">Payment Description</div>
          <div class="flex items-center col-span-2 font-medium text-gray-700">
            Amount (USD)
            <button class="ml-1 transition-transform hover:scale-110" @click="sortTransactions('amount')">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  :d="sortField === 'amount' && sortDirection === 'desc'
                     ? 'M5 15l7-7 7 7'
                     : 'M19 9l-7 7-7-7'">
                </path>
              </svg>
            </button>
          </div>
          <div class="flex items-center col-span-2 font-medium text-gray-700">
            Issued Date
            <button class="ml-1 transition-transform hover:scale-110" @click="sortTransactions('issuedDate')">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  :d="sortField === 'issuedDate' && sortDirection === 'desc'
                     ? 'M5 15l7-7 7 7'
                     : 'M19 9l-7 7-7-7'">
                </path>
              </svg>
            </button>
          </div>
          <div class="flex items-center col-span-2 font-medium text-gray-700">
            Due Date
            <button class="ml-1 transition-transform hover:scale-110" @click="sortTransactions('dueDate')">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  :d="sortField === 'dueDate' && sortDirection === 'desc'
                     ? 'M5 15l7-7 7 7'
                     : 'M19 9l-7 7-7-7'">
                </path>
              </svg>
            </button>
          </div>
          <div class="col-span-1 font-medium text-gray-700">Status</div>
          <div class="col-span-1 font-medium text-gray-700"></div>
        </div>

        <!-- Loading Indicator -->
        <div v-if="isLoadingTransactions" class="absolute inset-0 z-10 flex items-center justify-center bg-white bg-opacity-60">
          <svg class="w-10 h-10 text-gray-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <!-- Desktop Table Body -->
        <div v-if="paginatedTransactions.length > 0" class="hidden md:block">
          <!-- Transaction Items -->
          <div
            v-for="transaction in paginatedTransactions"
            :key="transaction.id"
            class="grid items-center grid-cols-12 px-4 py-4 border-b border-gray-200 table-row-hover"
            @click="showTransactionDetails(transaction)"
            :class="{ 'transaction-item-enter-active': isAnimating }"
          >
            <div class="col-span-4">
              <div>
                <span class="font-medium text-gray-600">{{ transaction.id }}</span>
                <span class="ml-2 text-gray-800">{{ transaction.description }}</span>
              </div>
            </div>
            <div class="col-span-2 text-gray-800">{{ transaction.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</div>
            <div class="col-span-2 text-gray-800">{{ formatDate(transaction.issuedDate) }}</div>
            <div class="col-span-2 text-gray-800">{{ formatDate(transaction.dueDate) }}</div>
            <div class="col-span-1">
              <span
                class="px-3 py-1 text-xs font-medium rounded-full payment-status-badge"
                :class="{
                  'bg-green-100 text-green-800': transaction.status === 'paid',
                  'bg-orange-100 text-orange-800': transaction.status === 'pending',
                  'bg-red-100 text-red-800': transaction.status === 'declined',
                  'bg-purple-100 text-purple-800': transaction.status === 'refunded'
                }"
              >
                {{ transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1) }}
              </span>
            </div>
            <div class="col-span-1 text-right">
              <button class="text-gray-500 transition-colors hover:text-gray-800" @click.stop="showTransactionDetails(transaction)">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile Table View -->
        <div v-if="paginatedTransactions.length > 0" class="md:hidden payment-table-mobile">
          <div
            v-for="transaction in paginatedTransactions"
            :key="transaction.id"
            class="table-row p-4 mb-4 border-b border-gray-200"
            @click="showTransactionDetails(transaction)"
          >
            <div class="mobile-table-cell">
              <span class="font-medium">Transaction ID</span>
              <span class="text-right">{{ transaction.id }}</span>
            </div>
            <div class="mobile-table-cell">
              <span class="font-medium">Description</span>
              <span class="text-right">{{ transaction.description }}</span>
            </div>
            <div class="mobile-table-cell">
              <span class="font-medium">Amount</span>
              <span class="font-bold text-right">{{ transaction.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
            </div>
            <div class="mobile-table-cell">
              <span class="font-medium">Issue Date</span>
              <span class="text-right">{{ formatDate(transaction.issuedDate) }}</span>
            </div>
            <div class="mobile-table-cell">
              <span class="font-medium">Due Date</span>
              <span class="text-right">{{ formatDate(transaction.dueDate) }}</span>
            </div>
            <div class="mobile-table-cell">
              <span class="font-medium">Status</span>
              <span
                class="px-3 py-1 text-xs font-medium rounded-full payment-status-badge"
                :class="{
                  'bg-green-100 text-green-800': transaction.status === 'paid',
                  'bg-orange-100 text-orange-800': transaction.status === 'pending',
                  'bg-red-100 text-red-800': transaction.status === 'declined',
                  'bg-purple-100 text-purple-800': transaction.status === 'refunded'
                }"
              >
                {{ transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1) }}
              </span>
            </div>
          </div>
        </div>

        <!-- No Results -->
        <div v-else class="p-8 text-center text-gray-500">
          No transactions matching your filters.
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
          <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ currentPageRange }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                <button
                  @click="prevPage"
                  class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 transition-colors duration-200 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50"
                  :disabled="currentPage === 1"
                  :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>

                <template v-for="page in totalPages" :key="page">
                  <button
                    v-if="page === currentPage || (page <= 2 || page >= totalPages - 1 || Math.abs(page - currentPage) <= 1)"
                    @click="goToPage(page)"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium transition-colors duration-200 bg-white border border-gray-300 hover:bg-gray-50"
                    :class="{
                      'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': page === currentPage,
                      'text-gray-500': page !== currentPage
                    }"
                  >
                    {{ page }}
                  </button>

                  <span
                    v-else-if="(page === 3 && currentPage > 3) || (page === totalPages - 2 && currentPage < totalPages - 2)"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300"
                  >
                    ...
                  </span>
                </template>

                <button
                  @click="nextPage"
                  class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 transition-colors duration-200 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50"
                  :disabled="currentPage === totalPages"
                  :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                >
                  <span class="sr-only">Next</span>
                  <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>

          <!-- Mobile Pagination -->
          <div class="flex items-center justify-between w-full sm:hidden">
            <button
              @click="prevPage"
              class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
              :disabled="currentPage === 1"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
            >
              Previous
            </button>
            <span class="text-sm text-gray-500">
              Page {{ currentPage }} of {{ totalPages }}
            </span>
            <button
              @click="nextPage"
              class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
              :disabled="currentPage === totalPages"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Transaction Detail Modal -->
      <div v-if="showTransactionModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Background overlay -->
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" @click="closeTransactionModal"></div>

          <!-- Modal panel -->
          <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                    Transaction Details
                  </h3>

                  <div class="mt-4 space-y-3" v-if="selectedTransaction">
                    <div class="flex justify-between pb-2 border-b">
                      <span class="text-gray-600">Transaction ID:</span>
                      <span class="font-medium">{{ selectedTransaction.id }}</span>
                    </div>

                    <div class="flex justify-between pb-2 border-b">
                      <span class="text-gray-600">Description:</span>
                      <span>{{ selectedTransaction.description }}</span>
                    </div>

                    <div class="flex justify-between pb-2 border-b">
                      <span class="text-gray-600">Amount:</span>
                      <span class="font-medium">{{ formatCurrency(selectedTransaction.amount) }}</span>
                    </div>

                    <div class="flex justify-between pb-2 border-b">
                      <span class="text-gray-600">Status:</span>
                      <span
                        class="px-2 py-1 text-xs font-medium rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': selectedTransaction.status === 'paid',
                          'bg-orange-100 text-orange-800': selectedTransaction.status === 'pending',
                          'bg-red-100 text-red-800': selectedTransaction.status === 'declined',
                          'bg-purple-100 text-purple-800': selectedTransaction.status === 'refunded'
                        }"
                      >
                        {{ selectedTransaction.status.charAt(0).toUpperCase() + selectedTransaction.status.slice(1) }}
                      </span>
                    </div>

                    <div class="flex justify-between pb-2 border-b">
                      <span class="text-gray-600">Issued Date:</span>
                      <span>{{ formatDate(selectedTransaction.issuedDate) }}</span>
                    </div>

                    <div class="flex justify-between pb-2 border-b">
                      <span class="text-gray-600">Due Date:</span>
                      <span>{{ formatDate(selectedTransaction.dueDate) }}</span>
                    </div>

                    <div class="flex justify-between" v-if="selectedTransaction.status === 'paid'">
                      <span class="text-gray-600">Payment Date:</span>
                      <span>{{ formatDate(selectedTransaction.dueDate) }}</span>
                    </div>

                    <div v-if="selectedTransaction.status === 'pending'" class="p-3 mt-4 rounded-md bg-orange-50">
                      <p class="text-sm font-medium text-orange-800">This payment is pending.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                v-if="selectedTransaction && selectedTransaction.status === 'pending'"
                type="button"
                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors duration-200 bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Pay Now
              </button>
              <button
                type="button"
                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                @click="closeTransactionModal"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Custom Date Range Modal -->
      <div v-if="isCustomDateOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Background overlay -->
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" @click="isCustomDateOpen = false"></div>

          <!-- Modal panel -->
          <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                    Select Date Range
                  </h3>

                  <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                    <div>
                      <label for="start-date" class="block text-sm font-medium text-gray-700">Start Date</label>
                      <input
                        type="date"
                        id="start-date"
                        v-model="customDateRange.start"
                        class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                    </div>

                    <div>
                      <label for="end-date" class="block text-sm font-medium text-gray-700">End Date</label>
                      <input
                        type="date"
                        id="end-date"
                        v-model="customDateRange.end"
                        class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors duration-200 bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                @click="applyCustomDateRange"
              >
                Apply
              </button>
              <button
                type="button"
                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                @click="isCustomDateOpen = false"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll To Top Button -->
    <div
      v-show="showScrollTopButton"
      @click="scrollToTop"
      class="fixed z-50 p-2 text-white transition-all duration-300 transform bg-gray-800 rounded-full shadow-lg cursor-pointer bottom-6 right-6 hover:bg-gray-700 hover:scale-110"
      :class="{ 'translate-y-0 opacity-100': showScrollTopButton, 'translate-y-10 opacity-0': !showScrollTopButton }"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
      </svg>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Payment',
  props: ['page_data', 'page_title'],
  data() {
    return {
      activeTransactionTab: 'all', // 'all' or 'invoices'
      activeStatusFilter: 'all', // 'all', 'paid', 'refunded', 'pending', 'declined'
      searchQuery: '',
      isProcessingPayment: false,
      isProcessingAllPayment: false,
      isAddingFunds: false,
      sortField: null,
      sortDirection: 'asc',
      pendingSortField: null,
      pendingSortDirection: 'asc',
      // Pagination data
      currentPage: 1,
      itemsPerPage: 10,
      totalPages: 1,
      // Transaction detail modal
      showTransactionModal: false,
      selectedTransaction: null,
      // Animation states
      isAnimating: false,
      // Loading state for data fetching
      isLoadingTransactions: false,
      // Date filter options
      dateFilter: 'all',
      isDateFilterOpen: false,
      isCustomDateOpen: false,
      customDateRange: {
        start: '',
        end: '',
      },
      // Scroll to top button
      showScrollTopButton: false,
      // Date format preferences
      dateFormat: 'DD MMM YYYY',
      timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
      wallet: {
        balance: 500000.00,
      },
      pendingPayment: {
        total: 1650.00
      },
      pendingPayments: [
        {
          id: '25-004',
          description: 'Contract Renewal 2025',
          amount: 200.00,
          dueDate: '27 Mar 2025',
          dueInDays: 2,
          selected: true
        },
        {
          id: '25-003',
          description: 'Corporate License Renewal 2025',
          amount: 150.00,
          dueDate: '29 Mar 2025',
          dueInDays: 4,
          selected: true
        },
        {
          id: '25-002',
          description: 'Flight Ticket to Dubai',
          amount: 750.00,
          dueDate: '01 May 2025',
          dueInDays: 7,
          selected: true
        },
        {
          id: '25-001',
          description: 'Business Cards Invoice',
          amount: 550.00,
          dueDate: '10 May 2025',
          selected: false
        }
      ],
      transactions: [
        {
          id: '24-006',
          description: 'Bookkeeping',
          amount: 4000.00,
          issuedDate: '05 Nov 2024',
          dueDate: '12 Nov 2024',
          status: 'paid'
        },
        {
          id: '24-005',
          description: 'Social Media Designs',
          amount: 1500.00,
          issuedDate: '01 Aug 2024',
          dueDate: '08 Aug 2024',
          status: 'pending'
        },
        {
          id: '24-004',
          description: 'Event management',
          amount: 6000.00,
          issuedDate: '14 Jul 2024',
          dueDate: '21 Jul 2024',
          status: 'declined'
        },
        {
          id: '24-003',
          description: 'Seo Management',
          amount: 1000.00,
          issuedDate: '27 May 2024',
          dueDate: '03 Jun 2024',
          status: 'refunded'
        },
        {
          id: '24-002',
          description: 'Contract Renewal 2024',
          amount: 3000.00,
          issuedDate: '13 Apr 2024',
          dueDate: '20 Apr 2024',
          status: 'paid'
        },
        {
          id: '24-001',
          description: 'New Website Revamp',
          amount: 5000.00,
          issuedDate: '10 Mar 2024',
          dueDate: '17 Mar 2024',
          status: 'pending'
        }
      ],
      isDateFilterOpen: false,
      isCustomDateOpen: false,
      dateFilterLabel: 'All time',
      customDateRange: {
        start: null,
        end: null
      },
      showScrollTopButton: false
    };
  },
  computed: {
    // Date filter label display
    dateFilterLabel() {
      switch (this.dateFilter) {
        case 'today':
          return 'Today';
        case 'week':
          return 'This week';
        case 'month':
          return 'This month';
        case 'quarter':
          return 'This quarter';
        case 'year':
          return 'This year';
        case 'custom':
          const start = new Date(this.customDateRange.start).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
          const end = new Date(this.customDateRange.end).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
          return `${start} - ${end}`;
        default:
          return 'All time';
      }
    },

    // Sort pending payments
    sortedPendingPayments() {
      let sorted = [...this.pendingPayments];

      // Sort pending payments if sort field is set
      if (this.pendingSortField) {
        sorted.sort((a, b) => {
          const aValue = a[this.pendingSortField];
          const bValue = b[this.pendingSortField];

          // Special handling for dates
          if (this.pendingSortField === 'dueDate') {
            const dateA = new Date(aValue);
            const dateB = new Date(bValue);
            return this.pendingSortDirection === 'asc' ? dateA - dateB : dateB - dateA;
          }

          // Regular comparison for other fields
          let comparison = 0;
          if (aValue < bValue) {
            comparison = -1;
          } else if (aValue > bValue) {
            comparison = 1;
          }

          return this.pendingSortDirection === 'asc' ? comparison : -comparison;
        });
      }

      return sorted;
    },

    // Calculate total for selected pending payments
    selectedPaymentsTotal() {
      return this.pendingPayments
        .filter(payment => payment.selected)
        .reduce((sum, payment) => sum + payment.amount, 0);
    },

    // Calculate VAT (5%)
    vatAmount() {
      return this.selectedPaymentsTotal * 0.05;
    },

    // Calculate total including VAT
    totalWithVat() {
      return this.selectedPaymentsTotal + this.vatAmount;
    },

    // Filter transactions based on selected status, date range and search query
    filteredTransactions() {
      let filtered = [...this.transactions];

      // Filter by status
      if (this.activeStatusFilter !== 'all') {
        filtered = filtered.filter(tx => tx.status === this.activeStatusFilter);
      }

      // Filter by date range
      if (this.dateFilter !== 'all') {
        const today = new Date();
        const startOfDay = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        let startDate, endDate;

        switch (this.dateFilter) {
          case 'today':
            startDate = startOfDay;
            endDate = new Date(startOfDay);
            endDate.setDate(endDate.getDate() + 1);
            break;

          case 'week':
            startDate = new Date(startOfDay);
            startDate.setDate(startDate.getDate() - startDate.getDay());
            endDate = new Date(startDate);
            endDate.setDate(endDate.getDate() + 7);
            break;

          case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;

          case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
            break;

          case 'year':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;

          case 'custom':
            startDate = new Date(this.customDateRange.start);
            endDate = new Date(this.customDateRange.end);
            endDate.setDate(endDate.getDate() + 1); // Include the end date
            break;
        }

        if (startDate && endDate) {
          filtered = filtered.filter(tx => {
            const txDate = new Date(tx.issuedDate);
            return txDate >= startDate && txDate < endDate;
          });
        }
      }

      // Filter by search query
      if (this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(tx =>
          tx.id.toLowerCase().includes(query) ||
          tx.description.toLowerCase().includes(query)
        );
      }

      // Sort filtered transactions
      if (this.sortField) {
        filtered.sort((a, b) => {
          const aValue = a[this.sortField];
          const bValue = b[this.sortField];

          // Special handling for dates
          if (this.sortField === 'issuedDate' || this.sortField === 'dueDate') {
            const dateA = new Date(aValue);
            const dateB = new Date(bValue);
            return this.sortDirection === 'asc' ? dateA - dateB : dateB - dateA;
          }

          // Regular comparison for other fields
          let comparison = 0;
          if (aValue < bValue) {
            comparison = -1;
          } else if (aValue > bValue) {
            comparison = 1;
          }

          return this.sortDirection === 'asc' ? comparison : -comparison;
        });
      }

      // Calculate total pages
      this.totalPages = Math.ceil(filtered.length / this.itemsPerPage);

      return filtered;
    },

    // Get paginated transactions for current page
    paginatedTransactions() {
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      const endIndex = startIndex + this.itemsPerPage;
      return this.filteredTransactions.slice(startIndex, endIndex);
    },

    // Format the current date range being displayed
    currentPageRange() {
      const startItem = (this.currentPage - 1) * this.itemsPerPage + 1;
      const endItem = Math.min(startItem + this.itemsPerPage - 1, this.filteredTransactions.length);
      return `${startItem}-${endItem} of ${this.filteredTransactions.length}`;
    },

    // Get formatted dates with proper timezone support
    formattedDates() {
      return this.paginatedTransactions.map(tx => {
        return {
          ...tx,
          formattedIssuedDate: this.formatDate(tx.issuedDate),
          formattedDueDate: this.formatDate(tx.dueDate)
        };
      });
    }
  },
  methods: {
    // Date filter methods
    setDateFilter(filter) {
      this.dateFilter = filter;
      this.isDateFilterOpen = false;
      this.currentPage = 1; // Reset to first page
      this.triggerAnimation();
    },

    applyCustomDateRange() {
      // Validate date range
      if (!this.customDateRange.start || !this.customDateRange.end) {
        alert('Please select both start and end dates');
        return;
      }

      const startDate = new Date(this.customDateRange.start);
      const endDate = new Date(this.customDateRange.end);

      if (startDate > endDate) {
        alert('Start date cannot be after end date');
        return;
      }

      this.dateFilter = 'custom';
      this.isCustomDateOpen = false;
      this.currentPage = 1; // Reset to first page
      this.triggerAnimation();
    },

    formatCurrency(value) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
      }).format(value);
    },

    // Format date string with timezone support
    formatDate(dateString) {
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
          day: 'numeric',
          month: 'short',
          year: 'numeric',
          timeZone: this.timezone
        });
      } catch (error) {
        console.error('Date formatting error:', error);
        return dateString; // Return original string if formatting fails
      }
    },

    // Calculate days remaining until due date
    calculateDaysRemaining(dueDateStr) {
      try {
        const dueDate = new Date(dueDateStr);
        const today = new Date();
        const diffTime = dueDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays > 0 ? diffDays : 0;
      } catch (error) {
        console.error('Date calculation error:', error);
        return 0;
      }
    },

    togglePaymentSelection(payment) {
      payment.selected = !payment.selected;
      // Add animation effect when selecting
      this.triggerAnimation();
    },

    // Trigger animation effects
    triggerAnimation() {
      this.isAnimating = true;
      setTimeout(() => {
        this.isAnimating = false;
      }, 300);
    },

    sortPendingPayments(field) {
      // If clicking the same field, toggle direction
      if (this.pendingSortField === field) {
        this.pendingSortDirection = this.pendingSortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        // If clicking a new field, set it as the sort field and default to ascending
        this.pendingSortField = field;
        this.pendingSortDirection = 'asc';
      }

      // Add animation effect when sorting
      this.triggerAnimation();
    },

    sortTransactions(field) {
      // If clicking the same field, toggle direction
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        // If clicking a new field, set it as the sort field and default to ascending
        this.sortField = field;
        this.sortDirection = 'asc';
      }

      // Reset to first page when sorting
      this.currentPage = 1;

      // Add animation effect when sorting
      this.triggerAnimation();
    },

    // Pagination methods
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
        this.triggerAnimation();
      }
    },

    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.triggerAnimation();
      }
    },

    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.triggerAnimation();
      }
    },

    // Transaction detail modal methods
    showTransactionDetails(transaction) {
      this.selectedTransaction = transaction;
      this.showTransactionModal = true;
    },

    closeTransactionModal() {
      this.showTransactionModal = false;
      setTimeout(() => {
        this.selectedTransaction = null;
      }, 300);
    },

    // Refresh transactions data
    refreshTransactions() {
      this.isLoadingTransactions = true;

      // Simulate API call delay
      setTimeout(() => {
        // Here you would normally fetch fresh data from the API
        console.log('Refreshing transaction data');

        // Reset loading state
        this.isLoadingTransactions = false;
      }, 1000);
    },

    paySelected() {
      // Set loading state
      this.isProcessingPayment = true;

      // Simulate API call delay
      setTimeout(() => {
        // Handle payment processing logic here
        alert(`Processing payment for ${this.formatCurrency(this.totalWithVat)}`);

        // Reset loading state
        this.isProcessingPayment = false;
      }, 1500);
    },

    payAll() {
      // Set loading state
      this.isProcessingAllPayment = true;

      // Simulate API call delay
      setTimeout(() => {
        // Handle pay all logic here
        alert(`Processing payment for all pending payments: ${this.formatCurrency(this.pendingPayment.total)}`);

        // Reset loading state
        this.isProcessingAllPayment = false;
      }, 1500);
    },

    addFunds() {
      // Set loading state
      this.isAddingFunds = true;

      // Simulate API call delay
      setTimeout(() => {
        // Handle add funds logic here
        alert('Opening add funds dialog');

        // Reset loading state
        this.isAddingFunds = false;
      }, 1000);
    },

    // Scroll to top method
    scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    },

    handleScroll() {
      this.showScrollTopButton = window.scrollY > 500;
    },
  },
  mounted() {
    // Add scroll event listener for scroll to top button
    window.addEventListener('scroll', this.handleScroll);

    // Initialize date range with current month if not set
    if (!this.customDateRange.start || !this.customDateRange.end) {
      const today = new Date();
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

      this.customDateRange.start = firstDay.toISOString().split('T')[0];
      this.customDateRange.end = lastDay.toISOString().split('T')[0];
    }
  },
  beforeUnmount() {
    // Remove scroll event listener when component is destroyed
    window.removeEventListener('scroll', this.handleScroll);
  }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

.payment-status-badge {
  transition: all 0.3s ease;
}
.payment-status-badge:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.due-date-warning {
  display: flex;
  align-items: center;
  transition: all 0.2s ease;
}
.due-date-warning:hover {
  transform: translateX(2px);
}

.payment-summary-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.payment-summary-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.table-row-hover {
  transition: all 0.2s ease;
  cursor: pointer;
}

.table-row-hover:hover {
  background-color: rgba(243, 244, 246, 0.8);
  transform: translateX(2px);
}

/* Button animations */
button {
  transition: all 0.2s ease;
}

button:active:not(:disabled) {
  transform: scale(0.97);
}

.tab-button-active {
  position: relative;
}

.tab-button-active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #f97316;
  transform-origin: center;
  animation: scaleIn 0.2s ease-out forwards;
}

@keyframes scaleIn {
  from { transform: scaleX(0); }
  to { transform: scaleX(1); }
}

@keyframes slideInRight {
  from { transform: translateX(-10px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.transaction-item-enter-active {
  animation: slideInRight 0.3s ease-out;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.pulse-animation {
  animation: pulse 2s infinite;
}

@media (max-width: 768px) {
  .grid-cols-12 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 0.5rem;
  }

  .col-span-1, .col-span-2, .col-span-3, .col-span-4, .col-span-5 {
    grid-column: span  1 / span 1;
  }

  .hidden-mobile {
    display: none;
  }

  .flex-col {
    flex-direction: column;
  }

  .lg\:w-3\/4, .lg\:w-1\/4 {
    width: 100%;
  }

  .payment-card {
    margin-bottom: 1rem;
  }

  .pending-payment-row {
    display: flex;
    flex-direction: column;
    padding: 0.75rem 0;
  }

  .pending-payment-row > div {
    padding: 0.25rem 0;
  }

  .due-date-warning {
    margin-top: 0.5rem;
  }

  /* Additional mobile styling */
  .payment-table-mobile {
    display: block;
  }

  .payment-table-mobile .table-header {
    display: none;
  }

  .payment-table-mobile .table-row {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    background-color: white;
  }

  .payment-table-mobile .mobile-table-cell {
    display: flex;
    justify-content: space-between;
       padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
  }

  .payment-table-mobile .mobile-table-cell:last-child {
    border-bottom: none;
  }

  .filter-buttons-mobile {
    display: flex;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 0.5rem;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
  }

  .filter-buttons-mobile::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .dark-mode-compatible {
    --text-color: #f3f4f6;
    --bg-color: #1f2937;
    --card-bg-color: #374151;
    --border-color: #4b5563;
  }
}
</style>
