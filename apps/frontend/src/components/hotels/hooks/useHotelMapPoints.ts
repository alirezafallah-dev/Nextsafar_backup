import { useMemo } from "react";
import type { Hotel } from "@/types/hotel";
import type { ListingMapPoint } from "@/components/shared/map";

export function useHotelMapPoints(hotels: Hotel[]): ListingMapPoint[] {
  return useMemo(
    () =>
      hotels
        .filter((h) => {
          if (!h.coordinates) return false;
          const { lat, lng } = h.coordinates;
          return (
            typeof lat === "number" &&
            typeof lng === "number" &&
            !isNaN(lat) &&
            !isNaN(lng) &&
            lat !== 0 &&
            lng !== 0
          );
        })
        .map((h) => ({
          key: `hotel-${h.id}`,
          lat: h.coordinates.lat,
          lng: h.coordinates.lng,
          title: h.title,
          type: "hotel",
          locationType: "",
        })),
    [hotels]
  );
}