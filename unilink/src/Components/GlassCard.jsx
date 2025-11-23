import React from 'react';

const GlassCard = ({ children, className = "" }) => {
    return (
        <div
            className={`
        relative overflow-hidden rounded-2xl border border-white/10 
        bg-white/5 backdrop-blur-xl shadow-[0_8px_32px_0_rgba(0,0,0,0.37)]
        transition-all duration-300 hover:bg-white/10 hover:scale-[1.02] hover:border-white/20
        ${className}
      `}
        >
            {/* Shine effect */}
            <div className="absolute inset-0 -z-10 bg-gradient-to-br from-white/10 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500" />

            {children}
        </div>
    );
};

export default GlassCard;
