import { useState } from 'react';
import { Eye, EyeOff, Mail, Lock, ArrowRight } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import AuthCard from '../../components/Login/AuthCard';
import BlobBackground from '../../components/Login/BlobBackground';
import TextInput from '../../components/Login/TextInput';
import PrimaryButton from '../../components/Login/PrimaryButton';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ email: '', password: '' });

  const navigate = useNavigate();

  const validateEmail = (email) => /^[^\s@]+@miuegypt\.edu\.eg$/.test(email);
  const isFormValid = () =>
    email && password && validateEmail(email) && !errors.email && !errors.password;

  const handleEmailChange = (e) => {
    const value = e.target.value;
    setEmail(value);
    setErrors((prev) => ({
      ...prev,
      email: value && !validateEmail(value)
        ? 'Email must end with @miuegypt.edu.eg'
        : '',
    }));
  };

  const handlePasswordChange = (e) => {
    const value = e.target.value;
    setPassword(value);
    setErrors((prev) => ({
      ...prev,
      password: value && value.length < 6
        ? 'Password must be at least 6 characters'
        : '',
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!isFormValid()) return;
    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      alert('Login successful!');
      navigate('/Profile'); // ✅ Example: redirect after login if needed
    }, 2000);
  };

  return (
    <div className="min-h-screen bg-[#0d1117] flex items-center justify-center p-4 relative overflow-hidden">
      <BlobBackground />
      <div className="w-full max-w-md relative">
        <AuthCard>
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#58a6ff] to-[#79b8ff] rounded-2xl mb-4">
              <span className="text-2xl font-bold text-white">UL</span>
            </div>
            <h1 className="text-3xl font-bold text-white mb-2">Welcome Back</h1>
            <p className="text-[#c9d1d9]">Sign in to continue to UniLink</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Email */}
            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">
                Email Address
              </label>
              <TextInput
                icon={Mail}
                type="email"
                value={email}
                onChange={handleEmailChange}
                placeholder="yourname@miuegypt.edu.eg"
                invalid={!!errors.email}
              />
              {errors.email && (
                <p className="text-red-400 text-sm ml-1 animate-shake">
                  {errors.email}
                </p>
              )}
            </div>

            {/* Password */}
            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">
                Password
              </label>
              <TextInput
                icon={Lock}
                type={showPassword ? 'text' : 'password'}
                value={password}
                onChange={handlePasswordChange}
                placeholder="Enter your password"
                invalid={!!errors.password}
                rightSlot={
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="text-[#8b949e] hover:text-[#c9d1d9]"
                  >
                    {showPassword ? (
                      <EyeOff className="w-5 h-5" />
                    ) : (
                      <Eye className="w-5 h-5" />
                    )}
                  </button>
                }
              />
              {errors.password && (
                <p className="text-red-400 text-sm ml-1 animate-shake">
                  {errors.password}
                </p>
              )}
            </div>

            {/* Forgot Password */}
            <div className="flex items-center justify-end">
              <button
                type="button"
                onClick={() => navigate('/forgot-password')} // ✅ changed
                className="text-sm text-[#58a6ff] hover:text-[#79b8ff] font-medium hover:underline"
              >
                Forgot Password?
              </button>
            </div>

            {/* Submit Button */}
            <PrimaryButton disabled={!isFormValid() || isLoading}>
              {isLoading ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Signing in...</span>
                </>
              ) : (
                <>
                  <span>Sign In</span>
                  <ArrowRight className="w-5 h-5" />
                </>
              )}
            </PrimaryButton>
          </form>

          {/* Sign Up */}
          <div className="mt-8 text-center">
            <p className="text-[#c9d1d9]">
              Don't have an account?{' '}
              <button
                onClick={() => navigate('/Signup')} // ✅ changed
                className="text-[#58a6ff] hover:text-[#79b8ff] font-semibold hover:underline"
              >
                Sign Up
              </button>
            </p>
          </div>
        </AuthCard>
      </div>
    </div>
  );
}
