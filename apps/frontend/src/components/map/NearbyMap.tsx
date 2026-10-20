"use client";

import { useState } from "react";
import { Maximize2, X, Footprints, Car } from "lucide-react";
import type { NearbyPlace } from "@/types/map";
import NextSafarMap from "@/components/map/NextSafarMap";
import { colorOf, TYPE_FA } from "./map-icons";

interface NearbyMapProps {
  center: { lat: number; lng: number };
  places: NearbyPlace[];
  height?: number;
}

export default function NearbyMap({ center, places, height = 260 }: NearbyMapProps) {
  const [open, setOpen] = useState(false);
  const [activeId, setActiveId] = useState<number | null>(null);

  // تبدیل NearbyPlace به MapPoint
  const points = places.map((p) => ({
    key: String(p.id),
    lat: p.lat,
    lng: p.lng,
    title: p.title,
    type: p.type,
    locationType: (p as any).location_type ?? "",
  }));

  // اضافه کردن مرکز به عنوان اولین point
  const allPoints = [
    {
      key: "center",
      lat: center.lat,
      lng: center.lng,
      title: "مکان فعلی",
      type: "center",
    },
    ...points,
  ];

  const focusPlace = (p: NearbyPlace) => {
    setActiveId(p.id);
    // TODO: در NextSafarMap می‌توانیم onPointClick اضافه کنیم
  };

  return (
    <div>
      {/* پیش‌نمایش */}
      <div
        className="relative rounded-lg overflow-hidden border border-border bg-bg-sec"
        style={{ height }}
      >
        <NextSafarMap
          preset="nearby"
          points={allPoints}
          center={center}
          height="100%"
        />
        <button
          type="button"
          onClick={() => setOpen(true)}
          className="absolute bottom-3 start-3 flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary text-white text-xs font-bold shadow-lg hover:opacity-95 transition cursor-pointer z-[400]"
        >
          <Maximize2 className="w-3.5 h-3.5" />
          نقشه کامل و مکان‌های نزدیک
        </button>
      </div>

      {/* مودال */}
      {open && (
        <div className="fixed inset-0 z-[400] bg-black/60 backdrop-blur-sm flex items-center justify-center p-3 md:p-6">
          <div className="relative w-full max-w-5xl h-[85vh] bg-white rounded-lg overflow-hidden flex flex-col md:flex-row shadow-2xl">
            {/* نقشه */}
            <div className="flex-1 h-1/2 md:h-full">
              <NextSafarMap
                preset="nearby"
                points={allPoints}
                center={center}
                height="100%"
                interactive={true}
                showControls={true}
              />
            </div>

            {/* لیست مکان‌ها */}
            <aside className="w-full md:w-80 border-t md:border-t-0 md:border-s border-divider overflow-y-auto p-3 space-y-2 bg-white">
              <div className="flex items-center justify-between mb-1">
                <h4 className="font-bold text-sm">نزدیک‌ترین‌ها ({places.length})</h4>
                <button
                  type="button"
                  onClick={() => setOpen(false)}
                  className="p-1.5 rounded-lg hover:bg-bg-sec transition"
                  aria-label="بستن"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
              {places.length === 0 && (
                <p className="text-xs text-text-muted py-6 text-center">
                  مکانی در این شعاع یافت نشد
                </p>
              )}
              {places.map((p) => (
                <button
                  key={p.id}
                  type="button"
                  onClick={() => focusPlace(p)}
                  className={`w-full text-start flex gap-2.5 p-2 rounded-lg border transition cursor-pointer ${
                    activeId === p.id
                      ? "border-primary bg-primary/5"
                      : "border-border hover:border-primary/40"
                  }`}
                >
                  <span
                    className="w-9 h-9 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                    style={{ background: colorOf(p.type) }}
                  >
                    {TYPE_FA[p.type]?.[0] ?? "•"}
                  </span>
                  <span className="flex-1 min-w-0">
                    <span className="block text-xs font-bold truncate">{p.title}</span>
                    <span className="flex items-center gap-2 text-[10px] text-text-muted mt-1">
                      <span>{p.distance_km} کیلومتر</span>
                      <span className="flex items-center gap-0.5">
                        <Footprints className="w-3 h-3" /> {p.walking_min} دقیقه
                      </span>
                      <span className="flex items-center gap-0.5">
                        <Car className="w-3 h-3" /> {p.driving_min} دقیقه
                      </span>
                    </span>
                  </span>
                </button>
              ))}
            </aside>
          </div>
        </div>
      )}
    </div>
  );
}