"use client";

import { useMemo } from "react";
import type { ListingMapPoint } from "./types";

interface MapPointSource {
  id: number | string;
  title: string;
  coordinates?: {
    lat: number;
    lng: number;
  } | null;
}

/**
 * هوک تبدیل داده‌های هتل/مقصد به نقاط نقشه
 * فقط نقاطی که مختصات معتبر دارند را برمی‌گرداند
 */
export function useMapPoints<T extends MapPointSource>(
  items: T[],
  type: string = "hotel"
): ListingMapPoint[] {
  return useMemo(
    () =>
      items
        .filter((item) => {
          if (!item.coordinates) return false;
          const { lat, lng } = item.coordinates;
          return (
            typeof lat === "number" &&
            typeof lng === "number" &&
            !isNaN(lat) &&
            !isNaN(lng) &&
            lat !== 0 &&
            lng !== 0
          );
        })
        .map((item) => ({
          key: `${type}-${item.id}`,
          lat: item.coordinates!.lat,
          lng: item.coordinates!.lng,
          title: item.title,
          type,
          locationType: "",
        })),
    [items, type]
  );
}