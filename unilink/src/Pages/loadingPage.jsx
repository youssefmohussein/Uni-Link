import React, { useEffect, useState } from 'react';
import { Canvas } from '@react-three/fiber';
import { Html } from '@react-three/drei';
import HolographicEarth from '../Components/loading/planetcomponent';

const LoadingPage = ({ onComplete }) => {
    const [fadeOut, setFadeOut] = useState(false);

    useEffect(() => {
        // Wait 2 seconds then trigger fade out
        const timer = setTimeout(() => {
            setFadeOut(true);
            // Give time for fade out animation before unmounting
            setTimeout(() => {
                if (onComplete) onComplete();
            }, 500);
        }, 3000);

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
                transition: 'opacity 0.5s ease-out',
                opacity: fadeOut ? 0 : 1,
                pointerEvents: fadeOut ? 'none' : 'auto'
            }}
        >
            <Canvas camera={{ position: [0, 0, 4], fov: 45 }}>
                <ambientLight intensity={0.5} />
                <pointLight position={[10, 10, 10]} intensity={1} />
                <pointLight position={[-10, -10, -10]} intensity={0.5} color="#00ffff" />

                {/* Center the planet by offsetting its internal Y position (0.7) */}
                <group position={[0, -0.7, 0]}>
                    <HolographicEarth isOpen={true} />
                </group>

                <Html center position={[0, -1.5, 0]}>
                    <div style={{
                        color: '#00ffff',
                        fontFamily: "'Orbitron', sans-serif",
                        letterSpacing: '0.2em',
                        fontSize: '1.2rem',
                        textAlign: 'center',
                        textShadow: '0 0 15px rgba(0, 255, 255, 0.8)',
                        whiteSpace: 'nowrap'
                    }}>
                        Loading Unilink...
                    </div>
                </Html>
            </Canvas>
        </div>
    );
};

export default LoadingPage;
