export interface City {
  id: string;
  name: string;
  slug: string;
  image: string;
}

export interface Country {
  id: string;
  name: string;
  slug: string;
  image: string;
  cities: City[];
}

export interface HotelCoordinates {
  lat: number;
  lng: number;
}

export interface Hotel {
  id: number;
  title: string;
  title_en: string;
  slug: string;
  country: string;
  city: string;
  stars: number;
  rating: number;
  reviews: number;
  image: string;
  gallery: string[];
  address: string;
  coordinates: { lat: number; lng: number };
  amenities: string[];
  price_per_night: number;
  description: string;
  featured: boolean;
}

export interface HotelsData {
  countries: Country[];
  hotels: Hotel[];
}

export interface HotelFilterState {
  country: string | null;
  city: string | null;
  minPrice: number;
  maxPrice: number;
  minRating: number;
  minStars: number;
  amenities: string[];
  searchQuery: string;
  sortBy: "price_asc" | "price_desc" | "rating" | "stars" | "popular";
}