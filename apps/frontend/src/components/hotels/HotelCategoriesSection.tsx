"use client";

import { useState, useMemo, useRef, useEffect, useCallback } from "react";
import Link from "next/link";
import Image from "next/image";
import { FolderOpen, ChevronLeft, ChevronRight, MapPin, Globe, Building2 } from "lucide-react";
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

/* ═══ کاروسل با دکمه‌های هوشمند ═══ */
function Carousel({
  children,
  scrollerClassName = "",
}: {
  children: React.ReactNode;
  scrollerClassName?: string;
}) {
  const ref = useRef<HTMLDivElement>(null);
  const [canPrev, setCanPrev] = useState(false);
  const [canNext, setCanNext] = useState(false);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const update = useCallback(() => {
    const el = ref.current;
    if (!el) return;
    const max = el.scrollWidth - el.clientWidth;
    const pos = Math.abs(el.scrollLeft);
    setCanPrev(pos > 8);
    setCanNext(max - pos > 8);
  }, []);

  useEffect(() => {
    update();
    const el = ref.current;
    if (!el) return;
    el.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    return () => {
      el.removeEventListener("scroll", update);
      window.removeEventListener("resize", update);
    };
  }, [update]);

  const scroll = (dir: 1 | -1) => {
    const el = ref.current;
    if (!el) return;
    const isRTL = document.documentElement.dir === "rtl";
    const delta = isRTL ? -dir : dir;
    const amount = el.clientWidth * 0.98;
    el.scrollBy({ left: delta * amount, behavior: "smooth" });
  };

  return (
    <div className="relative">
      {mounted && canPrev && (
        <button
          onClick={() => scroll(-1)}
          aria-label="قبلی"
          className="hidden md:flex absolute start-1 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/95 backdrop-blur shadow-lg border border-border items-center justify-center text-text hover:text-primary hover:scale-110 transition"
        >
          <ChevronRight className="w-5 h-5" />
        </button>
      )}

      <div ref={ref} className={scrollerClassName}>
        {children}
      </div>

      {mounted && canNext && (
        <button
          onClick={() => scroll(1)}
          aria-label="بعدی"
          className="hidden md:flex absolute end-1 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/95 backdrop-blur shadow-lg border border-border items-center justify-center text-text hover:text-primary hover:scale-110 transition"
        >
          <ChevronLeft className="w-5 h-5" />
        </button>
      )}
    </div>
  );
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
        <div className="flex items-center gap-2 mb-0">
          <FolderOpen className="w-5 h-5 md:w-6 md:h-6 text-primary flex-shrink-0" />
          <h2 className="text-xl md:text-2xl font-extrabold text-text-strong">
            {title}
          </h2>
        </div>
        <p className="text-sm md:text-base text-text-muted mt-2">{subtitle}</p>
      </div>

      {/* ═══ Layout اصلی ═══ */}
      <div className="flex flex-col lg:flex-row gap-5 lg:gap-6 lg:h-[635px]">
        {/* ═══ ویدیو ═══ */}
        <div className="w-full lg:w-[340px] flex-shrink-0 order-2 lg:order-1">
          <div className="lg:sticky lg:top-24 lg:h-full">
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
              <div className="absolute top-3 start-3 flex items-center gap-1.5 px-2 py-1 rounded-md bg-black/60 backdrop-blur z-10 pointer-events-none">
                <div className="w-1.5 h-1.5 rounded-full bg-danger animate-pulse" />
                <span className="text-[10px] font-bold text-white">LIVE</span>
              </div>
            </div>
          </div>
        </div>

        {/* ═══ 3 لیست ═══ */}
        <div className="flex-1 min-w-0 order-1 lg:order-2 flex flex-col gap-4 lg:gap-5">
          {/* ═══ منطقه — کوچک و جمع‌وجور ═══ */}
          <div className="flex-shrink-0">
            <div className="flex items-center justify-between mb-0 gap-2">
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

            <div className="mt-3">
              <Carousel
                scrollerClassName="flex gap-2 md:gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory"
              >
                {regions.map((region) => (
                  <button
                    key={region.id}
                    onClick={() => handleRegionSelect(region)}
                    className={`relative w-[90px] md:w-[100px] aspect-square flex-shrink-0 rounded-xl overflow-hidden transition-all duration-300 snap-start ${
                      selectedRegion === region.id
                        ? "ring-2 ring-primary shadow-lg"
                        : "hover:shadow-md"
                    }`}
                  >
                    <Image
                      src={region.image}
                      alt={region.name}
                      fill
                      sizes="100px"
                      className="object-cover"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                    {selectedRegion === region.id && (
                      <div className="absolute top-1.5 end-1.5 w-4 h-4 rounded-full bg-primary flex items-center justify-center z-10">
                        <svg className="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                    )}
                    <div className="absolute bottom-0 inset-x-0 p-1.5 text-white">
                      <h4 className="text-[11px] md:text-xs font-extrabold line-clamp-1">
                        {region.name}
                      </h4>
                    </div>
                  </button>
                ))}
              </Carousel>
            </div>
          </div>

          {/* ═══ کشور — کوچک و جمع‌وجور ═══ */}
          <div className="flex-shrink-0">
            <div className="flex items-center justify-between mb-0 gap-2">
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

            <div className="mt-3">
              {filteredCountries.length > 0 ? (
                <Carousel
                  scrollerClassName="flex gap-2 md:gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory"
                >
                  {filteredCountries.map((country) => (
                    <button
                      key={country.id}
                      onClick={() => handleCountrySelect(country)}
                      className={`relative w-[100px] md:w-[120px] aspect-square flex-shrink-0 rounded-xl overflow-hidden transition-all duration-300 snap-start ${
                        selectedCountry === country.id
                          ? "ring-2 ring-primary shadow-lg"
                          : "hover:shadow-md"
                      }`}
                    >
                      <Image
                        src={country.image}
                        alt={country.name}
                        fill
                        sizes="120px"
                        className="object-cover"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
                      {selectedCountry === country.id && (
                        <div className="absolute top-1.5 end-1.5 w-4 h-4 rounded-full bg-primary flex items-center justify-center z-10">
                          <svg className="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                          </svg>
                        </div>
                      )}
                      <div className="absolute bottom-0 inset-x-0 p-1.5 text-white">
                        <h4 className="text-[11px] md:text-xs font-extrabold line-clamp-1">
                          {country.name}
                        </h4>
                      </div>
                    </button>
                  ))}
                </Carousel>
              ) : (
                <div className="p-4 rounded-xl bg-bg-sec border border-border text-center flex items-center justify-center h-[100px]">
                  <p className="text-xs text-text-muted">
                    کشوری برای این منطقه یافت نشد
                  </p>
                </div>
              )}
            </div>
          </div>

          {/* ═══ شهر — دقیقاً 4 تایی در دسکتاپ ═══ */}
          <div className="flex-1 min-h-0 flex flex-col">
            <div className="flex items-center justify-between mb-0 gap-2">
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

            <div className="mt-3 flex-1 min-h-0">
              {filteredCities.length > 0 ? (
                <Carousel
                  scrollerClassName="flex gap-3 md:gap-4 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory"
                >
                  {filteredCities.map((city) => (
                    <Link
                      key={city.id}
                      href={`${basePath}/${city.slug}`}
                      className="group relative w-[200px] md:w-[calc(25%-0.75rem)] aspect-[4/5] md:aspect-auto md:h-[250px] flex-shrink-0 rounded-xl overflow-hidden hover:-translate-y-1 transition-all duration-300 snap-start"
                    >
                      <Image
                        src={city.image}
                        alt={city.name}
                        fill
                        sizes="(max-width: 768px) 200px, 25vw"
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
                    </Link>
                  ))}
                </Carousel>
              ) : (
                <div className="flex items-center justify-center p-8 rounded-xl bg-bg-sec border border-border h-full">
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
      </div>
    </section>
  );
}