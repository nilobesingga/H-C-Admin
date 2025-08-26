import axios from 'axios';

// Create axios instance with default config
const axiosInstance = axios.create({
    baseURL: window.location.origin,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Add a request interceptor
axiosInstance.interceptors.request.use(
    (config) => {
        // Add Authorization header if we have an access token
        const token = localStorage.getItem('access_token');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add a response interceptor
axiosInstance.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 401) {
            // Unauthorized, clear storage and redirect to login
            localStorage.removeItem('access_token');
            localStorage.removeItem('user');
            window.location.href = '/';
        }
        return Promise.reject(error);
    }
);

export default axiosInstance;
