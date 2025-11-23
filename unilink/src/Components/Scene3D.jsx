import React, { Suspense } from 'react';
import { Canvas } from '@react-three/fiber';
import { Environment, ScrollControls, Float, Stars, Scroll } from '@react-three/drei';
import Book3D from './Book3D';

const Scene3D = ({ children }) => {
    return (
        <div className="fixed inset-0 z-0">
            <Canvas shadows camera={{ position: [0, 0, 8], fov: 45 }}>
                <Suspense fallback={null}>
                    <color attach="background" args={['#050505']} />

                    {/* THREE-POINT CINEMATIC LIGHTING - Enhanced for Translucency */}

                    {/* KEY LIGHT - Warm Softbox (Front-Left) */}
                    <spotLight
                        position={[-6, 6, 6]}
                        angle={0.5}
                        penumbra={0.8}
                        intensity={2.5}
                        color="#fff8e1"
                        castShadow
                        shadow-mapSize-width={2048}
                        shadow-mapSize-height={2048}
                        shadow-bias={-0.0001}
                    />

                    {/* FILL LIGHT - Neutral Soft (Right) */}
                    <directionalLight
                        position={[4, 2, 3]}
                        intensity={0.6}
                        color="#ffffff"
                    />

                    {/* RIM LIGHT - Cool Backlight (Back-Right) - BOOSTED INTENSITY */}
                    {/* Increased intensity to 4.0 to catch translucent page edges */}
                    <spotLight
                        position={[5, 3, -6]}
                        angle={0.6}
                        penumbra={1}
                        intensity={4.0}
                        color="#b3d9ff"
                    />

                    {/* Soft Ambient Fill */}
                    <ambientLight intensity={0.25} color="#ffffff" />

                    {/* Environment - Studio for metallic reflections */}
                    <Environment preset="studio" blur={0.5} />
                    <Stars radius={100} depth={50} count={1500} factor={3} saturation={0} fade speed={0.3} />

                    {/* Scroll Controlled Content */}
                    <ScrollControls pages={5} damping={0.2}>
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
