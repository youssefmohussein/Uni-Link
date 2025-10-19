import React, { useState } from "react";

function CVSection() {
  const [cvFile, setCvFile] = useState(null);

  const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Check file type and size
    const validTypes = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
    if (!validTypes.includes(file.type)) {
      alert("Please upload a valid PDF or Word document.");
      return;
    }

    if (file.size > 10 * 1024 * 1024) {
      alert("File size must be under 10MB.");
      return;
    }

    // Create URL for preview/download
    const fileUrl = URL.createObjectURL(file);

    // Store file info
    setCvFile({
      name: file.name,
      size: (file.size / (1024 * 1024)).toFixed(2), // MB
      uploadedOn: new Date().toISOString().split("T")[0],
      url: fileUrl,
    });
  };

  return (
    <section className="bg-panel rounded-custom shadow-custom p-6 relative overflow-hidden">
      <h2 className="text-xl font-semibold mb-4 text-main flex items-center gap-2">
        📄 CV Documents
      </h2>

      {/* Upload Area */}
      <label
        htmlFor="cvFile"
        className="block border-2 border-dashed border-muted/60 hover:border-accent transition-all p-6 rounded-custom text-center cursor-pointer"
      >
        <input type="file" id="cvFile" hidden onChange={handleFileUpload} />
        <p className="text-main font-medium">
          {cvFile ? "Upload a new CV" : "Upload your CV"}
        </p>
        <p className="text-sm text-muted">PDF, DOC, DOCX up to 10MB</p>
      </label>

      {/* CV Preview */}
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
              <p className="text-sm text-muted">
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
