import type { LucideIcon } from "lucide-react";

/* ═══ تایپ‌های عمومی فیلتر ═══ */

export interface FilterOption {
  id: string | number;
  label: string;
  icon?: LucideIcon;
}

export interface FilterSectionProps {
  title: string;
  icon?: React.ReactNode;
  badge?: number;
  defaultOpen?: boolean;
  children: React.ReactNode;
}

/* ═══ فیلتر جستجو ═══ */
export interface SearchFilterProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  label?: string;
}

/* ═══ فیلتر ستاره ═══ */
export interface StarRatingFilterProps {
  options: number[];
  selected: number[];
  onChange: (stars: number[]) => void;
}

/* ═══ فیلتر امتیاز ═══ */
export interface RatingOption {
  val: number;
  label: string;
}

export interface RatingFilterProps {
  options: RatingOption[];
  selected: number;
  onChange: (rating: number) => void;
}

/* ═══ فیلتر امکانات ═══ */
export interface AmenitiesFilterProps {
  options: FilterOption[];
  selected: string[];
  onChange: (amenities: string[]) => void;
  columns?: 2 | 3;
}

/* ═══ فیلتر چک‌باکس ═══ */
export interface CheckboxGroupFilterProps {
  options: string[];
  selected?: string[];
  onChange?: (selected: string[]) => void;
}

/* ═══ چیپ‌های موبایل ═══ */
export interface FilterChip {
  key: string;
  label: string;
  onRemove: () => void;
}

export interface FilterChipsProps {
  chips: FilterChip[];
  className?: string;
}

/* ═══ مودال فیلتر موبایل ═══ */
export interface MobileFilterSheetProps {
  open: boolean;
  onClose: () => void;
  title?: string;
  resultCount?: number;
  resultLabel?: string;
  children: React.ReactNode;
}