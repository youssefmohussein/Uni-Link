const API_BASE_URL = 'http://localhost/backend';

/**
 * Centralized authentication handler
 * Provides consistent API calls and error handling for authentication operations
 */
class AuthHandler {
    /**
     * Login user with credentials
     * @param {string} identifier - Email or username
     * @param {string} password - User password
     * @returns {Promise<Object>} Login response with user data and redirect URL
     */
    async login(identifier, password) {
        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'include', // Important for session cookies
                body: JSON.stringify({
                    identifier: identifier.trim(),
                    password
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || data.message || 'Login failed');
            }

            // Save user data to localStorage for frontend authentication checks
            const userData = {
                id: data.id,
                username: data.username,
                email: data.email,
                role: data.role
            };
            localStorage.setItem('user', JSON.stringify(userData));

            return {
                success: true,
                user: userData,
                redirect: data.redirect
            };
        } catch (error) {
            return {
                success: false,
                error: error.message || 'Network error. Please try again.'
            };
        }
    }

    /**
     * Logout current user
     * @returns {Promise<Object>} Logout response
     */
    async logout() {
        try {
            const response = await fetch(`${API_BASE_URL}/logout`, {
                method: 'POST',
                credentials: 'include'
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Logout failed');
            }

            // Clear user data from localStorage
            localStorage.removeItem('user');

            return {
                success: true,
                message: data.message
            };
        } catch (error) {
            return {
                success: false,
                error: error.message || 'Logout failed'
            };
        }
    }

    /**
     * Check if user has active session
     * @returns {Promise<Object>} Session status and user data
     */
    async checkSession() {
        try {
            const response = await fetch(`${API_BASE_URL}/check-session`, {
                method: 'GET',
                credentials: 'include'
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Session check failed');
            }

            // Sync session data with localStorage
            if (data.authenticated && data.user) {
                localStorage.setItem('user', JSON.stringify(data.user));
            } else {
                localStorage.removeItem('user');
            }

            return {
                authenticated: data.authenticated,
                user: data.user || null
            };
        } catch (error) {
            console.error('Session check error:', error);
            return {
                authenticated: false,
                user: null
            };
        }
    }

    /**
     * Get current authenticated user
     * @returns {Promise<Object|null>} User data or null if not authenticated
     */
    async getCurrentUser() {
        const session = await this.checkSession();
        return session.authenticated ? session.user : null;
    }

    /**
     * Check if user has specific role
     * @param {string} role - Role to check
     * @returns {Promise<boolean>} True if user has the role
     */
    async hasRole(role) {
        const user = await this.getCurrentUser();
        return user && user.role === role;
    }

    /**
     * Check if user has any of the specified roles
     * @param {string[]} roles - Array of roles to check
     * @returns {Promise<boolean>} True if user has any of the roles
     */
    async hasAnyRole(roles) {
        const user = await this.getCurrentUser();
        return user && roles.includes(user.role);
    }
}

// Export singleton instance
const authHandler = new AuthHandler();
export default authHandler;
