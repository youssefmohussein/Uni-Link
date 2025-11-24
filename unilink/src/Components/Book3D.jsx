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

    // Materials
    const leatherMaterial = new THREE.MeshStandardMaterial({
        color: "#1a1a2e",
        roughness: 0.7,
        metalness: 0.15,
        envMapIntensity: 0.5,
    });

    // Enhanced paper material with micro-fiber texture and translucency
    const paperMaterial = useMemo(() => {
        const canvas = document.createElement('canvas');
        canvas.width = 512;
        canvas.height = 512;
        const ctx = canvas.getContext('2d');

        // Base paper color
        ctx.fillStyle = '#fdfbf7';
        ctx.fillRect(0, 0, 512, 512);

        // Add micro-fiber texture
        const imageData = ctx.getImageData(0, 0, 512, 512);
        for (let i = 0; i < imageData.data.length; i += 4) {
            const fiber = (Math.random() - 0.5) * 8;
            imageData.data[i] += fiber;
            imageData.data[i + 1] += fiber;
            imageData.data[i + 2] += fiber - 2;
        }
        ctx.putImageData(imageData, 0, 0);

        // Add subtle imperfections and frays
        ctx.fillStyle = 'rgba(200, 190, 180, 0.05)';
        for (let i = 0; i < 20; i++) {
            const x = Math.random() * 512;
            const y = Math.random() * 512;
            const size = Math.random() * 3 + 1;
            ctx.fillRect(x, y, size, size);
        }

        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = texture.wrapT = THREE.RepeatWrapping;

        return new THREE.MeshPhysicalMaterial({
            map: texture,
            color: '#fdfbf7',
            roughness: 0.95,
            metalness: 0,
            clearcoat: 0.08,
            clearcoatRoughness: 0.7,
            transmission: 0.02,
            thickness: 0.4,
            side: THREE.DoubleSide,
        });
    }, []);

    const goldFoilMaterial = new THREE.MeshStandardMaterial({
        color: "#d4af37",
        roughness: 0.25,
        metalness: 0.95,
        emissive: "#8b7355",
        emissiveIntensity: 0.2,
    });

    // Inner cover material (Endpapers)
    const innerCoverMaterial = useMemo(() => {
        return new THREE.MeshStandardMaterial({
            color: "#2c2c3a",
            roughness: 0.9,
            metalness: 0.0,
            side: THREE.DoubleSide,
        });
    }, []);

    // High-density page count for realistic stack look
    const FannedPages = ({ count = 40, isLeft = true }) => {
        const pages = useMemo(() => {
            return Array.from({ length: count }).map((_, i) => ({
                yOffset: (Math.random() - 0.5) * 0.005,
                zOffset: (isLeft ? -0.12 : -0.08) + (i * 0.0035),
                widthVar: (Math.random() - 0.5) * 0.015,
                rotY: (Math.random() - 0.5) * 0.002,
            }));
        }, [count, isLeft]);

        const xOffset = isLeft ? 1.5 : 0;

        return (
            <group>
                {pages.map((data, i) => (
                    <mesh
                        key={i}
                        position={[xOffset, data.yOffset, data.zOffset]}
                        rotation={[0, data.rotY, 0]}
                        castShadow
                        receiveShadow
                    >
                        <boxGeometry args={[2.9 + data.widthVar, 3.9, 0.004]} />
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

            {/* Back Cover */}
            <group position={[0, 0, -0.25]}>
                <RoundedBox
                    args={[3, 4, 0.18]}
                    radius={0.04}
                    smoothness={2}
                    material={leatherMaterial}
                    castShadow
                    receiveShadow
                />
                {/* Inner Cover */}
                <mesh position={[0, 0, 0.091]} material={innerCoverMaterial}>
                    <planeGeometry args={[2.9, 3.9]} />
                </mesh>
            </group>

            {/* Right Pages */}
            <group ref={rightPagesRef} position={[0, 0, 0]}>
                <FannedPages count={35} isLeft={false} />
            </group>

            {/* Planets/Plants */}
            <group position={[0, 0, 1]} rotation={[Math.PI / 3, 0, 0]}>
                <Planets isOpen={isOpen} />
            </group>

            {/* Front Cover Group */}
            <group position={[-1.5, 0, 0.25]} ref={frontCoverRef}>
                <group position={[1.5, 0, 0]}>
                    <RoundedBox
                        args={[3, 4, 0.18]}
                        radius={0.04}
                        smoothness={2}
                        material={leatherMaterial}
                        castShadow
                        receiveShadow
                    />
                    {/* Inner Cover */}
                    <mesh position={[0, 0, -0.091]} rotation={[0, Math.PI, 0]} material={innerCoverMaterial}>
                        <planeGeometry args={[2.9, 3.9]} />
                    </mesh>
                </group>

                {/* Left Pages */}
                <group ref={leftPagesRef} position={[0, 0, 0]}>
                    <FannedPages count={35} isLeft={true} />
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
                        UNI-LINK
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
