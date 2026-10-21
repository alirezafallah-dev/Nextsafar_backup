import { getPosts } from "@/lib/api/wordpress";
import { buildApiUrl } from "@/lib/api/config";
import { getBestImageUrl, IMG_TARGETS } from "@/lib/utils/image";
import TrendingClient from "./TrendingClient";
import type { HotelItem, TourCatItem } from "./TrendingClient";

const clean = (s: any) =>
  String(s ?? "")
    .replace(/<[^>]+>/g, "")
    .trim();

function pickMeta(p: any, keys: string[]): any {
  for (const k of keys) {
    const v = p.meta?.[k] ?? p.acf?.[k];
    if (v !== undefined && v !== null && v !== "") return v;
  }
  return null;
}

function extractHotelCity(p: any): string {
  const metaCity = pickMeta(p, ["_hotel_city", "_geo_city", "city"]);
  if (metaCity) return clean(metaCity);

  const hotelCats = p._embedded?.["wp:term"]?.find(
    (arr: any) => Array.isArray(arr) && arr[0]?.taxonomy === "hotel_category",
  );
  if (hotelCats?.length) return clean(hotelCats[0].name);

  const tourism = p._embedded?.["wp:term"]?.find(
    (arr: any) => Array.isArray(arr) && arr[0]?.taxonomy === "tourism",
  );
  if (tourism?.length) return clean(tourism[0].name);

  return clean(p._embedded?.["wp:term"]?.[0]?.[0]?.name) || "";
}

async function getTourCategories(): Promise<TourCatItem[]> {
  try {
    const res = await fetch(
      buildApiUrl("wp/v2/tour_category?per_page=30&orderby=count&order=desc"),
      { next: { revalidate: 3600 } },
    );
    if (!res.ok) return [];

    const terms: any[] = await res.json();
    return terms
      .filter((t) => t.count > 0 && t.name)
      .slice(0, 8)
      .map((t) => ({
        slug: decodeURIComponent(t.slug),
        name: clean(t.name),
        count: t.count,
        image: t.ns_term_image ?? null,
        duration: t.ns_tour_info?.duration
          ? Number(t.ns_tour_info.duration)
          : null,
      }));
  } catch (e) {
    console.error("Tour categories error:", e);
    return [];
  }
}

export default async function TrendingNow() {
  const [hotelPosts, tourCats] = await Promise.all([
    getPosts("hotel", { per_page: 8, orderby: "date", order: "desc" }).catch(
      () => [],
    ),
    getTourCategories(),
  ]);

  const hotels: HotelItem[] = (hotelPosts as any[])
    .map((p) => ({
      id: p.id || 0,
      slug: p.slug,
      title: clean(p.title?.rendered),
      title_en: clean(p.meta?._hotel_title_en || ""),
      image:
        getBestImageUrl(p._embedded?.["wp:featuredmedia"]?.[0], IMG_TARGETS.card) ||
        null,
      gallery: p.meta?._hotel_gallery?.map((g: any) => g.url) || [],
      stars: Number(pickMeta(p, ["_hotel_stars", "stars"])) || null,
      city: extractHotelCity(p),
      address: clean(p.meta?._hotel_address || extractHotelCity(p)),
      price: Number(pickMeta(p, ["_hotel_price", "price"])) || null,
      price_per_night: 0, // قیمت فعلاً نمایش داده نمی‌شود
      rating: Number(pickMeta(p, ["_hotel_rating", "rating"])) || null,
      reviews: Number(pickMeta(p, ["_hotel_reviews", "reviews"])) || 0,
      amenities: p.meta?._hotel_amenities || [],
      featured: Boolean(pickMeta(p, ["_hotel_featured", "featured"])),
      coordinates: {
        lat: Number(pickMeta(p, ["_hotel_lat", "lat"])) || 0,
        lng: Number(pickMeta(p, ["_hotel_lng", "lng"])) || 0,
      },
    }))
    .filter((i) => i.title)
    .slice(0, 8);

  const finalTourCats = tourCats.slice(0, 8);

  if (!hotels.length && !finalTourCats.length) return null;

  return (
    <section className="ns-container py-8 md:py-12">
      <TrendingClient hotels={hotels} tourCats={finalTourCats} />
    </section>
  );
}