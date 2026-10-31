// src/components/hotels/single/HotelLocationMap.tsx
"use client";

import { useState } from "react";
import { MapPin } from "lucide-react";
// ✅ import صحیح از shared/map
import ResponsiveMap from "@/components/shared/map/ResponsiveMap";
import MapModal from "@/components/shared/map/MapModal";
import type { Hotel } from "@/types/hotel";
import type { ListingMapPoint } from "@/components/shared/map/types";

interface Props {
  hotel: Hotel;
}

export default function HotelLocationMap({ hotel }: Props) {
  const [openModal, setOpenModal] = useState(false);

  /* ═══ ساخت نقطه نقشه از هتل — دقیقاً مطابق useHotelMapPoints ═══ */
  const point: ListingMapPoint = {
    key: hotel.slug,
    lat: hotel.coordinates.lat,
    lng: hotel.coordinates.lng,
    title: hotel.title,
    type: "hotel",
    locationType: "",
    href: `/hotels/${hotel.slug}`,
    image: hotel.image,
    stars: hotel.stars,
    rating: hotel.rating,
    address: hotel.address,
    description: hotel.description,
    reviewsCount: hotel.reviews,
  };

  const points = [point];

  return (
    <>
      {/* ═══ نقشه کوچک (همان کامپوننت استفاده شده در سایدبار لیست هتل‌ها) ═══ */}
      <div className="relative h-[200px]">
        <ResponsiveMap
          points={points}
          title="نقشه هتل"
          itemLabel="هتل"
          desktopHeight={200}
        />

        <button
          onClick={() => setOpenModal(true)}
          className="absolute bottom-3 left-1/2 -translate-x-1/2 inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-white shadow-md border border-border text-[11px] font-bold text-text-strong hover:text-primary transition z-[500]"
        >
          <MapPin className="w-3.5 h-3.5 text-primary" />
          نمایش روی نقشه
        </button>
      </div>

      {/* ═══ مودال نقشه (همان مودال استفاده شده در سیستم نقشه سایت) ═══ */}
      {openModal && (
        <MapModal
          isOpen={openModal}
          onClose={() => setOpenModal(false)}
          points={points}
          title={hotel.title}
          itemLabel="هتل"
        />
      )}
    </>
  );
}