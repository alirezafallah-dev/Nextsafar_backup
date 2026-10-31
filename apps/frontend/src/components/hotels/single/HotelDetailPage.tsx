// src/components/hotels/single/HotelDetailPage.tsx
"use client";

import Link from "next/link";
import { ArrowLeft } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import HotelHeader from "./HotelHeader";
import HotelHeroGallery from "./HotelHeroGallery";
import BookingSidebar from "./BookingSidebar";
import HotelAbout from "./HotelAbout";
import HotelAmenities from "./HotelAmenities";
import HotelRestaurants from "./HotelRestaurants";
import HotelNearbyPlaces from "./HotelNearbyPlaces";
import HotelReviewsSection from "./HotelReviewsSection";
import HotelPolicies from "./HotelPolicies";
import FAQSection from "@/components/shared/sections/FAQSection";
import type { FAQItem } from "@/components/shared/sections/FAQSection";
import SimilarHotels from "./SimilarHotels";
import { WeatherWidget } from "@/components/shared/weather";
import hotelsData from "@/data/hotels.json";

const TABS = [
  { id: "summary", label: "خلاصه" },
  { id: "amenities", label: "امکانات" },
  { id: "restaurants", label: "رستوران‌ها" },
  { id: "nearby", label: "موقعیت" },
  { id: "reviews", label: "نظرات" },
  { id: "policies", label: "قوانین" },
];

const HOTEL_FAQ_ITEMS: FAQItem[] = [
  { q: "ساعت ورود و خروج هتل چیست؟", a: "ورود از ساعت ۱۴:۰۰ و خروج تا ساعت ۱۲:۰۰ روز بعد امکان‌پذیر است." },
  { q: "آیا امکان رزرو بدون پیش‌پرداخت وجود دارد؟", a: "بله، برای برخی اتاق‌ها رزرو با پرداخت در محل نیز فراهم است." },
  { q: "آیا هتل ترانسفر فرودگاهی ارائه می‌دهد؟", a: "بله، با هماهنگی قبلی (حداقل ۲۴ ساعت) ترانسفر رفت و برگشت ارائه می‌شود." },
  { q: "آیا هتل برای کودکان مناسب است؟", a: "بله، هتل دارای باشگاه کودکان، زمین بازی و منوی مخصوص کودکان است." },
  { q: "آیا ساحل اختصاصی دارد؟", a: "بله، هتل دارای ساحل اختصاصی با سرویس شاتل رایگان است." },
  { q: "نوع سرویس هتل چیست؟", a: "هتل با سرویس UALL (Ultra All Inclusive) فعالیت می‌کند." },
];

interface Props { hotel: Hotel }

export default function HotelDetailPage({ hotel }: Props) {
  const similarHotels = (hotelsData.hotels as Hotel[])
    .filter(h => h.id !== hotel.id && (h.city === hotel.city || h.country === hotel.country))
    .slice(0, 3);

  return (
    <div className="min-h-screen bg-white">
      <HotelHeader hotel={hotel} />

      <div className="ns-container pb-8 md:pb-12">
        <div className="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6 items-start">

          {/* ═══ MAIN COLUMN ═══ */}
          <div className="min-w-0">

            {/* گالری */}
            <HotelHeroGallery
              images={hotel.gallery?.length ? hotel.gallery : [hotel.image]}
              title={hotel.title}
            />

            {/* ═══ Container برای تب‌ها و محتوا (تا Policies) ═══ */}
            <div className="space-y-8 md:space-y-12">

              {/* تب‌ها — sticky در این container */}
              <nav className="sticky top-[135px] z-30 bg-white/95 backdrop-blur -mx-4 md:-mx-6 px-4 md:px-6">
                <div className="flex items-center justify-center border-b border-border gap-4 md:gap-8 overflow-x-auto scrollbar-hide ns-tablist-scroll">
                  {TABS.map((t, i) => (
                    <a
                      key={t.id}
                      href={`#${t.id}`}
                      className={`py-2.5 md:py-3 text-xs md:text-sm font-bold whitespace-nowrap border-b-2 transition ${
                        i === 0
                          ? "border-primary text-primary"
                          : "border-transparent text-text-muted hover:text-primary"
                      }`}
                    >
                      {t.label}
                    </a>
                  ))}
                </div>
              </nav>

              <div id="summary" />
              <HotelAbout hotel={hotel} />
              <HotelAmenities hotel={hotel} />
              <HotelRestaurants />
              <HotelNearbyPlaces hotel={hotel} />
              <HotelReviewsSection hotel={hotel} />
              <HotelPolicies />
            </div>

            {/* ═══ آب و هوا — فاصله مناسب از Policies ═══ */}
            <section className="mt-8 md:mt-12">
              <WeatherWidget
                lat={hotel.coordinates.lat}
                lng={hotel.coordinates.lng}
                cityName={hotel.city}
                showForecast
              />
            </section>

            {/* ═══ بخش FAQ — خارج از container sticky ═══ */}
            <div className="mt-8 md:mt-12">
              <section>
                <div className="flex items-center justify-between gap-3 mb-5 md:mb-6">
                  <div className="min-w-0">
                    <h2 className="ns-section-title !mb-1">سؤالات متداول</h2>
                    <p className="text-xs md:text-sm text-text-muted">
                      پاسخ به رایج‌ترین پرسش‌های مسافران این هتل
                    </p>
                  </div>
                </div>

                <FAQSection
                  items={HOTEL_FAQ_ITEMS}
                  supportText="سوال دیگه‌ای دارید؟ تیم پشتیبانی ما ۲۴ ساعته آماده پاسخگویی است"
                  supportPhone="+982191001234"
                  showNumbers
                />
              </section>
            </div>

            {/* ═══ هتل‌های مشابه ═══ */}
            {similarHotels.length > 0 && (
              <section className="mt-8 md:mt-12">
                <div className="flex items-center justify-between gap-3 mb-5 md:mb-6">
                  <div className="min-w-0">
                    <h2 className="ns-section-title !mb-1">هتل‌های مشابه</h2>
                    <p className="text-xs md:text-sm text-text-muted">
                      شاید این هتل‌ها هم برای شما جذاب باشد
                    </p>
                  </div>
                  <Link
                    href="/hotels"
                    className="flex items-center gap-1.5 shrink-0 whitespace-nowrap px-3 py-1.5 md:px-4 md:py-2 rounded-sm border border-border bg-white text-[11px] md:text-xs font-bold text-text-muted hover:border-primary hover:text-primary-dark hover:bg-primary-lightest/30 transition"
                  >
                    مشاهده همه
                    <ArrowLeft className="w-3.5 h-3.5 shrink-0" />
                  </Link>
                </div>
                <SimilarHotels hotels={similarHotels} showHeader={false} />
              </section>
            )}
          </div>

          {/* ═══ SIDEBAR ═══ */}
          <BookingSidebar hotel={hotel} />

        </div>
      </div>
    </div>
  );
}