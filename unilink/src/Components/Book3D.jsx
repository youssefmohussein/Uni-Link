import React, { useRef, useState, useMemo } from 'react';
import { useFrame } from '@react-three/fiber';
import { useScroll, Text, MeshTransmissionMaterial } from '@react-three/drei';
import * as THREE from 'three';
import Planets from './Planets';

const Book3D = () => {
    const group = useRef();
    const frontCoverRef = useRef();
    const leftPagesRef = useRef();
    const rightPagesRef = useRef();
    const scroll = useScroll();
    const [isOpen, setIsOpen] = useState(false);

    useFrame((state, delta) => {
        const offset = scroll.offset;

        if (group.current) {
            // Movement & Rotation Logic
            const targetX = THREE.MathUtils.lerp(3.0, 0.5, offset);
            group.current.position.x = THREE.MathUtils.damp(group.current.position.x, targetX, 4, delta);

            const targetRotationY = THREE.MathUtils.lerp(-Math.PI / 4, 0, offset);
            group.current.rotation.y = THREE.MathUtils.damp(group.current.rotation.y, targetRotationY, 4, delta);

            const targetRotationX = THREE.MathUtils.lerp(Math.PI / 6, -Math.PI / 3, offset);
            group.current.rotation.x = THREE.MathUtils.damp(group.current.rotation.x, targetRotationX, 4, delta);

            group.current.position.y = -0.8 + Math.sin(state.clock.elapsedTime) * 0.1;

            const targetScale = THREE.MathUtils.lerp(1.3, 1, offset);
            const currentScale = THREE.MathUtils.damp(group.current.scale.x, targetScale, 4, delta);
            group.current.scale.set(currentScale, currentScale, currentScale);
        }

        if (frontCoverRef.current) {
            let targetOpenRotation = 0;
            if (offset > 0.9) {
                const openProgress = (offset - 0.9) / 0.1;
                targetOpenRotation = -Math.PI * 0.95 * openProgress;

                if (openProgress > 0.8 && !isOpen) setIsOpen(true);
                if (openProgress <= 0.8 && isOpen) setIsOpen(false);
            } else {
                if (isOpen) setIsOpen(false);
            }

            frontCoverRef.current.rotation.y = THREE.MathUtils.damp(
                frontCoverRef.current.rotation.y,
                targetOpenRotation,
                5,
                delta
            );
        }

        // Animate page fanning with organic delay
        if (leftPagesRef.current && rightPagesRef.current) {
            const fanProgress = isOpen ? 1 : 0;

            leftPagesRef.current.children.forEach((page, i) => {
                // Non-linear fanning for more natural look
                const targetRotation = fanProgress * (i * 0.015 + Math.sin(i * 0.5) * 0.002);
                page.rotation.y = THREE.MathUtils.damp(page.rotation.y, targetRotation, 3 - (i * 0.05), delta);
            });

            rightPagesRef.current.children.forEach((page, i) => {
                const targetRotation = -fanProgress * (i * 0.015 + Math.cos(i * 0.5) * 0.002);
                page.rotation.y = THREE.MathUtils.damp(page.rotation.y, targetRotation, 3 - (i * 0.05), delta);
            });
        }
    });

    // --- MATERIALS ---
    const leatherMaterial = new THREE.MeshStandardMaterial({
        color: "#2563eb",
        roughness: 0.8,
        metalness: 0.1,
        bumpScale: 0.02,
    });

    // Translucent Paper Material
    const paperProps = {
        color: '#fdfbf7',
        roughness: 1.0, // Matte
        transmission: 0.15, // Slight translucency
        thickness: 0.1, // Light bleed
        ior: 1.5,
        chromaticAberration: 0.02,
        anisotropy: 0.1,
        distortion: 0.1,
        distortionScale: 0.1,
        temporalDistortion: 0,
        background: new THREE.Color('#fdfbf7')
    };

    const goldFoilMaterial = new THREE.MeshStandardMaterial({
        color: "#FFD700",
        roughness: 0.2,
        metalness: 1.0,
        emissive: "#B8860B",
        emissiveIntensity: 0.3
    });

    // Fanned Pages Component with Organic Variation
    const FannedPages = ({ count = 24, isLeft = true }) => {
        // Generate static random variations once
        const pages = useMemo(() => {
            return Array.from({ length: count }).map((_, i) => ({
                yOffset: (Math.random() - 0.5) * 0.01, // Tiny vertical jitter
                zOffset: (isLeft ? -0.15 : -0.1) + (i * 0.006),
                widthVar: (Math.random() - 0.5) * 0.02, // Uneven page widths
            }));
        }, [count, isLeft]);

        const xOffset = isLeft ? 1.5 : 0;

        return (
            <group>
                {pages.map((data, i) => (
                    <mesh
                        key={i}
                        position={[xOffset, data.yOffset, data.zOffset]}
                        castShadow
                        receiveShadow
                    >
                        <boxGeometry args={[2.9 + data.widthVar, 3.9, 0.008]} /> {/* Thinner pages */}
                        <MeshTransmissionMaterial {...paperProps} />
                    </mesh>
                ))}
            </group>
        );
    };

    return (
        <group ref={group} scale={[1.3, 1.3, 1.3]}>
            {/* Spine */}
            <mesh position={[-1.5, 0, 0]} material={leatherMaterial} castShadow>
                <boxGeometry args={[0.25, 4, 0.6]} />
            </mesh>

            {/* Gutter Shadow (Inner Spine) */}
            <mesh position={[-1.5, 0, 0.2]}>
                <boxGeometry args={[0.1, 3.8, 0.1]} />
                <meshBasicMaterial color="#000000" opacity={0.5} transparent />
            </mesh>

            {/* Back Cover - RIGHT SIDE */}
            <mesh position={[0, 0, -0.25]} material={leatherMaterial} castShadow receiveShadow>
                <boxGeometry args={[3, 4, 0.15]} />
            </mesh>

            {/* Right Fanned Pages */}
            <group ref={rightPagesRef} position={[0, 0, 0]}>
                <FannedPages count={24} isLeft={false} />
            </group>

            {/* Planets Pop-up */}
            <group position={[0, 0, 1]} rotation={[Math.PI / 3, 0, 0]}>
                <Planets isOpen={isOpen} />
            </group>

            {/* Front Cover Group - LEFT SIDE */}
            <group position={[-1.5, 0, 0.25]} ref={frontCoverRef}>
                {/* Front Cover */}
                <mesh position={[1.5, 0, 0]} material={leatherMaterial} castShadow receiveShadow>
                    <boxGeometry args={[3, 4, 0.15]} />
                </mesh>

                {/* Left Fanned Pages */}
                <group ref={leftPagesRef} position={[0, 0, 0]}>
                    <FannedPages count={24} isLeft={true} />
                </group>

                {/* Gold Foil Stamping */}
                <group position={[1.5, 0, 0.08]}>
                    <Text
                        position={[0, 1, 0]}
                        fontSize={0.5}
                        anchorX="center"
                        anchorY="middle"
                        font="https://fonts.gstatic.com/s/inter/v12/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZ9hjp-Ek-_EeA.woff"
                        material={goldFoilMaterial}
                        castShadow
                    >
                        Uni-Link
                    </Text>

                    {/* Decorative Elements */}
                    <mesh position={[0, 0.6, 0]} material={goldFoilMaterial} castShadow>
                        <boxGeometry args={[2.2, 0.05, 0.01]} />
                    </mesh>
                    <mesh position={[0, 1.9, 0]} material={goldFoilMaterial} castShadow>
                        <boxGeometry args={[2.8, 0.02, 0.01]} />
                    </mesh>
                    <mesh position={[0, -1.9, 0]} material={goldFoilMaterial} castShadow>
                        <boxGeometry args={[2.8, 0.02, 0.01]} />
                    </mesh>
                </group>
            </group>
        </group>
    );
};

export default Book3D;
