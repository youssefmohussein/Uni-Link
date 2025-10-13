// src/components/CVSection.jsx
import React from "react";

function CVSection() {
  return (
    <section className="bg-panel rounded-custom shadow-custom p-6">
      <h2 className="text-lg font-semibold mb-4">📄 CV Documents</h2>
      <label
        htmlFor="cvFile"
        className="block border-2 border-dashed border-muted p-6 rounded-custom text-center cursor-pointer hover:border-accent"
      >
        <input type="file" id="cvFile" hidden />
        <p className="text-main font-medium">Upload your CV</p>
        <p className="text-sm text-muted">PDF, DOC, DOCX up to 10MB</p>
      </label>

      <div className="mt-4">
        <div className="flex items-center justify-between bg-main p-3 rounded-lg">
          <div>
            <a href="#" className="font-medium text-main hover:underline">
              AhmedMohamed_Resume.pdf
            </a>
            <p className="text-sm text-muted">
              1.2 MB • Uploaded on 2024-01-15
            </p>
          </div>
          <button classNalme="text-accent hover:opacity-80">⬇</button>
        </div>
      </div>
    </section>
  );
}

export default CVSection;
