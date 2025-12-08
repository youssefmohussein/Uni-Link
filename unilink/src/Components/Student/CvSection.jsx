import React, { useState, useEffect } from "react";
import * as studentHandler from "../../../api/studentHandler";

function CVSection({ userId }) {
  const [cvFile, setCvFile] = useState(null);
  const [uploading, setUploading] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (userId) {
      fetchExistingCV();
    }
  }, [userId]);

  const fetchExistingCV = async () => {
    try {
      setLoading(true);
      const cvData = await studentHandler.getCV(userId);

      if (cvData) {
        // Extract filename from path
        const fileName = cvData.file_path.split('/').pop();
        setCvFile({
          name: fileName,
          uploadedOn: new Date(cvData.created_at).toISOString().split("T")[0],
          url: `http://localhost/backend/${cvData.file_path}`,
          filePath: cvData.file_path
        });
      }
    } catch (err) {
      console.error("Failed to fetch CV:", err);
      // Set to null if no CV exists
      setCvFile(null);
    } finally {
      setLoading(false);
    }
  };

  const handleFileUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Only allow PDF files
    if (file.type !== "application/pdf") {
      alert("Please upload a valid PDF document.");
      return;
    }

    if (file.size > 10 * 1024 * 1024) {
      alert("File size must be under 10MB.");
      return;
    }

    try {
      setUploading(true);

      // Use studentHandler to upload CV
      await studentHandler.uploadCV(userId, file);

      // Fetch the updated CV
      await fetchExistingCV();
      alert("CV uploaded successfully!");
    } catch (err) {
      console.error("Failed to upload CV:", err);
      alert(err.message || "Failed to upload CV. Please try again.");
    } finally {
      setUploading(false);
    }
  };

  const handleDownloadCV = async () => {
    try {
      // Use the backend download endpoint
      const downloadUrl = `http://localhost/backend/index.php/downloadCV/${userId}`;

      // Create a temporary link and trigger download
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = cvFile.name;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } catch (err) {
      console.error("Failed to download CV:", err);
      alert("Failed to download CV. Please try again.");
    }
  };

  return (
    <section className="backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 relative overflow-hidden border border-white/20"
      style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
      <h2 className="text-xl font-semibold mb-4 text-white flex items-center gap-2">
        📄 CV Documents
      </h2>

      {loading ? (
        <div className="text-center py-8">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-accent"></div>
          <p className="mt-2 text-sm text-muted">Loading CV...</p>
        </div>
      ) : (
        <>
          <label
            htmlFor="cvFile"
            className={`block border-2 border-dashed border-white/30 hover:border-accent transition-all p-6 rounded-custom text-center cursor-pointer ${uploading ? 'opacity-50 cursor-not-allowed' : ''}`}
          >
            <input
              type="file"
              id="cvFile"
              hidden
              onChange={handleFileUpload}
              disabled={uploading}
              accept=".pdf"
            />
            <p className="text-white font-medium">
              {uploading ? "Uploading..." : cvFile ? "Upload a new CV" : "Upload your CV"}
            </p>
            <p className="text-sm text-gray-300">PDF up to 10MB</p>
          </label>

          {cvFile && (
            <div className="mt-5 transition-all duration-300">
              <div className="flex items-center justify-between bg-white/5 border border-white/10 p-4 rounded-xl shadow-inner hover:bg-white/10 transition">
                <div>
                  <p className="font-semibold text-white">
                    {cvFile.name}
                  </p>
                  <p className="text-sm text-gray-300">
                    Uploaded on {cvFile.uploadedOn}
                  </p>
                </div>
                <button
                  onClick={handleDownloadCV}
                  className="text-accent hover:text-accent/80 text-2xl transition"
                  title="Download CV"
                >
                  ⬇
                </button>
              </div>
            </div>
          )}
        </>
      )}
    </section>
  );
}

export default CVSection;

