"use client";

import type { CheckboxGroupFilterProps } from "./types";

export default function CheckboxGroupFilter({
  options,
  selected = [],
  onChange,
}: CheckboxGroupFilterProps) {
  const toggleOption = (option: string) => {
    const newSelected = selected.includes(option)
      ? selected.filter((o) => o !== option)
      : [...selected, option];
    onChange?.(newSelected);
  };

  return (
    <div className="space-y-1.5">
      {options.map((option) => (
        <label
          key={option}
          className="flex items-center gap-2 cursor-pointer group"
        >
          <input
            type="checkbox"
            checked={selected.includes(option)}
            onChange={() => toggleOption(option)}
            className="w-3.5 h-3.5 rounded accent-primary cursor-pointer"
          />
          <span className="text-xs text-text group-hover:text-primary transition">
            {option}
          </span>
        </label>
      ))}
    </div>
  );
}