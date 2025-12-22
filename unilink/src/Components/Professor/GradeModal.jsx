import React, { useState } from 'react';
import { toast } from 'react-hot-toast';

const GradeModal = ({ project, onClose, onSubmit }) => {
    const [grade, setGrade] = useState(project.grade || '');
    const [comments, setComments] = useState('');
    const [status, setStatus] = useState(project.status || 'APPROVED');
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!grade || grade < 0 || grade > 100) {
            toast.error('Please enter a valid grade between 0 and 100');
            return;
        }

        setSubmitting(true);
        try {
            await onSubmit(project.project_id, parseFloat(grade), comments || null, status);
            toast.success('Project graded successfully!');
            onClose();
        } catch (error) {
            toast.error('Failed to grade project: ' + error.message);
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div className="bg-[#1a1a1a] rounded-2xl border border-white/10 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                {/* Header */}
                <div className="p-6 border-b border-white/10">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-bold text-white">Grade Project</h2>
                        <button
                            onClick={onClose}
                            className="p-2 text-gray-400 hover:text-white transition rounded-full hover:bg-white/5"
                        >
                            <i className="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                </div>

                {/* Content */}
                <form onSubmit={handleSubmit} className="p-6 space-y-6">
                    {/* Project Info */}
                    <div className="bg-white/5 p-4 rounded-xl border border-white/5">
                        <h3 className="text-lg font-bold text-white mb-2">{project.title}</h3>
                        <div className="space-y-1 text-sm text-gray-400">
                            <p><span className="text-gray-500">Student:</span> {project.student_name}</p>
                            <p><span className="text-gray-500">Email:</span> {project.student_email}</p>
                            <p><span className="text-gray-500">Faculty:</span> {project.faculty_name}</p>
                            <p><span className="text-gray-500">Major:</span> {project.major_name}</p>
                            <p><span className="text-gray-500">Submitted:</span> {new Date(project.submitted_at).toLocaleDateString()}</p>
                        </div>
                    </div>

                    {/* Grade Input */}
                    <div>
                        <label className="block text-sm font-medium text-gray-300 mb-2">
                            Grade (0-100) <span className="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            value={grade}
                            onChange={(e) => setGrade(e.target.value)}
                            className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                            placeholder="Enter grade (e.g., 85.5)"
                            required
                        />
                        <p className="mt-1 text-xs text-gray-500">Enter a grade between 0 and 100</p>
                    </div>

                    {/* Status Dropdown */}
                    <div>
                        <label className="block text-sm font-medium text-gray-300 mb-2">
                            Project Status <span className="text-red-500">*</span>
                        </label>
                        <select
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                            className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                            required
                        >
                            <option value="APPROVED" className="bg-[#1a1a1a]">✅ Approved</option>
                            <option value="REJECTED" className="bg-[#1a1a1a]">❌ Rejected</option>
                            <option value="PENDING" className="bg-[#1a1a1a]">⏳ Pending</option>
                        </select>
                        <p className="mt-1 text-xs text-gray-500">Select the status for this project</p>
                    </div>

                    {/* Comments Input */}
                    <div>
                        <label className="block text-sm font-medium text-gray-300 mb-2">
                            Comments (Optional)
                        </label>
                        <textarea
                            value={comments}
                            onChange={(e) => setComments(e.target.value)}
                            rows="5"
                            className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent resize-none"
                            placeholder="Add feedback or comments for the student..."
                        />
                        <p className="mt-1 text-xs text-gray-500">Provide constructive feedback to help the student improve</p>
                    </div>

                    {/* Current Grade (if exists) */}
                    {project.grade && (
                        <div className="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4">
                            <div className="flex items-center gap-2 text-yellow-500">
                                <i className="fa-solid fa-triangle-exclamation"></i>
                                <span className="font-medium">Current Grade: {project.grade}/100</span>
                            </div>
                            <p className="text-xs text-gray-400 mt-1">This project has already been graded. Submitting will update the grade.</p>
                        </div>
                    )}

                    {/* Actions */}
                    <div className="flex gap-3 pt-4">
                        <button
                            type="submit"
                            disabled={submitting}
                            className="flex-1 px-6 py-3 bg-accent text-white rounded-xl font-medium hover:bg-accent/80 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            {submitting ? (
                                <>
                                    <i className="fa-solid fa-spinner fa-spin"></i>
                                    Submitting...
                                </>
                            ) : (
                                <>
                                    <i className="fa-solid fa-check"></i>
                                    Submit Grade
                                </>
                            )}
                        </button>
                        <button
                            type="button"
                            onClick={onClose}
                            disabled={submitting}
                            className="px-6 py-3 bg-white/5 text-gray-300 rounded-xl font-medium hover:bg-white/10 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default GradeModal;
