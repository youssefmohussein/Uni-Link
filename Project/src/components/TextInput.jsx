export default function TextInput({
  icon: Icon,
  type = "text",
  value,
  onChange,
  placeholder,
  invalid = false,
  rightSlot,
}) {
  return (
    <div className="relative group">
      {Icon && (
        <Icon className="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#8b949e] w-5 h-5 transition-colors group-focus-within:text-[#58a6ff]" />
      )}
      <input
        type={type}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        className={`w-full ${Icon ? 'pl-12' : 'pl-4'} ${rightSlot ? 'pr-12' : 'pr-4'} py-3.5 rounded-xl border-2 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#58a6ff]/20 bg-[#0d1117] text-white placeholder-[#8b949e] ${invalid ? 'border-red-500 focus:border-red-500' : 'border-[#30363d] focus:border-[#58a6ff]'}`}
      />
      {rightSlot && (
        <div className="absolute right-4 top-1/2 -translate-y-1/2">{rightSlot}</div>
      )}
    </div>
  );
}

