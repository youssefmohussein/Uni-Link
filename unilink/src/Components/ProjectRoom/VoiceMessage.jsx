import React, { useState, useRef, useEffect } from "react";
import { API_BASE_URL } from "../../../config/api";

const VoiceMessage = ({ filePath, isMe }) => {
    const [isPlaying, setIsPlaying] = useState(false);
    const [progress, setProgress] = useState(0);
    const [duration, setDuration] = useState(0);
    const audioRef = useRef(null);

    // Initial setup
    useEffect(() => {
        const audio = audioRef.current;
        if (!audio) return;

        const setAudioData = () => {
            if (isFinite(audio.duration)) {
                setDuration(audio.duration);
            }
        };

        const handleTimeUpdate = () => {
            if (isFinite(audio.duration) && isFinite(audio.currentTime)) {
                setProgress((audio.currentTime / audio.duration) * 100);
            }
        };

        const handleEnded = () => {
            setIsPlaying(false);
            setProgress(0);
        };

        // Events
        audio.addEventListener('loadedmetadata', setAudioData);
        audio.addEventListener('timeupdate', handleTimeUpdate);
        audio.addEventListener('ended', handleEnded);

        // Cleanup
        return () => {
            audio.removeEventListener('loadedmetadata', setAudioData);
            audio.removeEventListener('timeupdate', handleTimeUpdate);
            audio.removeEventListener('ended', handleEnded);
        };
    }, []);

    const togglePlay = () => {
        if (!audioRef.current) return;

        if (isPlaying) {
            audioRef.current.pause();
        } else {
            // Stop other playing audios if typically desired, but simplistic here:
            audioRef.current.play();
        }
        setIsPlaying(!isPlaying);
    };

    const handleSeek = (e) => {
        const newTime = (e.target.value / 100) * duration;
        audioRef.current.currentTime = newTime;
        setProgress(e.target.value);
    };

    const formatTime = (time) => {
        if (!isFinite(time)) return "0:00";
        const minutes = Math.floor(time / 60);
        const seconds = Math.floor(time % 60);
        return `${minutes}:${seconds.toString().padStart(2, '0')}`;
    };

    return (
        <div className={`flex items-center gap-3 p-3 rounded-full border shadow-lg w-[280px] transition-all duration-300 ${isMe
                ? 'bg-accent text-white border-accent'
                : 'bg-[#1a1a1a] text-gray-200 border-white/10'
            }`}>
            {/* Play Button */}
            <button
                onClick={togglePlay}
                className={`w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 transition-all hover:scale-105 ${isMe ? 'bg-white/20 hover:bg-white/30' : 'bg-white/10 hover:bg-white/20'
                    }`}
            >
                <i className={`fa-solid ${isPlaying ? 'fa-pause' : 'fa-play'} text-sm`}></i>
            </button>

            {/* Progress & Time */}
            <div className="flex-grow min-w-0 flex flex-col justify-center gap-1">
                {/* Progress Bar */}
                <div className="relative w-full h-1.5 bg-black/20 rounded-full overflow-hidden cursor-pointer">
                    <div
                        className={`absolute top-0 left-0 h-full rounded-full transition-all duration-100 ${isMe ? 'bg-white' : 'bg-accent'}`}
                        style={{ width: `${progress}%` }}
                    />
                    <input
                        type="range"
                        min="0"
                        max="100"
                        value={progress}
                        onChange={handleSeek}
                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    />
                </div>

                {/* Timer */}
                <div className="flex justify-between items-center text-[10px] opacity-70 px-0.5">
                    <span>{formatTime(audioRef.current?.currentTime)}</span>
                    <span>{formatTime(duration)}</span>
                </div>
            </div>

            <audio ref={audioRef} className="hidden">
                <source src={`${API_BASE_URL}/${filePath}`} type="audio/webm" />
                <source src={`${API_BASE_URL}/${filePath}`} type="audio/mp3" />
                Your browser does not support the audio element.
            </audio>
        </div>
    );
};

export default VoiceMessage;
