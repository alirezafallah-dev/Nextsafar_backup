import type { Metadata } from "next";
import { unstable_cache } from "next/cache";
import HotelsPage from "@/components/hotels/HotelsPage";
import HotelHero from "@/components/hotels/HotelHero";
import HotelHeroInfo from "@/components/hotels/HotelHeroInfo";
import HotelCategoriesSection from "@/components/hotels/HotelCategoriesSection";
import hotelsData from "@/data/hotels.json";
import categoriesData from "@/data/mock/hotel-categories.json";
import type { HotelsData } from "@/types/hotel";

export const metadata: Metadata = {
  title: "راهنمای جامع هتل‌ها | سفر بعدی ایرانیان",
  description: "اطلاعات کامل صدها هتل در سراسر جهان.",
  openGraph: {
    title: "راهنمای جامع هتل‌ها | سفر بعدی ایرانیان",
    description: "اطلاعات کامل هتل‌های داخلی و خارجی با موقعیت دقیق روی نقشه",
    images: ["/images/hero/Hotel.png"],
  },
};

const getHotelsData = unstable_cache(
  async (): Promise<HotelsData> => hotelsData as HotelsData,
  ["hotels-data"],
  { revalidate: 3600, tags: ["hotels"] }
);

export default async function HotelsRoute() {
  const data = await getHotelsData();

  return (
    <>
      <link
        rel="preload"
        href="/fonts/IRANSansX-Bold.woff2"
        as="font"
        type="font/woff2"
        crossOrigin="anonymous"
      />
      <link
        rel="preload"
        as="image"
        href="/images/hero/Hotel.png"
        fetchPriority="high"
      />

      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "ItemList",
            itemListElement: data.hotels.slice(0, 10).map((hotel, index) => ({
              "@type": "Hotel",
              position: index + 1,
              name: hotel.title,
              image: hotel.image,
              address: { "@type": "PostalAddress", streetAddress: hotel.address },
              starRating: { "@type": "Rating", ratingValue: hotel.stars },
              aggregateRating: hotel.rating
                ? {
                    "@type": "AggregateRating",
                    ratingValue: hotel.rating,
                    reviewCount: hotel.reviews || 0,
                  }
                : undefined,
            })),
          }),
        }}
      />

      <HotelHero />
      <HotelHeroInfo />

      {/* ✅ دسته‌بندی سه‌سطحی */}
      <HotelCategoriesSection
        title="دسته‌بندی هتل‌ها"
        subtitle="منطقه، کشور و شهر مورد نظر خود را انتخاب کنید"
        regions={categoriesData.regions}
        countries={categoriesData.countries}
        cities={categoriesData.cities}
        videoSrc="/videos/hotel-intro.mp4"
        videoPoster="/images/categories/video-poster.jpg"
        basePath="/hotels/category"
      />

      <HotelsPage data={data} />
    </>
  );
}