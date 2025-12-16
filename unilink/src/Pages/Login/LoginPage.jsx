import React, { useState } from "react";
import LightRays from "../../Components/Login_Components/LightRays/LightRays";
import GlassSurface from "../../Components/Login_Components/LiquidGlass/GlassSurface";
import AnimatedContent from '../../Animations/AnimatedContent/AnimatedContent';
import authHandler from '../../handlers/authHandler';

const GlassInput = ({ type, placeholder, value, onChange, className = '' }) => {
    const isPassword = type === 'password';
    const [isVisible, setIsVisible] = useState(false);

    const inputType = isPassword ? (isVisible ? 'text' : 'password') : type;

    return (
        <GlassSurface
            width="100%"
            height={55}
            borderRadius={12}
            backgroundOpacity={0.15}
            blur={12}
            className={`!p-0 ${className} relative`}
        >
            <input
                type={inputType}
                placeholder={placeholder}
                value={value}
                onChange={onChange}
                className="w-full h-full p-4 bg-transparent text-white placeholder-gray-300 focus:outline-none text-base pr-12"
                style={{
                    color: 'white',
                    fontWeight: '400',
                    letterSpacing: '0.5px'
                }}
            />

            {isPassword && (
                <button
                    type="button"
                    onClick={() => setIsVisible(!isVisible)}
                    className="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-white transition duration-200 text-lg"
                >
                    {isVisible ? (
                        <i className="fa-regular fa-eye-slash"></i>
                    ) : (
                        <i className="fa-regular fa-eye"></i>
                    )}
                </button>
            )}
        </GlassSurface>
    );
};

const GlassButton = ({ children, onClick, className = '' }) => (
    <GlassSurface
        width="100%"
        height={55}
        borderRadius={12}
        backgroundOpacity={0.25}
        blur={18}
        displace={1}
        distortionScale={-80}
        mixBlendMode="screen"
        className={`cursor-pointer transition duration-300 hover:scale-[1.01] active:scale-[0.99] ${className}`}
        style={{
            backgroundColor: 'rgba(255, 255, 255, 0.15)',
            border: '1px solid rgba(255, 255, 255, 0.25)'
        }}
    >
        <button
            onClick={onClick}
            className="w-full h-full text-lg font-bold text-white bg-transparent"
        >
            {children}
        </button>
    </GlassSurface>
);

const LiquidLoginForm = () => {
    const [identifier, setIdentifier] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [loading, setLoading] = useState(false);

    const handleLogin = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        setLoading(true);

        // Validate input
        if (!identifier.trim() || !password) {
            setError('Please enter both email/username and password');
            setLoading(false);
            return;
        }

        // Use centralized auth handler
        const result = await authHandler.login(identifier, password);

        if (!result.success) {
            setError(result.error);
            setLoading(false);
            return;
        }

        // Show success message
        setSuccess('Login successful! Redirecting...');

        // Redirect based on backend response or user role
        setTimeout(() => {
            if (result.redirect) {
                // Use redirect URL from backend (preferred)
                window.location.href = result.redirect;
            } else {
                // Fallback to role-based redirect (handle both uppercase and mixed case)
                const { role } = result.user;
                const roleUpper = role?.toUpperCase();
                
                if (roleUpper === 'ADMIN') {
                    window.location.href = '/admin/dashboard';
                } else if (roleUpper === 'PROFESSOR') {
                    window.location.href = '/professor';
                } else {
                    // Default to posts for STUDENT or any other role
                    window.location.href = '/posts';
                }
            }
        }, 1000);
    };

    return (
        <form onSubmit={handleLogin} className="w-full max-w-sm mx-auto">
            <GlassSurface
                width="100%"
                height={480}
                borderRadius={25}
                backgroundOpacity={0.001}
                blur={120}
                saturation={2.5}
                displace={12}
                distortionScale={-500}
                redOffset={-20}
                greenOffset={30}
                blueOffset={60}
                mixBlendMode="screen"
                className="w-full flex flex-col space-y-7 p-12 pt-14 shadow-3xl relative"
            >
                <h2 className="absolute top-4 left-1/2 transform -translate-x-1/2 text-4xl font-extrabold text-white text-center drop-shadow-lg leading-none z-20">
                    Login
                </h2>

                <div className="w-full h-full flex flex-col justify-center space-y-5 mt-16">
                    {error && (
                        <div className="bg-red-500/20 border border-red-500/50 rounded-lg p-3 text-red-200 text-sm text-center">
                            {error}
                        </div>
                    )}

                    {success && (
                        <div className="bg-green-500/20 border border-green-500/50 rounded-lg p-3 text-green-200 text-sm text-center">
                            {success}
                        </div>
                    )}

                    <GlassInput
                        type="text"
                        placeholder="Email or Username"
                        value={identifier}
                        onChange={(e) => setIdentifier(e.target.value)}
                    />

                    <GlassInput
                        type="password"
                        placeholder="Password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                    />

                    <GlassButton onClick={handleLogin} className={loading ? 'opacity-50' : ''}>
                        {loading ? 'LOGGING IN...' : 'LOG IN'}
                    </GlassButton>
                </div>

            </GlassSurface>
        </form>
    );
};

export default function LoginRenderer() {
    return (
        <div className="w-full h-screen relative bg-black overflow-hidden">

            {/* TOP RAYS */}
            <LightRays
                raysOrigin="top-center"
                raysColor="#00ffff"
                raysSpeed={1.5}
                lightSpread={0.8}
                rayLength={1.2}
                followMouse={true}
                mouseInfluence={0.1}
                noiseAmount={0.1}
                distortion={0.05}
                className="z-0 opacity-95 blur-md"
            />

            {/* BOTTOM RAYS (FLIPPED, GUARANTEED VISIBLE) */}
            <div className="absolute bottom-0 left-0 w-full h-[50vh] z-0 pointer-events-none">
                <LightRays
                    raysOrigin="top-center"
                    raysColor="#00ffff"
                    raysSpeed={1.2}
                    lightSpread={1.3}
                    rayLength={2}
                    brightness={1.8}
                    intensity={2.0}
                    rayThickness={1.4}
                    followMouse={false}
                    noiseAmount={0.22}
                    distortion={0.12}
                    className="opacity-90 blur-xl scale-y-[-1]"
                />
            </div>

            <div className="absolute inset-0 z-10 flex items-center justify-center">
                <AnimatedContent
                    distance={80}
                    direction="vertical"
                    reverse={false}
                    duration={1.0}
                    ease="power3.out"
                    initialOpacity={0}
                    animateOpacity
                    scale={1}
                    threshold={0.2}
                    delay={0.2}
                >
                    <LiquidLoginForm />
                </AnimatedContent>
            </div>
        </div>
    );
}