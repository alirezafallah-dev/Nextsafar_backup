"use client";

import { useState, useRef, useEffect } from "react";
import Image from "next/image";
import { MapPin, ChevronDown, ChevronLeft, Globe } from "lucide-react";
import type { Country, City } from "@/types/hotel";

interface Props {
  countries: Country[];
  selectedCountry: string | null;
  selectedCity: string | null;
  onCountrySelect: (slug: string | null) => void;
  onCitySelect: (slug: string | null) => void;
}

export default function CountryCitySelector({
  countries,
  selectedCountry,
  selectedCity,
  onCountrySelect,
  onCitySelect,
}: Props) {
  const [open, setOpen] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  // بستن dropdown با کلیک خارج
  useEffect(() => {
    const handleClick = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    };
    document.addEventListener("mousedown", handleClick);
    return () => document.removeEventListener("mousedown", handleClick);
  }, []);

  const currentCountry = countries.find((c) => c.slug === selectedCountry);
  const cities = currentCountry?.cities || [];

  // عکس بزرگ (سمت راست)
  const heroImage = currentCountry?.image || countries[0]?.image;
  const heroName = currentCountry?.name || "همه مقاصد";

  return (
    <section className="rounded-2xl overflow-hidden border border-border bg-white shadow-card">
      <div className="grid lg:grid-cols-2 gap-0">
        {/* ═══ سمت راست: عکس بزرگ ═══ */}
        <div className="relative h-64 lg:h-[400px] overflow-hidden group">
          {heroImage && (
            <Image
              src={heroImage}
              alt={heroName}
              fill
              className="object-cover group-hover:scale-105 transition-transform duration-700"
              priority
            />
          )}
          {/* Overlay gradient */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
          
          {/* محتوای روی عکس */}
          <div className="absolute inset-0 p-6 flex flex-col justify-end">
            <div className="flex items-center gap-2 text-white/90 text-sm mb-2">
              <Globe className="w-4 h-4" />
              <span>مقصد انتخابی</span>
            </div>
            <h2 className="text-2xl lg:text-3xl font-extrabold text-white">
              {heroName}
            </h2>
            {currentCountry && (
              <p className="text-white/80 text-sm mt-2">
                {cities.length} شهر • {countries.find(c => c.slug === selectedCountry)?.cities.reduce((sum, city) => sum + 1, 0)} هتل
              </p>
            )}
          </div>

          {/* دکمه تغییر */}
          <button
            onClick={() => setOpen(!open)}
            className="absolute top-4 end-4 px-4 py-2 rounded-lg bg-white/95 backdrop-blur text-sm font-bold text-text-strong hover:bg-white transition cursor-pointer flex items-center gap-2"
          >
            <MapPin className="w-4 h-4 text-primary" />
            تغییر مقصد
            <ChevronDown className={`w-4 h-4 transition-transform ${open ? "rotate-180" : ""}`} />
          </button>
        </div>

        {/* ═══ سمت چپ: دسته‌بندی‌ها ═══ */}
        <div className="p-6 lg:p-8 flex flex-col justify-center">
          <h3 className="text-lg font-extrabold text-text-strong mb-4">
            مقصد خود را انتخاب کنید
          </h3>

          {/* ═══ لاین ۱: کشورها ═══ */}
          <div className="mb-6">
            <p className="text-xs font-bold text-text-muted uppercase tracking-wider mb-3">
              کشورها
            </p>
            <div className="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
              {countries.map((country) => (
                <button
                  key={country.id}
                  onClick={() => {
                    onCountrySelect(country.slug === selectedCountry ? null : country.slug);
                    onCitySelect(null); // ریست شهر
                  }}
                  className={`relative flex-shrink-0 group cursor-pointer ${
                    country.slug === selectedCountry
                      ? "ring-2 ring-primary ring-offset-2"
                      : ""
                  }`}
                >
                  <div className="relative w-28 h-20 rounded-xl overflow-hidden">
                    <Image
                      src={country.image}
                      alt={country.name}
                      fill
                      className="object-cover group-hover:scale-110 transition-transform duration-300"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                    <span className="absolute bottom-2 inset-x-2 text-center text-xs font-bold text-white">
                      {country.name}
                    </span>
                  </div>
                </button>
              ))}
            </div>
          </div>

          {/* ═══ لاین ۲: شهرها ═══ */}
          {currentCountry && cities.length > 0 && (
            <div className="animate-in fade-in slide-in-from-top-2 duration-300">
              <p className="text-xs font-bold text-text-muted uppercase tracking-wider mb-3">
                شهرهای {currentCountry.name}
              </p>
              <div className="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                {/* دکمه "همه" */}
                <button
                  onClick={() => onCitySelect(null)}
                  className={`relative flex-shrink-0 group cursor-pointer ${
                    selectedCity === null
                      ? "ring-2 ring-primary ring-offset-2"
                      : ""
                  }`}
                >
                  <div className="relative w-28 h-20 rounded-xl overflow-hidden bg-primary/10 flex items-center justify-center">
                    <MapPin className="w-8 h-8 text-primary" />
                    <div className="absolute inset-0 bg-gradient-to-t from-primary/20 to-transparent" />
                    <span className="absolute bottom-2 inset-x-2 text-center text-xs font-bold text-text-strong">
                      همه شهرها
                    </span>
                  </div>
                </button>
                
                {cities.map((city) => (
                  <button
                    key={city.id}
                    onClick={() => onCitySelect(city.slug === selectedCity ? null : city.slug)}
                    className={`relative flex-shrink-0 group cursor-pointer ${
                      city.slug === selectedCity
                        ? "ring-2 ring-primary ring-offset-2"
                        : ""
                    }`}
                  >
                    <div className="relative w-28 h-20 rounded-xl overflow-hidden">
                      <Image
                        src={city.image}
                        alt={city.name}
                        fill
                        className="object-cover group-hover:scale-110 transition-transform duration-300"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                      <span className="absolute bottom-2 inset-x-2 text-center text-xs font-bold text-white">
                        {city.name}
                      </span>
                    </div>
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* ═══ وضعیت انتخاب ═══ */}
          {(selectedCountry || selectedCity) && (
            <div className="mt-4 p-3 rounded-lg bg-primary-lightest border border-primary/20">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2 text-sm">
                  <MapPin className="w-4 h-4 text-primary" />
                  <span className="font-bold text-text-strong">
                    {currentCountry?.name}
                    {selectedCity && cities.find(c => c.slug === selectedCity) && (
                      <>
                        <ChevronLeft className="w-3 h-3 inline mx-1" />
                        {cities.find(c => c.slug === selectedCity)?.name}
                      </>
                    )}
                  </span>
                </div>
                <button
                  onClick={() => {
                    onCountrySelect(null);
                    onCitySelect(null);
                  }}
                  className="text-xs text-danger hover:text-danger-dark font-bold cursor-pointer"
                >
                  پاک کردن
                </button>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* ═══ Dropdown کامل (برای موبایل) ═══ */}
      {open && (
        <div className="absolute inset-x-0 top-full z-50 bg-white border-t border-border shadow-lg animate-in slide-in-from-top-2 duration-200 lg:hidden">
          <div className="p-4 max-h-[60vh] overflow-y-auto">
            {/* لیست کشورها */}
            {countries.map((country) => (
              <div key={country.id} className="mb-4">
                <p className="text-xs font-bold text-text-muted mb-2">{country.name}</p>
                <div className="flex gap-2 overflow-x-auto">
                  {country.cities.map((city) => (
                    <button
                      key={city.id}
                      onClick={() => {
                        onCountrySelect(country.slug);
                        onCitySelect(city.slug);
                        setOpen(false);
                      }}
                      className="flex-shrink-0 px-3 py-2 rounded-lg bg-bg-sec hover:bg-primary-lightest text-xs font-bold text-text-strong cursor-pointer"
                    >
                      {city.name}
                    </button>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </section>
  );
}