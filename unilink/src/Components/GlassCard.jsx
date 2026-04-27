import React from 'react';

// Solid opaque card matching the features section style
const GlassCard = ({ children, className = "" }) => {
    return (
        <div
            className={`
        relative overflow-hidden rounded-2xl border border-white/10
        bg-[rgba(0,0,0,0.2)]
        ${className}
      `}
        >
            {children}
        </div>
    );
};

export default GlassCard;
