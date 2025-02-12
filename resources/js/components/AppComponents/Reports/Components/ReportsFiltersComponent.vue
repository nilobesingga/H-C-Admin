<template>
    <div class="pb-4 pt-4">
        <div class="flex items-center justify-between">
            <!-- Date Range Picker Text on the Left -->
            <div class="flex flex-col justify-start w-1/2 text-black font-bold text-2xl tracking-tight">
                {{ dateRangePickerText }}
            </div>

            <!-- Right Side Controls -->
            <div class="flex items-center gap-2 flex-wrap w-1/2 justify-end">
                <!-- Currency Select -->
                <div class="flex flex-col w-24">
                    <select class="select select-input" v-model="currency">
                        <option v-for="(currency, index) in currencies" :key="index" :value="currency">{{ currency }}</option>
                    </select>
                </div>
                <!-- Date Picker -->
                <div class="flex flex-col w-64">
                    <VueDatePicker
                        v-model="datePickerRange"
                        model-type="yyyy-MM-dd"
                        auto-apply
                        range
                        :multi-calendars="{ solo: true }"
                        placeholder="Select Date"
                        :enable-time-picker="false"
                        format="dd-MM-yyyy"
                        :clearable="false"
                        class="rounded-none"
                    />
                </div>
                <!-- Period Select -->
                <div class="flex flex-col w-36">
                    <select class="select select-input" v-model="period">
                        <option v-for="(period, index) in periods" :key="index" :value="period.key">
                            {{ period.value }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { DateTime } from "luxon";
import { sharedState } from "../../../../state.js";

export default {
    name: "reports-filters-component",
    data(){
        return {
            dateRange: this.initializeStoredValue("dateRange", [DateTime.now().toISODate(), DateTime.now().toISODate()]),
            period: this.initializeStoredValue("period", "last_60_days_plusplus"),
            currencies: ["AED", "USD", "EUR", "GBD", "CHF", "PHP", "SCR"],
            periods: [
                { key: "this_week", value: "This week" },
                { key: "last_week", value: "Last week" },
                { key: "this_month", value: "This month" },
                { key: "last_month", value: "Last month" },
                { key: "last_month_plusplus", value: "This month ++" },
                { key: "last_60_days_plusplus", value: "Last 60 days ++" },
                { key: "this_year", value: "This Year" },
                { key: "last_year", value: "Last Year" },
            ],
        }
    },
    methods: {
        // Initialize localStorage with a default value if it doesn't exist
        initializeStoredValue(key, defaultValue) {
            const storedValue = localStorage.getItem(key);
            if (!storedValue) {
                localStorage.setItem(key, JSON.stringify(defaultValue));
                return defaultValue;
            }
            return JSON.parse(storedValue);
        },
        updateDateRangeForPeriod(period) {
            const now = DateTime.now();
            let newDateRange;

            switch (period) {
                case "this_week":
                    newDateRange = [
                        now.startOf("week").toISODate(),
                        now.endOf("week").toISODate()];
                    break;
                case "last_week":
                    newDateRange = [
                        now.minus({weeks: 1}).startOf("week").toISODate(),
                        now.minus({weeks: 1}).endOf("week").toISODate()
                    ];
                    break;
                case "this_month":
                    newDateRange = [
                        now.startOf("month").toISODate(),
                        now.endOf("month").toISODate()
                    ];
                    break;
                case "last_month":
                    newDateRange = [
                        now.minus({months: 1}).startOf("month").toISODate(),
                        now.minus({months: 1}).endOf("month").toISODate()
                    ];
                    break;
                case "last_month_plusplus":
                    newDateRange = [
                        now.startOf("month").toISODate(),
                        now.plus({ years: 3 }).endOf("year").toISODate(),
                    ];
                    break;
                case "last_60_days_plusplus":
                    newDateRange = [
                        now.minus({days: 60}).startOf("day").toISODate(),
                        now.plus({ years: 3 }).endOf("year").toISODate()
                    ];
                    localStorage.setItem("dateRange", JSON.stringify(newDateRange));
                    this.dateRange = newDateRange;
                    break;
                case "this_year":
                    newDateRange = [
                        now.startOf("year").toISODate(),
                        now.endOf("year").toISODate()
                    ];
                    break;
                case "last_year":
                    newDateRange = [
                        now.minus({years: 1}).startOf("year").toISODate(),
                        now.minus({years: 1}).endOf("year").toISODate()
                    ];
                    break;
                default:
                    newDateRange = [
                        DateTime.now().toISODate(),
                        DateTime.now().toISODate()
                    ];
            }
            this.dateRange = newDateRange;
            localStorage.setItem("dateRange", JSON.stringify(newDateRange));
        },
    },
    computed: {
        datePickerRange: {
            get() {
                return this.dateRange
            },
            set(newRange) {
                this.dateRange =  newRange;
            },
        },
        dateRangePickerText(){
            if (!this.dateRange[0] || !this.dateRange[1]) {
                return "No date selected";
            }
            const formattedStart = DateTime.fromISO(this.dateRange[0]).toFormat("d MMM yyyy");
            const formattedEnd = DateTime.fromISO(this.dateRange[1]).toFormat("d MMM yyyy");
            return `${formattedStart} - ${formattedEnd}`;
        },
        currency: {
            get() {
                return sharedState.currency;
            },
            set(newCurrency) {
                sharedState.currency = newCurrency;
                localStorage.setItem("currency", newCurrency);
            },
        },
    },
    watch: {
        dateRange(newValue) {
            localStorage.setItem("dateRange", JSON.stringify(newValue));
            this.$emit("get-data");
        },
        period(newValue) {
            localStorage.setItem("period", JSON.stringify(newValue));
            this.updateDateRangeForPeriod(newValue);
        },
    },
    created() {
        // Ensure localStorage values are set for the first time
        // DateRange
        if (!localStorage.getItem('dateRange')) {
            if (this.period === 'last_60_days_plusplus') {
                const now = DateTime.now();
                const newDateRange = [
                    now.minus({ days: 60 }).startOf('day').toISODate(),
                    now.plus({ years: 3 }).toISODate()
                ];
                localStorage.setItem('dateRange', JSON.stringify(newDateRange));
                this.dateRange = newDateRange; // Set default in data
            } else {
                localStorage.setItem('dateRange', JSON.stringify(this.datePickerRange));
            }
        } else {
            this.dateRange = JSON.parse(localStorage.getItem('dateRange'));
        }

        // Currency
        if (!localStorage.getItem('currency')) {
            localStorage.setItem('currency', this.currency);
        }

        // Period
        if (!localStorage.getItem('period')) {
            localStorage.setItem('period', JSON.stringify(this.period));
        } else {
            this.period = JSON.parse(localStorage.getItem('period'));
        }

        // Ensure dateRange is updated if the period is already 'last_60_days_plusplus'
        if (this.period === 'last_60_days_plusplus') {
            this.updateDateRangeForPeriod('last_60_days_plusplus');
        }
    }

}
</script>
