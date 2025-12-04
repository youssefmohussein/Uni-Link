import React, { useState, useRef, useMemo } from 'react';
import { useFrame, useLoader } from '@react-three/fiber';
import { Text } from '@react-three/drei';
import * as THREE from 'three';
import RealisticPlanet from './RealisticPlanet';

// Realistic Sun Component
const Sun = ({ position }) => {
    const sunTexture = useLoader(THREE.TextureLoader, 'https://raw.githubusercontent.com/jeromeetienne/threex.planets/master/images/sunmap.jpg');

    return (
        <group position={position}>
            {/* Main Sun Sphere with Texture */}
            <mesh>
                <sphereGeometry args={[0.8, 64, 64]} />
                <meshStandardMaterial
                    map={sunTexture}
                    emissive="#FDB813"
                    emissiveMap={sunTexture}
                    emissiveIntensity={2.0}
                    toneMapped={false}
                />
            </mesh>
            {/* Outer Glow Effect */}
            <mesh>
                <sphereGeometry args={[0.88, 32, 32]} />
                <meshBasicMaterial
                    color="#FFA500"
                    transparent
                    opacity={0.2}
                />
            </mesh>
        </group>
    );
};

const Planets = ({ isOpen }) => {
    // State for rotation and hover
    const [rotation, setRotation] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
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
            name: "Architecture",
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
        const slug = facultyName.toLowerCase().replace(/\s+/g, '-');
        window.location.href = `/faculty/${slug}`;
    };

    // Orbit configuration
    const orbitRadius = 2.0;
    const centralPlanetY = 0.8; // Reduced from 1.5 to bring closer to book

    if (!isOpen) return null;

    return (
        <group ref={groupRef}>
            {/* Central Planet - Realistic Sun */}
            <Sun position={[0, centralPlanetY, 0]} />

            {/* Orbit Ring Visual */}
            <mesh position={[0, centralPlanetY, 0]} rotation={[Math.PI / 2, 0, 0]}>
                <torusGeometry args={[orbitRadius, 0.01, 16, 100]} />
                <meshBasicMaterial color="#4a90e2" transparent opacity={0.3} />
            </mesh>

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
                                fontSize={0.15}
                                color="#ffffff"
                                anchorX="center"
                                anchorY="bottom"
                                font="https://raw.githubusercontent.com/google/fonts/main/ofl/patrickhand/PatrickHand-Regular.ttf"
                                outlineWidth={0.01}
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
