<template>
    <button class="btn btn-sm btn-outline btn-danger"
            @click="syncDocuments"
            :disabled="loading"
    >
        {{ loading ? `Syncing... ${progress}%` : "Sync FSA Documents" }}
        <i class="ki-filled ki-arrows-circle"></i>
    </button>
</template>
<script>
export default {
    name: "sync-f-s-a-documents-component",
    data(){
        return {
            loading: false,
            progress: null,
            intervalId: null,
        }
    },
    methods:{
        syncDocuments(){
            this.loading = true;
            this.progress = 0;

            axios.get('/sync/FSA/documents')
                .then(response => {
                    this.pollProgress();
                    console.log(response.data)
                })
                .catch(error => {
                    console.log(error);
                    clearInterval(this.intervalId);
                    this.loading = false;
                });
            this.pollProgress();
        },
        pollProgress() {
            // Clear any existing interval to avoid multiple intervals
            if (this.intervalId) {
                clearInterval(this.intervalId);
            }
            // Poll the server every 5 seconds to get progress updates
            this.intervalId = setInterval(() => {
                axios.get("/sync/FSA/documents/progress")
                    .then((response) => {
                        this.progress = Math.round(response.data.progress);
                        if (this.progress >= 100) {
                            clearInterval(this.intervalId);
                            this.loading = false;
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        clearInterval(this.intervalId);
                        this.loading = false;
                    });
            }, 5000);
        },
        checkInitialProgress() {
            // Check if there's any sync in progress when the page is loaded
            axios.get("/sync/FSA/documents/progress")
                .then((response) => {
                    if(response.data.progress){
                        this.progress = Math.round(response.data.progress);
                        this.loading = true;
                        this.pollProgress();
                    }
                })
                .catch((error) => {
                    this.loading = false;
                    console.log(error);
                });
        },
    },
    created() {
        this.checkInitialProgress();
    },
}
</script>

<style scoped>

</style>
