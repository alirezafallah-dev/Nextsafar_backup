import FeaturedHotelsClient, { FeaturedHotel } from "./FeaturedHotelsClient";
import { buildApiUrl } from "@/lib/api/config";

export default async function FeaturedHotels() {
  let hotels: FeaturedHotel[] = [];
  
  try {
    const res = await fetch(
      buildApiUrl("nextsafar/v1/featured-hotels"),
      { next: { revalidate: 60 } }
    );
    if (res.ok) {
      const data = await res.json();
      // ✅ پشتیبانی از هر دو فرمت: array مستقیم و { hotels: [] }
      let rawHotels: any[] = [];
      if (Array.isArray(data)) {
        rawHotels = data;
      } else if (data?.hotels && Array.isArray(data.hotels)) {
        rawHotels = data.hotels;
      }
      
      hotels = rawHotels.map((h: any) => ({
        id: h.id,
        title: h.title,
        slug: h.slug,
        image: h.image,
        stars: h.stars,
        rating: h.rating,
        city: h.city,
        country: h.country,
        category_slug: h.category_slug || h.city_slug || "",
        city_slug: h.city_slug || "",
        country_slug: h.country_slug || "",
        amenities: h.amenities || [],
        url: h.url || "",  // ✅ اگر بک‌اند URL کامل می‌دهد
      }));
    }
  } catch {
    hotels = [];
  }

  // ✅ محدودیت نهایی: فقط 3 هتل
  hotels = hotels.slice(0, 3);

  if (hotels.length === 0) return null;
  
  return <FeaturedHotelsClient hotels={hotels} />;
}