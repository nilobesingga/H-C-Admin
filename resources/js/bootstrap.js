import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


// Add an interceptor to catch network errors
axios.interceptors.response.use(
    // Return the response if successful
    (response) => response,
    (error) => {
        if (!error.response) {
            // If no response, assume it's a network error
            // Default error type
            let errorType = 'unknown';
            // Determine the type of network error based on the URL
            // Dispatch a global event
            // if only network error
            if (error.config.url.includes('CrescoSage')) {
                errorType = 'sage';
            } else if (error.config.url.includes('Bitrix')) {
                errorType = 'bitrix';
            }

            // Dispatch a global event with the error type
            window.dispatchEvent(new CustomEvent('network-error', { detail: { type: errorType } }));
        }
        // Reject the promise with the error
        return Promise.reject(error);
    }
);
