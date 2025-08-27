import axios from '../plugins/axios';

export default {
    data() {
        return {
            appEnv: window.env.APP_ENV,
            appUrl: window.env.APP_URL.replace(/\/$/, ""),
        }
    },
    methods: {
        isProductionEnv() {
            return this.appEnv === 'production';
        },
        isLocalEnv() {
            return this.appEnv === 'local';
        },
        removeModalBackdrop(){
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        },
        setItemToLocalStorage(key, value){
            localStorage.setItem(key, JSON.stringify(value));
        },
        getItemFromLocalStorage(key){
            const value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        },
        removeItemFromLocalStorage(key) {
            localStorage.removeItem(key);
        },
        setTableNoDataColspan() {
            this.$nextTick(() => {
                const noDataRows = document.querySelectorAll("tr.table-no-data-available");

                noDataRows.forEach(row => {
                    const table = row.closest("table");
                    if (table) {
                        const headerRow = table.querySelector("thead tr");
                        const columnCount = headerRow ? headerRow.children.length : 1; // Default to 1 if no header is found
                        const noDataCell = row.querySelector("td");

                        if (noDataCell) {
                            noDataCell.setAttribute("colspan", columnCount);
                        }
                    }
                });
            });
        },
        adjustReportsTableHeight() {
            const calculateHeight = () => {
                this.$nextTick(() => {
                    const topHeaderHeight = document.querySelector('.top-header').offsetHeight;
                    const headerNavigationHeight = document.querySelector('.header-navigation') ? document.querySelector('.header-navigation').offsetHeight : 0;
                    // const headerHeight = 54 + 39 + 48;
                    const headerHeight = topHeaderHeight + (headerNavigationHeight ? headerNavigationHeight : 0) + 48;
                    const headerFilters = document.querySelector('.reports-header-filters');
                    const onlyFiltersElement = document.querySelector('.reports-only-filters');
                    const tableElement = document.querySelector('.reports-table-container');

                    if (headerFilters && onlyFiltersElement && tableElement) {
                        const filtersHeight = headerFilters.offsetHeight + onlyFiltersElement.offsetHeight;
                        const totalHeight = window.innerHeight;
                        const tableHeight = totalHeight - (headerHeight + filtersHeight);
                        tableElement.style.height = `${tableHeight}px`;
                    }
                });
            };

            // Initial call
            calculateHeight();

            // Add resize listener
            window.addEventListener('resize', calculateHeight);

            // Store reference for cleanup
            this._resizeHandler = calculateHeight;
        },
        successToast(text) {
            this.$swal.fire({
                toast: true,
                position: "top-right",
                icon: "success",
                text: text,
                showConfirmButton: false,
                timer: 3000
            });
        },
        infoToast(text) {
            this.$swal.fire({
                toast: true,
                position: "top-right",
                icon: "info",
                text: text,
                showConfirmButton: false,
                customClass: {
                    container: "sweet-toast-custom-success"
                },
            });
        },
        errorToast(text) {
            this.$swal.fire({
                toast: true,
                position: "top-right",
                icon: "error",
                text: text,
                showConfirmButton: false,
                customClass: {
                    container: "sweet-toast-custom-error"
                },
                timer: 3000
            });
        },
        successAlert(text) {
            this.$swal.fire({
                icon: "success",
                text: text,
                showConfirmButton: false,
                timer: 3000
            });
            $("body").removeClass("swal2-height-auto");
        },
        errorAlert(text) {
            this.$swal.fire({
                icon: "error",
                text: text,
                showConfirmButton: false,
                timer: 3000
            });
            $("body").removeClass("swal2-height-auto");
        },
        successAlertWithoutText() {
            this.$swal.fire({
                position: "top",
                icon: "success",
                showConfirmButton: false,
                timer: 3000
            });
        },
        errorAlertWithoutText() {
            this.$swal.fire({
                position: "top",
                icon: "error",
                showConfirmButton: false,
                timer: 3000
            });
            $("body").removeClass("swal2-height-auto");
        },
        deleteConfirmationAlert(url, callback) {
            this.$swal
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios({
                            url: url,
                            method: "DELETE"
                        })
                            .then(response => {
                                if (response.status === 200) {
                                    return response.data.message;
                                } else {
                                    return new Error(response.statusText);
                                }
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                );
                            });
                    }
                })
                .then(result => {
                    if (result.isConfirmed) {
                        this.$swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: result.value
                        });
                        if (callback && typeof callback === 'function') {
                            callback(); // Execute the callback function
                        }
                    }
                });
        },

        // Token management methods
        getToken() {
            return localStorage.getItem('access_token');
        },

        setToken(token) {
            localStorage.setItem('access_token', token);
        },

        removeToken() {
            localStorage.removeItem('access_token');
        },

        // Axios helper methods
        async apiGet(url, config = {}) {
            try {
                const response = await axios.get(url, config);
                return response.data;
            } catch (error) {
                this.handleApiError(error);
                throw error;
            }
        },

        async apiPost(url, data = {}, config = {}) {
            try {
                const response = await axios.post(url, data, config);
                return response.data;
            } catch (error) {
                this.handleApiError(error);
                throw error;
            }
        },

        async apiPut(url, data = {}, config = {}) {
            try {
                const response = await axios.put(url, data, config);
                return response.data;
            } catch (error) {
                this.handleApiError(error);
                throw error;
            }
        },

        async apiDelete(url, config = {}) {
            try {
                const response = await axios.delete(url, config);
                return response.data;
            } catch (error) {
                this.handleApiError(error);
                throw error;
            }
        },

        handleApiError(error) {
            const message = error.response?.data?.message || 'An error occurred';
            this.errorToast(message);
        },
    },
    mounted() {
        this.setTableNoDataColspan();
        // this.adjustReportsTableHeight();
    },
    updated() {
        this.setTableNoDataColspan();
        // this.adjustReportsTableHeight();
    },
    beforeDestroy() {
        if (this._resizeHandler) {
            window.removeEventListener('resize', this._resizeHandler);
        }
    }
}
