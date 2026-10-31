// src/app/hotels/[country]/[city]/page.tsx
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import HotelsPage from "@/components/hotels/HotelsPage";
import CategoryHero from "@/components/hotels/CategoryHero";
import hotelsData from "@/data/hotels.json";
import type { HotelsData } from "@/types/hotel";

interface Props {
  params: Promise<{ country: string; city: string }>;
}

export async function generateStaticParams() {
  const params: { country: string; city: string }[] = [];
  hotelsData.countries.forEach((country) => {
    country.cities.forEach((city) => {
      params.push({ country: country.slug, city: city.slug });
    });
  });
  return params;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { country: countrySlug, city: citySlug } = await params;

  const country = hotelsData.countries.find((c) => c.slug === countrySlug);
  if (!country) return { title: "کشور یافت نشد" };

  const city = country.cities.find((c) => c.slug === citySlug);
  if (!city) return { title: "شهر یافت نشد" };

  const hotelCount = hotelsData.hotels.filter(
    (h) => h.city === city.id
  ).length;

  return {
    title: `هتل‌های ${city.name}، ${country.name} | سفر بعدی ایرانیان`,
    description: `لیست کامل ${hotelCount} هتل در ${city.name}. رزرو آنلاین بهترین هتل‌ها.`,
    openGraph: {
      title: `هتل‌های ${city.name}`,
      description: `${hotelCount} هتل در ${city.name}، ${country.name}`,
      images: [city.image],
    },
  };
}

export default async function CityPage({ params }: Props) {
  const { country: countrySlug, city: citySlug } = await params;

  const country = hotelsData.countries.find((c) => c.slug === countrySlug);
  if (!country) notFound();

  const city = country.cities.find((c) => c.slug === citySlug);
  if (!city) notFound();

  const cityHotels = hotelsData.hotels.filter((h) => h.city === city.id);

  const data: HotelsData = {
    hotels: cityHotels,
  };

  return (
    <>
      <CategoryHero
        title={city.name}
        image={city.image}
        hotelCount={cityHotels.length}
        type="city"
        subtitle={`بهترین هتل‌های ${city.name} در ${country.name} برای اقامتی بی‌نظیر`}
      />
      <HotelsPage data={data} />
    </>
  );
}