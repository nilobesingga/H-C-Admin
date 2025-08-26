import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Important for authentication

// Initialize Pusher and Echo for WebSocket communication
window.Pusher = Pusher;

// Enable Pusher logging
Pusher.logToConsole = true;

// Check if required environment variables are set
const REVERB_APP_KEY = '0renst1fsvqti4xpuh6j';
if (!REVERB_APP_KEY) {
    console.error('Error: VITE_REVERB_APP_KEY is not set in your environment variables');
    throw new Error('Reverb app key is required. Please check your .env file');
}

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '0renst1fsvqti4xpuh6j',
    wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8084,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});


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
            // Dispatch a global event with the error type
            window.dispatchEvent(new CustomEvent('network-error', { detail: { type: errorType } }));
        }
                // Reject the promise with the error
        return Promise.reject(error);
    });

// Initialize Pusher and Echo for WebSocket communication
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '0renst1fsvqti4xpuh6j',
    wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8084,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    // disableStats: true,
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            Accept: 'application/json',
        }
    }
});


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
