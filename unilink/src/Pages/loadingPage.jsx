import React, { useEffect, useState } from 'react';

const LoadingPage = ({ onComplete }) => {
    const [fadeOut, setFadeOut] = useState(false);

    useEffect(() => {
        const timer = setTimeout(() => {
            setFadeOut(true);
            setTimeout(() => {
                if (onComplete) onComplete();
            }, 400);
        }, 1500);

        return () => clearTimeout(timer);
    }, [onComplete]);

    return (
        <div
            style={{
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100vw',
                height: '100vh',
                background: '#050505',
                zIndex: 9999,
                opacity: fadeOut ? 0 : 1,
                pointerEvents: fadeOut ? 'none' : 'auto',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                gap: '1.5rem',
                transition: 'opacity 0.4s ease-out',
            }}
        >
            <div
                style={{
                    width: '48px',
                    height: '48px',
                    borderRadius: '50%',
                    border: '3px solid rgba(88,166,255,0.2)',
                    borderTopColor: '#58a6ff',
                    animation: 'spin 0.8s linear infinite',
                }}
            />
            <div
                style={{
                    color: '#58a6ff',
                    fontFamily: "'Inter', sans-serif",
                    letterSpacing: '0.15em',
                    fontSize: '1rem',
                    textAlign: 'center',
                }}
            >
                Loading Uni-Link...
            </div>
            <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
        </div>
    );
};

export default LoadingPage;
