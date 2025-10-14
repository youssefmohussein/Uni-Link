import { useState } from 'react';
import { Mail, ArrowLeft, CheckCircle } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import AuthCard from '../components/AuthCard';
import BlobBackground from '../components/BlobBackground';
import TextInput from '../components/TextInput';
import PrimaryButton from '../components/PrimaryButton';

export default function ForgotPassword() {
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const validateEmail = (e) => /^[^\s@]+@miuegypt\.edu\.eg$/.test(e);
  const isFormValid = () => email && validateEmail(email) && !error;

  const handleEmailChange = (e) => {
    const v = e.target.value;
    setEmail(v);
    setError(v && !validateEmail(v) ? 'Email must end with @miuegypt.edu.eg' : '');
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!isFormValid()) return;
    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      setShowModal(true);
    }, 2000);
  };

  const handleCloseModal = () => {
    setShowModal(false);
    setTimeout(() => {
      navigate('/login');
    }, 300);
  };

  return (
    <>
      <div className="min-h-screen bg-[#0d1117] flex items-center justify-center p-4 relative overflow-hidden">
        <BlobBackground />
        <div className="w-full max-w-md relative">
          <AuthCard>
            {/* Back to Login Button */}
            <button
              onClick={() => navigate('/login')}
              className="mb-6 flex items-center space-x-2 text-[#c9d1d9] hover:text-white"
            >
              <ArrowLeft className="w-5 h-5" />
              <span className="font-medium">Back to Login</span>
            </button>

            <div className="text-center mb-8">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#58a6ff] to-[#79b8ff] rounded-2xl mb-4">
                <Mail className="w-8 h-8 text-white" />
              </div>
              <h1 className="text-3xl font-bold text-white mb-2">Forgot Password?</h1>
              <p className="text-[#c9d1d9]">No worries, we'll send you reset instructions</p>
            </div>

            {/* Form */}
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-2">
                <label className="block text-sm font-medium text-[#c9d1d9] ml-1">Email Address</label>
                <TextInput
                  icon={Mail}
                  type="email"
                  value={email}
                  onChange={handleEmailChange}
                  placeholder="yourname@miuegypt.edu.eg"
                  invalid={!!error}
                />
                {error && <p className="text-red-400 text-sm ml-1 animate-shake">{error}</p>}
              </div>

              <PrimaryButton disabled={!isFormValid() || isLoading}>
                {isLoading ? (
                  <>
                    <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span>Sending reset link...</span>
                  </>
                ) : (
                  <span>Send Reset Link</span>
                )}
              </PrimaryButton>
            </form>
          </AuthCard>
        </div>
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div className="bg-[#161b22] rounded-3xl shadow-[0_4px_12px_rgba(0,0,0,0.5)] p-8 max-w-md w-full">
            <div className="text-center">
              <div className="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-400 to-green-500 rounded-full mb-6">
                <CheckCircle className="w-10 h-10 text-white" />
              </div>
              <h2 className="text-3xl font-bold text-white mb-4">Check Your Email!</h2>
              <p className="text-[#c9d1d9] mb-6">
                We've sent a password reset link to{' '}
                <span className="font-semibold text-[#58a6ff]">{email}</span>
              </p>
              <button
                onClick={handleCloseModal}
                className="w-full py-4 rounded-xl font-semibold text-white bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] hover:from-[#388bfd] hover:to-[#58a6ff] transition-all"
              >
                Back to Login
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
