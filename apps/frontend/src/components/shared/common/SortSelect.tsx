"use client";
import { faDigits } from "@/lib/api/hotels";

export interface SortOption {
  key: string;
  label: string;
}

interface SortSelectProps {
  value: string;
  onChange: (value: string) => void;
  options: SortOption[];
  className?: string;
}

export default function SortSelect({
  value,
  onChange,
  options,
  className = "",
}: SortSelectProps) {
  return (
    <select
      value={value}
      onChange={(e) => onChange(e.target.value)}
      className={`px-3 py-2 rounded-lg border border-border bg-white text-sm font-bold text-text-strong outline-none focus:border-primary cursor-pointer ${className}`}
    >
      {options.map((o) => (
        <option key={o.key} value={o.key}>
          {o.label}
        </option>
      ))}
    </select>
  );
}