import React from "react";
import HomeHeader from "../../components/HomePage/HomeHeader";
import Footer from "../../components/HomePage/Footer";

export default function About() {
  return (
    <div className="min-h-screen bg-[#0a0f1a] font-main text-gray-200">
      <HomeHeader />

      <main className="pt-28 pb-20 px-6">
        <div className="max-w-5xl mx-auto space-y-16">
          {/* Header Section */}
          <section className="text-center">
            <h1 className="text-5xl font-extrabold text-white mb-4 tracking-wide">
              About <span className="text-blue-500">UniLink</span>
            </h1>
            <p className="text-gray-400 text-lg max-w-3xl mx-auto leading-relaxed">
              Bridging students, professors, and ideas — UniLink is the hub for collaboration,
              innovation, and academic excellence.
            </p>
          </section>

          {/* Mission Section */}
          <section className="bg-[#111827]/80 backdrop-blur-md rounded-2xl p-10 shadow-[0_0_25px_rgba(59,130,246,0.2)] border border-gray-700">
            <h2 className="text-3xl font-semibold text-white mb-4">
              🎯 Our Mission
            </h2>
            <p className="text-gray-300 text-lg leading-relaxed">
              UniLink is designed to create meaningful connections between students and professors.
              We believe in a future where learning extends beyond classrooms — where ideas, projects,
              and achievements are shared openly across academic communities.
            </p>
          </section>

          {/* What We Offer Section */}
          <section className="bg-[#0f172a] rounded-2xl p-10 border border-gray-700 shadow-lg hover:shadow-[0_0_25px_rgba(59,130,246,0.2)] transition-shadow">
            <h2 className="text-3xl font-semibold text-white mb-6">
              💡 What We Offer
            </h2>
            <ul className="space-y-4 text-gray-300 text-lg leading-relaxed">
              {[
                "Share and discover academic projects and research.",
                "Connect with peers and professors across majors.",
                "Collaborate on academic activities and university events.",
                "Build your academic portfolio and showcase your skills.",
              ].map((item, index) => (
                <li key={index} className="flex items-start">
                  <span className="text-blue-500 mr-3 mt-1 text-xl">•</span>
                  <span>{item}</span>
                </li>
              ))}
            </ul>
          </section>

          {/* Join Community Section */}
          <section className="text-center bg-gradient-to-r from-blue-600/20 via-blue-700/10 to-blue-900/10 rounded-2xl p-10 border border-blue-800/30 shadow-inner">
            <h2 className="text-3xl font-semibold text-white mb-4">
              🤝 Join Our Community
            </h2>
            <p className="text-gray-300 text-lg leading-relaxed max-w-3xl mx-auto">
              Whether you're a student aiming to showcase your work or a professor looking to engage
              with passionate learners, UniLink gives you the tools and visibility to make an impact.
            </p>
          </section>

          {/* Contact Section */}
          <section className="text-center border-t border-gray-700 pt-10">
            <h2 className="text-3xl font-semibold text-white mb-4">
              📩 Get In Touch
            </h2>
            <p className="text-gray-400 text-lg mb-6">
              Have questions or feedback? We'd love to hear from you!
            </p>
            <a
              href="mailto:ali2306123@miuegypt.edu.eg"
              className="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-10 rounded-full shadow-[0_0_20px_rgba(59,130,246,0.4)] hover:shadow-[0_0_30px_rgba(59,130,246,0.6)] transition-all"
            >
              Contact Us
            </a>
          </section>
        </div>
      </main>

      <Footer />
    </div>
  );
}
