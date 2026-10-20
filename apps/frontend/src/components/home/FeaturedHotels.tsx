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
      
      // ✅ اطمینان از اینکه همه فیلدهای مورد نیاز وجود دارن
      hotels = rawHotels.map((h: any) => ({
        id: h.id,
        title: h.title,
        slug: h.slug,
        image: h.image,
        stars: h.stars,
        rating: h.rating,
        city: h.city,
        country: h.country,
        amenities: h.amenities || [],
        url: h.url || `/hotels/${h.slug}`,
      }));
    }
  } catch {
    hotels = [];
  }

  if (hotels.length === 0) return null;
  
  return <FeaturedHotelsClient hotels={hotels} />;
}