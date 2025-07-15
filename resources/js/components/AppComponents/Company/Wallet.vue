<template>
    <div class="wallet-container">
        <div class="wallet-header">
            <h1>My Wallet</h1>
            <div class="wallet-actions">
                <button class="btn btn-primary">Add Funds</button>
                <button class="btn btn-outline">Withdraw</button>
            </div>
        </div>

        <div class="wallet-balance-card">
            <div class="balance-info">
                <h2>Available Balance</h2>
                <div class="balance-amount">
                    <span class="currency">AED</span>
                    <span class="amount">{{ formatCurrency(walletBalance) }}</span>
                </div>
                <div class="balance-details">
                    <div class="detail-item">
                        <span class="label">Pending</span>
                        <span class="value">AED {{ formatCurrency(pendingAmount) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Total Balance</span>
                        <span class="value">AED {{ formatCurrency(walletBalance + pendingAmount) }}</span>
                    </div>
                </div>
            </div>
            <div class="balance-chart">
                <div class="chart-placeholder">
                    <!-- Chart would go here -->
                    <div class="chart-circle">
                        <div class="inner-circle"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="wallet-section transaction-history">
            <div class="section-header">
                <h2>Transaction History</h2>
                <div class="section-actions">
                    <div class="search-container">
                        <input type="text" placeholder="Search transactions" v-model="searchQuery" class="search-input">
                    </div>
                    <div class="filter-container">
                        <select v-model="filterType" class="filter-select">
                            <option value="all">All Transactions</option>
                            <option value="deposit">Deposits</option>
                            <option value="withdrawal">Withdrawals</option>
                            <option value="payment">Payments</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="transaction-list">
                <div v-if="filteredTransactions.length === 0" class="no-transactions">
                    <p>No transactions found</p>
                </div>
                <div v-else class="transaction-table">
                    <div class="table-header">
                        <div class="header-cell date">Date</div>
                        <div class="header-cell description">Description</div>
                        <div class="header-cell type">Type</div>
                        <div class="header-cell amount">Amount</div>
                        <div class="header-cell status">Status</div>
                    </div>
                    <div v-for="(transaction, index) in filteredTransactions" :key="index" class="transaction-row">
                        <div class="cell date">{{ formatDate(transaction.date) }}</div>
                        <div class="cell description">{{ transaction.description }}</div>
                        <div class="cell type">
                            <span :class="'type-badge ' + transaction.type">{{ transaction.type }}</span>
                        </div>
                        <div class="cell amount" :class="transaction.type === 'deposit' ? 'positive' : (transaction.type === 'withdrawal' ? 'negative' : '')">
                            {{ transaction.type === 'deposit' ? '+' : (transaction.type === 'withdrawal' ? '-' : '') }}
                            AED {{ formatCurrency(transaction.amount) }}
                        </div>
                        <div class="cell status">
                            <span :class="'status-badge ' + transaction.status">{{ transaction.status }}</span>
                        </div>
                    </div>
                </div>
                <div class="pagination">
                    <button :disabled="currentPage === 1" @click="currentPage--" class="pagination-btn">
                        Previous
                    </button>
                    <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
                    <button :disabled="currentPage === totalPages" @click="currentPage++" class="pagination-btn">
                        Next
                    </button>
                </div>
            </div>
        </div>

        <div class="wallet-section payment-methods">
            <div class="section-header">
                <h2>Payment Methods</h2>
                <button class="btn btn-sm btn-outline">+ Add New</button>
            </div>

            <div class="payment-methods-list">
                <div v-for="(method, index) in paymentMethods" :key="index" class="payment-method-card">
                    <div class="card-icon" :class="method.type">
                        <i :class="'icon-' + method.type"></i>
                    </div>
                    <div class="card-details">
                        <h3>{{ method.name }}</h3>
                        <p>{{ method.type === 'card' ? '•••• •••• •••• ' + method.lastFour : method.details }}</p>
                    </div>
                    <div class="card-actions">
                        <button class="btn btn-icon btn-sm" title="Edit">
                            <i class="icon-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-sm" title="Delete">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                </div>

                <div v-if="paymentMethods.length === 0" class="no-payment-methods">
                    <p>No payment methods added yet</p>
                    <button class="btn btn-primary">Add Payment Method</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Wallet',
        data() {
            return {
                walletBalance: 12500.75,
                pendingAmount: 750.25,
                searchQuery: '',
                filterType: 'all',
                currentPage: 1,
                itemsPerPage: 5,
                transactions: [
                    {
                        date: new Date('2025-06-10T14:30:00'),
                        description: 'Salary Deposit',
                        type: 'deposit',
                        amount: 5000.00,
                        status: 'completed'
                    },
                    {
                        date: new Date('2025-06-08T09:15:00'),
                        description: 'Office Supplies Payment',
                        type: 'payment',
                        amount: 250.50,
                        status: 'completed'
                    },
                    {
                        date: new Date('2025-06-05T16:45:00'),
                        description: 'Bank Transfer',
                        type: 'withdrawal',
                        amount: 1000.00,
                        status: 'completed'
                    },
                    {
                        date: new Date('2025-06-03T11:20:00'),
                        description: 'Client Payment',
                        type: 'deposit',
                        amount: 3500.00,
                        status: 'completed'
                    },
                    {
                        date: new Date('2025-05-28T13:10:00'),
                        description: 'Service Subscription',
                        type: 'payment',
                        amount: 99.99,
                        status: 'completed'
                    },
                    {
                        date: new Date('2025-05-25T10:30:00'),
                        description: 'Vendor Payment',
                        type: 'payment',
                        amount: 350.75,
                        status: 'pending'
                    },
                    {
                        date: new Date('2025-05-20T15:45:00'),
                        description: 'Client Refund',
                        type: 'withdrawal',
                        amount: 450.00,
                        status: 'processing'
                    }
                ],
                paymentMethods: [
                    {
                        name: 'Visa Debit Card',
                        type: 'card',
                        lastFour: '4321',
                        isDefault: true
                    },
                    {
                        name: 'Bank Account',
                        type: 'bank',
                        details: 'Emirates NBD ****3456',
                        isDefault: false
                    }
                ]
            }
        },
        computed: {
            filteredTransactions() {
                let filtered = this.transactions;

                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(transaction =>
                        transaction.description.toLowerCase().includes(query) ||
                        transaction.type.toLowerCase().includes(query) ||
                        transaction.status.toLowerCase().includes(query)
                    );
                }

                if (this.filterType !== 'all') {
                    filtered = filtered.filter(transaction => transaction.type === this.filterType);
                }

                // Apply pagination
                const startIndex = (this.currentPage - 1) * this.itemsPerPage;
                const endIndex = startIndex + this.itemsPerPage;

                return filtered.slice(startIndex, endIndex);
            },
            totalTransactions() {
                let filtered = this.transactions;

                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(transaction =>
                        transaction.description.toLowerCase().includes(query) ||
                        transaction.type.toLowerCase().includes(query) ||
                        transaction.status.toLowerCase().includes(query)
                    );
                }

                if (this.filterType !== 'all') {
                    filtered = filtered.filter(transaction => transaction.type === this.filterType);
                }

                return filtered.length;
            },
            totalPages() {
                return Math.ceil(this.totalTransactions / this.itemsPerPage);
            }
        },
        methods: {
            formatCurrency(value) {
                return value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            },
            formatDate(date) {
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .wallet-container {
        width: 100%;
        margin: 0 auto;
        padding: 2rem;
        color: #333;
        font-family: 'Roboto', Arial, sans-serif;
    }

    .wallet-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;

        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .wallet-actions {
            display: flex;
            gap: 1rem;
        }
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;

        &.btn-primary {
            background-color: #4267B2;
            color: white;

            &:hover {
                background-color: #3b5998;
            }
        }

        &.btn-outline {
            background-color: transparent;
            color: #4267B2;
            border: 1px solid #4267B2;

            &:hover {
                background-color: rgba(66, 103, 178, 0.1);
            }
        }

        &.btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        &.btn-icon {
            padding: 0.5rem;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }

    .wallet-balance-card {
        background: linear-gradient(135deg, #4267B2, #3b5998);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 10px 20px rgba(66, 103, 178, 0.2);
        display: flex;
        justify-content: space-between;

        .balance-info {
            flex: 1;

            h2 {
                font-size: 1.2rem;
                font-weight: 400;
                margin-top: 0;
                margin-bottom: 0.5rem;
                opacity: 0.9;
            }

            .balance-amount {
                margin-bottom: 1.5rem;

                .currency {
                    font-size: 1.5rem;
                    font-weight: 500;
                    margin-right: 0.5rem;
                }

                .amount {
                    font-size: 3rem;
                    font-weight: 700;
                }
            }

            .balance-details {
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                padding: 1rem;

                .detail-item {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 0.5rem;

                    &:last-child {
                        margin-bottom: 0;
                    }

                    .label {
                        opacity: 0.8;
                    }

                    .value {
                        font-weight: 500;
                    }
                }
            }
        }

        .balance-chart {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 180px;

            .chart-placeholder {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;

                .chart-circle {
                    width: 140px;
                    height: 140px;
                    border-radius: 50%;
                    border: 10px solid rgba(255, 255, 255, 0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;

                    &:before {
                        content: '';
                        position: absolute;
                        top: -10px;
                        left: -10px;
                        right: -10px;
                        bottom: -10px;
                        border-radius: 50%;
                        border: 10px solid rgba(255, 255, 255, 0.8);
                        border-right-color: transparent;
                        border-bottom-color: transparent;
                        transform: rotate(45deg);
                    }

                    .inner-circle {
                        width: 90px;
                        height: 90px;
                        border-radius: 50%;
                        background-color: rgba(255, 255, 255, 0.15);
                    }
                }
            }
        }
    }

    .wallet-section {
        background-color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;

            h2 {
                font-size: 1.5rem;
                font-weight: 600;
                margin: 0;
                color: #333;
            }

            .section-actions {
                display: flex;
                gap: 1rem;
                align-items: center;
            }
        }
    }

    .search-container {
        .search-input {
            padding: 0.6rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            width: 250px;
            transition: all 0.3s;

            &:focus {
                outline: none;
                border-color: #4267B2;
                box-shadow: 0 0 0 2px rgba(66, 103, 178, 0.2);
            }
        }
    }

    .filter-container {
        .filter-select {
            padding: 0.6rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: white;
            min-width: 160px;
            cursor: pointer;

            &:focus {
                outline: none;
                border-color: #4267B2;
            }
        }
    }

    .transaction-table {
        border: 1px solid #eaeaea;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1rem;

        .table-header {
            display: flex;
            background-color: #f8f9fa;
            padding: 1rem;
            font-weight: 600;
            border-bottom: 1px solid #eaeaea;
        }

        .transaction-row {
            display: flex;
            padding: 1rem;
            border-bottom: 1px solid #eaeaea;
            transition: background-color 0.2s;

            &:last-child {
                border-bottom: none;
            }

            &:hover {
                background-color: #f8f9fa;
            }
        }

        .header-cell, .cell {
            &.date {
                width: 15%;
            }
            &.description {
                width: 30%;
            }
            &.type {
                width: 15%;
            }
            &.amount {
                width: 20%;
                text-align: right;
                font-weight: 500;
            }
            &.status {
                width: 20%;
                text-align: center;
            }
        }

        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }

        .type-badge, .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            display: inline-block;
            text-transform: capitalize;
        }

        .type-badge {
            &.deposit {
                background-color: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }
            &.withdrawal {
                background-color: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }
            &.payment {
                background-color: rgba(108, 117, 125, 0.1);
                color: #6c757d;
            }
        }

        .status-badge {
            &.completed {
                background-color: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }
            &.pending {
                background-color: rgba(255, 193, 7, 0.1);
                color: #ffc107;
            }
            &.processing {
                background-color: rgba(0, 123, 255, 0.1);
                color: #007bff;
            }
            &.failed {
                background-color: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }
        }
    }

    .no-transactions, .no-payment-methods {
        text-align: center;
        padding: 3rem;
        color: #6c757d;

        p {
            margin-bottom: 1rem;
        }
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 1.5rem;

        .pagination-btn {
            padding: 0.5rem 1rem;
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;

            &:hover:not(:disabled) {
                background-color: #f8f9fa;
                border-color: #d0d0d0;
            }

            &:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        }

        .page-info {
            margin: 0 1.5rem;
            color: #6c757d;
        }
    }

    .payment-methods-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .payment-method-card {
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        background-color: white;
        transition: box-shadow 0.3s, transform 0.3s;

        &:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;

            &.card {
                background-color: #4267B2;
            }

            &.bank {
                background-color: #28a745;
            }
        }

        .card-details {
            flex: 1;

            h3 {
                font-size: 1.1rem;
                margin: 0 0 0.25rem 0;
                font-weight: 500;
            }

            p {
                margin: 0;
                color: #6c757d;
                font-size: 0.9rem;
            }
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
        }
    }

    // Icons (simulated)
    [class^="icon-"] {
        display: inline-block;
        width: 1em;
        height: 1em;
        position: relative;

        &:before, &:after {
            content: '';
            position: absolute;
        }
    }

    .icon-card:before {
        width: 0.8em;
        height: 0.5em;
        border: 0.1em solid currentColor;
        border-radius: 0.1em;
        left: 0.1em;
        top: 0.25em;
    }

    .icon-bank:before {
        width: 0.8em;
        height: 0.4em;
        border: 0.1em solid currentColor;
        border-radius: 0.1em;
        left: 0.1em;
        top: 0.4em;
    }

    .icon-bank:after {
        width: 0.6em;
        height: 0.3em;
        border: 0.1em solid currentColor;
        border-bottom: none;
        border-radius: 0.1em 0.1em 0 0;
        left: 0.2em;
        top: 0.1em;
    }

    .icon-edit:before {
        width: 0.7em;
        height: 0.7em;
        border-right: 0.1em solid currentColor;
        transform: rotate(45deg);
        left: 0.15em;
        top: 0.15em;
    }

    .icon-trash:before {
        width: 0.6em;
        height: 0.7em;
        border: 0.1em solid currentColor;
        border-top: none;
        border-radius: 0 0 0.1em 0.1em;
        left: 0.2em;
        top: 0.2em;
    }

    .icon-trash:after {
        width: 0.4em;
        height: 0.1em;
        background-color: currentColor;
        left: 0.3em;
        top: 0.15em;
        box-shadow: 0 -0.1em 0 0 currentColor;
    }

    @media (max-width: 768px) {
        .wallet-container {
            padding: 1rem;
        }

        .wallet-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .wallet-balance-card {
            flex-direction: column;
            gap: 2rem;
        }

        .balance-chart {
            width: 100% !important;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .section-actions {
            flex-direction: column;
            width: 100%;
        }

        .search-container, .filter-container {
            width: 100%;
        }

        .search-input, .filter-select {
            width: 100% !important;
        }

        .transaction-table {
            overflow-x: auto;
        }

        .table-header, .transaction-row {
            min-width: 800px;
        }

        .payment-methods-list {
            grid-template-columns: 1fr;
        }
    }
</style>
