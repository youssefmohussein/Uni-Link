import { useState } from 'react';
import { Eye, EyeOff, User, Phone, Mail, Lock, GraduationCap, Calendar, ArrowRight } from 'lucide-react';

interface SignupProps {
  onNavigateToLogin: () => void;
}

export default function Signup({ onNavigateToLogin }: SignupProps) {
  const [formData, setFormData] = useState({
    fullName: '',
    phone: '',
    faculty: '',
    year: '',
    email: '',
    password: '',
    confirmPassword: ''
  });

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [showSuccess, setShowSuccess] = useState(false);

  const faculties = ['CS', 'Dentistry', 'ECE', 'Pharma'];
  const years = ['1', '2', '3', '4', '5'];

  const validateEmail = (email: string) => {
    const emailRegex = /^[^\s@]+@miuegypt\.edu\.eg$/;
    return emailRegex.test(email);
  };

  const validatePassword = (password: string) => {
    const minLength = password.length >= 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    return minLength && hasUpperCase && hasLowerCase && hasNumber && hasSpecial;
  };

  const formatPhoneNumber = (value: string) => {
    const cleaned = value.replace(/\D/g, '');

    if (cleaned.length <= 2) {
      return `+20 ${cleaned}`;
    } else if (cleaned.length <= 4) {
      return `+20 ${cleaned.slice(2, 4)}`;
    } else if (cleaned.length <= 7) {
      return `+20 ${cleaned.slice(2, 4)} ${cleaned.slice(4, 7)}`;
    } else {
      return `+20 ${cleaned.slice(2, 4)} ${cleaned.slice(4, 7)} ${cleaned.slice(7, 11)}`;
    }
  };

  const handlePhoneChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    const cleaned = value.replace(/\D/g, '');

    if (cleaned.length <= 11) {
      const formatted = formatPhoneNumber(cleaned);
      setFormData(prev => ({ ...prev, phone: formatted }));

      if (cleaned.length > 2 && cleaned.length < 11) {
        setErrors(prev => ({ ...prev, phone: 'Phone number must be 11 digits' }));
      } else {
        setErrors(prev => ({ ...prev, phone: '' }));
      }
    }
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }));

    if (field === 'email' && value && !validateEmail(value)) {
      setErrors(prev => ({ ...prev, email: 'Email must end with @miuegypt.edu.eg' }));
    } else if (field === 'email') {
      setErrors(prev => ({ ...prev, email: '' }));
    }

    if (field === 'password') {
      if (value && !validatePassword(value)) {
        setErrors(prev => ({
          ...prev,
          password: 'Password must be 8+ chars with uppercase, lowercase, number & special character'
        }));
      } else {
        setErrors(prev => ({ ...prev, password: '' }));
      }

      if (formData.confirmPassword && value !== formData.confirmPassword) {
        setErrors(prev => ({ ...prev, confirmPassword: 'Passwords do not match' }));
      } else {
        setErrors(prev => ({ ...prev, confirmPassword: '' }));
      }
    }

    if (field === 'confirmPassword') {
      if (value && value !== formData.password) {
        setErrors(prev => ({ ...prev, confirmPassword: 'Passwords do not match' }));
      } else {
        setErrors(prev => ({ ...prev, confirmPassword: '' }));
      }
    }
  };

  const isFormValid = () => {
    const { fullName, phone, faculty, year, email, password, confirmPassword } = formData;
    const phoneClean = phone.replace(/\D/g, '');

    return (
      fullName.trim() &&
      phoneClean.length === 11 &&
      faculty &&
      year &&
      validateEmail(email) &&
      validatePassword(password) &&
      password === confirmPassword &&
      Object.values(errors).every(error => !error)
    );
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!isFormValid()) return;

    setIsLoading(true);

    setTimeout(() => {
      setIsLoading(false);
      setShowSuccess(true);

      setTimeout(() => {
        onNavigateToLogin();
      }, 2000);
    }, 2000);
  };

  if (showSuccess) {
    return (
      <div className="min-h-screen bg-[#0d1117] flex items-center justify-center p-4">
        <div className="bg-[#161b22] rounded-3xl shadow-[0_4px_12px_rgba(0,0,0,0.5)] p-8 max-w-md w-full text-center transform animate-scale-in">
          <div className="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#58a6ff] to-[#79b8ff] rounded-full mb-6 animate-bounce-slow">
            <svg className="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h2 className="text-3xl font-bold text-white mb-3">Account Created!</h2>
          <p className="text-[#c9d1d9] mb-4">Your account has been successfully created.</p>
          <p className="text-sm text-[#8b949e]">Redirecting to login...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#0d1117] py-12 px-4 relative overflow-hidden">
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-80 h-80 bg-[#58a6ff] rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
        <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-[#58a6ff] rounded-full mix-blend-multiply filter blur-xl opacity-15 animate-blob animation-delay-2000"></div>
        <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-[#79b8ff] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-4000"></div>
      </div>

      <div className="max-w-2xl mx-auto relative">
        <div className="bg-[#161b22] backdrop-blur-lg rounded-3xl shadow-[0_4px_12px_rgba(0,0,0,0.5)] p-8 transform transition-all duration-500">
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#58a6ff] to-[#79b8ff] rounded-2xl mb-4 transform transition-transform hover:scale-110 hover:rotate-3">
              <span className="text-2xl font-bold text-white">UL</span>
            </div>
            <h1 className="text-3xl font-bold text-white mb-2">Create Account</h1>
            <p className="text-[#c9d1d9]">Join UniLink community today</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Full Name</label>
              <div className="relative group">
                <User className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
                <input
                  type="text"
                  value={formData.fullName}
                  onChange={(e) => handleInputChange('fullName', e.target.value)}
                  placeholder="John Doe"
                  className="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-[#30363d] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 focus:border-[#58a6ff] bg-[#0d1117] text-white placeholder-[#8b949e]"
                />
              </div>
            </div>

            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Phone Number</label>
              <div className="relative group">
                <Phone className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
                <input
                  type="text"
                  value={formData.phone}
                  onChange={handlePhoneChange}
                  placeholder="+20 XX XXX XXXX"
                  className={`w-full pl-12 pr-4 py-3.5 rounded-xl border-2 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 bg-[#0d1117] text-white placeholder-[#8b949e] ${
                    errors.phone ? 'border-red-500 focus:border-red-500' : 'border-[#30363d] focus:border-[#58a6ff]'
                  }`}
                />
              </div>
              {errors.phone && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.phone}</p>}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Faculty</label>
                <div className="relative group">
                  <GraduationCap className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff] pointer-events-none" />
                  <select
                    value={formData.faculty}
                    onChange={(e) => handleInputChange('faculty', e.target.value)}
                    className="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-[#30363d] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 focus:border-[#58a6ff] appearance-none bg-[#0d1117] text-white cursor-pointer"
                  >
                    <option value="">Select Faculty</option>
                    {faculties.map((faculty) => (
                      <option key={faculty} value={faculty}>
                        {faculty}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Year</label>
                <div className="relative group">
                  <Calendar className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff] pointer-events-none" />
                  <select
                    value={formData.year}
                    onChange={(e) => handleInputChange('year', e.target.value)}
                    className="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-[#30363d] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 focus:border-[#58a6ff] appearance-none bg-[#0d1117] text-white cursor-pointer"
                  >
                    <option value="">Select Year</option>
                    {years.map((year) => (
                      <option key={year} value={year}>
                        Year {year}
                      </option>
                    ))}
                  </select>
                </div>
              </div>
            </div>

            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Email Address</label>
              <div className="relative group">
                <Mail className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
                <input
                  type="email"
                  value={formData.email}
                  onChange={(e) => handleInputChange('email', e.target.value)}
                  placeholder="yourname@miuegypt.edu.eg"
                  className={`w-full pl-12 pr-4 py-3.5 rounded-xl border-2 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 bg-[#0d1117] text-white placeholder-[#8b949e] ${
                    errors.email ? 'border-red-500 focus:border-red-500' : 'border-[#30363d] focus:border-[#58a6ff]'
                  }`}
                />
              </div>
              {errors.email && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.email}</p>}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Password</label>
                <div className="relative group">
                  <Lock className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
                  <input
                    type={showPassword ? 'text' : 'password'}
                    value={formData.password}
                    onChange={(e) => handleInputChange('password', e.target.value)}
                    placeholder="Enter password"
                    className={`w-full pl-12 pr-12 py-3.5 rounded-xl border-2 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 bg-[#0d1117] text-white placeholder-[#8b949e] ${
                      errors.password ? 'border-red-500 focus:border-red-500' : 'border-[#30363d] focus:border-[#58a6ff]'
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
              </div>

              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Confirm Password</label>
                <div className="relative group">
                  <Lock className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
                  <input
                    type={showConfirmPassword ? 'text' : 'password'}
                    value={formData.confirmPassword}
                    onChange={(e) => handleInputChange('confirmPassword', e.target.value)}
                    placeholder="Confirm password"
                    className={`w-full pl-12 pr-12 py-3.5 rounded-xl border-2 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 bg-[#0d1117] text-white placeholder-[#8b949e] ${
                      errors.confirmPassword ? 'border-red-500 focus:border-red-500' : 'border-[#30363d] focus:border-[#58a6ff]'
                    }`}
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                    className="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] hover:text-[#c9d1d9] transition-colors focus:outline-none"
                  >
                    {showConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                  </button>
                </div>
              </div>
            </div>

            {errors.password && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.password}</p>}
            {errors.confirmPassword && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.confirmPassword}</p>}

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
                  <span>Creating account...</span>
                </>
              ) : (
                <>
                  <span>Create Account</span>
                  <ArrowRight className="w-5 h-5 transform transition-transform group-hover:translate-x-1" />
                </>
              )}
            </button>
          </form>

          <div className="mt-8 text-center">
            <p className="text-[#c9d1d9]">
              Already have an account?{' '}
              <button
                onClick={onNavigateToLogin}
                className="text-[#58a6ff] hover:text-[#79b8ff] font-semibold transition-colors hover:underline"
              >
                Sign In
              </button>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
