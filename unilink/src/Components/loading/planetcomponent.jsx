import React, { useState, useRef, useMemo } from 'react';
import { Html } from '@react-three/drei';
import { useSpring, animated } from '@react-spring/three';
import { useFrame } from '@react-three/fiber';
import * as THREE from 'three';

const PointCloudSphere = ({ size, color }) => {
    const points = useMemo(() => {
        const temp = [];
        const count = 2000; // Increased for better visibility
        const radius = size;

        for (let i = 0; i < count; i++) {
            const theta = Math.random() * Math.PI * 2;
            const phi = Math.acos((Math.random() * 2) - 1);

            let noise = 0;
            noise += Math.sin(theta * 2.5) * Math.cos(phi * 2.5);
            noise += Math.sin(theta * 6 + phi) * 0.5;
            noise += Math.cos(phi * 12) * 0.2;

            if (noise > 0.1) {
                const x = radius * Math.sin(phi) * Math.cos(theta);
                const y = radius * Math.sin(phi) * Math.sin(theta);
                const z = radius * Math.cos(phi);
                temp.push(x, y, z);
            }
        }
        return new Float32Array(temp);
    }, [size]);

    return (
        <points>
            <bufferGeometry>
                <bufferAttribute
                    attach="attributes-position"
                    count={points.length / 3}
                    array={points}
                    itemSize={3}
                />
            </bufferGeometry>
            <pointsMaterial
                size={0.012}
                color={color}
                transparent
                opacity={1.0} // Increased for better visibility
                blending={THREE.AdditiveBlending}
                sizeAttenuation={true}
                depthWrite={false}
            />
        </points>
    );
};

const NetworkLines = ({ size, color }) => {
    const linesGroupRef = useRef();
    const markersRef = useRef();
    const dataPacketsRef = useRef();

    const cities = useMemo(() => [
        // North America
        { lat: 40.7128, lon: -74.0060 }, { lat: 34.0522, lon: -118.2437 },
        { lat: 41.8781, lon: -87.6298 }, { lat: 25.7617, lon: -80.1918 },
        { lat: 49.2827, lon: -123.1207 }, { lat: 19.4326, lon: -99.1332 },
        // South America
        { lat: -23.5505, lon: -46.6333 }, { lat: -34.6037, lon: -58.3816 },
        { lat: -12.0464, lon: -77.0428 }, { lat: 4.7110, lon: -74.0721 },
        // Europe
        { lat: 51.5074, lon: -0.1278 }, { lat: 48.8566, lon: 2.3522 },
        { lat: 55.7558, lon: 37.6173 }, { lat: 40.4168, lon: -3.7038 },
        { lat: 52.5200, lon: 13.4050 }, { lat: 41.9028, lon: 12.4964 },
        // Africa
        { lat: -33.9249, lon: 18.4241 }, { lat: 30.0444, lon: 31.2357 },
        { lat: -1.2921, lon: 36.8219 }, { lat: 6.5244, lon: 3.3792 },
        // Asia
        { lat: 35.6762, lon: 139.6503 }, { lat: 39.9042, lon: 116.4074 },
        { lat: 1.3521, lon: 103.8198 }, { lat: 19.0760, lon: 72.8777 },
        { lat: 25.2048, lon: 55.2708 }, { lat: 13.7563, lon: 100.5018 },
        { lat: 37.5665, lon: 126.9780 },
        // Oceania
        { lat: -33.8688, lon: 151.2093 }, { lat: -36.8485, lon: 174.7633 },
        { lat: -31.9505, lon: 115.8605 }
    ], []);

    const latLonToVector3 = (lat, lon, radius) => {
        const phi = (90 - lat) * (Math.PI / 180);
        const theta = (lon + 180) * (Math.PI / 180);
        return new THREE.Vector3(
            -radius * Math.sin(phi) * Math.cos(theta),
            radius * Math.cos(phi),
            radius * Math.sin(phi) * Math.sin(theta)
        );
    };

    const cityPositions = useMemo(() => {
        const radius = size * 1.08;
        return cities.map(city => latLonToVector3(city.lat, city.lon, radius));
    }, [cities, size]);

    const { connectionMeshes, connectionCurves } = useMemo(() => {
        const meshes = [];
        const curves = [];
        const radius = size * 1.08;
        const routes = [];

        for (let i = 0; i < cities.length; i++) {
            let connections = 0;
            let attempts = 0;
            while (connections < 2 && attempts < 10) {
                const target = Math.floor(Math.random() * cities.length);
                if (target !== i) {
                    routes.push([i, target]);
                    connections++;
                }
                attempts++;
            }
        }

        routes.forEach(([i, j]) => {
            const start = cityPositions[i];
            const end = cityPositions[j];
            const distance = start.distanceTo(end);

            const midPoint = new THREE.Vector3()
                .lerpVectors(start, end, 0.5)
                .normalize()
                .multiplyScalar(radius + distance * 0.4);

            const curve = new THREE.QuadraticBezierCurve3(start, midPoint, end);
            curves.push(curve);

            const tubeGeometry = new THREE.TubeGeometry(curve, 40, 0.0025, 6, false);
            meshes.push(tubeGeometry);
        });

        return { connectionMeshes: meshes, connectionCurves: curves };
    }, [cityPositions, size, cities.length]);

    const dataPackets = useMemo(() => {
        const packets = [];
        // One packet per line
        connectionCurves.forEach((curve, index) => {
            packets.push({
                lineIndex: index,
                progress: Math.random(),
                speed: 0.3 + Math.random() * 0.4,
                curve: curve
            });
        });
        return packets;
    }, [connectionCurves]);

    useFrame((state, delta) => {
        if (linesGroupRef.current) {
            linesGroupRef.current.children.forEach((line, index) => {
                if (line.material) {
                    const offset = index * 0.5;
                    const pulse = Math.sin(state.clock.elapsedTime * 2 + offset) * 0.05 + 0.2;
                    line.material.opacity = pulse;
                }
            });
        }

        if (markersRef.current && markersRef.current.material) {
            const pulse = Math.sin(state.clock.elapsedTime * 3) * 0.2 + 0.8;
            markersRef.current.material.opacity = pulse;
        }

        if (dataPacketsRef.current && dataPacketsRef.current.geometry) {
            const positions = dataPacketsRef.current.geometry.attributes.position;

            dataPackets.forEach((packet, i) => {
                packet.progress += delta * packet.speed;

                if (packet.progress >= 1) {
                    packet.progress = 0;
                }

                const point = packet.curve.getPoint(packet.progress);
                positions.setXYZ(i, point.x, point.y, point.z);
            });

            positions.needsUpdate = true;
        }
    });

    const packetPositions = useMemo(() => {
        const positions = new Float32Array(dataPackets.length * 3);
        dataPackets.forEach((packet, i) => {
            const point = packet.curve.getPoint(packet.progress);
            positions[i * 3] = point.x;
            positions[i * 3 + 1] = point.y;
            positions[i * 3 + 2] = point.z;
        });
        return positions;
    }, [dataPackets]);

    return (
        <group>
            <group ref={linesGroupRef}>
                {connectionMeshes.map((geometry, index) => (
                    <mesh key={`tube-${index}`} geometry={geometry}>
                        <meshBasicMaterial
                            color={color}
                            transparent
                            opacity={0.2}
                            blending={THREE.AdditiveBlending}
                        />
                    </mesh>
                ))}
            </group>

            <points ref={dataPacketsRef}>
                <bufferGeometry>
                    <bufferAttribute
                        attach="attributes-position"
                        count={dataPackets.length}
                        array={packetPositions}
                        itemSize={3}
                    />
                </bufferGeometry>
                <pointsMaterial
                    size={0.05}
                    color="#ffffff"
                    transparent
                    opacity={1.0}
                    blending={THREE.AdditiveBlending}
                    sizeAttenuation={true}
                    depthWrite={false}
                />
            </points>

            <points ref={markersRef}>
                <bufferGeometry>
                    <bufferAttribute
                        attach="attributes-position"
                        count={cityPositions.length}
                        array={new Float32Array(cityPositions.flatMap(p => [p.x, p.y, p.z]))}
                        itemSize={3}
                    />
                </bufferGeometry>
                <pointsMaterial
                    size={0.035}
                    color={color}
                    transparent
                    opacity={0.9}
                    blending={THREE.AdditiveBlending}
                    sizeAttenuation={true}
                />
            </points>
        </group>
    );
};

const HolographicEarth = ({ isOpen }) => {
    const groupRef = useRef();
    const [hovered, setHovered] = useState(false);
    const [dbConnected, setDbConnected] = useState(false);
    const [dbStatus, setDbStatus] = useState('Checking...');

    const { scale } = useSpring({
        scale: isOpen ? (hovered ? 1.05 : 1) : 0,
        config: { tension: 100, friction: 20 }
    });

    // Check database connection on mount
    React.useEffect(() => {
        const checkDatabase = async () => {
            try {
                console.log('🔍 Checking database connection...');

                // Add timeout to make check faster (1 second max)
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 1000);

                // Use existing working endpoint that requires database access
                const response = await fetch('http://localhost:8000/getUsers', {
                    credentials: 'include',
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                console.log('📡 Response status:', response.status);
                const data = await response.json();
                console.log('📦 Response data:', data);

                // If we get data back (even if not authenticated), database is working
                if (response.ok && data.status === 'success' && data.data) {
                    setDbConnected(true);
                    setDbStatus('Database Connected');
                    console.log('✅ Database is connected');
                } else {
                    setDbConnected(false);
                    setDbStatus('Database Not Connected');
                    console.log('❌ Database NOT connected');
                }
            } catch (error) {
                setDbConnected(false);
                setDbStatus('Database Not Connected');
                console.log('❌ Error checking database:', error.name === 'AbortError' ? 'Timeout' : error);
            }
        };

        if (isOpen) {
            checkDatabase();
        }
    }, [isOpen]);

    useFrame((state) => {
        if (groupRef.current) {
            groupRef.current.rotation.y += 0.003;
        }
    });

    const color = "#00ffff";
    const size = 0.65;

    return (
        <animated.group position={[0, 0.7, 0]} scale={scale}>
            <group ref={groupRef}>
                <PointCloudSphere size={size} color={color} />
                {/* Only render NetworkLines if database is connected */}
                {dbConnected && <NetworkLines size={size} color={color} />}

                <mesh frustumCulled={false}>
                    <sphereGeometry args={[size * 0.98, 12, 12]} />
                    <meshBasicMaterial
                        color={color}
                        wireframe
                        transparent
                        opacity={0.08}
                        blending={THREE.AdditiveBlending}
                        depthWrite={false}
                    />
                </mesh>
            </group>

            <mesh
                visible={false}
                scale={1.5}
                onPointerOver={() => { document.body.style.cursor = 'pointer'; setHovered(true); }}
                onPointerOut={() => { document.body.style.cursor = 'auto'; setHovered(false); }}
            >
                <sphereGeometry args={[size, 16, 16]} />
            </mesh>

            {hovered && isOpen && (
                <Html
                    position={[0, size + 0.4, 0]}
                    center
                    style={{ pointerEvents: 'none', whiteSpace: 'nowrap' }}
                >
                    <div
                        className="px-6 py-3 rounded-xl text-lg font-bold"
                        style={{
                            background: 'rgba(0, 20, 40, 0.85)',
                            backdropFilter: 'blur(12px)',
                            border: `1px solid ${dbConnected ? color : '#ff4444'}`,
                            boxShadow: `0 0 20px ${dbConnected ? color : '#ff4444'}60`,
                            color: '#ffffff',
                            fontFamily: "'Orbitron', sans-serif",
                            letterSpacing: '0.1em',
                            textTransform: 'uppercase',
                            textAlign: 'center'
                        }}
                    >
                        <div>Global Network</div>
                        <div style={{
                            fontSize: '0.6em',
                            color: dbConnected ? color : '#ff4444',
                            marginTop: '4px'
                        }}>
                            {dbStatus}
                        </div>
                    </div>
                </Html>
            )}
        </animated.group>
    );
};

export default HolographicEarth;
