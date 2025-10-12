import { useState } from 'react';
import { Mail, ArrowLeft, CheckCircle } from 'lucide-react';

export default function ForgotPassword({ onNavigateToLogin }) {
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [error, setError] = useState('');

  const validateEmail = (email) => {
    const emailRegex = /^[^\s@]+@miuegypt\.edu\.eg$/;
    return emailRegex.test(email);
  };

  const handleEmailChange = (e) => {
    const value = e.target.value;
    setEmail(value);

    if (value && !validateEmail(value)) {
      setError('Email must end with @miuegypt.edu.eg');
    } else {
      setError('');
    }
  };

  const isFormValid = () => {
    return email && validateEmail(email) && !error;
  };

  const handleSubmit = async (e) => {
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
      onNavigateToLogin();
    }, 300);
  };

  return (
    <>
      <div className="min-h-screen bg-[#0d1117] flex items-center justify-center p-4 relative overflow-hidden">
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          <div className="absolute -top-40 -right-40 w-80 h-80 bg-[#58a6ff] rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
          <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-[#58a6ff] rounded-full mix-blend-multiply filter blur-xl opacity-15 animate-blob animation-delay-2000"></div>
          <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-[#79b8ff] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-4000"></div>
        </div>

        <div className="w-full max-w-md relative">
          <div className="bg-[#161b22] backdrop-blur-lg rounded-3xl shadow-[0_4px_12px_rgba(0,0,0,0.5)] p-8 transform transition-all duration-500">
            <button
              onClick={onNavigateToLogin}
              className="mb-6 flex items-center space-x-2 text-[#c9d1d9] hover:text-white transition-colors group"
            >
              <ArrowLeft className="w-5 h-5 transform transition-transform group-hover:-translate-x-1" />
              <span className="font-medium">Back to Login</span>
            </button>

            <div className="text-center mb-8">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#58a6ff] to-[#79b8ff] rounded-2xl mb-4 transform transition-transform hover:scale-110 hover:rotate-3">
                <Mail className="w-8 h-8 text-white" />
              </div>
              <h1 className="text-3xl font-bold text-white mb-2">Forgot Password?</h1>
              <p className="text-[#c9d1d9]">No worries, we'll send you reset instructions</p>
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
                      error
                        ? 'border-red-500 focus:border-red-500'
                        : 'border-[#30363d] focus:border-[#58a6ff]'
                    }`}
                  />
                </div>
                {error && (
                  <p className="text-red-400 text-sm ml-1 animate-shake">{error}</p>
                )}
              </div>

              <button
                type="submit"
                disabled={!isFormValid() || isLoading}
                className={`w-full py-4 rounded-xl font-semibold text-white transition-all duration-300 flex items-center justify-center space-x-2 ${
                  isFormValid() && !isLoading
                    ? 'bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] hover:from-[#388bfd] hover:to-[#58a6ff] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5'
                    : 'bg-[#21262d] cursor-not-allowed'
                }`}
              >
                {isLoading ? (
                  <>
                    <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span>Sending reset link...</span>
                  </>
                ) : (
                  <span>Send Reset Link</span>
                )}
              </button>
            </form>

            <div className="mt-6 text-center">
              <p className="text-sm text-[#c9d1d9]">
                Remember your password?{' '}
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

      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 animate-fade-in">
          <div className="bg-[#161b22] rounded-3xl shadow-[0_4px_12px_rgba(0,0,0,0.5)] p-8 max-w-md w-full transform animate-scale-in">
            <div className="text-center">
              <div className="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-400 to-green-500 rounded-full mb-6 animate-bounce-slow">
                <CheckCircle className="w-10 h-10 text-white" />
              </div>
              <h2 className="text-3xl font-bold text-white mb-4">Check Your Email!</h2>
              <p className="text-[#c9d1d9] mb-6">
                We've sent a password reset link to <span className="font-semibold text-[#58a6ff]">{email}</span>
              </p>
              <p className="text-sm text-[#8b949e] mb-8">
                Please check your inbox and follow the instructions to reset your password.
              </p>
              <button
                onClick={handleCloseModal}
                className="w-full py-4 rounded-xl font-semibold text-white bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] hover:from-[#388bfd] hover:to-[#58a6ff] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300"
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
