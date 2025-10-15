export default function PrimaryButton({ children, disabled }) {
  return (
    <button
      type="submit"
      disabled={disabled}
      className={`w-full py-4 rounded-xl font-semibold text-white transition-all duration-300 flex items-center justify-center space-x-2 ${
        !disabled
          ? 'bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] hover:from-[#388bfd] hover:to-[#58a6ff] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5'
          : 'bg-[#21262d] cursor-not-allowed'
      }`}
    >
      {children}
    </button>
  );
}

