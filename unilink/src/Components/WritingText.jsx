import React, { useState, useEffect, useMemo } from 'react';
import { Text } from '@react-three/drei';

const WritingText = ({ text, position, isOpen, delay = 0, color = "black", fontSize = 0.2 }) => {
    const [displayedText, setDisplayedText] = useState("");
    const [started, setStarted] = useState(false);

    useEffect(() => {
        if (isOpen) {
            const startTimeout = setTimeout(() => {
                setStarted(true);
            }, delay);
            return () => clearTimeout(startTimeout);
        } else {
            setStarted(false);
            setDisplayedText("");
        }
    }, [isOpen, delay]);

    useEffect(() => {
        if (started) {
            let currentText = "";
            const chars = text.split("");
            let i = 0;

            const interval = setInterval(() => {
                if (i < chars.length) {
                    currentText += chars[i];
                    setDisplayedText(currentText);
                    i++;
                } else {
                    clearInterval(interval);
                }
            }, 50); // Speed of writing

            return () => clearInterval(interval);
        }
    }, [started, text]);

    // If not open, render nothing to prevent "appearing before book opens"
    if (!isOpen && !started) return null;

    return (
        <group position={position} rotation={[-Math.PI / 6, 0, 0]}> {/* Slight rotation to align better with view if needed */}
            <Text
                fontSize={fontSize}
                color={color}
                anchorX="center"
                anchorY="middle"
                font="https://raw.githubusercontent.com/google/fonts/main/ofl/patrickhand/PatrickHand-Regular.ttf"
                outlineWidth={0.005}
                outlineColor="#000000"
                outlineOpacity={0.1}
            >
                {displayedText}
            </Text>
        </group>
    );
};

export default WritingText;
