<template>
    <div class="flex flex-col gap-4">
        <div class="w-full">
            <div class="alert alert-danger bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                <p class="font-bold">
                    Unable to access CRESCO Sage. Follow the steps below to troubleshoot:
                </p>
                <ol class="list-decimal list-inside text-sm">
                    <li>
                        Click <a class="text-blue-500 underline" target="_blank" :href="sageUrl">here</a> to verify if you have access.
                    </li>
                    <li>
                        If the link above shows "Connection not secure or connection is not private," click show
                        details and then proceed to the site.
                    </li>
                    <li>Reload the reports page after allowing access in the previous step.</li>
                    <li>
                        If the issue persists, try to turn your VPN or Wi-Fi on/off and refresh the reports
                        page.
                    </li>
                    <li>
                        If the issue persists, try to open the reports page in incognito mode or private window
                        (Shift+Command+N).
                    </li>
                    <li>If the issue persists, contact IT.</li>
                </ol>
            </div>
        </div>
    </div>
</template>
<script>
import { sharedState } from '../../state.js'

export default {
    name: "sage-network-error",
    data(){
        return {
            sageUrl: "https://10.0.1.17/CrescoSage/api/V1/FOBank/2/Transactions",
        }
    }
}
</script>

<style scoped>

</style>
