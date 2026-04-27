import React, { useState } from "react";
import authHandler from '../../handlers/authHandler';

const SimpleInput = ({ type, placeholder, value, onChange, className = '' }) => {
    const isPassword = type === 'password';
    const [isVisible, setIsVisible] = useState(false);
    const inputType = isPassword ? (isVisible ? 'text' : 'password') : type;

    return (
        <div className={`relative ${className}`}>
            <input
                type={inputType}
                placeholder={placeholder}
                value={value}
                onChange={onChange}
                className="w-full h-[55px] px-4 bg-white/5 border border-white/15 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-accent text-base pr-12"
                style={{ color: 'white', letterSpacing: '0.5px' }}
            />
            {isPassword && (
                <button
                    type="button"
                    onClick={() => setIsVisible(!isVisible)}
                    className="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-white transition-colors text-lg"
                >
                    {isVisible ? (
                        <i className="fa-regular fa-eye-slash"></i>
                    ) : (
                        <i className="fa-regular fa-eye"></i>
                    )}
                </button>
            )}
        </div>
    );
};

const LoginForm = () => {
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

        if (!identifier.trim() || !password) {
            setError('Please enter both email/username and password');
            setLoading(false);
            return;
        }

        const result = await authHandler.login(identifier, password);

        if (!result.success) {
            setError(result.error);
            setLoading(false);
            return;
        }

        setSuccess('Login successful! Redirecting...');

        setTimeout(() => {
            if (result.redirect) {
                window.location.href = result.redirect;
            } else {
                const { role } = result.user;
                const roleUpper = role?.toUpperCase();

                if (roleUpper === 'ADMIN') {
                    window.location.href = '/admin/dashboard';
                } else if (roleUpper === 'PROFESSOR') {
                    window.location.href = '/professor';
                } else {
                    window.location.href = '/posts';
                }
            }
        }, 1000);
    };

    return (
        <form onSubmit={handleLogin} className="w-full max-w-sm mx-auto">
            <div className="bg-[rgba(0,0,0,0.6)] border border-white/10 rounded-3xl p-10 flex flex-col space-y-6">
                <h2 className="text-4xl font-extrabold text-white text-center">Login</h2>

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

                <SimpleInput
                    type="text"
                    placeholder="Email or Username"
                    value={identifier}
                    onChange={(e) => setIdentifier(e.target.value)}
                />

                <SimpleInput
                    type="password"
                    placeholder="Password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                />

                <button
                    type="submit"
                    disabled={loading}
                    className={`w-full h-[55px] bg-accent hover:bg-accent-alt text-white font-bold rounded-xl text-lg transition-colors ${loading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}`}
                >
                    {loading ? 'LOGGING IN...' : 'LOG IN'}
                </button>
            </div>
        </form>
    );
};

export default function LoginRenderer() {
    return (
        <div className="w-full h-screen relative bg-black overflow-hidden flex items-center justify-center">
            <LoginForm />
        </div>
    );
}
