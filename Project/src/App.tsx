import { useState } from 'react';
import Login from './components/Login';
import Signup from './components/Signup';
import ForgotPassword from './components/ForgetPassword';

export default function App() {
  const [currentPage, setCurrentPage] = useState('login');

  return (
    <>
      {currentPage === 'login' && (
        <Login
          onNavigateToSignup={() => setCurrentPage('signup')}
          onNavigateToForgotPassword={() => setCurrentPage('forgot-password')}
        />
      )}
      {currentPage === 'signup' && (
        <Signup onNavigateToLogin={() => setCurrentPage('login')} />
      )}
      {currentPage === 'forgot-password' && (
        <ForgotPassword onNavigateToLogin={() => setCurrentPage('login')} />
      )}
    </>
  );
}
