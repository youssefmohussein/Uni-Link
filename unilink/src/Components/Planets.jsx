import React, { useState } from 'react';
import { useSpring, animated, config } from '@react-spring/three';
import RealisticPlanet from './RealisticPlanet';
import WritingText from './WritingText';

const AnimatedPlanetGroup = ({ planet, isOpen, index }) => {
    const [hovered, setHovered] = useState(false);

    const { scale } = useSpring({
        scale: isOpen ? (hovered ? 1.2 : 1) : 0,
        config: { tension: 180, friction: 12, mass: 1, delay: index * 200 + 500 } // Staggered pop-up delay
    });

    const handleClick = () => {
        const slug = planet.name.toLowerCase().replace(/\s+/g, '-');
        window.location.href = `/faculty/${slug}`;
    };

    return (
        <group position={planet.position}>
            {/* Writing Text Animation - On the page surface */}
            <WritingText
                text={planet.name}
                position={[0, 0, 0.05]} // Slightly above 0 to avoid z-fighting
                isOpen={isOpen}
                delay={index * 300 + 200} // Text appears first
                fontSize={0.18}
                color="#1a1a1a" // Ink color
            />

            {/* Pop-up Planet - Floating above */}
            <animated.group scale={scale} position={[0, 0.8, 0]}> {/* Lift planet up */}
                <group
                    onPointerOver={() => { document.body.style.cursor = 'pointer'; setHovered(true); }}
                    onPointerOut={() => { document.body.style.cursor = 'auto'; setHovered(false); }}
                    onClick={handleClick}
                >
                    <RealisticPlanet
                        name={planet.name}
                        textures={planet.textures}
                        size={planet.size}
                        hasAtmosphere={planet.hasAtmosphere}
                        hasClouds={planet.hasClouds}
                        ring={planet.ring}
                    />
                </group>
            </animated.group>
        </group>
    );
};

const Planets = ({ isOpen }) => {
    // Texture Base URLs
    // Using jeromeetienne/threex.planets for reliable textures
    const THREEX = 'https://raw.githubusercontent.com/jeromeetienne/threex.planets/master/images/';
    const THREE_EX = 'https://raw.githubusercontent.com/mrdoob/three.js/master/examples/textures/planets/';

    const planets = [
        // --- LEFT PAGE (Center approx -2.8) ---
        // Range: -3.5 to -2.0
        {
            name: "Computer Science",
            position: [-3.5, 0.05, 0],
            size: 0.18,
            textures: { map: THREEX + 'mercurymap.jpg' }
        },
        {
            name: "Dentistry",
            position: [-2.8, 0.05, -0.2],
            size: 0.22,
            textures: { map: THREEX + 'venusmap.jpg' },
            hasAtmosphere: true,
            atmosphereColor: '#ffaa00'
        },
        {
            name: "Pharmacy",
            position: [-2.1, 0.05, 0],
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

        // --- RIGHT PAGE (Center approx 0.0) ---
        // Range: -0.8 to 1.2
        {
            name: "Business",
            position: [-0.9, 0.05, 0],
            size: 0.2,
            textures: { map: THREEX + 'marsmap1k.jpg' }
        },
        {
            name: "Engineering",
            position: [-0.2, 0.05, -0.2],
            size: 0.35,
            textures: { map: THREEX + 'jupitermap.jpg' }
        },
        {
            name: "Mass-Com", // Shortened for better fit
            position: [0.6, 0.05, 0],
            size: 0.3,
            textures: {
                map: THREEX + 'saturnmap.jpg',
                ringMap: THREEX + 'saturnringcolor.jpg'
            },
            ring: { innerRadius: 0.35, outerRadius: 0.6 }
        },
        {
            name: "Architecture",
            position: [1.3, 0.05, -0.1],
            size: 0.25,
            textures: {
                map: THREEX + 'uranusmap.jpg',
                ringMap: THREEX + 'uranusringtrans.gif'
            },
            ring: { innerRadius: 0.3, outerRadius: 0.45 }
        }
    ];

    return (
        <group>
            {planets.map((planet, index) => (
                <AnimatedPlanetGroup
                    key={index}
                    planet={planet}
                    isOpen={isOpen}
                    index={index}
                />
            ))}
        </group>
    );
};

export default Planets;
