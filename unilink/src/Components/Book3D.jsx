import React, { useRef, useState, useMemo } from 'react';
import { useFrame } from '@react-three/fiber';
import { useScroll, Text, RoundedBox } from '@react-three/drei';
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

        if (leftPagesRef.current && rightPagesRef.current) {
            const fanProgress = isOpen ? 1 : 0;

            leftPagesRef.current.children.forEach((page, i) => {
                const targetRotation = fanProgress * (i * 0.015 + Math.sin(i * 0.5) * 0.002);
                page.rotation.y = THREE.MathUtils.damp(page.rotation.y, targetRotation, 3 - (i * 0.05), delta);
            });

            rightPagesRef.current.children.forEach((page, i) => {
                const targetRotation = -fanProgress * (i * 0.015 + Math.cos(i * 0.5) * 0.002);
                page.rotation.y = THREE.MathUtils.damp(page.rotation.y, targetRotation, 3 - (i * 0.05), delta);
            });
        }
    });

    // Better quality materials
    const leatherMaterial = new THREE.MeshStandardMaterial({
        color: "#1a1a2e",
        roughness: 0.7,
        metalness: 0.15,
        envMapIntensity: 0.5,
    });

    // Better paper material with slight transparency
    const paperMaterial = new THREE.MeshPhysicalMaterial({
        color: '#fdfbf7',
        roughness: 0.9,
        metalness: 0,
        clearcoat: 0.1,
        clearcoatRoughness: 0.5,
        side: THREE.DoubleSide,
    });

    const goldFoilMaterial = new THREE.MeshStandardMaterial({
        color: "#d4af37",
        roughness: 0.25,
        metalness: 0.95,
        emissive: "#8b7355",
        emissiveIntensity: 0.2,
    });

    // Moderate page count - balance between quality and performance
    const FannedPages = ({ count = 15, isLeft = true }) => {
        const pages = useMemo(() => {
            return Array.from({ length: count }).map((_, i) => ({
                yOffset: (Math.random() - 0.5) * 0.01,
                zOffset: (isLeft ? -0.15 : -0.1) + (i * 0.008),
                widthVar: (Math.random() - 0.5) * 0.02,
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
                        <boxGeometry args={[2.9 + data.widthVar, 3.9, 0.01]} />
                        <primitive object={paperMaterial} />
                    </mesh>
                ))}
            </group>
        );
    };

    return (
        <group ref={group} scale={[1.3, 1.3, 1.3]}>
            {/* Spine */}
            <mesh position={[-1.5, 0, 0]} material={leatherMaterial} castShadow receiveShadow>
                <boxGeometry args={[0.25, 4, 0.6]} />
            </mesh>

            {/* Gutter Shadow */}
            <mesh position={[-1.5, 0, 0.2]}>
                <boxGeometry args={[0.12, 3.8, 0.12]} />
                <meshBasicMaterial color="#000000" opacity={0.6} transparent />
            </mesh>

            {/* Back Cover with rounded edges */}
            <RoundedBox
                args={[3, 4, 0.18]}
                radius={0.04}
                smoothness={2}
                position={[0, 0, -0.25]}
                material={leatherMaterial}
                castShadow
                receiveShadow
            />

            {/* Right Pages */}
            <group ref={rightPagesRef} position={[0, 0, 0]}>
                <FannedPages count={15} isLeft={false} />
            </group>

            {/* Planets */}
            <group position={[0, 0, 1]} rotation={[Math.PI / 3, 0, 0]}>
                <Planets isOpen={isOpen} />
            </group>

            {/* Front Cover Group */}
            <group position={[-1.5, 0, 0.25]} ref={frontCoverRef}>
                {/* Front Cover with rounded edges */}
                <RoundedBox
                    args={[3, 4, 0.18]}
                    radius={0.04}
                    smoothness={2}
                    position={[1.5, 0, 0]}
                    material={leatherMaterial}
                    castShadow
                    receiveShadow
                />

                {/* Left Pages */}
                <group ref={leftPagesRef} position={[0, 0, 0]}>
                    <FannedPages count={15} isLeft={true} />
                </group>

                {/* Title and decorations */}
                <group position={[1.5, 0, 0.095]}>
                    <Text
                        position={[0, 0.8, 0]}
                        fontSize={0.55}
                        anchorX="center"
                        anchorY="middle"
                        font="https://fonts.gstatic.com/s/inter/v12/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZ9hjp-Ek-_EeA.woff"
                        material={goldFoilMaterial}
                        castShadow
                        letterSpacing={0.05}
                    >
                        UNILINK
                    </Text>

                    {/* Decorative borders */}
                    <mesh position={[0, 0.4, 0]} material={goldFoilMaterial} castShadow>
                        <boxGeometry args={[2.3, 0.04, 0.008]} />
                    </mesh>
                    <mesh position={[0, 1.7, 0]} material={goldFoilMaterial} castShadow>
                        <boxGeometry args={[2.6, 0.025, 0.008]} />
                    </mesh>
                    <mesh position={[0, -1.7, 0]} material={goldFoilMaterial} castShadow>
                        <boxGeometry args={[2.6, 0.025, 0.008]} />
                    </mesh>

                    {/* Corner ornaments */}
                    {[
                        [-1.2, 1.8],
                        [1.2, 1.8],
                        [-1.2, -1.8],
                        [1.2, -1.8]
                    ].map(([x, y], i) => (
                        <mesh key={i} position={[x, y, 0]} material={goldFoilMaterial} castShadow>
                            <boxGeometry args={[0.08, 0.08, 0.008]} />
                        </mesh>
                    ))}
                </group>
            </group>
        </group>
    );
};

export default Book3D;
