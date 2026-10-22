"use client";
import { useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { MapPin, ZoomIn, ZoomOut, X, LocateFixed } from "lucide-react";
import { loadLeaflet, waitForSize } from "@/lib/map-loader";
import { getMapConfig } from "@/lib/api/map";
import { addTiles, makeMarkerIcon, colorOf } from "@/components/map/map-icons";
import { faDigits } from "@/lib/api/hotels";
import type { ListingMapPoint } from "./types";
import PlaceInfoPanel from "./PlaceInfoPanel";

interface MapModalProps {
  isOpen: boolean;
  onClose: () => void;
  points: ListingMapPoint[];
  onPointClick?: (key: string) => void;
  title?: string;
}

export default function MapModal({
  isOpen,
  onClose,
  points,
  onPointClick,
  title = "نقشه",
}: MapModalProps) {
  const modalMapRef = useRef<HTMLDivElement>(null);
  const modalMapInstance = useRef<any>(null);
  const modalMarkersRef = useRef<Map<string, any>>(new Map());
  const [isLoaded, setIsLoaded] = useState(false);
  const [selectedPlace, setSelectedPlace] = useState<ListingMapPoint | null>(null);
  const [isPanelOpen, setIsPanelOpen] = useState(false);

  /* ═══ راه‌اندازی نقشه ═══ */
  useEffect(() => {
    if (!isOpen) return;
    let cancelled = false;
    let modalMap: any = null;

    (async () => {
      const [L, cfg] = await Promise.all([loadLeaflet(), getMapConfig()]);
      if (cancelled || !modalMapRef.current) return;

      await new Promise((r) => setTimeout(r, 100));
      await waitForSize(modalMapRef.current);
      if (cancelled || !modalMapRef.current) return;

      modalMap = L.map(modalMapRef.current, {
        zoomControl: false,
        attributionControl: false,
      });
      addTiles(L, modalMap, cfg);
      modalMapInstance.current = modalMap;

      const validPts = points.filter(
        (p) =>
          typeof p.lat === "number" &&
          typeof p.lng === "number" &&
          !isNaN(p.lat) &&
          !isNaN(p.lng) &&
          p.lat !== 0 &&
          p.lng !== 0
      );

      if (validPts.length > 0) {
        const latLngs = validPts.map((p) => L.latLng(p.lat, p.lng));
        const bounds = L.latLngBounds(latLngs);
        try {
          modalMap.fitBounds(bounds, { padding: [80, 80], maxZoom: 14 });
        } catch (err) {
          console.warn("[MapModal] fitBounds error:", err);
        }
      }

      validPts.forEach((point) => {
        try {
          const latLng = L.latLng(point.lat, point.lng);
          const marker = L.marker(latLng, {
            icon: makeMarkerIcon(L, colorOf(point.type), point.type, point.locationType || ""),
          })
            .addTo(modalMap)
            .on("click", () => {
              setSelectedPlace(point);
              setIsPanelOpen(true);
              onPointClick?.(point.key);
            });
          modalMarkersRef.current.set(point.key, marker);
        } catch (err) {
          console.warn("[MapModal] marker error:", point.key, err);
        }
      });

      setIsLoaded(true);
    })();

    return () => {
      cancelled = true;
      if (modalMap) {
        modalMap.remove();
        modalMap = null;
      }
      modalMapInstance.current = null;
      modalMarkersRef.current.clear();
      setIsLoaded(false);
      setSelectedPlace(null);
      setIsPanelOpen(false);
    };
  }, [isOpen, points, onPointClick]);

  /* ═══ کنترل‌ها ═══ */
  const handleZoomIn = () => {
    try {
      modalMapInstance.current?.zoomIn();
    } catch (err) {}
  };

  const handleZoomOut = () => {
    try {
      modalMapInstance.current?.zoomOut();
    } catch (err) {}
  };

  const handleResetView = () => {
    if (!modalMapInstance.current) return;
    const L = (window as any).L;
    if (!L) return;

    const validPts = points.filter(
      (p) =>
        typeof p.lat === "number" &&
        typeof p.lng === "number" &&
        !isNaN(p.lat) &&
        !isNaN(p.lng) &&
        p.lat !== 0 &&
        p.lng !== 0
    );
    if (validPts.length === 0) return;

    try {
      const latLngs = validPts.map((p) => L.latLng(p.lat, p.lng));
      const bounds = L.latLngBounds(latLngs);
      modalMapInstance.current.flyToBounds(bounds, {
        padding: [80, 80],
        maxZoom: 14,
        animate: true,
        duration: 0.8,
      });
    } catch (err) {
      console.warn("[MapModal] ResetView error:", err);
    }
  };

  const handleLocate = () => {
    if (!modalMapInstance.current) return;
    const L = (window as any).L;
    if (!L || !navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        try {
          const { latitude, longitude } = pos.coords;
          if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;
          const latLng = L.latLng(latitude, longitude);
          modalMapInstance.current.setView(latLng, 13);
          L.marker(latLng, {
            icon: makeMarkerIcon(L, "#ef4444", "center", ""),
          })
            .addTo(modalMapInstance.current)
            .bindPopup("موقعیت شما")
            .openPopup();
        } catch (err) {
          console.error("[MapModal] Locate error:", err);
        }
      },
      () => alert("دسترسی به موقعیت مکانی رد شد")
    );
  };

  const handleClosePanel = () => {
    setIsPanelOpen(false);
    setTimeout(() => setSelectedPlace(null), 300);
  };

  if (!isOpen || typeof document === "undefined") return null;

  const btnClass =
    "w-11 h-11 rounded-xl bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group";

  return createPortal(
    <div className="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 animate-in fade-in duration-200">
      <div className="relative w-full max-w-[98vw] xl:max-w-[95vw] h-[95vh] sm:h-[92vh] bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-2xl flex flex-col">
        {/* ═══ Header ═══ */}
        <div className="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-border bg-white/95 backdrop-blur-sm z-10">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center shadow-lg shadow-primary/25">
              <MapPin className="w-5 h-5 text-white" />
            </div>
            <div>
              <h3 className="font-extrabold text-text-strong text-sm sm:text-base">{title}</h3>
              <p className="text-[11px] sm:text-xs text-text-muted">
                {faDigits(points.length)} مکان یافت شد
              </p>
            </div>
          </div>
          <button
            type="button"
            onClick={onClose}
            className="w-10 h-10 rounded-xl bg-bg-sec hover:bg-danger/10 hover:text-danger flex items-center justify-center transition-all cursor-pointer group"
            aria-label="بستن"
          >
            <X className="w-5 h-5 group-hover:rotate-90 transition-transform" />
          </button>
        </div>

        {/* ═══ Map Container ═══ */}
        <div className="relative flex-1 bg-bg-sec">
          <div ref={modalMapRef} className="w-full h-full" />

          {/* لودینگ */}
          {!isLoaded && (
            <div className="absolute inset-0 flex items-center justify-center bg-bg-sec/90 backdrop-blur-sm z-20">
              <div className="flex flex-col items-center gap-3">
                <div className="w-10 h-10 border-[3px] border-primary border-t-transparent rounded-full animate-spin" />
                <span className="text-sm text-text-muted font-medium">در حال بارگذاری نقشه...</span>
              </div>
            </div>
          )}

          {/* ═══ کنترل‌ها ═══ */}
          {isLoaded && (
            <div className="absolute start-4 top-4 z-[400] flex flex-col gap-2">
              <button type="button" onClick={handleResetView} title="نمایش همه" className={btnClass}>
                <MapPin className="w-5 h-5 group-hover:scale-110 transition-transform" />
              </button>
              <button type="button" onClick={handleLocate} title="موقعیت من" className={btnClass}>
                <LocateFixed className="w-5 h-5 group-hover:scale-110 transition-transform" />
              </button>
              <div className="h-px bg-border/50 my-1" />
              <button type="button" onClick={handleZoomIn} title="بزرگ‌نمایی" className={btnClass}>
                <ZoomIn className="w-5 h-5 group-hover:scale-110 transition-transform" />
              </button>
              <button type="button" onClick={handleZoomOut} title="کوچک‌نمایی" className={btnClass}>
                <ZoomOut className="w-5 h-5 group-hover:scale-110 transition-transform" />
              </button>
            </div>
          )}

          {/* ═══ پنل اطلاعات مکان ═══ */}
          <PlaceInfoPanel
            place={selectedPlace}
            isOpen={isPanelOpen}
            onClose={handleClosePanel}
          />

          {/* ═══ راهنمای کلیک ═══ */}
          {isLoaded && !isPanelOpen && (
            <div className="absolute bottom-4 start-1/2 -translate-x-1/2 px-4 py-2 rounded-full bg-black/60 backdrop-blur text-white text-xs font-medium z-[400] animate-in fade-in slide-in-from-bottom-2 duration-500">
              روی مارکرها کلیک کنید تا اطلاعات نمایش داده شود
            </div>
          )}
        </div>
      </div>
    </div>,
    document.body
  );
}