import React, { useRef, useMemo } from 'react';
import { useFrame } from '@react-three/fiber';
import { useScroll, Float, useTexture } from '@react-three/drei';
import * as THREE from 'three';

const HolographicPlanet = ({ facultiesCount = 1 }) => {
    const groupRef = useRef();
    const planetRef = useRef();
    const cloudsRef = useRef();
    const atmosphereRef = useRef();
    const scroll = useScroll();

    // Load optimized textures (2K) for better performance
    const [colorMap, normalMap, specularMap, cloudsMap] = useTexture([
        'https://raw.githubusercontent.com/mrdoob/three.js/master/examples/textures/planets/earth_atmos_2048.jpg',
        'https://raw.githubusercontent.com/mrdoob/three.js/master/examples/textures/planets/earth_normal_2048.jpg',
        'https://raw.githubusercontent.com/mrdoob/three.js/master/examples/textures/planets/earth_specular_2048.jpg',
        'https://raw.githubusercontent.com/mrdoob/three.js/master/examples/textures/planets/earth_clouds_1024.png'
    ]);

    useFrame((state, delta) => {
        const offset = scroll.offset;

        if (groupRef.current) {
            if (facultiesCount <= 1) return;

            const totalProgress = offset * (facultiesCount - 1);
            const currentIndex = Math.floor(totalProgress);
            const sectionProgress = totalProgress - currentIndex;

            // Sharper ease to minimize center overlap time
            const ease = (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
            const smoothProgress = ease(sectionProgress);

            const isEven = currentIndex % 2 === 0;
            const startX = isEven ? -3.5 : 3.5;
            const endX = isEven ? 3.5 : -3.5;

            const targetX = THREE.MathUtils.lerp(startX, endX, smoothProgress);

            groupRef.current.position.x = THREE.MathUtils.damp(
                groupRef.current.position.x,
                targetX,
                10,
                delta
            );

            groupRef.current.position.y = Math.sin(state.clock.elapsedTime * 0.5) * 0.1;

            // Rotate planet (Day cycle)
            if (planetRef.current) {
                planetRef.current.rotation.y += delta * 0.05;
            }
            // Rotate clouds slightly faster
            if (cloudsRef.current) {
                cloudsRef.current.rotation.y += delta * 0.07;
            }
        }
    });

    return (
        <Float
            speed={1.5}
            rotationIntensity={0.1}
            floatIntensity={0.15}
        >
            <group ref={groupRef} scale={1.8}>
                {/* Earth Sphere */}
                <mesh ref={planetRef} castShadow receiveShadow>
                    <sphereGeometry args={[0.8, 64, 64]} />
                    <meshPhongMaterial
                        map={colorMap}
                        normalMap={normalMap}
                        specularMap={specularMap}
                        specular={new THREE.Color(0x333333)}
                        shininess={15}
                    />
                </mesh>

                {/* Cloud Layer */}
                <mesh ref={cloudsRef} scale={1.015}>
                    <sphereGeometry args={[0.8, 64, 64]} />
                    <meshStandardMaterial
                        map={cloudsMap}
                        transparent
                        opacity={0.8}
                        blending={THREE.AdditiveBlending}
                        side={THREE.DoubleSide}
                        depthWrite={false}
                    />
                </mesh>

                {/* Atmosphere Glow (Fresnel-like effect) */}
                <mesh ref={atmosphereRef} scale={1.1}>
                    <sphereGeometry args={[0.8, 64, 64]} />
                    <meshPhongMaterial
                        color="#0077ff"
                        transparent
                        opacity={0.1}
                        side={THREE.BackSide}
                        blending={THREE.AdditiveBlending}
                        depthWrite={false}
                    />
                </mesh>
            </group>
        </Float>
    );
};

export default HolographicPlanet;
