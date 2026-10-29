import { useState, useMemo, useCallback } from "react";
import { useSearchParams, useRouter, usePathname } from "next/navigation";
import type { Hotel, HotelFiltersState, SortKey, ViewMode } from "../constants";
import { DEFAULT_PRICE_MAX, sortHotels } from "../constants";

export function useHotelFilters(hotels: Hotel[]) {
  const searchParams = useSearchParams();
  const router = useRouter();
  const pathname = usePathname();

  // ═══ Price Max ═══
  const priceMax = useMemo(() => {
    return Math.max(
      ...hotels.map((h) => h.price_per_night || 0),
      DEFAULT_PRICE_MAX
    );
  }, [hotels]);

  // ═══ Initial Filters from URL ═══
  const initialFilters: HotelFiltersState = useMemo(() => {
    const search = searchParams.get("q") || "";
    const starsParam = searchParams.get("stars");
    const stars = starsParam
      ? starsParam.split(",").map(Number).filter((n) => !isNaN(n))
      : [];
    const amenitiesParam = searchParams.get("amenities");
    const amenities = amenitiesParam
      ? amenitiesParam.split(",").filter(Boolean)
      : [];
    const minPrice = parseInt(searchParams.get("minPrice") || "0");
    const maxPrice = parseInt(searchParams.get("maxPrice") || String(priceMax));
    const minRating = parseInt(searchParams.get("rating") || "0");

    return {
      search,
      stars,
      amenities,
      priceRange: [
        isNaN(minPrice) ? 0 : minPrice,
        isNaN(maxPrice) ? priceMax : maxPrice,
      ] as [number, number],
      minRating: isNaN(minRating) ? 0 : minRating,
    };
  }, [searchParams, priceMax]);

  const [filters, setFiltersState] = useState<HotelFiltersState>(initialFilters);
  const [sortBy, setSortBy] = useState<SortKey>(
    (searchParams.get("sort") as SortKey) || "popular"
  );
  const [view, setView] = useState<ViewMode>(
    (searchParams.get("view") as ViewMode) || "list"
  );

  // ═══ URL Sync ═══
  const syncToURL = useCallback(
    (newFilters: HotelFiltersState, newSort: SortKey, newView: ViewMode) => {
      const params = new URLSearchParams(searchParams.toString());

      if (newFilters.search) params.set("q", newFilters.search);
      else params.delete("q");

      if (newFilters.stars.length > 0)
        params.set("stars", newFilters.stars.join(","));
      else params.delete("stars");

      if (newFilters.amenities.length > 0)
        params.set("amenities", newFilters.amenities.join(","));
      else params.delete("amenities");

      if (newFilters.priceRange[0] > 0)
        params.set("minPrice", String(newFilters.priceRange[0]));
      else params.delete("minPrice");

      if (newFilters.priceRange[1] < priceMax)
        params.set("maxPrice", String(newFilters.priceRange[1]));
      else params.delete("maxPrice");

      if (newFilters.minRating > 0)
        params.set("rating", String(newFilters.minRating));
      else params.delete("rating");

      params.set("sort", newSort);
      params.set("view", newView);

      router.replace(`${pathname}?${params.toString()}`, { scroll: false });
    },
    [searchParams, router, pathname, priceMax]
  );

  const setFilters = useCallback(
    (newFilters: HotelFiltersState) => {
      setFiltersState(newFilters);
      syncToURL(newFilters, sortBy, view);
    },
    [sortBy, view, syncToURL]
  );

  const handleSortChange = useCallback(
    (newSort: SortKey) => {
      setSortBy(newSort);
      syncToURL(filters, newSort, view);
    },
    [filters, view, syncToURL]
  );

  const handleViewChange = useCallback(
    (newView: ViewMode) => {
      setView(newView);
      syncToURL(filters, sortBy, newView);
    },
    [filters, sortBy, syncToURL]
  );

  // ═══ Filtered & Sorted Hotels ═══
  const filteredHotels = useMemo(() => {
    let result = [...hotels];

    if (filters.search) {
      const q = filters.search.toLowerCase();
      result = result.filter(
        (h) =>
          h.title.toLowerCase().includes(q) ||
          (h.title_en?.toLowerCase().includes(q) ?? false) ||
          h.address.toLowerCase().includes(q)
      );
    }

    if (filters.stars.length > 0) {
      result = result.filter((h) => filters.stars.includes(h.stars));
    }

    if (filters.amenities.length > 0) {
      result = result.filter((h) =>
        filters.amenities.every((a) => h.amenities.includes(a))
      );
    }

    result = result.filter(
      (h) =>
        (h.price_per_night || 0) >= filters.priceRange[0] &&
        (h.price_per_night || 0) <= filters.priceRange[1]
    );

    if (filters.minRating > 0) {
      result = result.filter((h) => h.rating >= filters.minRating);
    }

    return sortHotels(result, sortBy);
  }, [hotels, filters, sortBy]);

  // ═══ Active Filters Count ═══
  const activeCount =
    filters.stars.length +
    filters.amenities.length +
    (filters.minRating > 0 ? 1 : 0) +
    (filters.priceRange[0] > 0 || filters.priceRange[1] < priceMax ? 1 : 0);

  return {
    filters,
    setFilters,
    sortBy,
    handleSortChange,
    view,
    handleViewChange,
    filteredHotels,
    activeCount,
    priceMax,
  };
}