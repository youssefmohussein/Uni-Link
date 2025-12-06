import React, { useState } from "react";

function CVSection({ userId }) {
  const [cvFile, setCvFile] = useState(null);
  const [uploading, setUploading] = useState(false);

  const handleFileUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;


    const validTypes = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
    if (!validTypes.includes(file.type)) {
      alert("Please upload a valid PDF or Word document.");
      return;
    }

    if (file.size > 10 * 1024 * 1024) {
      alert("File size must be under 10MB.");
      return;
    }

    try {
      setUploading(true);

      // Create FormData for file upload
      const formData = new FormData();
      formData.append('cv_file', file);
      formData.append('user_id', userId);

      // TODO: Implement actual upload to backend
      // const response = await fetch('http://localhost/backend/index.php/uploadCV', {
      //   method: 'POST',
      //   body: formData,
      //   credentials: 'include'
      // });
      // const data = await response.json();

      // For now, create local preview
      const fileUrl = URL.createObjectURL(file);

      setCvFile({
        name: file.name,
        size: (file.size / (1024 * 1024)).toFixed(2), // MB
        uploadedOn: new Date().toISOString().split("T")[0],
        url: fileUrl,
      });

      alert("CV uploaded successfully! (Note: Backend integration pending)");
    } catch (err) {
      console.error("Failed to upload CV:", err);
      alert("Failed to upload CV. Please try again.");
    } finally {
      setUploading(false);
    }
  };

  return (
    <section className="backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 relative overflow-hidden border border-white/20"
      style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
      <h2 className="text-xl font-semibold mb-4 text-white flex items-center gap-2">
        📄 CV Documents
      </h2>


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
        />
        <p className="text-white font-medium">
          {uploading ? "Uploading..." : cvFile ? "Upload a new CV" : "Upload your CV"}
        </p>
        <p className="text-sm text-gray-300">PDF, DOC, DOCX up to 10MB</p>
      </label>


      {cvFile && (
        <div className="mt-5 transition-all duration-300">
          <div className="flex items-center justify-between bg-white/5 border border-white/10 p-4 rounded-xl shadow-inner">
            <div>
              <a
                href={cvFile.url}
                download={cvFile.name}
                className="font-semibold text-accent hover:underline"
              >
                {cvFile.name}
              </a>
              <p className="text-sm text-gray-300">
                {cvFile.size} MB • Uploaded on {cvFile.uploadedOn}
              </p>
            </div>
            <a
              href={cvFile.url}
              download={cvFile.name}
              className="text-accent hover:text-accent/70 text-xl"
              title="Download CV"
            >
              ⬇
            </a>
          </div>
        </div>
      )}
    </section>
  );
}

export default CVSection;
