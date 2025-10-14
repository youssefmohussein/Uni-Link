import { useState } from 'react';
import { Eye, EyeOff, User, Phone, Mail, Lock, GraduationCap, Calendar, ArrowRight } from 'lucide-react';
import AuthCard from '../components/AuthCard';
import BlobBackground from '../components/BlobBackground';
import TextInput from '../components/TextInput';
import PrimaryButton from '../components/PrimaryButton';

export default function Signup({ onNavigateToLogin }) {
  const [formData, setFormData] = useState({
    fullName: '', phone: '', faculty: '', year: '', email: '', password: '', confirmPassword: ''
  });
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const faculties = ['CS', 'Dentistry', 'ECE', 'Pharma'];
  const years = ['1', '2', '3', '4', '5'];

  const validateEmail = (email) => /^[^\s@]+@miuegypt\.edu\.eg$/.test(email);
  const validatePassword = (p) => p.length >= 8 && /[A-Z]/.test(p) && /[a-z]/.test(p) && /[0-9]/.test(p) && /[!@#$%^&*(),.?":{}|<>]/.test(p);
  const formatPhoneNumber = (v) => {
    const c = v.replace(/\D/g, '');
    if (c.length <= 2) return `+20 ${c}`;
    if (c.length <= 4) return `+20 ${c.slice(2, 4)}`;
    if (c.length <= 7) return `+20 ${c.slice(2, 4)} ${c.slice(4, 7)}`;
    return `+20 ${c.slice(2, 4)} ${c.slice(4, 7)} ${c.slice(7, 11)}`;
  };

  const handlePhoneChange = (e) => {
    const cleaned = e.target.value.replace(/\D/g, '');
    if (cleaned.length <= 11) {
      const formatted = formatPhoneNumber(cleaned);
      setFormData(prev => ({ ...prev, phone: formatted }));
      setErrors(prev => ({ ...prev, phone: cleaned.length > 2 && cleaned.length < 11 ? 'Phone number must be 11 digits' : '' }));
    }
  };

  const handleInputChange = (field, value) => {
    setFormData(prev => ({ ...prev, [field]: value }));
    if (field === 'email') setErrors(prev => ({ ...prev, email: value && !validateEmail(value) ? 'Email must end with @miuegypt.edu.eg' : '' }));
    if (field === 'password') {
      setErrors(prev => ({ ...prev, password: value && !validatePassword(value) ? 'Password must be 8+ chars with uppercase, lowercase, number & special character' : '' }));
      setErrors(prev => ({ ...prev, confirmPassword: formData.confirmPassword && value !== formData.confirmPassword ? 'Passwords do not match' : '' }));
    }
    if (field === 'confirmPassword') setErrors(prev => ({ ...prev, confirmPassword: value && value !== formData.password ? 'Passwords do not match' : '' }));
  };

  const isFormValid = () => {
    const { fullName, phone, faculty, year, email, password, confirmPassword } = formData;
    const phoneClean = phone.replace(/\D/g, '');
    return fullName.trim() && phoneClean.length === 11 && faculty && year && validateEmail(email) && validatePassword(password) && password === confirmPassword && Object.values(errors).every(e => !e);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!isFormValid()) return;
    setIsLoading(true);
    setTimeout(() => { setIsLoading(false); onNavigateToLogin(); }, 2000);
  };

  return (
    <div className="min-h-screen bg-[#0d1117] py-12 px-4 relative overflow-hidden">
      <BlobBackground />
      <div className="max-w-2xl mx-auto relative">
        <AuthCard>
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#58a6ff] to-[#79b8ff] rounded-2xl mb-4">
              <span className="text-2xl font-bold text-white">UL</span>
            </div>
            <h1 className="text-3xl font-bold text-white mb-2">Create Account</h1>
            <p className="text-[#c9d1d9]">Join UniLink community today</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Full Name</label>
              <TextInput icon={User} value={formData.fullName} onChange={(e) => handleInputChange('fullName', e.target.value)} placeholder="Enter your full name" />
            </div>

            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Phone Number</label>
              <TextInput icon={Phone} value={formData.phone} onChange={handlePhoneChange} placeholder="+20 XX XXX XXXX" invalid={!!errors.phone} />
              {errors.phone && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.phone}</p>}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Faculty</label>
                <div className="relative group">
                  <GraduationCap className="absolute left-4 top-1/2 -translate-y-1/2 text-[#8b949e] w-5 h-5 pointer-events-none" />
                  <select value={formData.faculty} onChange={(e) => handleInputChange('faculty', e.target.value)} className="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-[#30363d] bg-[#0d1117] text-white focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 focus:border-[#58a6ff]">
                    <option value="">Select Faculty</option>
                    {faculties.map((f) => (<option key={f} value={f}>{f}</option>))}
                  </select>
                </div>
              </div>
              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Year</label>
                <div className="relative group">
                  <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 text-[#8b949e] w-5 h-5 pointer-events-none" />
                  <select value={formData.year} onChange={(e) => handleInputChange('year', e.target.value)} className="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-[#30363d] bg-[#0d1117] text-white focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 focus:border-[#58a6ff]">
                    <option value="">Select Year</option>
                    {years.map((y) => (<option key={y} value={y}>Year {y}</option>))}
                  </select>
                </div>
              </div>
            </div>

            <div className="space-y-2">
              <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Email Address</label>
              <TextInput icon={Mail} type="email" value={formData.email} onChange={(e) => handleInputChange('email', e.target.value)} placeholder="yourname@miuegypt.edu.eg" invalid={!!errors.email} />
              {errors.email && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.email}</p>}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Password</label>
                <TextInput
                  icon={Lock}
                  type={showPassword ? 'text' : 'password'}
                  value={formData.password}
                  onChange={(e) => handleInputChange('password', e.target.value)}
                  placeholder="Enter password"
                  invalid={!!errors.password}
                  rightSlot={<button type="button" onClick={() => setShowPassword(!showPassword)} className="text-[#8b949e] hover:text-[#c9d1d9]">{showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}</button>}
                />
              </div>
              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Confirm Password</label>
                <TextInput
                  icon={Lock}
                  type={showConfirmPassword ? 'text' : 'password'}
                  value={formData.confirmPassword}
                  onChange={(e) => handleInputChange('confirmPassword', e.target.value)}
                  placeholder="Confirm password"
                  invalid={!!errors.confirmPassword}
                  rightSlot={<button type="button" onClick={() => setShowConfirmPassword(!showConfirmPassword)} className="text-[#8b949e] hover:text-[#c9d1d9]">{showConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}</button>}
                />
              </div>
            </div>

            {errors.password && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.password}</p>}
            {errors.confirmPassword && <p className="text-red-400 text-sm ml-1 animate-shake">{errors.confirmPassword}</p>}

            <PrimaryButton disabled={!isFormValid() || isLoading}>
              {isLoading ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Creating account...</span>
                </>
              ) : (
                <>
                  <span>Create Account</span>
                  <ArrowRight className="w-5 h-5" />
                </>
              )}
            </PrimaryButton>
          </form>

          <div className="mt-8 text-center">
            <p className="text-[#c9d1d9]">
              Already have an account?{' '}
              <button onClick={onNavigateToLogin} className="text-[#58a6ff] hover:text-[#79b8ff] font-semibold hover:underline">Sign In</button>
            </p>
          </div>
        </AuthCard>
      </div>
    </div>
  );
}

