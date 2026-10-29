import {
  Wifi, Waves, Car, Utensils, Dumbbell, Coffee, Sparkles, Plane,
} from "lucide-react";

export const AMENITY_CONFIG: Record<string, { icon: React.ComponentType<{ className?: string }>; label: string }> = {
  wifi: { icon: Wifi, label: "وای‌فای" },
  pool: { icon: Waves, label: "استخر" },
  parking: { icon: Car, label: "پارکینگ" },
  restaurant: { icon: Utensils, label: "رستوران" },
  gym: { icon: Dumbbell, label: "باشگاه" },
  spa: { icon: Sparkles, label: "اسپا" },
  beach: { icon: Waves, label: "ساحل" },
  bar: { icon: Coffee, label: "کافه" },
  airport_transfer: { icon: Plane, label: "ترانسفر" },
};

export function getAmenityLabel(key: string): string {
  return AMENITY_CONFIG[key]?.label || key;
}