import type { Hotel } from "@/types/hotel";

export const SORT_OPTIONS = [
  { key: "popular", label: "پیشنهادی" },
  { key: "rating", label: "بالاترین امتیاز" },
  { key: "stars", label: "بیشترین ستاره" },
  { key: "price_asc", label: "ارزان‌ترین" },
  { key: "price_desc", label: "گران‌ترین" },
];

export const VIEW_OPTIONS = ["list", "grid"] as const;
export type ViewMode = (typeof VIEW_OPTIONS)[number];

export const DEFAULT_PRICE_MAX = 10_000_000;

export type SortKey = (typeof SORT_OPTIONS)[number]["key"];

export interface HotelFiltersState {
  search: string;
  stars: number[];
  amenities: string[];
  priceRange: [number, number];
  minRating: number;
}

export const sortHotels = (hotels: Hotel[], sortBy: SortKey): Hotel[] => {
  const sorted = [...hotels];
  
  switch (sortBy) {
    case "rating":
      return sorted.sort((a, b) => b.rating - a.rating);
    case "stars":
      return sorted.sort((a, b) => b.stars - a.stars);
    case "price_asc":
      return sorted.sort(
        (a, b) => (a.price_per_night || 0) - (b.price_per_night || 0)
      );
    case "price_desc":
      return sorted.sort(
        (a, b) => (b.price_per_night || 0) - (a.price_per_night || 0)
      );
    default:
      return sorted.sort((a, b) => {
        const sA = (a.featured ? 100 : 0) + a.rating + (a.reviews || 0) / 1000;
        const sB = (b.featured ? 100 : 0) + b.rating + (b.reviews || 0) / 1000;
        return sB - sA;
      });
  }
};