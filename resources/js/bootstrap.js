import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


// Add an interceptor to catch network errors
// axios.interceptors.response.use(
//     (response) => response, // Return the response if successful
//     (error) => {
//         if (!error.response) {
//             // If no response, assume it's a network error
//             window.dispatchEvent(new Event('network-error')); // Dispatch a global event
//         }
//         return Promise.reject(error); // Reject the promise with the error
//     }
// );
