import React, { useEffect, useState } from 'react';
import { Navigate } from 'react-router-dom';
import authHandler from '../handlers/authHandler';

const ProtectedRoute = ({ children, requiredRole }) => {
    const [authState, setAuthState] = useState({
        loading: true,
        authenticated: false,
        user: null
    });

    useEffect(() => {
        let isMounted = true;

        const checkAuth = async () => {
            try {
                // Use shared auth handler for consistent authentication checking
                const session = await authHandler.checkSession();

                if (!isMounted) return;

                // Debug logging
                console.log('ProtectedRoute - Session check result:', {
                    authenticated: session.authenticated,
                    user: session.user,
                    requiredRole: requiredRole
                });

                setAuthState({
                    loading: false,
                    authenticated: session.authenticated,
                    user: session.user
                });
            } catch (err) {
                console.error('Auth check error:', err);
                if (!isMounted) return;
                setAuthState({
                    loading: false,
                    authenticated: false,
                    user: null
                });
            }
        };

        checkAuth();

        return () => {
            isMounted = false;
        };
    }, []); // Only run once on mount

    if (authState.loading) {
        return (
            <div className="flex items-center justify-center h-screen bg-dark">
                <div className="text-white text-xl">Loading...</div>
            </div>
        );
    }

    if (!authState.authenticated) {
        return <Navigate to="/login" replace />;
    }

    // Check role (case-insensitive comparison)
    if (requiredRole) {
        const userRole = authState.user?.role?.toUpperCase() || '';
        const requiredRoleUpper = requiredRole.toUpperCase();
        
        console.log('ProtectedRoute - Role check:', {
            userRole: authState.user?.role,
            userRoleUpper: userRole,
            requiredRole: requiredRole,
            requiredRoleUpper: requiredRoleUpper,
            match: userRole === requiredRoleUpper
        });
        
        if (userRole !== requiredRoleUpper) {
            console.warn(`Access denied: User role '${authState.user?.role}' (${userRole}) does not match required role '${requiredRole}' (${requiredRoleUpper})`);
            return <Navigate to="/login" replace />;
        }
    }

    return children;
};

export default ProtectedRoute;
