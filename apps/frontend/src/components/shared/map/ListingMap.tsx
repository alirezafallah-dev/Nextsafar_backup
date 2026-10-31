"use client";

import { useEffect, useRef, useState, useCallback, memo } from "react";
import { MapPin } from "lucide-react";
import { loadLeaflet, waitForSize } from "@/lib/map-loader";
import { getMapConfig } from "@/lib/api/map";
import {
  addTiles,
  makeMarkerIcon,
  makeActiveMarkerIcon,
  colorOf,
} from "@/components/map/map-icons";
import type { ListingMapPoint } from "./types";
import MapControls from "./MapControls";
import MapModal from "./MapModal";
import MapLoading from "./MapLoading";

const HOVER_ZOOM_LEVEL = 15;
const MIN_ZOOM_FOR_MARKERS = 10; // ✅ حداقل زوم برای نمایش آیکون‌ها

interface ListingMapProps {
  points: ListingMapPoint[];
  activeKey?: string | null;
  height?: number;
  onPointClick?: (key: string) => void;
  onPointHover?: (key: string | null) => void;
  className?: string;
  /** نمایش دکمه "نمایش روی نقشه" - پیش‌فرض: true */
  showFullscreenButton?: boolean;
}

const ListingMap = memo(function ListingMap({
  points,
  activeKey = null,
  height = 600,
  onPointClick,
  onPointHover,
  className = "",
  showFullscreenButton = true, // ✅ prop جدید با مقدار پیش‌فرض
}: ListingMapProps) {
  const mapRef = useRef<HTMLDivElement>(null);
  const mapInstance = useRef<any>(null);
  const markersRef = useRef<Map<string, any>>(new Map());
  const [isLoaded, setIsLoaded] = useState(false);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const [currentZoom, setCurrentZoom] = useState(0); // ✅ زوم فعلی
  const prevActiveKeyRef = useRef<string | null>(null);
  const flyTimeoutRef = useRef<NodeJS.Timeout | null>(null);

  const validPoints = useCallback(() => {
    return points.filter(
      (p) =>
        typeof p.lat === "number" &&
        typeof p.lng === "number" &&
        !isNaN(p.lat) &&
        !isNaN(p.lng) &&
        p.lat !== 0 &&
        p.lng !== 0
    );
  }, [points]);

  const handlePointClick = useCallback(
    (key: string) => onPointClick?.(key),
    [onPointClick]
  );

  const handlePointHover = useCallback(
    (key: string | null) => onPointHover?.(key),
    [onPointHover]
  );

  /* ═══ راه‌اندازی نقشه اصلی ═══ */
  useEffect(() => {
    let cancelled = false;
    let map: any = null;

    (async () => {
      const [L, cfg] = await Promise.all([loadLeaflet(), getMapConfig()]);
      if (cancelled || !mapRef.current) return;

      await waitForSize(mapRef.current);
      if (cancelled || !mapRef.current) return;

      map = L.map(mapRef.current, {
        zoomControl: false,
        attributionControl: false,
      });
      addTiles(L, map, cfg);
      mapInstance.current = map;

      // ✅ گوش‌دهنده تغییر زوم
      map.on("zoomend", () => {
        setCurrentZoom(map.getZoom());
      });

      const validPts = validPoints();
      if (validPts.length > 0) {
        const latLngs = validPts.map((p) => L.latLng(p.lat, p.lng));
        const bounds = L.latLngBounds(latLngs);
        try {
          map.fitBounds(bounds, { padding: [50, 50], maxZoom: 11 });
          setCurrentZoom(map.getZoom()); // ✅ ست زوم اولیه
        } catch (err) {
          console.warn("[ListingMap] fitBounds error:", err);
          map.setView([35.6892, 51.389], 5);
        }
      } else {
        map.setView([35.6892, 51.389], 5);
      }

      setIsLoaded(true);
    })();

    return () => {
      cancelled = true;
      if (map) {
        map.remove();
        map = null;
      }
      mapInstance.current = null;
      markersRef.current.clear();
    };
  }, [validPoints]);

  /* ═══ مدیریت مارکرها (وابسته به زوم) ═══ */
  useEffect(() => {
    if (!isLoaded || !mapInstance.current) return;
    const L = (window as any).L;
    if (!L) return;
    const map = mapInstance.current;

    // پاک کردن مارکرهای قبلی
    markersRef.current.forEach((m) => {
      try {
        m.remove();
      } catch (err) {}
    });
    markersRef.current.clear();

    // ✅ فقط وقتی زوم >= حداقل، آیکون‌ها نمایش داده بشن
    if (currentZoom < MIN_ZOOM_FOR_MARKERS) return;

    points.forEach((point) => {
      if (
        typeof point.lat !== "number" ||
        typeof point.lng !== "number" ||
        isNaN(point.lat) ||
        isNaN(point.lng)
      )
        return;
      try {
        const latLng = L.latLng(point.lat, point.lng);
        const isActive = point.key === activeKey;
        const icon = isActive
          ? makeActiveMarkerIcon(L, colorOf(point.type), point.type, point.locationType || "")
          : makeMarkerIcon(L, colorOf(point.type), point.type, point.locationType || "");
        const marker = L.marker(latLng, { icon })
          .addTo(map)
          .on("click", () => handlePointClick(point.key))
          .on("mouseover", () => handlePointHover(point.key))
          .on("mouseout", () => handlePointHover(null));
        markersRef.current.set(point.key, marker);
      } catch (err) {
        console.warn("[ListingMap] Error creating marker:", point.key, err);
      }
    });
  }, [points, isLoaded, activeKey, currentZoom, handlePointClick, handlePointHover]);

  /* ═══ هایلایت و زوم هنگام هاور روی کارت ═══ */
  useEffect(() => {
    if (!isLoaded || !mapInstance.current) return;
    const map = mapInstance.current;
    const L = (window as any).L;
    if (!L) return;

    if (activeKey === prevActiveKeyRef.current) return;
    prevActiveKeyRef.current = activeKey;

    // پاک کردن تایمر قبلی
    if (flyTimeoutRef.current) {
      clearTimeout(flyTimeoutRef.current);
      flyTimeoutRef.current = null;
    }

    if (activeKey) {
      const point = points.find((p) => p.key === activeKey);
      if (!point) return;

      // ✅ تغییر فوری آیکون مارکر (اگه مارکر وجود داشته باشه)
      const marker = markersRef.current.get(activeKey);
      if (marker) {
        try {
          marker.setIcon(
            makeActiveMarkerIcon(L, colorOf(point.type), point.type, point.locationType || "")
          );
        } catch (err) {
          console.warn("[ListingMap] Icon swap error:", err);
        }
      }

      // ✅ زوم به موقعیت هتل بعد از ۱ ثانیه
      const keyForTimeout = activeKey;
      flyTimeoutRef.current = setTimeout(() => {
        try {
          const freshPoint = points.find((p) => p.key === keyForTimeout);
          if (!freshPoint) return;
          const latLng = L.latLng(freshPoint.lat, freshPoint.lng);
          map.flyTo(latLng, HOVER_ZOOM_LEVEL, {
            animate: true,
            duration: 0.8,
            easeLinearity: 0.25,
          });
        } catch (err) {}
        flyTimeoutRef.current = null;
      }, 1000);
    } else {
      // ✅ برگشت آیکون‌ها به حالت عادی
      markersRef.current.forEach((marker, key) => {
        const point = points.find((p) => p.key === key);
        if (point) {
          try {
            marker.setIcon(
              makeMarkerIcon(L, colorOf(point.type), point.type, point.locationType || "")
            );
          } catch (err) {}
        }
      });
    }
  }, [activeKey, isLoaded, points]);

  // ✅ پاک کردن تایمر هنگام حذف کامپوننت
  useEffect(() => {
    return () => {
      if (flyTimeoutRef.current) clearTimeout(flyTimeoutRef.current);
    };
  }, []);

  /* ═══ کنترل‌ها ═══ */
  const handleZoomIn = () => {
    try {
      mapInstance.current?.zoomIn();
    } catch (err) {}
  };

  const handleZoomOut = () => {
    try {
      mapInstance.current?.zoomOut();
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
          L.marker(latLng, {
            icon: makeMarkerIcon(L, "#ef4444", "center", ""),
          })
            .addTo(mapInstance.current)
            .bindPopup("موقعیت شما")
            .openPopup();
        } catch (err) {
          console.error("[ListingMap] Locate error:", err);
        }
      },
      () => alert("دسترسی به موقعیت مکانی رد شد")
    );
  };

  const handleResetView = () => {
    if (!mapInstance.current) return;
    const L = (window as any).L;
    if (!L) return;
    const validPts = validPoints();
    if (validPts.length === 0) return;
    try {
      const latLngs = validPts.map((p) => L.latLng(p.lat, p.lng));
      const bounds = L.latLngBounds(latLngs);
      mapInstance.current.flyToBounds(bounds, {
        padding: [50, 50],
        maxZoom: 11,
        animate: true,
        duration: 0.8,
      });
    } catch (err) {
      console.warn("[ListingMap] ResetView error:", err);
    }
  };

  const compact = height < 250;

  return (
    <>
      <div className={`relative ${className}`} style={{ height }}>
        <div ref={mapRef} className="w-full h-full rounded-md bg-bg-sec" />

        {!isLoaded && <MapLoading />}

        {isLoaded && (
          <MapControls
            onResetView={handleResetView}
            onLocate={handleLocate}
            onZoomIn={handleZoomIn}
            onZoomOut={handleZoomOut}
            compact={compact}
          />
        )}

        {/* ✅ دکمه نمایش روی نقشه — شرطی با showFullscreenButton */}
        {isLoaded && showFullscreenButton && (
          <button
            type="button"
            onClick={() => setIsFullscreen(true)}
            className="absolute bottom-3 left-1/2 -translate-x-1/2 z-[500]
                      inline-flex items-center gap-1.5
                      px-4 py-2 rounded-full
                      bg-white border border-border/60
                      text-[11px] font-bold text-text-strong
                      hover:border-primary hover:text-primary
                      shadow-md hover:shadow-lg
                      transition-all duration-200"
          >
            <MapPin className="w-3.5 h-3.5 text-primary" />
            نمایش روی نقشه
          </button>
        )}
      </div>

      {/* مودال تمام‌صفحه */}
      <MapModal
        isOpen={isFullscreen}
        onClose={() => setIsFullscreen(false)}
        points={points}
        onPointClick={handlePointClick}
      />
    </>
  );
});

export default ListingMap;