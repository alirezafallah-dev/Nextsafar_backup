/* ═══ انواع مکان‌ها ═══ */
export type PlaceType =
  | "hotel"
  | "restaurant"
  | "airport"
  | "destination"
  | "hospital"
  | "center"
  | "attraction";

/* ═══ نقطه نقشه (گسترش‌یافته) ═══ */
export interface ListingMapPoint {
  key: string;
  lat: number;
  lng: number;
  title: string;
  type: PlaceType;
  locationType?: string;
  href?: string;

  // اطلاعات عمومی
  image?: string | null;
  description?: string;
  address?: string;
  rating?: number | null;
  reviewsCount?: number;

  // مخصوص هتل
  stars?: number;

  // مخصوص رستوران
  cuisine?: string;
  priceRange?: string;

  // مخصوص فرودگاه
  airportCode?: string;
  city?: string;

  // مخصوص جاذبه
  category?: string;
}