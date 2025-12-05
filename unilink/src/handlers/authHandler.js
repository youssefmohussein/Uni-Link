const API_BASE_URL = 'http://localhost/backend/index.php';

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

            return {
                success: true,
                user: {
                    id: data.id,
                    username: data.username,
                    email: data.email,
                    role: data.role
                },
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
