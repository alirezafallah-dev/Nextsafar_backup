// src/components/hotels/single/HotelNearbyPlaces.tsx
"use client";

import { useState } from "react";
import { MapPin, Plane, Landmark, Navigation, Coffee, ShoppingBag, Map as MapIcon } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import type { ListingMapPoint, PlaceType } from "@/components/shared/map/types";
import ListingMap from "@/components/shared/map/ListingMap";
import MapModal from "@/components/shared/map/MapModal";
import { faDigits } from "@/lib/api/hotels";

const ICON_MAP: Record<PlaceType, any> = {
  hotel: MapPin,
  airport: Plane,
  attraction: Landmark,
  destination: MapPin,
  restaurant: Coffee,
  hospital: Landmark,
  center: ShoppingBag,
};

const COLOR_MAP: Record<PlaceType, string> = {
  hotel: "bg-primary/10 text-primary",
  airport: "bg-warning/10 text-warning",
  attraction: "bg-success/10 text-success",
  destination: "bg-info/10 text-info",
  restaurant: "bg-danger/10 text-danger",
  hospital: "bg-danger/10 text-danger",
  center: "bg-text-muted/10 text-text-muted",
};

const TYPE_LABELS: Record<PlaceType, string> = {
  hotel: "هتل",
  airport: "فرودگاه",
  attraction: "جاذبه",
  destination: "مقصد",
  restaurant: "رستوران",
  hospital: "بیمارستان",
  center: "مرکز خرید",
};

interface Props { hotel: Hotel }

export default function HotelNearbyPlaces({ hotel }: Props) {
  const [activeKey, setActiveKey] = useState<string>(hotel.slug);
  const [openModal, setOpenModal] = useState(false);

  const places: ListingMapPoint[] = [
    {
      key: hotel.slug,
      lat: hotel.coordinates.lat,
      lng: hotel.coordinates.lng,
      title: hotel.title,
      type: "hotel",
      image: hotel.image,
      stars: hotel.stars,
      rating: hotel.rating,
      address: hotel.address,
    },
    { key: "belek-beach", lat: hotel.coordinates.lat + 0.05, lng: hotel.coordinates.lng - 0.08, title: "ساحل بلک", type: "attraction", address: "۱۲ کیلومتر" },
    { key: "mall-of-antalya", lat: hotel.coordinates.lat + 0.08, lng: hotel.coordinates.lng - 0.25, title: "مرکز خرید مال آف آنتالیا", type: "center", address: "۱۵ کیلومتر" },
    { key: "land-of-legends", lat: hotel.coordinates.lat - 0.12, lng: hotel.coordinates.lng + 0.22, title: "پارک لند آو لجندز", type: "attraction", address: "۲۰ کیلومتر" },
    { key: "antalya-airport", lat: hotel.coordinates.lat + 0.25, lng: hotel.coordinates.lng + 0.18, title: "فرودگاه آنتالیا", type: "airport", address: "۳۵ کیلومتر" },
    { key: "kaleici-old-town", lat: hotel.coordinates.lat - 0.3, lng: hotel.coordinates.lng - 0.1, title: "شهر قدیمی کالچی", type: "attraction", address: "۴۰ کیلومتر" },
  ];

  return (
    <section id="nearby">
      {/* Section Header هم‌استایل صفحه اصلی */}
      <div className="flex items-center justify-between gap-3 mb-5 md:mb-6">
        <div className="min-w-0">
          <h2 className="ns-section-title !mb-1">موقعیت و جاذبه‌های اطراف</h2>
          <p className="text-xs md:text-sm text-text-muted">
            مکان‌های نزدیک به هتل و فاصله آن‌ها
          </p>
        </div>
        <span className="flex items-center gap-1.5 shrink-0 px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-[11px] md:text-xs font-bold whitespace-nowrap">
          <MapPin className="w-3.5 h-3.5" />
          {faDigits(places.length)} مکان
        </span>
      </div>

      {/* باکس با radius هم‌استایل کارت‌های اصلی */}
      <div className="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-4 rounded-[10px] md:rounded-lg border border-border bg-white p-4 md:p-5">

        {/* لیست مکان‌ها */}
        <div className="rounded-lg bg-bg-sec/50 p-3">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-[320px] overflow-y-auto pe-1">
            {places.map((place) => {
              const Icon = ICON_MAP[place.type] || MapPin;
              const color = COLOR_MAP[place.type] || "bg-primary/10 text-primary";
              const isActive = place.key === activeKey;

              return (
                <button
                  key={place.key}
                  onClick={() => setActiveKey(place.key)}
                  className={`flex items-start gap-2.5 p-2.5 rounded-[10px] md:rounded-lg border text-start transition-all duration-500 ease-out ${
                    isActive
                      ? "border-primary-light bg-white shadow-card-hover ring-2 ring-primary/15"
                      : "border-border bg-white hover:border-primary-light hover:shadow-card-hover"
                  }`}
                >
                  <div className={`w-9 h-9 rounded-lg flex items-center justify-center shrink-0 ${color}`}>
                    <Icon className="w-4 h-4" />
                  </div>
                  <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-1.5">
                      <div className="text-[11px] md:text-xs font-bold text-text-strong line-clamp-1">
                        {place.title}
                      </div>
                      {place.type === "hotel" && (
                        <span className="px-1.5 py-0.5 rounded-md bg-primary/10 text-primary text-[8px] font-black shrink-0">
                          هتل فعلی
                        </span>
                      )}
                    </div>
                    <div className="flex items-center gap-1 mt-1 text-[10px] md:text-[11px] text-text-muted">
                      {place.type !== "hotel" && place.address && (
                        <>
                          <Navigation className="w-3 h-3 shrink-0 text-primary" />
                          <span>{place.address}</span>
                          <span className="text-text-subtle">•</span>
                        </>
                      )}
                      <span>{TYPE_LABELS[place.type]}</span>
                    </div>
                  </div>
                </button>
              );
            })}
          </div>
        </div>

        {/* نقشه */}
        <div className="relative isolate z-0 h-[220px] sm:h-[260px] lg:h-[320px] rounded-[10px] md:rounded-lg overflow-hidden border border-border/60">
          <ListingMap
            points={places}
            activeKey={activeKey}
            height={320}
            onPointClick={setActiveKey}
            showFullscreenButton={false}
            className="rounded-lg"
          />

          <button
            onClick={() => setOpenModal(true)}
            className="absolute bottom-3 left-1/2 -translate-x-1/2 z-[500]
                      inline-flex items-center gap-1.5
                      px-4 py-2 rounded-[10px] md:rounded-lg
                      bg-white border border-border/60
                      text-[11px] md:text-xs font-bold text-text-strong
                      hover:border-primary hover:text-primary
                      shadow-md hover:shadow-lg
                      transition-all duration-200"
          >
            <MapIcon className="w-3.5 h-3.5 text-primary" />
            نقشه اطراف هتل
          </button>
        </div>
      </div>

      {openModal && (
        <MapModal
          isOpen={openModal}
          onClose={() => setOpenModal(false)}
          points={places}
          title="جاذبه‌های اطراف"
          itemLabel="مکان"
        />
      )}
    </section>
  );
}