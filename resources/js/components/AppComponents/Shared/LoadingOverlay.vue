<template>
    <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-xl text-center animate-appear">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-t-orange-500 border-r-orange-500 border-b-gray-300 border-l-gray-300 rounded-full animate-spin mb-4"></div>
                <p class="text-lg font-semibold text-gray-800">{{ message }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'LoadingOverlay',
    props: {
        isVisible: {
            type: Boolean,
            default: false
        },
        message: {
            type: String,
            default: 'Loading...'
        }
    }
}
</script>

<style scoped>
.animate-appear {
    animation: appear 0.3s ease-out;
}

@keyframes appear {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
