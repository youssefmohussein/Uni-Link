export default function AuthCard({ children, className = "" }) {
  return (
    <div className={"bg-[#161b22] backdrop-blur-lg rounded-3xl shadow-[0_4px_12px_rgba(0,0,0,0.5)] p-8 " + className}>
      {children}
    </div>
  );
}

