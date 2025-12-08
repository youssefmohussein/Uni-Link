import React, { useRef } from 'react';
import { useFrame, useLoader } from '@react-three/fiber';
import * as THREE from 'three';

const RealisticPlanet = ({
    name,
    size = 0.2,
    textures = {},
    hasAtmosphere = false,
    atmosphereColor = '#88ccff',
    hasClouds = false,
    ring = null
}) => {
    const planetRef = useRef();
    const cloudsRef = useRef();
    const atmosphereRef = useRef();

    // Load textures
    const colorMap = textures.map ? useLoader(THREE.TextureLoader, textures.map) : null;
    const normalMap = textures.normalMap ? useLoader(THREE.TextureLoader, textures.normalMap) : null;
    const specularMap = textures.specularMap ? useLoader(THREE.TextureLoader, textures.specularMap) : null;
    const cloudsMap = textures.cloudsMap && hasClouds ? useLoader(THREE.TextureLoader, textures.cloudsMap) : null;
    const ringMapTexture = ring?.ringMap ? useLoader(THREE.TextureLoader, textures.ringMap) : null;

    // Realistic rotation speeds (different for each layer)
    useFrame((state, delta) => {
        if (planetRef.current) {
            // Planets rotate at different realistic speeds
            planetRef.current.rotation.y += delta * 0.15;
        }
        if (cloudsRef.current) {
            // Clouds move slightly faster for dynamic effect
            cloudsRef.current.rotation.y += delta * 0.18;
        }
        if (atmosphereRef.current) {
            // Very subtle atmosphere shimmer
            const shimmer = Math.sin(state.clock.elapsedTime * 0.5) * 0.05 + 1;
            atmosphereRef.current.scale.setScalar(shimmer);
        }
    });

    return (
        <group>
            {/* Main Planet Surface */}
            <mesh ref={planetRef} castShadow receiveShadow>
                <sphereGeometry args={[size, 64, 64]} />
                <meshStandardMaterial
                    map={colorMap}
                    normalMap={normalMap}
                    normalScale={new THREE.Vector2(0.8, 0.8)}
                    roughness={0.9}
                    metalness={0.1}
                    emissive="#000000"
                    emissiveIntensity={0}
                    {...(specularMap && {
                        roughnessMap: specularMap,
                        metalnessMap: specularMap
                    })}
                />
            </mesh>

            {/* Cloud Layer (for Earth-like planets) */}
            {hasClouds && cloudsMap && (
                <mesh ref={cloudsRef}>
                    <sphereGeometry args={[size + 0.01, 64, 64]} />
                    <meshStandardMaterial
                        map={cloudsMap}
                        transparent
                        opacity={0.4}
                        depthWrite={false}
                        side={THREE.DoubleSide}
                    />
                </mesh>
            )}

            {/* Atmospheric Glow Layer */}
            {hasAtmosphere && (
                <>
                    {/* Inner atmosphere - subtle glow */}
                    <mesh ref={atmosphereRef}>
                        <sphereGeometry args={[size + 0.03, 48, 48]} />
                        <meshBasicMaterial
                            color={atmosphereColor}
                            transparent
                            opacity={0.15}
                            side={THREE.BackSide}
                            blending={THREE.AdditiveBlending}
                        />
                    </mesh>

                    {/* Outer atmosphere - diffuse */}
                    <mesh>
                        <sphereGeometry args={[size + 0.05, 32, 32]} />
                        <meshBasicMaterial
                            color={atmosphereColor}
                            transparent
                            opacity={0.08}
                            side={THREE.BackSide}
                            blending={THREE.AdditiveBlending}
                        />
                    </mesh>
                </>
            )}

            {/* Planetary Ring System (Saturn, Uranus style) */}
            {ring && ringMapTexture && (
                <mesh rotation={[Math.PI / 2.5, 0, 0]}>
                    <ringGeometry
                        args={[
                            ring.innerRadius,
                            ring.outerRadius,
                            64
                        ]}
                    />
                    <meshBasicMaterial
                        map={ringMapTexture}
                        side={THREE.DoubleSide}
                        transparent
                        opacity={0.8}
                        depthWrite={false}
                    />
                </mesh>
            )}

            {/* Subtle ambient point light from planet */}
            <pointLight
                color={atmosphereColor || '#ffffff'}
                intensity={hasAtmosphere ? 0.1 : 0.05}
                distance={size * 3}
                decay={2}
            />
        </group>
    );
};

export default RealisticPlanet;
