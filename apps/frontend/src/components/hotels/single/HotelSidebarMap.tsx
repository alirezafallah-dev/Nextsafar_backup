// src/components/hotels/single/HotelSidebarMap.tsx
"use client";

import { useMemo, useState } from "react";
import { MapPin } from "lucide-react";
import ListingMap from "@/components/shared/map/ListingMap";
import HotelMapModal from "./HotelMapModal";
import type { Hotel } from "@/types/hotel";
import type { ListingMapPoint } from "@/components/shared/map/types";
import hotelsData from "@/data/hotels.json";

interface Props { hotel: Hotel }

const isValid = (h: Hotel) =>
  h.coordinates?.lat && h.coordinates?.lng &&
  h.coordinates.lat !== 0 && h.coordinates.lng !== 0;

const toPoint = (h: Hotel): ListingMapPoint => ({
  key: h.slug,
  lat: h.coordinates.lat,
  lng: h.coordinates.lng,
  title: h.title,
  type: "hotel",
  href: `/hotels/${h.slug}`,
  image: h.image,
  stars: h.stars,
  rating: h.rating,
  address: h.address,
  reviewsCount: h.reviews,
});

export default function HotelSidebarMap({ hotel }: Props) {
  const [openModal, setOpenModal] = useState(false);

  const currentPoint: ListingMapPoint = {
    key: hotel.slug,
    lat: hotel.coordinates.lat,
    lng: hotel.coordinates.lng,
    title: hotel.title,
    type: "hotel",
    image: hotel.image,
    stars: hotel.stars,
    rating: hotel.rating,
    address: hotel.address,
  };

  /* ═══ ✅ فقط هتل‌های همان منطقه (شهر → کشور) تا نقشه زوم باشه ═══ */
  const { regionPoints, regionTitle } = useMemo(() => {
    const all = (hotelsData.hotels as Hotel[]).filter(isValid);

    const sameCity = all.filter((h) => h.city === hotel.city);
    const sameCountry = all.filter((h) => h.country === hotel.country);

    // اولویت: هم‌شهری‌ها → هم‌کشوری‌ها → فقط هتل فعلی
    const region = sameCity.length >= 2 ? sameCity : sameCountry.length >= 2 ? sameCountry : [];

    const others = region.filter((h) => h.slug !== hotel.slug).slice(0, 30);
    const points = [currentPoint, ...others.map(toPoint)];

    const title =
      sameCity.length >= 2
        ? `هتل‌های ${hotel.city}`
        : sameCountry.length >= 2
        ? `هتل‌های ${hotel.country}`
        : hotel.title;

    return { regionPoints: points, regionTitle: title };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [hotel.slug]);

  return (
    <>
      {/* ═══ نقشه کوچک سایدبار — ۱۸۰px ═══ */}
      <div className="relative isolate z-0 h-[180px] overflow-hidden [&>div>button.absolute.bottom-3.end-3]:hidden [&>div>button.absolute.bottom-4.end-4]:hidden">
        <ListingMap
          points={[currentPoint]}
          activeKey={hotel.slug}
          height={180}
          className="rounded-b-none"
        />

        <button
          onClick={() => setOpenModal(true)}
          className="absolute bottom-3 left-1/2 -translate-x-1/2 z-[500]
                     inline-flex items-center gap-1.5
                     px-4 py-2 rounded-md
                     bg-white border border-border/60
                     text-[11px] font-bold text-text-strong
                     hover:border-primary hover:text-primary
                     shadow-md hover:shadow-lg
                     transition-all duration-200"
        >
          <MapPin className="w-3.5 h-3.5 text-primary" />
          نمایش روی نقشه
        </button>
      </div>

      {/* ═══ مودال — فقط منطقه هتل (زوم اولیه روی منطقه) ═══ */}
      {openModal && (
        <HotelMapModal
          isOpen={openModal}
          onClose={() => setOpenModal(false)}
          points={regionPoints}
          currentHotelKey={hotel.slug}
          title={regionTitle}
          itemLabel="هتل"
        />
      )}
    </>
  );
}