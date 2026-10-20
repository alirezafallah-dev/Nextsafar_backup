"use client";
import { useState, useMemo, useEffect, useCallback } from "react";
import { useSearchParams, useRouter, usePathname } from "next/navigation";
import { BedDouble, SlidersHorizontal } from "lucide-react";
import type { HotelsData } from "@/types/hotel";
import HotelListItem from "./HotelListItem";
import HotelFiltersSidebar, { HotelFiltersState } from "./HotelFiltersSidebar";
import MobileFilterSheet from "./MobileFilterSheet";
import ViewToggle from "@/components/shared/ViewToggle";
import { pullFromServer } from "@/lib/favorites";

import type { ListingMapPoint } from "@/components/shared/ListingMap";
import dynamic from "next/dynamic";

const ListingMap = dynamic(() => import("@/components/shared/ListingMap"), {
  ssr: false,
  loading: () => (
    <div className="w-full h-[400px] rounded-lg bg-bg-sec animate-pulse flex items-center justify-center text-text-muted">
      بارگذاری نقشه...
    </div>
  ),
});

import { faDigits } from "@/lib/api/hotels";
import HotelGridCard from "./HotelGridCard";

interface Props {
  data: HotelsData;
}

const SORT_OPTIONS = [
  { key: "popular", label: "پیشنهادی" },
  { key: "rating", label: "بالاترین امتیاز" },
  { key: "stars", label: "بیشترین ستاره" },
  { key: "price_asc", label: "ارزان‌ترین" },
  { key: "price_desc", label: "گران‌ترین" },
];

export default function HotelsPage({ data }: Props) {
  const searchParams = useSearchParams();
  const router = useRouter();
  const pathname = usePathname();

  const [mobileFilterOpen, setMobileFilterOpen] = useState(false);
  const [hoveredKey, setHoveredKey] = useState<string | null>(null);

  // ✅ Pull favorites از سرور در mount
  useEffect(() => {
    pullFromServer();
  }, []);

  const priceMax = useMemo(() => {
    return Math.max(...data.hotels.map((h) => h.price_per_night || 0), 10_000_000);
  }, [data.hotels]);

  // ✅ خواندن فیلترها از URL در mount
  const initialFilters: HotelFiltersState = useMemo(() => {
    const search = searchParams.get("q") || "";
    const starsParam = searchParams.get("stars");
    const stars = starsParam ? starsParam.split(",").map(Number).filter(n => !isNaN(n)) : [];
    const amenitiesParam = searchParams.get("amenities");
    const amenities = amenitiesParam ? amenitiesParam.split(",").filter(Boolean) : [];
    const minPrice = parseInt(searchParams.get("minPrice") || "0");
    const maxPrice = parseInt(searchParams.get("maxPrice") || String(priceMax));
    const minRating = parseInt(searchParams.get("rating") || "0");

    return {
      search,
      stars,
      amenities,
      priceRange: [isNaN(minPrice) ? 0 : minPrice, isNaN(maxPrice) ? priceMax : maxPrice] as [number, number],
      minRating: isNaN(minRating) ? 0 : minRating,
    };
  }, [searchParams, priceMax]);

  const [filters, setFiltersState] = useState<HotelFiltersState>(initialFilters);
  const [sortBy, setSortBy] = useState(searchParams.get("sort") || "popular");
  const [view, setView] = useState<(typeof viewOptions)[number]>(
    (searchParams.get("view") as typeof viewOptions[number]) || "list"
  );

  const viewOptions = ["list", "grid"] as const;

  // ✅ Sync فیلترها با URL
  const setFilters = useCallback(
    (newFilters: HotelFiltersState) => {
      setFiltersState(newFilters);

      const params = new URLSearchParams(searchParams.toString());

      // Search
      if (newFilters.search) {
        params.set("q", newFilters.search);
      } else {
        params.delete("q");
      }

      // Stars
      if (newFilters.stars.length > 0) {
        params.set("stars", newFilters.stars.join(","));
      } else {
        params.delete("stars");
      }

      // Amenities
      if (newFilters.amenities.length > 0) {
        params.set("amenities", newFilters.amenities.join(","));
      } else {
        params.delete("amenities");
      }

      // Price range
      if (newFilters.priceRange[0] > 0) {
        params.set("minPrice", String(newFilters.priceRange[0]));
      } else {
        params.delete("minPrice");
      }
      if (newFilters.priceRange[1] < priceMax) {
        params.set("maxPrice", String(newFilters.priceRange[1]));
      } else {
        params.delete("maxPrice");
      }

      // Min rating
      if (newFilters.minRating > 0) {
        params.set("rating", String(newFilters.minRating));
      } else {
        params.delete("rating");
      }

      // Preserve sort and view
      params.set("sort", sortBy);
      params.set("view", view);

      // Update URL without reload
      router.replace(`${pathname}?${params.toString()}`, { scroll: false });
    },
    [searchParams, router, pathname, priceMax, sortBy, view]
  );

  // ✅ Sync sort با URL
  const handleSortChange = useCallback(
    (newSort: string) => {
      setSortBy(newSort);
      const params = new URLSearchParams(searchParams.toString());
      params.set("sort", newSort);
      router.replace(`${pathname}?${params.toString()}`, { scroll: false });
    },
    [searchParams, router, pathname]
  );

  // ✅ Sync view با URL
  const handleViewChange = useCallback(
    (newView: typeof viewOptions[number]) => {
      setView(newView);
      const params = new URLSearchParams(searchParams.toString());
      params.set("view", newView);
      router.replace(`${pathname}?${params.toString()}`, { scroll: false });
    },
    [searchParams, router, pathname]
  );

  const filteredHotels = useMemo(() => {
    let result = [...data.hotels];

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

    switch (sortBy) {
      case "rating":
        result.sort((a, b) => b.rating - a.rating);
        break;
      case "stars":
        result.sort((a, b) => b.stars - a.stars);
        break;
      case "price_asc":
        result.sort((a, b) => (a.price_per_night || 0) - (b.price_per_night || 0));
        break;
      case "price_desc":
        result.sort((a, b) => (b.price_per_night || 0) - (a.price_per_night || 0));
        break;
      default:
        result.sort((a, b) => {
          const sA = (a.featured ? 100 : 0) + a.rating + (a.reviews || 0) / 1000;
          const sB = (b.featured ? 100 : 0) + b.rating + (b.reviews || 0) / 1000;
          return sB - sA;
        });
    }
    return result;
  }, [data.hotels, sortBy, filters]);

  const mapPoints: ListingMapPoint[] = useMemo(
    () =>
      filteredHotels
        .filter((h) => {
          // ✅ بررسی دقیق‌تر مختصات
          if (!h.coordinates) return false;
          const lat = h.coordinates.lat;
          const lng = h.coordinates.lng;
          return (
            typeof lat === 'number' &&
            typeof lng === 'number' &&
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
    [filteredHotels]
  );

  const activeCount =
    filters.stars.length +
    filters.amenities.length +
    (filters.minRating > 0 ? 1 : 0) +
    (filters.priceRange[0] > 0 || filters.priceRange[1] < priceMax ? 1 : 0);

  return (
    <div className="ns-container py-6">
      {/* ═══ Toolbar بالا ═══ */}
      <div className="flex items-center justify-between gap-3 mb-5">
        <span className="text-sm text-text-muted">
          <strong className="text-text-strong">
            {faDigits(filteredHotels.length)}
          </strong>{" "}
          هتل یافت شد
        </span>
        <div className="flex items-center gap-2">
          {/* دکمه فیلتر موبایل */}
          <button
            onClick={() => setMobileFilterOpen(true)}
            className="lg:hidden flex items-center gap-1.5 px-3 py-2 rounded-lg border border-border bg-white text-xs font-bold hover:border-primary/40 transition"
          >
            <SlidersHorizontal className="w-4 h-4" />
            فیلتر
            {activeCount > 0 && (
              <span className="px-1.5 py-0.5 rounded-full bg-primary text-white text-[10px]">
                {faDigits(activeCount)}
              </span>
            )}
          </button>
          {/* مرتب‌سازی */}
          <select
            value={sortBy}
            onChange={(e) => handleSortChange(e.target.value)}
            className="px-3 py-2 rounded-lg border border-border bg-white text-sm font-bold text-text-strong outline-none focus:border-primary cursor-pointer"
          >
            {SORT_OPTIONS.map((o) => (
              <option key={o.key} value={o.key}>
                {o.label}
              </option>
            ))}
          </select>
          {/* View Toggle */}
          <ViewToggle view={view} onChange={handleViewChange} />
        </div>
      </div>

      {/* ═══ Mobile Filter Chips ═══ */}
      {activeCount > 0 && (
        <div className="lg:hidden flex flex-wrap gap-2 mb-4">
          {filters.stars.map((s) => (
            <button
              key={s}
              onClick={() =>
                setFilters({ ...filters, stars: filters.stars.filter((x) => x !== s) })
              }
              className="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center gap-1"
            >
              {faDigits(s)} ستاره
              <span className="text-primary/60">×</span>
            </button>
          ))}
          {filters.amenities.map((a) => (
            <button
              key={a}
              onClick={() =>
                setFilters({
                  ...filters,
                  amenities: filters.amenities.filter((x) => x !== a),
                })
              }
              className="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center gap-1"
            >
              {a}
              <span className="text-primary/60">×</span>
            </button>
          ))}
        </div>
      )}

      {/* ═══ Layout اصلی ═══ */}
      <div className="flex gap-6">
        {/* ستون راست: نقشه + سایدبار */}
        <aside className="hidden lg:block w-[300px] xl:w-[320px] flex-shrink-0 sticky bottom-6 self-end">
          <div className="space-y-3">
            <div className="bg-white rounded-lg border border-border overflow-hidden shadow-sm">
              <ListingMap
                points={mapPoints}
                height={180}
                className="!rounded-none"
                activeKey={hoveredKey}
                onPointHover={setHoveredKey}
              />
            </div>

            <HotelFiltersSidebar
              filters={filters}
              onChange={setFilters}
              totalCount={data.hotels.length}
              filteredCount={filteredHotels.length}
              priceMax={priceMax}
            />
          </div>
        </aside>

        {/* ستون چپ: کارت‌ها */}
        <main className="flex-1 min-w-0">
          {filteredHotels.length === 0 ? (
            <div className="rounded-lg border-2 border-dashed border-border bg-bg-sec/30 p-12 text-center">
              <BedDouble className="w-12 h-12 text-primary/50 mx-auto mb-4" />
              <h3 className="text-lg font-extrabold text-text-strong mb-2">
                هتلی یافت نشد
              </h3>
              <p className="text-sm text-text-muted">
                فیلترهای خود را تغییر دهید یا مقصد دیگری را انتخاب کنید
              </p>
            </div>
          ) : view === "list" ? (
            <div className="space-y-4">
              {filteredHotels.map((h) => (
                <HotelListItem 
                  key={h.id} 
                  hotel={h} 
                  onHover={setHoveredKey}
                />
              ))}
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
              {filteredHotels.map((h) => (
                <HotelGridCard key={h.id} hotel={h} />
              ))}
            </div>
          )}
        </main>
      </div>

      {/* ═══ نقشه موبایل ═══ */}
      <div className="lg:hidden mt-8">
        <div className="rounded-lg border border-border overflow-hidden bg-white shadow-sm">
          <ListingMap
            points={mapPoints}
            height={220}
            className="!rounded-none"
            activeKey={hoveredKey}
            onPointHover={setHoveredKey}
          />
        </div>
      </div>

      {/* Mobile Filter Sheet */}
      <MobileFilterSheet
        open={mobileFilterOpen}
        onClose={() => setMobileFilterOpen(false)}
        filters={filters}
        onChange={setFilters}
        totalCount={data.hotels.length}
        filteredCount={filteredHotels.length}
        priceMax={priceMax}
      />
    </div>
  );
}