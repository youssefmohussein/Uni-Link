import React, { useState, useEffect } from "react";
import * as studentHandler from "../../../api/studentHandler";

function EditProjectModal({ isOpen, onClose, project, userId, onSuccess }) {
    const [form, setForm] = useState({
        title: "",
        description: "",
        file: null,
    });
    const [updating, setUpdating] = useState(false);

    // Populate form when project changes
    useEffect(() => {
        if (project) {
            setForm({
                title: project.title || "",
                description: project.description || "",
                file: null,
            });
        }
    }, [project]);

    if (!isOpen || !project) return null;

    const handleChange = (e) => {
        const { name, value, files } = e.target;
        setForm({
            ...form,
            [name]: files ? files[0] : value,
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!form.title.trim() || !form.description.trim()) {
            alert("Please fill in title and description");
            return;
        }

        try {
            setUpdating(true);

            // Prepare FormData for update
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('project_id', project.project_id);
            formData.append('title', form.title.trim());
            formData.append('description', form.description.trim());

            // Add file if changed
            if (form.file) {
                formData.append('project_file', form.file);
            }

            // Update project via API
            const response = await fetch('http://localhost:8000/updateProject', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });

            const data = await response.json();
            if (data.status !== 'success') {
                throw new Error(data.message || 'Failed to update project');
            }

            // Reset form
            setForm({
                title: "",
                description: "",
                file: null,
            });

            // Call onSuccess to refresh the project list
            if (onSuccess) {
                await onSuccess(userId);
            }

            // Close modal
            onClose();

            // Show success message
            alert("Project updated successfully!");

        } catch (err) {
            console.error("Failed to update project:", err);
            alert(err.message || "Failed to update project. Please try again.");
        } finally {
            setUpdating(false);
        }
    };

    return (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-2xl animate-fadeIn">
                {/* Header */}
                <h2 className="text-2xl font-semibold text-main mb-6 border-b border-muted pb-3">
                    ✏️ Edit Project
                </h2>

                <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Title */}
                    <div>
                        <label className="block text-sm text-muted mb-2">Project Title</label>
                        <input
                            type="text"
                            name="title"
                            placeholder="Project Title"
                            value={form.title}
                            onChange={handleChange}
                            className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
                        />
                    </div>

                    {/* Description */}
                    <div>
                        <label className="block text-sm text-muted mb-2">Description</label>
                        <textarea
                            name="description"
                            placeholder="Project Description"
                            rows="4"
                            value={form.description}
                            onChange={handleChange}
                            className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
                        />
                    </div>

                    {/* File Upload */}
                    <div>
                        <label className="block text-sm text-muted mb-2">
                            Replace Project File (Optional)
                        </label>
                        <input
                            type="file"
                            name="file"
                            accept=".pdf,.zip,.rar,.doc,.docx"
                            onChange={handleChange}
                            className="mt-2 block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent file:text-white hover:file:bg-accent/80"
                        />
                        {project.file_path && !form.file && (
                            <p className="text-xs text-gray-400 mt-2">
                                Current file: {project.file_path.split('/').pop()}
                            </p>
                        )}
                    </div>

                    <div className="flex justify-end gap-3 pt-4">
                        <button
                            type="button"
                            onClick={onClose}
                            disabled={updating}
                            className="px-5 py-2 rounded-xl bg-muted/20 text-muted hover:bg-muted/30 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={updating}
                            className="px-6 py-2 rounded-xl bg-accent text-white font-semibold shadow-md hover:shadow-lg hover:bg-accent/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {updating ? "Updating..." : "Save Changes ✏️"}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default EditProjectModal;
