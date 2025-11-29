export default function CVSection() {
  return (
    <div className="bg-panel p-5 rounded-custom mb-10 transition-smooth font-main">
      <h2 className="text-xl font-semibold mb-3 text-main font-secondary">
        CV Documents
      </h2>

      <button className="bg-accent text-white px-4 py-2 rounded-custom 
        hover:bg-accent-dark transition-smooth mb-4
      ">
        Download CV
      </button>

      <div className="border-2 border-dashed 
          rounded-custom p-6 text-center cursor-pointer 
          transition-smooth 
          bg-bg border-[var(--border)]
          hover:bg-hover-bg
        "
      >
        <p className="text-main">Upload your CV</p>
        <p className="text-muted text-sm">
          PDF, DOC, DOCX up to 10MB
        </p>
      </div>

      <div className="mt-4 text-main text-sm">
        <p>Sarah_Johnson_Resume_2024.pdf</p>
        <p className="text-muted">1.2 MB • Uploaded on 2024-01-15</p>
      </div>
    </div>
  );
}
