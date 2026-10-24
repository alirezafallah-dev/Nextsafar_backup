export interface SiteHotel {
  id: number;
  slug: string;
  title: string;
  title_en?: string;
  stars: number;
  rating: number;
  address: string;
  image: string | null;
  amenities: string[];
  featured: boolean;
  url: string;
  lat: number | null;
  lng: number | null;
}

export interface OnlineHotel {
  property_id: string;
  name: string;
  image: string | null;
  rating: number;
  reviews: number;
  address: string;
  lat: number | null;
  lng: number | null;
  stars: number;
  amenities: string[];
  link: string;
  token?: string;
}

export interface HotelPrice {
  per_night_toman: number;
  usd: number;
}

export interface UnifiedHotel {
  source: "site" | "online";
  match_confidence: number | null;
  site: SiteHotel | null;
  online: OnlineHotel | null;
  price: HotelPrice | null;
  badges: string[];
}

export interface HotelsSearchResult {
  ok: boolean;
  items: UnifiedHotel[];
  from_cache: boolean;
  provider: string;
  meta: {
    nights: number;
    site_count: number;
    online_count: number;
    matched?: number;
    stale: boolean;
  };
}

export interface OnlineHotelRoom {
  name: string;
  description: string;
  image: string | null;
  beds: string;
  max_occupancy: number | null;
  price_usd: number;
  per_night_toman: number;
  free_cancellation: boolean;
  breakfast_included: boolean;
}

export interface OnlineHotelDetails {
  name: string;
  type: string;
  address: string;
  rating: number;
  reviews_count: number;
  stars: number;
  images: string[];
  rooms: OnlineHotelRoom[];
  reviews: { author: string; rating: number; date: string; snippet: string }[];
  amenities: string[];
  gps: { latitude: number; longitude: number } | null;
  check_in_time: string | null;
  check_out_time: string | null;
  nearby_places: { name?: string; distance?: string }[];
  price_usd: number;
  per_night_toman: number;
}

export interface HotelQueryInfo {
  city: string;
  checkIn: string;
  checkOut: string;
  adults: number;
}