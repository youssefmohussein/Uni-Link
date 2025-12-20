import React, { useState, useRef, useMemo } from 'react';
import { useFrame, useLoader } from '@react-three/fiber';
import { Text } from '@react-three/drei';
import * as THREE from 'three';
import { useNavigate } from 'react-router-dom';
import RealisticPlanet from './RealisticPlanet';

// Ultra-Realistic Sun Component - Space Quality
const Sun = ({ position }) => {
    const sunRef = useRef();
    const coronaRef = useRef();

    // Load high quality sun texture
    const sunTexture = useLoader(THREE.TextureLoader, 'https://raw.githubusercontent.com/jeromeetienne/threex.planets/master/images/sunmap.jpg');

    // Animate sun rotation and corona pulsing
    useFrame((state, delta) => {
        if (sunRef.current) {
            sunRef.current.rotation.y += delta * 0.05; // Slow rotation
        }
        if (coronaRef.current) {
            // Pulsing corona effect
            const pulse = Math.sin(state.clock.elapsedTime * 0.5) * 0.02 + 1;
            coronaRef.current.scale.setScalar(pulse);
        }
    });

    return (
        <group position={position}>
            {/* Core Sun - Bright center */}
            <mesh ref={sunRef}>
                <sphereGeometry args={[0.75, 128, 128]} />
                <meshStandardMaterial
                    map={sunTexture}
                    emissive="#FFD700"
                    emissiveMap={sunTexture}
                    emissiveIntensity={3.5}
                    toneMapped={false}
                    roughness={1}
                />
            </mesh>

            {/* Chromosphere Layer - Orange glow */}
            <mesh>
                <sphereGeometry args={[0.78, 64, 64]} />
                <meshBasicMaterial
                    color="#FF6B35"
                    transparent
                    opacity={0.25}
                    side={THREE.BackSide}
                />
            </mesh>

            {/* Inner Corona - Yellow-white intense glow */}
            <mesh>
                <sphereGeometry args={[0.85, 64, 64]} />
                <meshBasicMaterial
                    color="#FFF4E6"
                    transparent
                    opacity={0.3}
                    blending={THREE.AdditiveBlending}
                />
            </mesh>

            {/* Middle Corona - Warm orange */}
            <mesh ref={coronaRef}>
                <sphereGeometry args={[0.95, 48, 48]} />
                <meshBasicMaterial
                    color="#FFA500"
                    transparent
                    opacity={0.15}
                    blending={THREE.AdditiveBlending}
                />
            </mesh>

            {/* Outer Corona - Large diffuse glow */}
            <mesh>
                <sphereGeometry args={[1.1, 32, 32]} />
                <meshBasicMaterial
                    color="#FFE4B5"
                    transparent
                    opacity={0.08}
                    blending={THREE.AdditiveBlending}
                />
            </mesh>

            {/* Extended Atmosphere - Very subtle */}
            <mesh>
                <sphereGeometry args={[1.3, 32, 32]} />
                <meshBasicMaterial
                    color="#FFF8DC"
                    transparent
                    opacity={0.03}
                    blending={THREE.AdditiveBlending}
                />
            </mesh>

            {/* Point light for sun illumination */}
            <pointLight
                color="#FFF4E6"
                intensity={2}
                distance={8}
                decay={1.5}
            />
        </group>
    );
};

const Planets = ({ isOpen }) => {
    // State for rotation and hover
    const [rotation, setRotation] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
    const navigate = useNavigate();
    const [hoveredPlanet, setHoveredPlanet] = useState(null);
    const groupRef = useRef();

    // Texture Base URLs
    const THREEX = 'https://raw.githubusercontent.com/jeromeetienne/threex.planets/master/images/';
    const THREE_EX = 'https://raw.githubusercontent.com/mrdoob/three.js/master/examples/textures/planets/';

    // Faculty planets configuration
    const faculties = [
        {
            name: "Computer Science",
            size: 0.18,
            textures: { map: THREEX + 'mercurymap.jpg' }
        },
        {
            name: "Dentistry",
            size: 0.22,
            textures: { map: THREEX + 'venusmap.jpg' },
            hasAtmosphere: true,
            atmosphereColor: '#ffaa00'
        },
        {
            name: "Pharmacy",
            size: 0.23,
            textures: {
                map: THREE_EX + 'earth_atmos_2048.jpg',
                normalMap: THREE_EX + 'earth_normal_2048.jpg',
                specularMap: THREE_EX + 'earth_specular_2048.jpg',
                cloudsMap: THREE_EX + 'earth_clouds_1024.png'
            },
            hasAtmosphere: true,
            hasClouds: true
        },
        {
            name: "Business",
            size: 0.2,
            textures: { map: THREEX + 'marsmap1k.jpg' }
        },
        {
            name: "Engineering",
            size: 0.25,
            textures: { map: THREEX + 'jupitermap.jpg' }
        },
        {
            name: "Mass-Com",
            size: 0.22,
            textures: {
                map: THREEX + 'saturnmap.jpg',
                ringMap: THREEX + 'saturnringcolor.jpg'
            },
            ring: { innerRadius: 0.25, outerRadius: 0.45 }
        },
        {
            name: "Al-Alsun",
            size: 0.2,
            textures: {
                map: THREEX + 'uranusmap.jpg',
                ringMap: THREEX + 'uranusringtrans.gif'
            },
            ring: { innerRadius: 0.24, outerRadius: 0.38 }
        }
    ];

    // Animation loop
    useFrame((state, delta) => {
        if (!isPaused && isOpen) {
            setRotation(prev => prev + delta * 0.3);
        }
    });

    // Hover handlers
    const handlePointerOver = (index) => {
        setHoveredPlanet(index);
        setIsPaused(true);
        document.body.style.cursor = 'pointer';
    };

    const handlePointerOut = () => {
        setHoveredPlanet(null);
        setIsPaused(false);
        document.body.style.cursor = 'auto';
    };

    const handleClick = (facultyName) => {
        // Create a URL-friendly slug
        let slug = facultyName.replace(/\s+/g, '-');
        if (!slug.startsWith('Faculty-of-')) {
            slug = `Faculty-of-${slug}`;
        }
        // Use navigate + encodeURIComponent for robust routing
        navigate(`/faculty/${encodeURIComponent(slug)}`);
    };

    // Orbit configuration
    const orbitRadius = 2.0;
    const centralPlanetY = 0.8; // Reduced from 1.5 to bring closer to book

    if (!isOpen) return null;

    return (
        <group ref={groupRef}>
            {/* Central Planet - Realistic Sun */}
            <Sun position={[0, centralPlanetY, 0]} />

            {/* Orbit Ring - Milky Way Style */}
            <group position={[0, centralPlanetY, 0]} rotation={[Math.PI / 2, 0, 0]}>
                {/* Main ring with galaxy gradient */}
                <mesh>
                    <torusGeometry args={[orbitRadius, 0.02, 16, 100]} />
                    <meshBasicMaterial
                        color="#9D84B7"
                        transparent
                        opacity={0.4}
                    />
                </mesh>
                {/* Inner glow */}
                <mesh>
                    <torusGeometry args={[orbitRadius, 0.04, 16, 100]} />
                    <meshBasicMaterial
                        color="#E8D5F2"
                        transparent
                        opacity={0.15}
                    />
                </mesh>
                {/* Outer cosmic dust */}
                <mesh>
                    <torusGeometry args={[orbitRadius, 0.06, 16, 100]} />
                    <meshBasicMaterial
                        color="#4A3B5C"
                        transparent
                        opacity={0.1}
                    />
                </mesh>
            </group>

            {/* Orbiting Faculty Planets */}
            {faculties.map((faculty, index) => {
                const angle = (index / faculties.length) * Math.PI * 2 + rotation;
                const x = Math.cos(angle) * orbitRadius;
                const z = Math.sin(angle) * orbitRadius;

                return (
                    <group
                        key={index}
                        position={[x, centralPlanetY, z]}
                        onPointerOver={() => handlePointerOver(index)}
                        onPointerOut={handlePointerOut}
                        onClick={() => handleClick(faculty.name)}
                        onPointerDown={() => handleClick(faculty.name)} // Added for reliability
                    >
                        <RealisticPlanet
                            name={faculty.name}
                            size={faculty.size}
                            textures={faculty.textures}
                            hasAtmosphere={faculty.hasAtmosphere}
                            atmosphereColor={faculty.atmosphereColor}
                            hasClouds={faculty.hasClouds}
                            ring={faculty.ring}
                        />

                        {/* Faculty Name on Hover */}
                        {hoveredPlanet === index && (
                            <Text
                                position={[0, faculty.size + 0.3, 0]}
                                fontSize={0.18}
                                color="#ffffff"
                                anchorX="center"
                                anchorY="bottom"
                                fontWeight="bold"
                                outlineWidth={0.015}
                                outlineColor="#000000"
                            >
                                {faculty.name}
                            </Text>
                        )}
                    </group>
                );
            })}
        </group>
    );
};

export default Planets;
