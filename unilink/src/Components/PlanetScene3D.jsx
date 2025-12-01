import React, { Suspense } from 'react';
import { Canvas } from '@react-three/fiber';
import { Environment, ScrollControls, Scroll } from '@react-three/drei';
import HolographicPlanet from './HolographicPlanet';

const PlanetScene3D = ({ children, facultiesCount = 1 }) => {
    const [ready, setReady] = React.useState(false);

    React.useEffect(() => {
        const timer = setTimeout(() => setReady(true), 100);
        return () => clearTimeout(timer);
    }, []);

    return (
        <div className="fixed inset-0 z-10">
            <Canvas
                shadows
                dpr={[1, 1.5]}
                camera={{ position: [0, 0, 8], fov: 45 }}
                gl={{
                    antialias: false,
                    powerPreference: "high-performance",
                    alpha: true
                }}
                performance={{ min: 0.5 }}
            >
                {/* Lighting - Lightweight, render immediately */}
                <spotLight
                    position={[-6, 6, 6]}
                    angle={0.5}
                    penumbra={0.8}
                    intensity={2.0}
                    color="#fff8e1"
                    castShadow
                    shadow-mapSize={[256, 256]}
                />

                <directionalLight
                    position={[4, 2, 3]}
                    intensity={0.6}
                    color="#ffffff"
                />

                <spotLight
                    position={[5, 3, -6]}
                    angle={0.6}
                    penumbra={1}
                    intensity={3.0}
                    color="#b3d9ff"
                />

                <ambientLight intensity={0.3} />

                {/* Environment for reflections - Deferred */}
                {ready && (
                    <Suspense fallback={null}>
                        <Environment preset="studio" blur={0.8} background={false} />
                    </Suspense>
                )}

                {/* Scroll Controlled Content */}
                <ScrollControls pages={facultiesCount} damping={0.2}>
                    {/* Planet - Deferred */}
                    {ready && (
                        <Suspense fallback={null}>
                            <HolographicPlanet facultiesCount={facultiesCount} />
                        </Suspense>
                    )}

                    {/* HTML Content - Renders immediately */}
                    {children && (
                        <Scroll html style={{ width: '100%' }}>
                            {children}
                        </Scroll>
                    )}
                </ScrollControls>
            </Canvas>
        </div>
    );
};

export default PlanetScene3D;
