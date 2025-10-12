import { useState } from 'react';
import { Eye, EyeOff, Mail, Lock, ArrowRight } from 'lucide-react';

export default function Login({ onNavigateToSignup, onNavigateToForgotPassword }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ email: '', password: '' });

  const validateEmail = (email) => {
    const emailRegex = /^[^\s@]+@miuegypt\.edu\.eg$/;
    return emailRegex.test(email);
  };

  const handleEmailChange = (e) => {
    const value = e.target.value;
    setEmail(value);

    if (value && !validateEmail(value)) {
      setErrors(prev => ({ ...prev, email: 'Email must end with @miuegypt.edu.eg' }));
    } else {
      setErrors(prev => ({ ...prev, email: '' }));
    }
  };

  const handlePasswordChange = (e) => {
    const value = e.target.value;
    setPassword(value);

    if (value && value.length < 6) {
      setErrors(prev => ({ ...prev, password: 'Password must be at least 6 characters' }));
    } else {
      setErrors(prev => ({ ...prev, password: '' }));
    }
  };

  const isFormValid = () => {
    return email && password && validateEmail(email) && !errors.email && !errors.password;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!isFormValid()) return;

    setIsLoading(true);

    setTimeout(() => {
      setIsLoading(false);
      alert('Login successful!');
    }, 2000);
  };

  return (
    <div className="min-h-screen bg-[#0d1117] flex items-center justify-center p-4 relative overflow-hidden">
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-80 h-80 bg-[#58a6ff] rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
        <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-[#58a6ff] rounded-full mix-blend-multiply filter blur-xl opacity-15 animate-blob animation-delay-2000"></div>
        <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-[#79b8ff] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-4000"></div>
      </div>

      <div className="w-full max-w-md relative">
        <div className="bg-[#161b22] backdrop-blur-lg rounded-3xl shadow-[0_4px_12px_rgba(0,0,0,0.5)] p-8 transform transition-all duration-500 hover:shadow-[0_6px_16px_rgba(0,0,0,0.6)]">
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#58a6ff] to-[#79b8ff] rounded-2xl mb-4 transform transition-transform hover:scale-110 hover:rotate-3">
              <span className="text-2xl font-bold text-white">UL</span>
            </div>
            <h1 className="text-3xl font-bold text-white mb-2">Welcome Back</h1>
            <p className="text-[#c9d1d9]">Sign in to continue to UniLink</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Email Address</label>
              <div className="relative group">
                <Mail className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
                <input
                  type="email"
                  value={email}
                  onChange={handleEmailChange}
                  placeholder="yourname@miuegypt.edu.eg"
                  className={`w-full pl-12 pr-4 py-3.5 rounded-xl border-2 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 bg-[#0d1117] text-white placeholder-[#8b949e] ${
                    errors.email
                      ? 'border-red-500 focus:border-red-500'
                      : 'border-[#30363d] focus:border-[#58a6ff]'
                  }`}
                />
              </div>
              {errors.email && (
                <p className="text-red-400 text-sm ml-1 animate-shake">{errors.email}</p>
              )}
            </div>

            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Password</label>
              <div className="relative group">
                <Lock className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
                <input
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={handlePasswordChange}
                  placeholder="Enter your password"
                  className={`w-full pl-12 pr-12 py-3.5 rounded-xl border-2 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 bg-[#0d1117] text-white placeholder-[#8b949e] ${
                    errors.password
                      ? 'border-red-500 focus:border-red-500'
                      : 'border-[#30363d] focus:border-[#58a6ff]'
                  }`}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] hover:text-[#c9d1d9] transition-colors focus:outline-none"
                >
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
              {errors.password && (
                <p className="text-red-400 text-sm ml-1 animate-shake">{errors.password}</p>
              )}
            </div>

            <div className="flex items-center justify-end">
              <button
                type="button"
                onClick={onNavigateToForgotPassword}
                className="text-sm text-[#58a6ff] hover:text-[#79b8ff] font-medium transition-colors hover:underline"
              >
                Forgot Password?
              </button>
            </div>

            <button
              type="submit"
              disabled={!isFormValid() || isLoading}
              className={`w-full py-4 rounded-xl font-semibold text-white transition-all duration-300 flex items-center justify-center space-x-2 group ${
                isFormValid() && !isLoading
                  ? 'bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] hover:from-[#388bfd] hover:to-[#58a6ff] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5'
                  : 'bg-[#21262d] cursor-not-allowed'
              }`}
            >
              {isLoading ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Signing in...</span>
                </>
              ) : (
                <>
                  <span>Sign In</span>
                  <ArrowRight className="w-5 h-5 transform transition-transform group-hover:translate-x-1" />
                </>
              )}
            </button>
          </form>

          <div className="mt-8 text-center">
            <p className="text-[#c9d1d9]">
              Don't have an account?{' '}
              <button
                onClick={onNavigateToSignup}
                className="text-[#58a6ff] hover:text-[#79b8ff] font-semibold transition-colors hover:underline"
              >
                Sign Up
              </button>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
