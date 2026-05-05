/**
 * API Configuration — Zero-Config, Auto-Detecting
 *
 * HOW IT WORKS:
 *  ┌─ Local dev ──────────────────────────────────────────────────────┐
 *  │  Create unilink/.env.local:  VITE_API_BASE_URL=http://localhost:8000 │
 *  │  Then run:  npm run dev                                          │
 *  └──────────────────────────────────────────────────────────────────┘
 *  ┌─ Linux server (production) ──────────────────────────────────────┐
 *  │  Just build and upload — NO env vars needed on the server.       │
 *  │  The URL is read from window.location at runtime in the browser, │
 *  │  so it auto-detects the correct backend URL automatically.       │
 *  │  Backend must be reachable at:  <same-origin>/backend            │
 *  │  e.g. http://192.168.1.10/backend  or  https://uni-link.com/backend │
 *  └──────────────────────────────────────────────────────────────────┘
 */

const resolveApiBase = () => {
    // 1. Explicit override — used in local dev via .env.local
    //    (Vite replaces import.meta.env.VITE_* at build time)
    const envUrl = import.meta.env.VITE_API_BASE_URL;
    if (envUrl) {
        return envUrl.replace(/\/$/, '');
    }

    // 2. Runtime auto-detect — evaluated in the browser after page load.
    //    Works on ANY server without editing any files:
    //    window.location.origin gives the current protocol + host + port.
    //    Both frontend and backend live on the same server, backend at /backend.
    if (typeof window !== 'undefined') {
        return window.location.origin + '/backend';
    }

    // 3. Absolute last resort (SSR / unit tests)
    return 'http://localhost:8000';
};

export const API_BASE_URL = resolveApiBase();

/**
 * Central fetch wrapper — always uses the dynamic base URL.
 * @param {string} endpoint - e.g. '/login', '/api/posts'
 * @param {RequestInit} options - standard fetch options
 * @returns {Promise<Response>}
 */
export const apiCall = async (endpoint, options = {}) => {
    // Allow callers to pass a full URL (e.g. for third-party APIs)
    const url = endpoint.startsWith('http')
        ? endpoint
        : `${API_BASE_URL}${endpoint.startsWith('/') ? endpoint : '/' + endpoint}`;

    const { headers: extraHeaders, ...restOptions } = options;

    const defaultOptions = {
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            ...extraHeaders,
        },
        ...restOptions,
    };

    const response = await fetch(url, defaultOptions);
    return response;
};

export default API_BASE_URL;


