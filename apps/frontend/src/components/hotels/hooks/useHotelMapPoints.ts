// src/components/hotels/hooks/useHotelMapPoints.ts
import { useMemo } from "react";
import type { Hotel } from "@/types/hotel";
import type { ListingMapPoint } from "@/components/shared/map/types";

export function useHotelMapPoints(hotels: Hotel[]): ListingMapPoint[] {
  return useMemo(() => {
    return hotels
      .filter(
        (h) =>
          h.coordinates?.lat &&
          h.coordinates?.lng &&
          h.coordinates.lat !== 0 &&
          h.coordinates.lng !== 0
      )
      .map((hotel) => ({
        key: hotel.slug,
        lat: hotel.coordinates.lat,
        lng: hotel.coordinates.lng,
        title: hotel.title,
        type: "hotel" as const,
        locationType: hotel.category || "",
        href: `/hotels/${hotel.slug}`,

        // اطلاعات برای پنل
        image: hotel.image,
        stars: hotel.stars,
        rating: hotel.rating,
        address: hotel.address,
        description: hotel.description,
        reviewsCount: hotel.reviews,
      }));
  }, [hotels]);
}