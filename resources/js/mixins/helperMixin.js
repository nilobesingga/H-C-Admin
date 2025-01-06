export default {
    data() {
        return {
            appEnv: window.env.APP_ENV,
            appUrl: window.env.APP_URL,
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
    },
    mounted() {
        this.setTableNoDataColspan();
    },
    updated() {
        this.setTableNoDataColspan();
    },
}
