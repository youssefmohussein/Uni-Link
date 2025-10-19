import React from 'react';
import HomeHeader from '../../components/HomePage/HomeHeader';
import Footer from '../../components/HomePage/Footer';

export default function About() {
  return (
    <div className="min-h-screen bg-[#0d1117] font-main">
      <HomeHeader />

      <main className="pt-24 pb-16 px-4">
        <div className="container mx-auto max-w-4xl">
          <h1 className="text-4xl font-bold text-white mb-8">About UniLink</h1>

          <div className="bg-gray-800 rounded-lg p-8 shadow-xl space-y-6">
            <section>
              <h2 className="text-2xl font-semibold text-white mb-4">Our Mission</h2>
              <p className="text-gray-300 leading-relaxed">
                UniLink is a comprehensive platform designed to connect students, professors, and academic communities.
                We provide a space where knowledge sharing, collaboration, and academic excellence thrive.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-semibold text-white mb-4">What We Offer</h2>
              <ul className="text-gray-300 leading-relaxed space-y-3">
                <li className="flex items-start">
                  <span className="text-blue-400 mr-3">•</span>
                  <span>Share and discover academic projects and research</span>
                </li>
                <li className="flex items-start">
                  <span className="text-blue-400 mr-3">•</span>
                  <span>Connect with peers and professors in your field</span>
                </li>
                <li className="flex items-start">
                  <span className="text-blue-400 mr-3">•</span>
                  <span>Collaborate on academic activities and events</span>
                </li>
                <li className="flex items-start">
                  <span className="text-blue-400 mr-3">•</span>
                  <span>Build your academic portfolio and showcase your skills</span>
                </li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-semibold text-white mb-4">Join Our Community</h2>
              <p className="text-gray-300 leading-relaxed">
                Whether you're a student looking to showcase your work or a professor seeking to engage with
                talented individuals, UniLink provides the tools and community to help you succeed.
              </p>
            </section>

            <section className="pt-6 border-t border-gray-700">
              <h2 className="text-2xl font-semibold text-white mb-4">Get In Touch</h2>
              <p className="text-gray-300 leading-relaxed">
                Have questions or suggestions? We'd love to hear from you!
              </p>
              <a
                href="mailto:ali2306123@miuegypt.edu.eg"
                className="inline-block mt-4 bg-blue-600 text-white rounded-full py-3 px-8 font-semibold hover:bg-blue-700 transition-colors shadow-lg"
              >
                Contact Us
              </a>
            </section>
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
}
