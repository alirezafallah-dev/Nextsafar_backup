"use client";
import { useState, useRef, useEffect, useMemo } from "react";
import Link from "next/link";
import Image from "next/image";
import { motion } from "framer-motion";
import {
  MapPin,
  List,
  Map as MapIcon,
  X,
  ZoomIn,
  ZoomOut,
  LocateFixed,
  Search,
  Star,
  ChevronLeft,
  BedDouble,
} from "lucide-react";
import { loadLeaflet, waitForSize } from "@/lib/map-loader";
import { getMapConfig } from "@/lib/api/map";
import { addTiles, makeMarkerIcon, makeActiveMarkerIcon, colorOf } from "@/components/map/map-icons";
import { faDigits } from "@/lib/api/hotels";
import { useMediaQuery } from "@/hooks/use-media-query";
import PlaceCard from "./PlaceCard";
import type { ListingMapPoint } from "./types";
import ModalCloseButton from "../common/ModalCloseButton";

interface ListMapSplitViewProps {
  items: ListingMapPoint[];
  title?: string;
  itemLabel?: string;
  showHeader?: boolean;
  onClose?: () => void;
  onItemClick?: (place: ListingMapPoint) => void;
  actionLabel?: string;
  searchable?: boolean;
  defaultMobileView?: "list" | "map";
}

type MobileView = "list" | "map";

/* ═══ Preview کارت موبایل با Swipe to Dismiss ═══ */
function MobileMapPreview({
  place,
  onClose,
  actionLabel = "مشاهده",
}: {
  place: ListingMapPoint | null;
  onClose: () => void;
  actionLabel?: string;
}) {
  const [dragY, setDragY] = useState(0);
  const [isDragging, setIsDragging] = useState(false);
  const touchStartY = useRef<number>(0);

  if (!place) return null;

  const href = place.href || `/${place.type}s/${place.key}`;
  const typeLabel =
    place.type === "hotel"
      ? "هتل"
      : place.type === "restaurant"
      ? "رستوران"
      : place.type === "airport"
      ? "فرودگاه"
      : place.type === "destination" || place.type === "attraction"
      ? "جاذبه"
      : "مکان";

  // آستانه swipe برای بستن (100px)
  const DISMISS_THRESHOLD = 100;

  const handleTouchStart = (e: React.TouchEvent) => {
    touchStartY.current = e.touches[0].clientY;
    setIsDragging(true);
  };

  const handleTouchMove = (e: React.TouchEvent) => {
    if (!isDragging) return;
    const currentY = e.touches[0].clientY;
    const deltaY = currentY - touchStartY.current;
    // فقط اجازه swipe به پایین (مثبت)
    if (deltaY > 0) {
      setDragY(deltaY);
    }
  };

  const handleTouchEnd = () => {
    setIsDragging(false);
    // اگر بیش از آستانه swipe شد، ببند
    if (dragY > DISMISS_THRESHOLD) {
      onClose();
    } else {
      // بازگشت به موقعیت اولیه
      setDragY(0);
    }
  };

  // محاسبه opacity بر اساس drag
  const opacity = Math.max(0.3, 1 - dragY / 300);

  return (
    <motion.div
      initial={{ y: "100%", opacity: 0 }}
      animate={{ 
        y: dragY > 0 ? dragY : 0, 
        opacity: dragY > 0 ? opacity : 1 
      }}
      exit={{ y: "100%", opacity: 0 }}
      transition={
        isDragging 
          ? { type: "tween", duration: 0 } 
          : { type: "spring", damping: 25, stiffness: 200 }
      }
      onTouchStart={handleTouchStart}
      onTouchMove={handleTouchMove}
      onTouchEnd={handleTouchEnd}
      className="absolute bottom-0 start-0 end-0 z-[450] bg-white rounded-t-2xl shadow-2xl border-t border-border overflow-hidden cursor-grab active:cursor-grabbing"
    >
      {/* دستگیره بالا با عملکرد swipe */}
      <div className="flex flex-col items-center justify-center pt-3 pb-2">
        <div className="w-12 h-1.5 rounded-full bg-border/60 mb-1" />
        <p className="text-[10px] text-text-subtle">
          برای بستن به پایین بکشید
        </p>
      </div>

      <div className="flex gap-3 p-3 pb-4">
        {/* تصویر */}
        <div className="relative w-[100px] h-[120px] flex-shrink-0 rounded-xl overflow-hidden bg-bg-sec">
          {place.image ? (
            <Image
              src={place.image}
              alt={place.title}
              fill
              className="object-cover"
              sizes="100px"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center text-primary/50">
              <BedDouble className="w-8 h-8" />
            </div>
          )}
          <div className="absolute top-1.5 start-1.5 px-1.5 py-0.5 rounded-md bg-black/70 backdrop-blur text-white text-[9px] font-bold">
            {typeLabel}
          </div>
        </div>

        {/* اطلاعات */}
        <div className="flex-1 min-w-0 flex flex-col">
          <div className="flex items-start justify-between gap-2 mb-1">
            <h4 className="font-extrabold text-sm text-text-strong line-clamp-2 leading-tight">
              {place.title}
            </h4>
            <button
              onClick={(e) => {
                e.stopPropagation();
                onClose();
              }}
              className="w-7 h-7 rounded-full bg-bg-sec hover:bg-danger/10 hover:text-danger flex items-center justify-center transition shrink-0"
              aria-label="بستن"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          </div>

          {/* ستاره و امتیاز */}
          <div className="flex items-center gap-2 mb-1.5 flex-wrap">
            {place.stars && place.stars > 0 && (
              <div className="flex items-center gap-0.5">
                {Array.from({ length: Math.min(place.stars, 5) }).map((_, i) => (
                  <Star
                    key={i}
                    className="w-3 h-3 text-amber-400 fill-amber-400"
                  />
                ))}
              </div>
            )}
            {place.rating && (
              <span className="px-1.5 py-0.5 rounded-md bg-primary/10 text-primary text-[10px] font-extrabold">
                {faDigits(place.rating.toFixed(1))}
              </span>
            )}
            {place.reviewsCount && place.reviewsCount > 0 && (
              <span className="text-[10px] text-text-muted">
                ({faDigits(place.reviewsCount)} نظر)
              </span>
            )}
          </div>

          {/* آدرس */}
          {place.address && (
            <div className="flex items-start gap-1 text-[11px] text-text-muted mb-2">
              <MapPin className="w-3 h-3 mt-0.5 flex-shrink-0 text-primary" />
              <span className="line-clamp-1">{place.address}</span>
            </div>
          )}

          {/* دکمه مشاهده جزئیات */}
          <div className="mt-auto">
            <Link
              href={href}
              onClick={(e) => e.stopPropagation()}
              className="w-full flex items-center justify-center gap-1.5 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white font-bold text-xs transition-all shadow-md shadow-primary/25"
            >
              {actionLabel} {typeLabel}
              <ChevronLeft className="w-3.5 h-3.5 rtl:rotate-180" />
            </Link>
          </div>
        </div>
      </div>
    </motion.div>
  );
}

export default function ListMapSplitView({
  items,
  title = "نتایج",
  itemLabel = "مورد",
  showHeader = true,
  onClose,
  onItemClick,
  actionLabel = "مشاهده",
  searchable = true,
  defaultMobileView = "map",
}: ListMapSplitViewProps) {
  const isMobile = useMediaQuery("(max-width: 1023px)");
  const [mobileView, setMobileView] = useState<MobileView>(defaultMobileView);
  const [selectedIndex, setSelectedIndex] = useState<number>(-1);
  const [hoveredKey, setHoveredKey] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [mobilePreviewKey, setMobilePreviewKey] = useState<string | null>(null);

  const mapRef = useRef<HTMLDivElement>(null);
  const mapInstance = useRef<any>(null);
  const markersRef = useRef<Map<string, any>>(new Map());
  const [isMapLoaded, setIsMapLoaded] = useState(false);

  const listRef = useRef<HTMLDivElement>(null);
  const cardRefs = useRef<Map<string, HTMLDivElement>>(new Map());
  const prevSelectedKey = useRef<string | null>(null);

  const validItems = useMemo(
    () =>
      items.filter(
        (p) =>
          typeof p.lat === "number" &&
          typeof p.lng === "number" &&
          !isNaN(p.lat) &&
          !isNaN(p.lng) &&
          p.lat !== 0 &&
          p.lng !== 0
      ),
    [items]
  );

  const filteredItems = useMemo(() => {
    if (!searchQuery.trim()) return validItems;
    const q = searchQuery.trim().toLowerCase();
    return validItems.filter(
      (p) =>
        p.title.toLowerCase().includes(q) ||
        (p.address && p.address.toLowerCase().includes(q))
    );
  }, [validItems, searchQuery]);

  const selectedPlace = selectedIndex >= 0 ? validItems[selectedIndex] : null;

  /* ═══ راه‌اندازی نقشه ═══ */
  useEffect(() => {
    let cancelled = false;
    let map: any = null;

    (async () => {
      const [L, cfg] = await Promise.all([loadLeaflet(), getMapConfig()]);
      if (cancelled || !mapRef.current) return;
      await new Promise((r) => setTimeout(r, 50));
      await waitForSize(mapRef.current);
      if (cancelled || !mapRef.current) return;

      map = L.map(mapRef.current, {
        zoomControl: false,
        attributionControl: false,
      });
      addTiles(L, map, cfg);
      mapInstance.current = map;

      if (validItems.length > 0) {
        const latLngs = validItems.map((p) => L.latLng(p.lat, p.lng));
        const bounds = L.latLngBounds(latLngs);
        try {
          map.fitBounds(bounds, { padding: [60, 60], maxZoom: 13 });
        } catch (err) {
          console.warn("[SplitView] fitBounds error:", err);
        }
      }

      validItems.forEach((point, idx) => {
        try {
          const latLng = L.latLng(point.lat, point.lng);
          const marker = L.marker(latLng, {
            icon: makeMarkerIcon(L, colorOf(point.type), point.type, point.locationType || ""),
          })
            .addTo(map)
            .on("click", () => {
              setSelectedIndex(idx);
              if (!isMobile) {
                const cardEl = cardRefs.current.get(point.key);
                if (cardEl) {
                  cardEl.scrollIntoView({ behavior: "smooth", block: "center" });
                }
              } else {
                // ✅ موبایل: نمایش preview
                setMobilePreviewKey(point.key);
              }
              onItemClick?.(point);
            });
          markersRef.current.set(point.key, marker);
        } catch (err) {
          console.warn("[SplitView] marker error:", point.key, err);
        }
      });

      setIsMapLoaded(true);
    })();

    return () => {
      cancelled = true;
      if (map) {
        map.remove();
        map = null;
      }
      mapInstance.current = null;
      markersRef.current.clear();
      setIsMapLoaded(false);
    };
  }, [validItems, isMobile, onItemClick]);

  /* ═══ flyTo فقط هنگام انتخاب کاربر ═══ */
  useEffect(() => {
    if (!isMapLoaded || !mapInstance.current) return;
    if (!selectedPlace) return;

    if (prevSelectedKey.current === selectedPlace.key) return;
    prevSelectedKey.current = selectedPlace.key;

    try {
      const L = (window as any).L;
      if (!L) return;
      const latLng = L.latLng(selectedPlace.lat, selectedPlace.lng);
      mapInstance.current.flyTo(latLng, 15, {
        animate: true,
        duration: 0.6,
      });
    } catch (err) {
      console.warn("[SplitView] flyTo error:", err);
    }
  }, [selectedPlace, isMapLoaded]);

  /* ═══ هایلایت مارکر هنگام هاور ═══ */
  useEffect(() => {
    if (!isMapLoaded) return;
    const L = (window as any).L;
    if (!L) return;

    const selectedKey = selectedPlace?.key;
    const targetKey = hoveredKey || selectedKey;

    markersRef.current.forEach((marker, key) => {
      const point = validItems.find((p) => p.key === key);
      if (!point) return;

      const isHighlighted = key === targetKey;
      const color = colorOf(point.type);

      if (isHighlighted) {
        const activeIcon = makeActiveMarkerIcon(
          L,
          color,
          point.type,
          point.locationType || ""
        );
        marker.setIcon(activeIcon);
        marker.setZIndexOffset(1000);
      } else {
        const normalIcon = makeMarkerIcon(
          L,
          color,
          point.type,
          point.locationType || ""
        );
        marker.setIcon(normalIcon);
        marker.setZIndexOffset(0);
      }
    });
  }, [hoveredKey, selectedPlace, isMapLoaded, validItems]);

  /* ═══ کنترل‌های نقشه ═══ */
  const handleResetView = () => {
    if (!mapInstance.current) return;
    const L = (window as any).L;
    if (!L || validItems.length === 0) return;
    try {
      const latLngs = validItems.map((p) => L.latLng(p.lat, p.lng));
      const bounds = L.latLngBounds(latLngs);
      mapInstance.current.flyToBounds(bounds, {
        padding: [60, 60],
        maxZoom: 13,
        animate: true,
        duration: 0.6,
      });
    } catch (err) {}
  };

  const handleLocate = () => {
    if (!mapInstance.current) return;
    const L = (window as any).L;
    if (!L || !navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        try {
          const { latitude, longitude } = pos.coords;
          if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;
          const latLng = L.latLng(latitude, longitude);
          mapInstance.current.setView(latLng, 13);
          L.marker(latLng, { icon: makeMarkerIcon(L, "#ef4444", "center", "") })
            .addTo(mapInstance.current)
            .bindPopup("موقعیت شما")
            .openPopup();
        } catch (err) {}
      },
      () => alert("دسترسی به موقعیت مکانی رد شد")
    );
  };

  const handleZoomIn = () => {
    try { mapInstance.current?.zoomIn(); } catch {}
  };

  const handleZoomOut = () => {
    try { mapInstance.current?.zoomOut(); } catch {}
  };

  const handleCardClick = (place: ListingMapPoint) => {
    const idx = validItems.findIndex((p) => p.key === place.key);
    if (idx >= 0) setSelectedIndex(idx);
    onItemClick?.(place);
    if (isMobile) {
      setMobileView("map");
    }
  };

  useEffect(() => {
    const handleKey = (e: KeyboardEvent) => {
      if (e.key === "ArrowDown" && selectedIndex < validItems.length - 1) {
        e.preventDefault();
        setSelectedIndex(selectedIndex + 1);
      }
      if (e.key === "ArrowUp" && selectedIndex > 0) {
        e.preventDefault();
        setSelectedIndex(selectedIndex - 1);
      }
      if (e.key === "Escape" && onClose) onClose();
    };
    window.addEventListener("keydown", handleKey);
    return () => window.removeEventListener("keydown", handleKey);
  }, [selectedIndex, validItems.length, onClose]);

  const btnClass =
    "w-10 h-10 rounded-xl bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer";

  return (
    <div className="w-full h-full flex flex-col bg-white overflow-hidden">
      {/* ═══ Header ═══ */}
      {showHeader && (
        <div className="flex items-center justify-between px-3 sm:px-5 py-2.5 sm:py-3 border-b border-border bg-white shrink-0 z-10">
          <div className="flex items-center gap-2 sm:gap-2.5 min-w-0">
            <MapPin className="w-4 h-4 text-primary flex-shrink-0" />
            <h3 className="font-bold text-text-strong text-sm sm:text-[15px] truncate">
              {title}
            </h3>
            <span className="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] sm:text-xs font-bold whitespace-nowrap">
              {faDigits(filteredItems.length)} {itemLabel}
            </span>
          </div>

          <div className="flex items-center gap-2">
            {isMobile && (
              <div className="flex items-center gap-1 p-0.5 bg-bg-sec rounded-lg">
                <button
                  onClick={() => setMobileView("list")}
                  className={`flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold transition-all ${
                    mobileView === "list"
                      ? "bg-white text-primary shadow-sm"
                      : "text-text-muted"
                  }`}
                >
                  <List className="w-3 h-3" />
                  لیست
                </button>
                <button
                  onClick={() => setMobileView("map")}
                  className={`flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold transition-all ${
                    mobileView === "map"
                      ? "bg-white text-primary shadow-sm"
                      : "text-text-muted"
                  }`}
                >
                  <MapIcon className="w-3 h-3" />
                  نقشه
                </button>
              </div>
            )}

            {onClose && (
              <ModalCloseButton onClick={onClose} />
            )}
          </div>
        </div>
      )}

      {/* ═══ محتوای اصلی ═══ */}
      <div className="flex-1 min-h-0 flex relative">
        
        {/* ═══ پنل لیست ═══ */}
        <div
          className={`${
            isMobile
              ? mobileView === "list" ? "flex" : "hidden"
              : "flex"
          } flex-col w-full sm:w-[380px] lg:w-[420px] xl:w-[460px] shrink-0 sm:border-l border-border bg-bg-sec h-full`}
        >
          {searchable && (
            <div className="p-3 border-b border-border bg-white shrink-0">
              <div className="relative">
                <Search className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="جستجو در نتایج..."
                  className="w-full ps-9 pe-3 py-2 rounded-lg bg-bg-sec border border-border text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                />
              </div>
            </div>
          )}

          <div
            ref={listRef}
            className="flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-3 space-y-2"
            style={{ 
              WebkitOverflowScrolling: "touch",
              overscrollBehavior: "contain"
            }}
          >
            {filteredItems.length > 0 ? (
              filteredItems.map((place) => {
                const idx = validItems.findIndex((p) => p.key === place.key);
                return (
                  <div
                    key={place.key}
                    ref={(el) => {
                      if (el) cardRefs.current.set(place.key, el);
                    }}
                  >
                    <PlaceCard
                      place={place}
                      isActive={idx === selectedIndex}
                      isHovered={hoveredKey === place.key}
                      onHover={setHoveredKey}
                      onClick={handleCardClick}
                      actionLabel={actionLabel}
                    />
                  </div>
                );
              })
            ) : (
              <div className="flex flex-col items-center justify-center py-12 text-center">
                <Search className="w-10 h-10 text-text-subtle mb-3" />
                <p className="text-sm text-text-muted">موردی یافت نشد</p>
                <p className="text-xs text-text-subtle mt-1">
                  عبارت جستجو را تغییر دهید
                </p>
              </div>
            )}
            
            <div className="h-4" />
          </div>
        </div>

        {/* ═══ پنل نقشه ═══ */}
        <div
          className={`${
            isMobile
              ? mobileView === "map" ? "flex" : "hidden"
              : "flex"
          } flex-1 flex-col relative bg-bg-sec min-h-[300px] h-full`}
        >
          <div ref={mapRef} className="absolute inset-0" />

          {!isMapLoaded && (
            <div className="absolute inset-0 flex items-center justify-center bg-bg-sec/80 backdrop-blur-sm z-10">
              <div className="flex flex-col items-center gap-3">
                <div className="w-10 h-10 border-[3px] border-primary border-t-transparent rounded-full animate-spin" />
                <span className="text-xs text-text-muted">بارگذاری نقشه...</span>
              </div>
            </div>
          )}

          {isMapLoaded && (
            <div className="absolute end-3 top-3 z-[400] flex flex-col gap-1.5">
              <button onClick={handleResetView} title="نمایش همه" className={btnClass}>
                <MapPin className="w-4 h-4" />
              </button>
              <button onClick={handleLocate} title="موقعیت من" className={btnClass}>
                <LocateFixed className="w-4 h-4" />
              </button>
              <div className="h-px bg-border/50 my-0.5" />
              <button onClick={handleZoomIn} title="بزرگ‌نمایی" className={btnClass}>
                <ZoomIn className="w-4 h-4" />
              </button>
              <button onClick={handleZoomOut} title="کوچک‌نمایی" className={btnClass}>
                <ZoomOut className="w-4 h-4" />
              </button>
            </div>
          )}

          {/* ═══ Preview کارت موبایل ═══ */}
          {isMobile && isMapLoaded && mobilePreviewKey && (
            <MobileMapPreview
              place={validItems.find((p) => p.key === mobilePreviewKey) || null}
              onClose={() => setMobilePreviewKey(null)}
              actionLabel={actionLabel}
            />
          )}
        </div>
      </div>
    </div>
  );
}