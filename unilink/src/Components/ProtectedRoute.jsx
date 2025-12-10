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
                // Reuse shared auth handler so we hit the same base URL/port as login
                const session = await authHandler.checkSession();

                if (!isMounted) return;

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
    }, []);

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

    if (requiredRole && authState.user.role !== requiredRole) {
        return <Navigate to="/login" replace />;
    }

    return children;
};

export default ProtectedRoute;
