import React, { useState } from "react";
import LightRays from "../../Components/Login_Components/LightRays/LightRays";
import GlassSurface from "../../Components/Login_Components/LiquidGlass/GlassSurface";

// --- Helper Components ---

// 1. Glass Input Field Component (No Changes Needed)
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
                    {/* FONT AWESOME ICONS */}
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

// 2. Glass Button Component (No Changes Needed)
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


// 3. The Login Form structure (LiquidLoginForm) - Login Title Adjusted with Absolute Positioning
const LiquidLoginForm = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    const handleLogin = (e) => {
        e.preventDefault();
        console.log('Login attempt with:', { email, password });
        // Authentication logic goes here
    };

    return (
        <form onSubmit={handleLogin} className="w-full max-w-sm mx-auto">
            <GlassSurface
                // ABSOLUTE MAX LIQUID GLASS SETTINGS
                width="100%"
                height={400}
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
                
                // Added 'relative' to make it the positioning context for the 'Login' title
                className="w-full flex flex-col space-y-7 p-12 pt-14 shadow-3xl relative" 
            >
                {/* LOGIN TITLE: Now absolutely positioned and centered near the top */}
                <h2 className="absolute top-4 left-1/2 transform -translate-x-1/2 text-4xl font-extrabold text-white text-center drop-shadow-lg leading-none z-20"> 
                    Login
                </h2>
                
                {/* The rest of the content must be moved down to avoid overlap */}
                <div className="w-full h-full flex flex-col justify-center space-y-5 mt-16">
                    
                    <GlassInput 
                        type="email" 
                        placeholder="Email" 
                        value={email} 
                        onChange={(e) => setEmail(e.target.value)} 
                    />
                    
                    <GlassInput 
                        type="password" 
                        placeholder="Password" 
                        value={password} 
                        onChange={(e) => setPassword(e.target.value)} 
                    />
                    
                    <GlassButton onClick={handleLogin}>
                        LOG IN
                    </GlassButton>
                </div>
                
            </GlassSurface>
        </form>
    );
};
// --- End Helper Components ---


export default function LoginRenderer() {
  return (
    <div className="w-full h-screen relative bg-black">
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
        className="z-0"
      />

      <div className="absolute inset-0 z-10 flex items-center justify-center">
        <LiquidLoginForm />
      </div>
    </div>
  );
}