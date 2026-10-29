"use client";

import NextSafarMap from "@/components/map/NextSafarMap";
import type { TripEntity } from "@/types/map";

interface TripMapPanelProps {
  entities: TripEntity[];
  center?: { lat: number; lng: number } | null;
  hover?: string | null;
}

export default function TripMapPanel({ entities, center, hover }: TripMapPanelProps) {
  // تبدیل TripEntity به MapPoint
  const points = entities
    .filter((e) => e.lat != null && e.lng != null)
    .map((e, idx) => ({
      key: e.slug,
      lat: e.lat!,
      lng: e.lng!,
      title: e.title,
      type: e.type,
      url: e.url,
      index: idx,
    }));

  return (
    <NextSafarMap
      preset="trip"
      points={points}
      center={center}
      hoverKey={hover || undefined}
      height="100%"
      className="min-h-[320px]"
    />
  );
}