import React, { useRef, useMemo } from 'react';
import { useFrame } from '@react-three/fiber';
import { useTexture } from '@react-three/drei';
import * as THREE from 'three';

const RealisticPlanet = ({
    name,
    textures,
    size = 1,
    rotationSpeed = 0.005,
    hasAtmosphere = false,
    atmosphereColor = '#0077ff',
    hasClouds = false,
    cloudsSpeed = 0.007,
    ring = null
}) => {
    const planetRef = useRef();
    const cloudsRef = useRef();
    const ringRef = useRef();

    // Load textures
    const textureMap = useTexture(textures);

    useFrame((state, delta) => {
        if (planetRef.current) {
            planetRef.current.rotation.y += rotationSpeed * 10 * delta;
        }
        if (cloudsRef.current) {
            cloudsRef.current.rotation.y += cloudsSpeed * 10 * delta;
        }
        if (ringRef.current) {
            ringRef.current.rotation.z += rotationSpeed * 5 * delta;
        }
    });

    return (
        <group>
            {/* Planet Sphere */}
            <mesh ref={planetRef} castShadow receiveShadow>
                <sphereGeometry args={[size, 64, 64]} />
                <meshStandardMaterial
                    map={textureMap.map}
                    normalMap={textureMap.normalMap}
                    roughness={0.7}
                    metalness={0.1}
                />
            </mesh>

            {/* Clouds */}
            {hasClouds && textureMap.cloudsMap && (
                <mesh ref={cloudsRef} scale={1.01}>
                    <sphereGeometry args={[size, 64, 64]} />
                    <meshStandardMaterial
                        map={textureMap.cloudsMap}
                        transparent
                        opacity={0.8}
                        blending={THREE.AdditiveBlending}
                        side={THREE.DoubleSide}
                        depthWrite={false}
                    />
                </mesh>
            )}

            {/* Atmosphere Glow */}
            {hasAtmosphere && (
                <mesh scale={1.1}>
                    <sphereGeometry args={[size, 64, 64]} />
                    <meshPhongMaterial
                        color={atmosphereColor}
                        transparent
                        opacity={0.15}
                        side={THREE.BackSide}
                        blending={THREE.AdditiveBlending}
                        depthWrite={false}
                    />
                </mesh>
            )}

            {/* Rings */}
            {ring && textureMap.ringMap && (
                <mesh
                    ref={ringRef}
                    rotation={[Math.PI / 2.2, 0, 0]}
                    scale={1}
                >
                    <ringGeometry args={[ring.innerRadius, ring.outerRadius, 64]} />
                    <meshStandardMaterial
                        map={textureMap.ringMap}
                        transparent
                        opacity={0.9}
                        side={THREE.DoubleSide}
                    />
                </mesh>
            )}
        </group>
    );
};

export default RealisticPlanet;
