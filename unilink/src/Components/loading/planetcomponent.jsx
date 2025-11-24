import React, { useState, useRef, useMemo } from 'react';
import { Html, Sparkles } from '@react-three/drei';
import { useSpring, animated } from '@react-spring/three';
import { useFrame } from '@react-three/fiber';
import * as THREE from 'three';

// Holographic Point Cloud Sphere with Better Continents
const PointCloudSphere = ({ size, color }) => {
    const points = useMemo(() => {
        const temp = [];
        const count = 8000; // High density for detail
        const radius = size;

        for (let i = 0; i < count; i++) {
            // Random point on sphere
            const theta = Math.random() * Math.PI * 2;
            const phi = Math.acos((Math.random() * 2) - 1);

            // Improved Noise Function for Continents
            // Combining multiple frequencies to create "landmasses"
            let noise = 0;

            // Base low frequency (Continents)
            noise += Math.sin(theta * 2.5) * Math.cos(phi * 2.5);

            // Medium frequency (Islands/Peninsulas)
            noise += Math.sin(theta * 6 + phi) * 0.5;

            // High frequency (Detail/Coastlines)
            noise += Math.cos(phi * 12) * 0.2;

            // Threshold: Only add points where noise is "high" (Land)
            // Higher threshold = fewer points, more "ocean"
            if (noise > 0.1) {
                const x = radius * Math.sin(phi) * Math.cos(theta);
                const y = radius * Math.sin(phi) * Math.sin(theta);
                const z = radius * Math.cos(phi);

                temp.push(x, y, z);
            }
        }
        return new Float32Array(temp);
    }, [size]);

    return (
        <points>
            <bufferGeometry>
                <bufferAttribute
                    attach="attributes-position"
                    count={points.length / 3}
                    array={points}
                    itemSize={3}
                />
            </bufferGeometry>
            <pointsMaterial
                size={0.01}
                color={color}
                transparent
                opacity={0.8}
                blending={THREE.AdditiveBlending}
                sizeAttenuation={true}
            />
        </points>
    );
};

// Complex Holographic Rings
const HoloRings = ({ size, color }) => {
    const ref = useRef();

    useFrame((state) => {
        if (ref.current) {
            ref.current.rotation.z += 0.002;
            ref.current.rotation.x = Math.sin(state.clock.elapsedTime * 0.2) * 0.1;
        }
    });

    return (
        <group ref={ref} rotation={[Math.PI / 3, 0, 0]}>
            {/* Main Data Ring */}
            <mesh rotation={[-Math.PI / 2, 0, 0]}>
                <ringGeometry args={[size * 1.4, size * 1.8, 128]} />
                <meshBasicMaterial
                    color={color}
                    transparent
                    opacity={0.08}
                    side={THREE.DoubleSide}
                    blending={THREE.AdditiveBlending}
                    wireframe
                />
            </mesh>

            {/* Inner Glow Ring */}
            <mesh rotation={[-Math.PI / 2, 0, 0]}>
                <ringGeometry args={[size * 1.5, size * 1.6, 64]} />
                <meshBasicMaterial
                    color={color}
                    transparent
                    opacity={0.15}
                    side={THREE.DoubleSide}
                    blending={THREE.AdditiveBlending}
                />
            </mesh>

            {/* Outer Detail Ring */}
            <mesh rotation={[-Math.PI / 2, 0, 0]}>
                <ringGeometry args={[size * 2.1, size * 2.15, 64]} />
                <meshBasicMaterial
                    color="#ffffff"
                    transparent
                    opacity={0.1}
                    side={THREE.DoubleSide}
                    blending={THREE.AdditiveBlending}
                />
            </mesh>

            {/* Floating Data Particles in Ring */}
            <Sparkles
                count={60}
                scale={[size * 4, size * 0.2, size * 4]}
                size={1.2}
                speed={0.4}
                opacity={0.3}
                color={color}
            />
        </group>
    );
};

// Main Holographic Earth Component
const HolographicEarth = ({ isOpen }) => {
    const groupRef = useRef();
    const [hovered, setHovered] = useState(false);

    const { scale } = useSpring({
        scale: isOpen ? (hovered ? 1.05 : 1) : 0,
        config: { tension: 100, friction: 20 }
    });

    useFrame((state) => {
        if (groupRef.current) {
            groupRef.current.rotation.y += 0.003;
        }
    });

    const color = "#00ffff";
    const size = 0.65; // Reduced size further (was 0.85)

    return (
        <animated.group position={[0, 0.7, 0]} scale={scale}> {/* Lowered position (was 1.2) */}
            <group ref={groupRef}>
                {/* Point Cloud Surface with Continents */}
                <PointCloudSphere size={size} color={color} />

                {/* Faint Wireframe Grid Overlay - No Solid Core */}
                <mesh>
                    <sphereGeometry args={[size * 0.98, 32, 32]} />
                    <meshBasicMaterial
                        color={color}
                        wireframe
                        transparent
                        opacity={0.03} // Extremely faint
                        blending={THREE.AdditiveBlending}
                    />
                </mesh>
            </group>

            {/* Orbital Rings */}
            <HoloRings size={size} color={color} />

            {/* Interaction Mesh */}
            <mesh
                visible={false}
                scale={1.5}
                onPointerOver={() => { document.body.style.cursor = 'pointer'; setHovered(true); }}
                onPointerOut={() => { document.body.style.cursor = 'auto'; setHovered(false); }}
            >
                <sphereGeometry args={[size, 16, 16]} />
            </mesh>

            {/* Tooltip */}
            {hovered && isOpen && (
                <Html
                    position={[0, size + 0.4, 0]}
                    center
                    style={{ pointerEvents: 'none', whiteSpace: 'nowrap' }}
                >
                    <div
                        className="px-6 py-3 rounded-xl text-lg font-bold"
                        style={{
                            background: 'rgba(0, 20, 40, 0.85)',
                            backdropFilter: 'blur(12px)',
                            border: `1px solid ${color}`,
                            boxShadow: `0 0 20px ${color}60`,
                            color: '#ffffff',
                            fontFamily: "'Orbitron', sans-serif",
                            letterSpacing: '0.1em',
                            textTransform: 'uppercase',
                            textAlign: 'center'
                        }}
                    >
                        <div>Global Network</div>
                        <div style={{ fontSize: '0.6em', color: color, marginTop: '4px' }}>Connected</div>
                    </div>
                </Html>
            )}
        </animated.group>
    );
};

export default HolographicEarth;
