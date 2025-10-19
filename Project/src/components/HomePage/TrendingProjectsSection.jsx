import React from 'react';
import { Link } from 'react-router-dom';
import ProjectCard from '../Student/ProjectCard';

const mockProjects = [
  {
    id: 1,
    image: 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=800',
    title: 'AI-Powered Healthcare Assistant',
    description: 'A machine learning application that helps diagnose common health issues using computer vision and natural language processing.',
    skills: ['Python', 'TensorFlow', 'React'],
    major: 'Computer Science',
    creator: 'Sarah Ahmed',
    reactions: 342
  },
  {
    id: 2,
    image: 'https://images.pexels.com/photos/3861972/pexels-photo-3861972.jpeg?auto=compress&cs=tinysrgb&w=800',
    title: 'Smart City Infrastructure Model',
    description: 'Comprehensive urban planning project focusing on sustainable development and IoT integration for modern cities.',
    skills: ['AutoCAD', 'SketchUp', 'IoT'],
    major: 'Civil Engineering',
    creator: 'Mohamed Ali',
    reactions: 287
  },
  {
    id: 3,
    image: 'https://images.pexels.com/photos/2280571/pexels-photo-2280571.jpeg?auto=compress&cs=tinysrgb&w=800',
    title: 'Blockchain-Based Voting System',
    description: 'Secure and transparent digital voting platform leveraging blockchain technology for election integrity.',
    skills: ['Solidity', 'Web3.js', 'Node.js'],
    major: 'Information Systems',
    creator: 'Layla Hassan',
    reactions: 421
  },
  {
    id: 4,
    image: 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=800',
    title: 'Renewable Energy Optimization',
    description: 'Advanced algorithms for maximizing solar panel efficiency and energy storage in residential applications.',
    skills: ['MATLAB', 'Python', 'CAD'],
    major: 'Electrical Engineering',
    creator: 'Omar Youssef',
    reactions: 356
  },
  {
    id: 5,
    image: 'https://images.pexels.com/photos/8438922/pexels-photo-8438922.jpeg?auto=compress&cs=tinysrgb&w=800',
    title: 'Marketing Analytics Dashboard',
    description: 'Interactive dashboard for real-time marketing campaign analysis and consumer behavior insights.',
    skills: ['Tableau', 'SQL', 'Python'],
    major: 'Business Administration',
    creator: 'Nour Mahmoud',
    reactions: 298
  },
  {
    id: 6,
    image: 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=800',
    title: 'Augmented Reality Art Gallery',
    description: 'Immersive AR experience showcasing digital art installations with interactive elements and social features.',
    skills: ['Unity', 'C#', 'ARKit'],
    major: 'Digital Media',
    creator: 'Yasmin Ibrahim',
    reactions: 512
  }
];

export default function TrendingProjectsSection() {
  return (
    <section className="py-20 px-4 relative">
      <div className="container mx-auto max-w-7xl">
        <div className="flex items-center justify-between mb-12">
          <div>
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-3 flex items-center gap-3">
              <span className="text-3xl">🔥</span> Trending Projects
            </h2>
            <p className="text-gray-400 text-lg">Discover the most innovative work from students across all majors</p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center mb-12">
          {mockProjects.map((project) => (
            <ProjectCard
              key={project.id}
              image={project.image}
              title={project.title}
              description={project.description}
              skills={project.skills}
            />
          ))}
        </div>

        <div className="flex justify-center">
          <Link
            to="/home"
            className="px-8 py-4 rounded-xl font-semibold text-white bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] hover:from-[#388bfd] hover:to-[#58a6ff] shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-2"
          >
            View All Projects
            <i className="fas fa-arrow-right"></i>
          </Link>
        </div>
      </div>
    </section>
  );
}
