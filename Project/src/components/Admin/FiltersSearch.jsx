import React from "react";

export default function FiltersSearch({
  searchValue,
  onSearchChange,
  searchPlaceholder = "Search...",
  filters = [],
  className = "",
}) {
  return (
    <div className={`space-y-3 ${className}`}>
      {/* Search Input */}
      <input
        value={searchValue}
        onChange={onSearchChange}
        placeholder={searchPlaceholder}
        className="w-full px-3 py-2 rounded-custom bg-panel border border-white/10 text-main placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-accent"
      />
      
      {/* Dynamic Filters */}
      {filters.map((filter, index) => (
        <select
          key={index}
          value={filter.value}
          onChange={filter.onChange}
          className="w-full px-3 py-2 rounded-custom bg-panel border border-white/10 text-main focus:ring-2 focus:ring-accent"
        >
          {filter.options.map((option) => (
            <option key={option} value={option}>
              {option}
            </option>
          ))}
        </select>
      ))}
    </div>
  );
}
