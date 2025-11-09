import React from 'react';
import { useNavigate } from 'react-router-dom';
import { GraduationCap, ArrowRight } from 'lucide-react';
import HomeHeader from '../../components/HomePage/HomeHeader';
import Footer from '../../components/HomePage/Footer';
import BlobBackground from '../../components/Login/BlobBackground';

const majors = [
  {
    id: 'cs',
    name: 'Computer Science',
    abbreviation: 'CS',
    description: 'Explore the world of algorithms, software development, artificial intelligence, and cutting-edge technology. Build innovative solutions and shape the digital future.',
    icon: '💻',
    color: 'from-blue-500 to-cyan-500'
  },
  {
    id: 'dentistry',
    name: 'Dentistry',
    abbreviation: 'Dentistry',
    description: 'Master the art and science of oral health care. Learn advanced dental procedures, patient care, and contribute to improving smiles and overall health.',
    icon: '🦷',
    color: 'from-green-500 to-emerald-500'
  },
  {
    id: 'ece',
    name: 'Electrical & Computer Engineering',
    abbreviation: 'ECE',
    description: 'Design and innovate in electronics, telecommunications, and computer hardware. Bridge the gap between electrical systems and computational technology.',
    icon: '⚡',
    color: 'from-yellow-500 to-orange-500'
  },
  {
    id: 'pharma',
    name: 'Pharmacy',
    abbreviation: 'Pharma',
    description: 'Discover pharmaceutical sciences, drug development, and patient care. Play a vital role in healthcare by ensuring safe and effective medication use.',
    icon: '💊',
    color: 'from-purple-500 to-pink-500'
  }
];

export default function MajorsPage() {
  const navigate = useNavigate();

  const handleMajorClick = (major) => {
    navigate('/home', { state: { major: major.abbreviation } });
  };

  return (
    <div className="min-h-screen bg-[#0d1117] font-main relative overflow-hidden">
      <BlobBackground />
      <HomeHeader />

      <main className="pt-24 pb-16 px-4 relative z-10">
        <div className="container mx-auto max-w-7xl">
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-5xl font-bold text-white mb-4">
              Explore Our Majors
            </h1>
            <p className="text-gray-300 text-lg max-w-2xl mx-auto">
              Discover the diverse fields of study at our university. Click on any major to explore projects and connect with students.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {majors.map((major) => (
              <div
                key={major.id}
                onClick={() => handleMajorClick(major)}
                className="group relative bg-gray-800/50 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-white/10 hover:border-white/30 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-blue-500/20"
              >
                <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br opacity-10 rounded-bl-full" style={{ background: `linear-gradient(to bottom right, var(--tw-gradient-stops))` }}></div>

                <div className="relative">
                  <div className="flex items-start justify-between mb-4">
                    <div className={`text-5xl p-4 rounded-2xl bg-gradient-to-br ${major.color} shadow-lg`}>
                      {major.icon}
                    </div>
                    <div className="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                      <ArrowRight className="w-6 h-6 text-blue-400" />
                    </div>
                  </div>

                  <h2 className="text-2xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                    {major.name}
                  </h2>

                  <div className="flex items-center gap-2 mb-4">
                    <GraduationCap className="w-4 h-4 text-gray-400" />
                    <span className="text-sm text-gray-400 font-medium">{major.abbreviation}</span>
                  </div>

                  <p className="text-gray-300 leading-relaxed">
                    {major.description}
                  </p>

                  <div className="mt-6 pt-6 border-t border-gray-700">
                    <button className="text-blue-400 font-semibold hover:text-blue-300 transition-colors flex items-center gap-2 group-hover:gap-3 transition-all">
                      <span>Explore Projects</span>
                      <ArrowRight className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div className="mt-16 text-center">
            <div className="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-white/10 max-w-3xl mx-auto">
              <h2 className="text-2xl font-bold text-white mb-4">
                Can't Find Your Major?
              </h2>
              <p className="text-gray-300 mb-6">
                We're constantly expanding our platform. Contact us to learn about upcoming majors or suggest additions.
              </p>
              <button
                onClick={() => navigate('/contact')}
                className="px-8 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300"
              >
                Contact Us
              </button>
            </div>
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
}
