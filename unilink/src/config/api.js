// API Configuration
// Change this URL based on your setup:
// - Development with PHP built-in server: 'http://localhost:8000'
// - Development with Apache/XAMPP: 'http://localhost/backend/index.php'
// - Production: your production URL

export const API_BASE_URL = 'http://localhost/backend';

// Helper function for API calls
export const apiCall = async (endpoint, options = {}) => {
    const url = endpoint.startsWith('http')
        ? endpoint
        : `${API_BASE_URL}${endpoint.startsWith('/') ? endpoint : '/' + endpoint}`;

    const defaultOptions = {
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            ...options.headers
        }
    };

    const response = await fetch(url, { ...defaultOptions, ...options });
    return response;
};

export default API_BASE_URL;
