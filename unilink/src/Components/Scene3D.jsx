import React, { Suspense } from 'react';
import { Canvas } from '@react-three/fiber';
import { Environment, ScrollControls, Float, Scroll } from '@react-three/drei';
import Book3D from './Book3D';

const Scene3D = ({ children, pages = 5, onCreated }) => {
    return (
        <div className="fixed inset-0 z-10">
            <Canvas
                shadows
                dpr={[1, 1.5]}
                camera={{ position: [0, 0, 8], fov: 45 }}
                onCreated={onCreated}
                gl={{
                    antialias: false,
                    powerPreference: "high-performance",
                    alpha: true
                }}
                performance={{ min: 0.5 }}
            >
                <Suspense fallback={null}>
                    {/* Removed black background to show Galaxy behind */}

                    {/* Improved lighting - Reduced intensity for performance */}
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

                    <ambientLight intensity={0.25} />

                    {/* Environment for reflections - low quality for performance */}
                    <Environment preset="studio" blur={0.8} background={false} />

                    {/* Scroll Controlled Content */}
                    <ScrollControls pages={pages} damping={0.2}>
                        <Float
                            speed={1.5}
                            rotationIntensity={0.15}
                            floatIntensity={0.15}
                        >
                            <Book3D />
                        </Float>

                        {/* HTML Content */}
                        {children && (
                            <Scroll html style={{ width: '100%' }}>
                                {children}
                            </Scroll>
                        )}
                    </ScrollControls>
                </Suspense>
            </Canvas>
        </div>
    );
};

export default Scene3D;
