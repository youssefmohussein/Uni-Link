import React, { useState } from 'react';
import { Html } from '@react-three/drei';
import { useSpring, animated } from '@react-spring/three';

const Planet = ({ position, color, name, isOpen, size = 0.3, hasRing = false, textureType = "standard", ringColor, ringRotation = [Math.PI / 2, 0, 0] }) => {
    const [hovered, setHovered] = useState(false);

    const { scale } = useSpring({
        scale: isOpen ? (hovered ? 1.2 : 1) : 0,
        config: { tension: 150, friction: 12 }
    });

    const handleClick = () => {
        const slug = name.toLowerCase().replace(/\s+/g, '-');
        window.location.href = `/faculty/${slug}`;
    };

    return (
        <animated.group position={position} scale={scale}>
            <mesh
                onPointerOver={() => { document.body.style.cursor = 'pointer'; setHovered(true); }}
                onPointerOut={() => { document.body.style.cursor = 'auto'; setHovered(false); }}
                onClick={handleClick}
            >
                <sphereGeometry args={[size, 32, 32]} />
                <meshStandardMaterial
                    color={color}
                    roughness={textureType === "gas" ? 0.8 : 0.4}
                    metalness={textureType === "metal" ? 0.8 : 0.2}
                />
            </mesh>

            {/* Ring */}
            {hasRing && (
                <mesh rotation={ringRotation}>
                    <ringGeometry args={[size * 1.4, size * 2.2, 32]} />
                    <meshStandardMaterial color={ringColor || color} opacity={0.6} transparent side={2} />
                </mesh>
            )}

            {hovered && isOpen && (
                <Html
                    zIndexRange={[100, 0]}
                    center // Centers the tooltip on the position
                    position={[0, size + 0.3, 0]}
                    style={{ pointerEvents: 'none', whiteSpace: 'nowrap' }}
                >
                    <div
                        className="px-4 py-2 rounded-xl text-sm font-bold"
                        style={{
                            background: 'rgba(0, 0, 0, 0.7)',
                            backdropFilter: 'blur(10px)',
                            border: '1px solid rgba(100, 200, 255, 0.3)',
                            boxShadow: '0 0 15px rgba(0, 150, 255, 0.5), inset 0 0 10px rgba(0, 150, 255, 0.2)',
                            color: '#e0f2fe',
                            textShadow: '0 0 5px rgba(0, 200, 255, 0.8)',
                            fontFamily: "'Orbitron', sans-serif",
                            letterSpacing: '0.05em',
                        }}
                    >
                        {name}
                    </div>
                </Html>
            )}
        </animated.group>
    );
};

const Planets = ({ isOpen }) => {
    // Corrected Coordinates based on Book Geometry:
    // Left Page Range: x = [-4.5, -1.5] (Center: -3.0)
    // Right Page Range: x = [-1.5, 1.5] (Center: 0.0)

    const planets = [
        // --- LEFT PAGE (x ~ -3.0) ---
        { name: "Computer Science", color: "#A5A5A5", position: [-3.8, 0.5, 0], size: 0.15, textureType: "standard" },
        { name: "Dentistry", color: "#E3BB76", position: [-3.0, 0.8, -0.3], size: 0.22, textureType: "gas" },
        { name: "Pharmacy", color: "#22A6F3", position: [-2.2, 0.6, 0], size: 0.23, textureType: "standard" },

        // --- RIGHT PAGE (x ~ 0.0) ---
        { name: "Business", color: "#DD4C22", position: [-0.8, 0.6, 0], size: 0.18, textureType: "standard" },
        { name: "Engineering", color: "#D8CA9D", position: [0.0, 0.8, -0.3], size: 0.35, textureType: "gas" },
        { name: "Mass-Comunication", color: "#EAD6B8", position: [0.8, 0.5, 0], size: 0.3, textureType: "gas", hasRing: true, ringColor: "#C5A881" },
        { name: "Architecture", color: "#D1F4FA", position: [1.4, 0.7, -0.2], size: 0.25, textureType: "gas", hasRing: true, ringColor: "#FFFFFF", ringRotation: [Math.PI / 1.8, 0, 0] },
    ];

    return (
        <group>
            {planets.map((planet, index) => (
                <Planet
                    key={index}
                    {...planet}
                    isOpen={isOpen}
                />
            ))}
        </group>
    );
};

export default Planets;
