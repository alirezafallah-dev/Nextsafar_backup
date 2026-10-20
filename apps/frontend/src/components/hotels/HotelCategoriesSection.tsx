"use client";

import { useState, useMemo } from "react";
import Link from "next/link";
import Image from "next/image";
import { FolderOpen, ChevronLeft, MapPin, Play, Globe, Building2 } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";

/* ═══ Types ═══ */
export interface Region {
  id: number;
  name: string;
  slug: string;
  image: string;
}

export interface Country {
  id: number;
  regionId: number;
  name: string;
  slug: string;
  image: string;
}

export interface City {
  id: number;
  countryId: number;
  name: string;
  slug: string;
  image: string;
  count: number;
}

export interface HotelCategoriesSectionProps {
  title?: string;
  subtitle?: string;
  regions: Region[];
  countries: Country[];
  cities: City[];
  initialRegionId?: number;
  initialCountryId?: number;
  videoSrc: string;
  videoPoster?: string;
  basePath?: string;
  className?: string;
}

export default function HotelCategoriesSection({
  title = "دسته‌بندی هتل‌ها",
  subtitle = "منطقه، کشور و شهر مورد نظر خود را انتخاب کنید",
  regions,
  countries,
  cities,
  initialRegionId,
  initialCountryId,
  videoSrc,
  videoPoster,
  basePath = "/hotels/category",
  className = "",
}: HotelCategoriesSectionProps) {
  const [selectedRegion, setSelectedRegion] = useState<number>(
    initialRegionId ?? regions[0]?.id ?? 0
  );

  const firstCountryOfRegion = useMemo(
    () => countries.find((c) => c.regionId === selectedRegion),
    [countries, selectedRegion]
  );

  const [selectedCountry, setSelectedCountry] = useState<number>(
    initialCountryId ?? firstCountryOfRegion?.id ?? 0
  );

  const filteredCountries = useMemo(
    () => countries.filter((c) => c.regionId === selectedRegion),
    [countries, selectedRegion]
  );

  const filteredCities = useMemo(
    () => cities.filter((c) => c.countryId === selectedCountry),
    [cities, selectedCountry]
  );

  const handleRegionSelect = (region: Region) => {
    setSelectedRegion(region.id);
    const firstCountry = countries.find((c) => c.regionId === region.id);
    setSelectedCountry(firstCountry?.id ?? 0);
  };

  const handleCountrySelect = (country: Country) => {
    setSelectedCountry(country.id);
  };

  const selectedRegionName = regions.find((r) => r.id === selectedRegion)?.name ?? "";
  const selectedCountryName = countries.find((c) => c.id === selectedCountry)?.name ?? "";

  return (
    <section className={`ns-container py-8 md:py-12 ${className}`}>
      {/* ═══ عنوان ═══ */}
      <div className="mb-6 md:mb-8">
        <div className="flex items-center gap-2 mb-2">
          <FolderOpen className="w-5 h-5 md:w-6 md:h-6 text-primary flex-shrink-0" />
          <h2 className="text-xl md:text-2xl font-extrabold text-text-strong">
            {title}
          </h2>
        </div>
        <p className="text-sm md:text-base text-text-muted">{subtitle}</p>
      </div>

      {/* ═══ Layout اصلی ═══ */}
      <div className="flex flex-col lg:flex-row gap-5 lg:gap-6 lg:h-[710px]">
        {/* ═══ ویدیو - سمت راست (RTL) ═══ */}
        <div className="w-full lg:w-[340px] flex-shrink-0 order-2 lg:order-1">
          <div className="lg:sticky lg:top-24 lg:h-full">
            {/* 🔧 اصلاح: حذف h-full در موبایل و تنظیم ارتفاع مشخص */}
            <div className="relative w-full h-[280px] sm:h-[320px] lg:h-full lg:aspect-auto rounded-2xl overflow-hidden bg-bg-sec shadow-lg">
              <video
                src={videoSrc}
                poster={videoPoster}
                autoPlay
                muted
                loop
                playsInline
                controls
                preload="metadata"
                className="w-full h-full object-cover"
              >
                مرورگر شما از ویدیو پشتیبانی نمی‌کند.
              </video>

              {/* LIVE badge */}
              <div className="absolute top-3 start-3 flex items-center gap-1.5 px-2 py-1 rounded-md bg-black/60 backdrop-blur z-10 pointer-events-none">
                <div className="w-1.5 h-1.5 rounded-full bg-danger animate-pulse" />
                <span className="text-[10px] font-bold text-white">LIVE</span>
              </div>
            </div>
          </div>
        </div>

        {/* ═══ 3 لیست - سمت چپ ═══ */}
        <div className="flex-1 min-w-0 order-1 lg:order-2 flex flex-col gap-4 lg:gap-5">
          {/* ═══ لیست 1: منطقه (کوچک) ═══ */}
          <div className="lg:h-[170px] flex-shrink-0">
            <div className="flex items-center justify-between mb-3 gap-2">
              <div className="flex items-center gap-2 min-w-0">
                <Globe className="w-4 h-4 text-primary flex-shrink-0" />
                <h3 className="text-base font-extrabold text-text-strong truncate">
                  منطقه
                </h3>
              </div>
              <span className="text-xs text-text-muted font-bold flex-shrink-0">
                {faDigits(regions.length)} منطقه
              </span>
            </div>

            {/* 🔧 اصلاح: wrapper جداگانه برای mx منفی تا اسکرول از لبه شروع شود */}
            <div className="-mx-2 px-2">
              <div className="flex gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory">
                {regions.map((region) => (
                  <button
                    key={region.id}
                    onClick={() => handleRegionSelect(region)}
                    className={`relative w-[140px] aspect-[4/3] flex-shrink-0 rounded-xl overflow-visible transition-all duration-300 snap-start ${
                      selectedRegion === region.id
                        ? "ring-2 ring-primary shadow-lg"
                        : "hover:shadow-md"
                    }`}
                  >
                    <div className="relative w-full h-full rounded-xl overflow-hidden">
                      <Image
                        src={region.image}
                        alt={region.name}
                        fill
                        sizes="140px"
                        className="object-cover"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />

                      {selectedRegion === region.id && (
                        <div className="absolute top-2 end-2 w-5 h-5 rounded-full bg-primary flex items-center justify-center z-10">
                          <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                          </svg>
                        </div>
                      )}

                      <div className="absolute bottom-0 inset-x-0 p-2.5 text-white">
                        <h4 className="text-sm font-extrabold line-clamp-1">
                          {region.name}
                        </h4>
                      </div>
                    </div>
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* ═══ لیست 2: کشور (متوسط) ═══ */}
          <div className="lg:h-[200px] flex-shrink-0">
            <div className="flex items-center justify-between mb-3 gap-2">
              <div className="flex items-center gap-2 min-w-0">
                <Building2 className="w-4 h-4 text-primary flex-shrink-0" />
                <h3 className="text-base font-extrabold text-text-strong truncate">
                  کشور
                  {selectedRegionName && (
                    <span className="ms-2 text-xs font-bold text-text-muted">
                      در {selectedRegionName}
                    </span>
                  )}
                </h3>
              </div>
              <span className="text-xs text-text-muted font-bold flex-shrink-0">
                {faDigits(filteredCountries.length)} کشور
              </span>
            </div>

            {filteredCountries.length > 0 ? (
              <div className="-mx-2 px-2">
                <div className="flex gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory">
                  {filteredCountries.map((country) => (
                    <button
                      key={country.id}
                      onClick={() => handleCountrySelect(country)}
                      className={`relative w-[160px] aspect-[4/3] flex-shrink-0 rounded-xl overflow-visible transition-all duration-300 snap-start ${
                        selectedCountry === country.id
                          ? "ring-2 ring-primary shadow-lg"
                          : "hover:shadow-md"
                      }`}
                    >
                      <div className="relative w-full h-full rounded-xl overflow-hidden">
                        <Image
                          src={country.image}
                          alt={country.name}
                          fill
                          sizes="160px"
                          className="object-cover"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />

                        {selectedCountry === country.id && (
                          <div className="absolute top-2 end-2 w-5 h-5 rounded-full bg-primary flex items-center justify-center z-10">
                            <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                            </svg>
                          </div>
                        )}

                        <div className="absolute bottom-0 inset-x-0 p-2.5 text-white">
                          <h4 className="text-sm font-extrabold line-clamp-1">
                            {country.name}
                          </h4>
                        </div>
                      </div>
                    </button>
                  ))}
                </div>
              </div>
            ) : (
              <div className="p-4 rounded-xl bg-bg-sec border border-border text-center h-full flex items-center justify-center">
                <p className="text-xs text-text-muted">
                  کشوری برای این منطقه یافت نشد
                </p>
              </div>
            )}
          </div>

          {/* ═══ لیست 3: شهر (بزرگ - flex-1) ═══ */}
          <div className="flex-1 min-h-0 flex flex-col">
            <div className="flex items-center justify-between mb-3 gap-2">
              <div className="flex items-center gap-2 min-w-0">
                <MapPin className="w-4 h-4 text-primary flex-shrink-0" />
                <h3 className="text-base font-extrabold text-text-strong truncate">
                  شهر
                  {selectedCountryName && (
                    <span className="ms-2 text-xs font-bold text-text-muted">
                      در {selectedCountryName}
                    </span>
                  )}
                </h3>
              </div>
              <span className="text-xs text-text-muted font-bold flex-shrink-0">
                {faDigits(filteredCities.length)} شهر
              </span>
            </div>

            {filteredCities.length > 0 ? (
              <div className="-mx-2 px-2 flex-1 min-h-0">
                <div className="flex gap-3 md:gap-4 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory h-full">
                  {filteredCities.map((city) => (
                    <Link
                      key={city.id}
                      href={`${basePath}/${city.slug}`}
                      className="group relative w-[200px] md:w-[220px] h-[250px] flex-shrink-0 rounded-xl overflow-visible hover:-translate-y-1 transition-all duration-300 snap-start"
                    >
                      <div className="relative w-full h-full rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <Image
                          src={city.image}
                          alt={city.name}
                          fill
                          sizes="220px"
                          className="object-cover group-hover:scale-110 transition-transform duration-500"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />

                        <div className="absolute bottom-0 inset-x-0 p-3 text-white">
                          <h4 className="text-sm md:text-base font-extrabold mb-1 line-clamp-1 group-hover:text-primary-lightest transition-colors">
                            {city.name}
                          </h4>
                          <div className="flex items-center justify-between">
                            <div className="flex items-center gap-1 text-[10px] md:text-xs text-white/80">
                              <MapPin className="w-3 h-3" />
                              <span>{faDigits(city.count)} هتل</span>
                            </div>
                            <ChevronLeft className="w-4 h-4 text-white/80 group-hover:text-white group-hover:-translate-x-1 transition-all rtl:rotate-180" />
                          </div>
                        </div>
                      </div>
                    </Link>
                  ))}
                </div>
              </div>
            ) : (
              <div className="flex-1 flex items-center justify-center p-8 rounded-xl bg-bg-sec border border-border">
                <div className="text-center">
                  <MapPin className="w-10 h-10 text-text-subtle mx-auto mb-2" />
                  <p className="text-sm text-text-muted">
                    شهری برای این کشور یافت نشد
                  </p>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </section>
  );
}