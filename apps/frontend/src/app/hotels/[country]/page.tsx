// src/app/hotels/[country]/page.tsx
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import Image from "next/image";
import { MapPin, ChevronLeft, Building2 } from "lucide-react";
import hotelsData from "@/data/hotels.json";
import { faDigits } from "@/lib/api/hotels";
import HotelDetailPage from "@/components/hotels/single/HotelDetailPage";
import type { Hotel } from "@/types/hotel";

interface Props {
  params: Promise<{ country: string }>;
}

export async function generateStaticParams() {
  // پارامترهای کشورها
  const countryParams = hotelsData.countries.map((c) => ({
    country: c.slug,
  }));
  
  // پارامترهای هتل‌ها (slug کوتاه)
  const hotelParams = hotelsData.hotels.map((h) => ({
    country: h.slug,
  }));
  
  return [...countryParams, ...hotelParams];
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { country: slug } = await params;
  
  // اول country را چک کن
  const country = hotelsData.countries.find((c) => c.slug === slug);
  if (country) {
    const hotelCount = hotelsData.hotels.filter(
      (h) => h.country === country.id
    ).length;
    
    return {
      title: `هتل‌های ${country.name} | سفر بعدی ایرانیان`,
      description: `لیست کامل ${hotelCount} هتل در ${country.name} و شهرهای مختلف آن. رزرو آنلاین بهترین هتل‌ها.`,
      openGraph: { 
        title: `هتل‌های ${country.name}`,
        description: `${hotelCount} هتل در ${country.name}`,
        images: [country.image] 
      },
    };
  }
  
  // اگر country نبود، hotel را چک کن
  const hotel = hotelsData.hotels.find((h) => h.slug === slug);
  if (hotel) {
    const country = hotelsData.countries.find((c) => c.id === hotel.country);
    const city = country?.cities.find((c) => c.id === hotel.city);
    
    return {
      title: `${hotel.title} | ${city?.name || ""} ${country?.name || ""}`,
      description: hotel.description?.slice(0, 160),
      openGraph: { 
        title: hotel.title, 
        description: hotel.description?.slice(0, 160),
        images: [hotel.image] 
      },
    };
  }
  
  return { title: "یافت نشد" };
}

export default async function CountryOrHotelPage({ params }: Props) {
  const { country: slug } = await params;

  /* ═══════════════════════════════════════════
     مرحله ۱: بررسی country
  ═══════════════════════════════════════════ */
  const country = hotelsData.countries.find((c) => c.slug === slug);

  if (country) {
    const countryHotels = hotelsData.hotels.filter((h) => h.country === country.id);

    return (
      <div className="min-h-screen bg-white">
        {/* Hero کشور */}
        <section className="pt-5 md:pt-0">
          <div className="ns-container">
            <div className="relative w-full aspect-[16/9] md:aspect-[2172/650] rounded-2xl md:rounded-none overflow-hidden">
              <Image
                src={country.image}
                alt={`هتل‌های ${country.name}`}
                fill
                sizes="(max-width: 1280px) 100vw, 1280px"
                quality={85}
                priority
                className="object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-black/10" />

              <div className="absolute inset-0 flex flex-col justify-end p-5 md:p-8 lg:p-10">
                <div className="max-w-2xl">
                  <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-white text-[11px] md:text-xs font-bold mb-3">
                    <Building2 className="w-3.5 h-3.5" />
                    <span>کشور</span>
                  </div>

                  <h1 className="text-2xl md:text-4xl lg:text-5xl font-black text-white drop-shadow-lg mb-3">
                    {country.name}
                  </h1>

                  <div className="flex items-center gap-4 flex-wrap">
                    <div className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/15 backdrop-blur-md border border-white/20">
                      <Building2 className="w-4 h-4 text-white" />
                      <span className="text-white text-xs md:text-sm font-bold">
                        {faDigits(countryHotels.length)} هتل
                      </span>
                    </div>
                    <div className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/15 backdrop-blur-md border border-white/20">
                      <MapPin className="w-4 h-4 text-white" />
                      <span className="text-white text-xs md:text-sm font-bold">
                        {faDigits(country.cities.length)} شهر
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* شهرهای این کشور */}
        <section className="ns-container py-8 md:py-12">
          <div className="mb-6">
            <h2 className="text-xl md:text-2xl font-black text-text-strong mb-2">
              شهرهای {country.name}
            </h2>
            <p className="text-sm text-text-muted">
              شهر مورد نظر خود را انتخاب کنید
            </p>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
            {country.cities.map((city) => {
              const cityHotels = countryHotels.filter((h) => h.city === city.id);

              return (
                <Link
                  key={city.id}
                  href={`/hotels/${country.slug}/${city.slug}`}
                  className="group relative aspect-[4/5] rounded-[10px] md:rounded-lg overflow-hidden border border-border hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out"
                >
                  <Image
                    src={city.image}
                    alt={city.name}
                    fill
                    sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                    className="object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent" />

                  <div className="absolute top-3 right-3 flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-white text-[10px] font-bold">
                    <Building2 className="w-3 h-3" />
                    <span>{faDigits(cityHotels.length)} هتل</span>
                  </div>

                  <div className="absolute bottom-0 inset-x-0 p-4">
                    <h3 className="text-base md:text-lg font-black text-white mb-2 drop-shadow-lg">
                      {city.name}
                    </h3>
                    <div className="flex items-center justify-between">
                      <span className="text-[11px] text-white/80">مشاهده هتل‌ها</span>
                      <ChevronLeft className="w-4 h-4 text-white group-hover:text-primary-lightest group-hover:-translate-x-1 transition-all rtl:rotate-180" />
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>
        </section>
      </div>
    );
  }

  /* ═══════════════════════════════════════════
     مرحله ۲: بررسی hotel slug
  ═══════════════════════════════════════════ */
  const hotel = hotelsData.hotels.find((h) => h.slug === slug) as Hotel | undefined;

  if (hotel) {
    return <HotelDetailPage hotel={hotel} />;
  }

  /* ═══════════════════════════════════════════
     مرحله ۳: هیچکدام پیدا نشد
  ═══════════════════════════════════════════ */
  notFound();
}