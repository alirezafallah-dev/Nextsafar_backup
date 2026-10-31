// src/components/hotels/single/HotelAmenities.tsx
"use client";

import { useState } from "react";
import { X } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import { faDigits } from "@/lib/api/hotels";
import { AMENITY_CATEGORIES, TOP_AMENITIES } from "@/lib/hotel-amenities";
import ModalCloseButton from "@/components/shared/common/ModalCloseButton";

/* ═══ نگاشت کلیدهای قدیمی داده → کلیدهای metabox ═══ */
const LEGACY_KEY_MAP: Record<string, string> = {
  wifi: "hotel_wifi",
  free_wi_fi: "hotel_wifi",
  pool: "hotel_outdoor_pool",
  pools: "hotel_outdoor_pool",
  parking: "hotel_free_parking",
  free_parking: "hotel_free_parking",
  restaurant: "hotel_restaurant",
  gym: "hotel_fitness_center",
  fitness_center: "hotel_fitness_center",
  spa: "hotel_spa",
  beach: "hotel_Private_beach_area",
  bar: "hotel_bar",
  airport_transfer: "hotel_airport_shuttle_service",
  airport_shuttle: "hotel_airport_shuttle_service",
  breakfast: "hotel_free_breakfast",
  kid_friendly: "hotel_family_rooms",
  kids_club: "hotel_kids_club",
  pet_friendly: "hotel_pet_friendly",
  accessible: "hotel_Wheelchair_accessible",
  air_conditioning: "hotel_air_conditioning",
  room_service: "hotel_room_servies",
  hot_tub: "hotel_Hot_tub",
  water_park: "hotel_water_park",
};

/* ═══ داده‌های تستی — برای مشاهده کامل مودال ═══ */
const TEST_AMENITIES = [
  // حمام
  "hotel_shower", "hotel_Towels", "hotel_Hairdryer", "hotel_private_bathroom",
  "hotel_Free_toiletries", "hotel_Bathtub", "hotel_Slippers",
  
  // اتاق
  "hotel_wardrobe_closet", "hotel_Linens", "hotel_safe_box",
  "hotel_Socket_near_bed", "hotel_room_servies", "hotel_room_sofa",
  
  // فضای باز
  "hotel_Private_beach_area", "hotel_garden", "hotel_terrace",
  "hotel_Sun_terrace", "hotel_outdoor_furniture",
  
  // آشپزخانه
  "hotel_Electric_kettle", "hotel_coffee_tea_maker", "hotel_Refrigerator",
  
  // فعالیت‌ها
  "hotel_water_park", "hotel_kids_club", "hotel_tennis_court",
  "hotel_live_music", "hotel_playground", "hotel_game_room",
  "hotel_golf_course", "hotel_Cycling", "hotel_bicycle_rental",
  
  // رسانه
  "hotel_wifi", "hotel_flatscreen_tv", "hotel_Satellite_channels",
  "hotel_room_telephone",
  
  // رفاهی
  "hotel_fitness_center", "hotel_gift_shop", "hotel_multilingual_staff",
  "hotel_tour_ticket_assistant",
  
  // غذا و نوشیدنی
  "hotel_restaurant", "hotel_bar", "hotel_free_breakfast",
  "hotel_minibar", "hotel_snack_bar", "hotel_Kids_meals",
  
  // پذیرش
  "hotel_24_hour_reception", "hotel_concierge_service",
  "hotel_luggage_storage", "hotel_early_checkin", "hotel_late_checkout",
  
  // نظافت
  "hotel_cleaning_daily_service", "hotel_laundry", "hotel_Ironing_service",
  
  // تجاری
  "hotel_business_center", "hotel_Meeting_Banquet_facilities",
  
  // ایمنی
  "hotel_CCTV_outside_property", "hotel_24_hour_security",
  "hotel_Fire_extinguishers", "hotel_Smoke_alarms", "hotel_Key_card_access",
  
  // استخر
  "hotel_outdoor_pool", "hotel_Indoor_pool", "hotel_heated_pool",
  "hotel_Pool_with_view", "hotel_Pool_bar", "hotel_Beach_chairs_Loungers",
  
  // پارکینگ
  "hotel_free_parking", "hotel_valet_parking",
  
  // حمل و نقل
  "hotel_airport_shuttle_service", "hotel_shuttle_service", "hotel_car_rental",
  
  // عمومی
  "hotel_air_conditioning", "hotel_heating", "hotel_elevator",
  "hotel_soundproof_rooms", "hotel_Shared_lounge_TV_area",
  
  // اسپا
  "hotel_spa", "hotel_sauna", "hotel_jacuzzi", "hotel_Hot_tub",
  "hotel_Steam_room", "hotel_yoga_classes", "hotel_body_treatments",
  
  // انواع اتاق
  "hotel_city_view", "hotel_pool_view", "hotel_family_rooms",
  "hotel_non_smoking_rooms", "hotel_suites",
  
  // زبان
  "hotel_english_staff", "hotel_arabic_staff", "hotel_turkish_staff",
  "hotel_russian_staff", "hotel_germani_staff",
  
  // دسترسی
  "hotel_Wheelchair_accessible", "hotel_Upper_floors_elevator",
];

interface Props {
  hotel: Hotel;
  /** استفاده از داده تستی برای تست UI */
  useTestData?: boolean;
}

export default function HotelAmenities({ hotel, useTestData = true }: Props) {
  const [showAllAmenities, setShowAllAmenities] = useState(false);

  /* ✅ normalize کلیدهای قدیمی به کلیدهای metabox */
  const normalizedAmenities = (hotel.amenities || []).map(
    (k) => LEGACY_KEY_MAP[k] ?? k
  );

  /* ✅ حالت تست: override کامل با داده تستی */
  const hotelAmenities = useTestData ? TEST_AMENITIES : normalizedAmenities;

  // فیلتر دسته‌های دارای امکانات فعال
  const activeCategories = AMENITY_CATEGORIES.map((category) => ({
    ...category,
    amenities: category.amenities.filter((amenity) =>
      hotelAmenities.includes(amenity.key)
    ),
  })).filter((category) => category.amenities.length > 0);

  const totalActiveAmenities = activeCategories.reduce(
    (sum, cat) => sum + cat.amenities.length,
    0
  );

  return (
    <section id="amenities">
      <div className="flex items-center justify-between gap-3 mb-5">
        <div className="min-w-0">
          <h2 className="ns-section-title !mb-1">امکانات و خدمات</h2>
          <p className="text-xs md:text-sm text-text-muted">
            امکاناتی که این هتل در اختیار شما قرار می‌دهد
          </p>
        </div>
      </div>

      <div className="rounded-[10px] md:rounded-lg border border-border bg-white p-5 md:p-6">
        {/* 8 امکان برتر */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
          {TOP_AMENITIES.map((amenity, i) => {
            const Icon = amenity.icon;
            const isActive = hotelAmenities.includes(amenity.key);

            return (
              <div
                key={i}
                className={`flex flex-col items-center gap-2 p-3 md:p-4 rounded-[10px] md:rounded-lg border transition-all duration-500 ease-out ${
                  isActive
                    ? "border-primary-light bg-primary/10"
                    : "border-border bg-white hover:border-primary-light hover:shadow-card-hover"
                }`}
              >

                <div
                  className={`w-10 h-10 md:w-11 md:h-11 rounded-md flex items-center justify-center transition-colors ${
                    isActive
                      ? "bg-primary text-white shadow-md shadow-primary/30"
                      : "bg-white border border-border shadow-sm"
                  }`}
                >
                  <Icon
                    className={`w-5 h-5 md:w-6 md:h-6 ${
                      isActive ? "text-white" : "text-primary"
                    }`}
                  />
                </div>
                <span
                  className={`text-[11px] md:text-xs font-bold text-center leading-4 ${
                    isActive ? "text-text-strong" : "text-text"
                  }`}
                >
                  {amenity.label}
                </span>
              </div>
            );
          })}
        </div>

        <button
          onClick={() => setShowAllAmenities(true)}
          className="mt-4 w-full py-2.5 rounded-lg border border-primary/30 bg-primary-lightest/30 text-primary text-xs md:text-sm font-bold hover:bg-primary-lightest/60 hover:border-primary transition"
        >
          مشاهده تمام امکانات ({faDigits(totalActiveAmenities)})
        </button>
      </div>

      {/* ═══ مودال همه امکانات ═══ */}
      {showAllAmenities && (
        <div
          className="fixed inset-0 z-[9999] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 animate-ns-fade"
          onClick={() => setShowAllAmenities(false)}
          role="dialog"
          aria-modal="true"
        >
          <div
            className="bg-white rounded-[10px] md:rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl border border-border"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Header */}
            <div className="flex items-center justify-between px-5 md:px-6 py-4 border-b border-border bg-bg-sec shrink-0">
              <div>
                <h3 className="text-sm md:text-base font-extrabold text-text-strong">
                  تمام امکانات و خدمات هتل
                </h3>
                <p className="text-xs text-text-muted mt-0.5">
                  {faDigits(totalActiveAmenities)} امکانات فعال در{" "}
                  {faDigits(activeCategories.length)} دسته
                </p>
              </div>
              <ModalCloseButton onClick={() => setShowAllAmenities(false)} />
            </div>

            {/* Body - اسکرول‌شونده با دسته‌بندی */}
            <div className="overflow-y-auto p-5 md:p-6 space-y-6">
              {activeCategories.map((category) => {
                const CategoryIcon = category.icon;
                return (
                  <div key={category.id}>
                    {/* عنوان دسته */}
                    <div className="flex items-center gap-2 mb-3 pb-2 border-b border-border/50">
                      <div className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                        <CategoryIcon className="w-4 h-4 text-primary" />
                      </div>
                      <h4 className="text-sm md:text-base font-extrabold text-text-strong">
                        {category.title}
                      </h4>
                      <span className="text-xs text-text-muted font-bold">
                        ({faDigits(category.amenities.length)} مورد)
                      </span>
                    </div>

                    {/* آیتم‌های دسته */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                      {category.amenities.map((amenity, i) => {
                        const AmenityIcon = amenity.icon;
                        return (
                          <div
                            key={i}
                            className="flex items-center gap-2.5 p-3 rounded-lg bg-bg-sec/50 border border-border/40 hover:border-primary/30 hover:bg-primary/5 transition"
                          >
                            <div className="w-8 h-8 rounded-md bg-white border border-border flex items-center justify-center shrink-0">
                              <AmenityIcon className="w-4 h-4 text-primary" />
                            </div>
                            <span className="text-xs md:text-sm font-bold text-text leading-4">
                              {amenity.label}
                            </span>
                          </div>
                        );
                      })}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      )}
    </section>
  );
}