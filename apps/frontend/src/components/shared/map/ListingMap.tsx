"use client";

import { useEffect, useRef, useState, useCallback, memo } from "react";
import { createPortal } from "react-dom";
import {
  MapPin, ZoomIn, ZoomOut, Maximize2,
  LocateFixed, Map as MapIcon, X,
} from "lucide-react";
import { loadLeaflet, waitForSize } from "@/lib/map-loader";
import { getMapConfig } from "@/lib/api/map";
import { addTiles, makeMarkerIcon, makeActiveMarkerIcon, colorOf } from "@/components/map/map-icons";
import { faDigits } from "@/lib/api/hotels";

export interface ListingMapPoint {
  key: string;
  lat: number;
  lng: number;
  title: string;
  type: string;
  locationType?: string;
}

interface ListingMapProps {
  points: ListingMapPoint[];
  activeKey?: string | null;
  height?: number;
  onPointClick?: (key: string) => void;
  onPointHover?: (key: string | null) => void;
  className?: string;
}

const MARKER_ZOOM_THRESHOLD = 12;
const HOVER_ZOOM_LEVEL = 15;
const MIN_ZOOM_FOR_MARKERS = 11;

/* ═══ Helper Functions ═══ */
function isValidCoord(value: unknown): value is number {
  if (value === null || value === undefined) return false;
  const num = Number(value);
  return Number.isFinite(num) && num !== 0;
}

function isValidPoint(point: ListingMapPoint | undefined | null): boolean {
  if (!point) return false;
  return isValidCoord(point.lat) && isValidCoord(point.lng);
}

function toNumber(value: unknown): number {
  return Number(value) || 0;
}

const ListingMap = memo(function ListingMap({
  points,
  activeKey = null,
  height = 600,
  onPointClick,
  onPointHover,
  className = "",
}: ListingMapProps) {
  const mapRef = useRef<HTMLDivElement>(null);
  const modalMapRef = useRef<HTMLDivElement>(null);
  const mapInstance = useRef<any>(null);
  const modalMapInstance = useRef<any>(null);
  const markersRef = useRef<Map<string, any>>(new Map());
  const modalMarkersRef = useRef<Map<string, any>>(new Map());
  const [isLoaded, setIsLoaded] = useState(false);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const [fullscreenLoaded, setFullscreenLoaded] = useState(false);
  const [currentZoom, setCurrentZoom] = useState(5);
  const prevActiveKeyRef = useRef<string | null>(null);
  const flyTimeoutRef = useRef<NodeJS.Timeout | null>(null); // ✅ اضافه شود

  const validPoints = useCallback(() => {
    return points.filter(isValidPoint);
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

      const validPts = validPoints();

      if (validPts.length > 0) {
        const latLngs = validPts.map((p) => L.latLng(toNumber(p.lat), toNumber(p.lng)));
        const bounds = L.latLngBounds(latLngs);
        
        try {
          map.fitBounds(bounds, { padding: [50, 50], maxZoom: 11 });
        } catch (err) {
          console.warn('[ListingMap] fitBounds error:', err);
          map.setView([35.6892, 51.389], 5);
        }
      } else {
        map.setView([35.6892, 51.389], 5);
      }

      map.on("zoomend", () => {
        setCurrentZoom(map.getZoom());
      });

      setCurrentZoom(map.getZoom());
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

  /* ═══ مدیریت مارکرها - فقط در zoom کافی ═══ */
  useEffect(() => {
    if (!isLoaded || !mapInstance.current) return;
    const L = (window as any).L;
    if (!L) return;
    const map = mapInstance.current;

    markersRef.current.forEach((m) => {
      try {
        m.remove();
      } catch (err) {
        // ignore
      }
    });
    markersRef.current.clear();

    if (currentZoom < MIN_ZOOM_FOR_MARKERS) {
      return;
    }

    points.forEach((point) => {
      if (!isValidPoint(point)) {
        return; // skip نقاط نامعتبر
      }

      const lat = toNumber(point.lat);
      const lng = toNumber(point.lng);

      try {
        const latLng = L.latLng(lat, lng);
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
        console.warn('[ListingMap] Error creating marker for:', point.key, err);
      }
    });
  }, [points, isLoaded, currentZoom, activeKey, handlePointClick, handlePointHover]);

  /* ═══ هایلایت و زوم هنگام hover روی کارت ═══ */
  useEffect(() => {
    if (!isLoaded || !mapInstance.current) return;
    const map = mapInstance.current;
    const L = (window as any).L;
    if (!L) return;

    if (activeKey !== prevActiveKeyRef.current) {
      prevActiveKeyRef.current = activeKey;

      if (flyTimeoutRef.current) {
        clearTimeout(flyTimeoutRef.current);
        flyTimeoutRef.current = null;
      }

      if (activeKey) {
        const point = points.find((p) => p.key === activeKey);

        if (!point || !isValidPoint(point)) {
          console.warn('[ListingMap] Skipping invalid point:', activeKey);
          return;
        }

        const lat = toNumber(point.lat);
        const lng = toNumber(point.lng);

        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
          console.warn('[ListingMap] Invalid after conversion:', activeKey, { lat, lng });
          return;
        }

        const marker = markersRef.current.get(activeKey);

        try {
          const latLng = L.latLng(lat, lng);
          if (marker) {
            marker.setIcon(makeActiveMarkerIcon(L, colorOf(point.type), point.type, point.locationType || ""));
          } else {
            const newMarker = L.marker(latLng, {
              icon: makeActiveMarkerIcon(L, colorOf(point.type), point.type, point.locationType || ""),
            })
              .addTo(map)
              .on("click", () => handlePointClick(point.key))
              .on("mouseover", () => handlePointHover(point.key))
              .on("mouseout", () => handlePointHover(null));

            markersRef.current.set(point.key, newMarker);
          }
        } catch (err) {
          console.warn('[ListingMap] Icon swap error:', err);
          return;
        }

        const keyForTimeout = activeKey;
        
        flyTimeoutRef.current = setTimeout(() => {
          try {
            const freshPoint = points.find((p) => p.key === keyForTimeout);
            
            if (!freshPoint || !isValidPoint(freshPoint)) {
              return;
            }

            const freshLat = toNumber(freshPoint.lat);
            const freshLng = toNumber(freshPoint.lng);

            if (!Number.isFinite(freshLat) || !Number.isFinite(freshLng)) {
              return;
            }

            const latLng = L.latLng(freshLat, freshLng);
            map.flyTo(latLng, HOVER_ZOOM_LEVEL, {
              animate: true,
              duration: 0.8,
              easeLinearity: 0.25,
            });
          } catch (err) {
          }
          flyTimeoutRef.current = null;
        }, 100);

      } else {
        markersRef.current.forEach((marker, key) => {
          const point = points.find((p) => p.key === key);
          if (point && isValidPoint(point)) {
            try {
              marker.setIcon(makeMarkerIcon(L, colorOf(point.type), point.type, point.locationType || ""));
            } catch (err) {
              // ignore
            }
          }
        });
      }
    }
  }, [activeKey, isLoaded, points, handlePointClick, handlePointHover]);

  useEffect(() => {
    return () => {
      if (flyTimeoutRef.current) {
        clearTimeout(flyTimeoutRef.current);
      }
    };
  }, []);

  /* ═══ نقشه Fullscreen ═══ */
  useEffect(() => {
    if (!isFullscreen) return;
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

      const validPts = validPoints();

      if (validPts.length > 0) {
        const latLngs = validPts.map((p) => L.latLng(toNumber(p.lat), toNumber(p.lng)));
        const bounds = L.latLngBounds(latLngs);
        
        try {
          modalMap.fitBounds(bounds, { padding: [80, 80], maxZoom: 14 });
        } catch (err) {
          console.warn('[ListingMap] Modal fitBounds error:', err);
        }
      }

      validPts.forEach((point) => {
        try {
          const latLng = L.latLng(toNumber(point.lat), toNumber(point.lng));
          const marker = L.marker(latLng, {
            icon: makeMarkerIcon(L, colorOf(point.type), point.type, point.locationType || ""),
          })
            .addTo(modalMap)
            .on("click", () => handlePointClick(point.key));
          modalMarkersRef.current.set(point.key, marker);
        } catch (err) {
          console.warn('[ListingMap] Modal marker error:', point.key, err);
        }
      });

      setFullscreenLoaded(true);
    })();

    return () => {
      cancelled = true;
      if (modalMap) {
        modalMap.remove();
        modalMap = null;
      }
      modalMapInstance.current = null;
      modalMarkersRef.current.clear();
      setFullscreenLoaded(false);
    };
  }, [isFullscreen, validPoints, handlePointClick]);

  const handleZoomIn = () => {
    try {
      mapInstance.current?.zoomIn();
    } catch (err) {
      // ignore
    }
  };
  
  const handleZoomOut = () => {
    try {
      mapInstance.current?.zoomOut();
    } catch (err) {
      // ignore
    }
  };
  
  const handleLocate = () => {
    if (!mapInstance.current) return;
    const L = (window as any).L;
    if (!L || !navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        try {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;
          
          if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            alert("موقعیت مکانی نامعتبر است");
            return;
          }
          
          const latLng = L.latLng(lat, lng);
          mapInstance.current.setView(latLng, 13);
          
          L.marker(latLng, {
            icon: makeMarkerIcon(L, "#ef4444", "center", ""),
          })
            .addTo(mapInstance.current)
            .bindPopup("موقعیت شما")
            .openPopup();
        } catch (err) {
          console.error('[ListingMap] Locate error:', err);
          alert("خطا در دریافت موقعیت مکانی");
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
      const latLngs = validPts.map((p) => L.latLng(toNumber(p.lat), toNumber(p.lng)));
      const bounds = L.latLngBounds(latLngs);
      mapInstance.current.flyToBounds(bounds, { 
        padding: [50, 50], 
        maxZoom: 11,
        animate: true,
        duration: 0.8,
      });
    } catch (err) {
      console.warn('[ListingMap] ResetView error:', err);
    }
  };

  const compact = height < 250;
  const btnSize = compact ? "w-8 h-8" : "w-10 h-10";
  const btnRadius = compact ? "rounded-lg" : "rounded-xl";
  const iconSize = compact ? "w-3.5 h-3.5" : "w-4 h-4";
  const controlGap = compact ? "gap-1.5" : "gap-2";
  const controlPos = compact ? "start-3 top-3" : "start-4 top-4";

  const validCount = validPoints().length;
  const showZoomHint = currentZoom < MIN_ZOOM_FOR_MARKERS && validCount > 0;

  return (
    <>
      <div className={`relative ${className}`} style={{ height }}>
        <div ref={mapRef} className="w-full h-full rounded-md bg-bg-sec" />

        {!isLoaded && (
          <div className="absolute inset-0 flex items-center justify-center bg-bg-sec rounded-xl">
            <div className="flex flex-col items-center gap-3">
              <div className="w-10 h-10 border-3 border-primary border-t-transparent rounded-full animate-spin" />
              <span className="text-sm text-text-muted font-medium">
                در حال بارگذاری نقشه...
              </span>
            </div>
          </div>
        )}

        {/* کنترل‌های نقشه */}
        {isLoaded && (
          <div className={`absolute ${controlPos} z-[400] flex flex-col ${controlGap}`}>
            <button
              type="button"
              onClick={handleResetView}
              className={`${btnSize} ${btnRadius} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
              title="نمایش همه هتل‌ها"
            >
              <MapIcon className={`${iconSize} group-hover:scale-110 transition-transform`} />
            </button>

            <button
              type="button"
              onClick={handleLocate}
              className={`${btnSize} ${btnRadius} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
              title="موقعیت من"
            >
              <LocateFixed className={`${iconSize} group-hover:scale-110 transition-transform`} />
            </button>

            <div className="h-px bg-border/50 my-1" />

            <button
              type="button"
              onClick={handleZoomIn}
              className={`${btnSize} ${btnRadius} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
              title="بزرگ‌نمایی"
            >
              <ZoomIn className={`${iconSize} group-hover:scale-110 transition-transform`} />
            </button>

            <button
              type="button"
              onClick={handleZoomOut}
              className={`${btnSize} ${btnRadius} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
              title="کوچک‌نمایی"
            >
              <ZoomOut className={`${iconSize} group-hover:scale-110 transition-transform`} />
            </button>
          </div>
        )}

        {/* دکمه تمام‌صفحه */}
        {isLoaded && (
          <button
            type="button"
            onClick={() => setIsFullscreen(true)}
            className={`absolute ${compact ? "bottom-3 end-3" : "bottom-4 end-4"} z-[400] flex items-center gap-2 ${compact ? "px-3 py-2" : "px-4 py-2.5"} ${compact ? "rounded-lg" : "rounded-xl"} bg-primary hover:bg-primary-dark text-white shadow-lg hover:shadow-xl transition-all font-bold ${compact ? "text-[10px]" : "text-xs"} cursor-pointer group`}
          >
            <Maximize2 className={`${iconSize} group-hover:scale-110 transition-transform`} />
            نقشه تمام‌صفحه
          </button>
        )}

      </div>

      {/* مودال Fullscreen */}
      {isFullscreen && typeof document !== "undefined" && createPortal(
        <div className="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-200">
          <div className="relative w-full max-w-7xl h-[90vh] bg-white rounded-lg overflow-hidden shadow-2xl flex flex-col">
            <div className="flex items-center justify-between p-4 border-b border-border bg-white">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                  <MapPin className="w-5 h-5 text-primary" />
                </div>
                <div>
                  <h3 className="font-extrabold text-text-strong">نقشه هتل‌ها</h3>
                  <p className="text-xs text-text-muted">
                    {faDigits(validCount)} هتل یافت شد
                  </p>
                </div>
              </div>
              <button
                type="button"
                onClick={() => setIsFullscreen(false)}
                className="w-10 h-10 rounded-lg bg-bg-sec hover:bg-danger/10 hover:text-danger flex items-center justify-center transition-colors cursor-pointer"
                aria-label="بستن"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div ref={modalMapRef} className="flex-1 bg-bg-sec" />

            {fullscreenLoaded && (
              <div className="absolute start-6 top-20 z-[400] flex flex-col gap-2">
                <button
                  type="button"
                  onClick={() => {
                    try {
                      modalMapInstance.current?.zoomIn();
                    } catch (err) {
                      // ignore
                    }
                  }}
                  className="w-11 h-11 rounded-lg bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white flex items-center justify-center text-text-strong hover:text-primary cursor-pointer"
                >
                  <ZoomIn className="w-5 h-5" />
                </button>
                <button
                  type="button"
                  onClick={() => {
                    try {
                      modalMapInstance.current?.zoomOut();
                    } catch (err) {
                      // ignore
                    }
                  }}
                  className="w-11 h-11 rounded-lg bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white flex items-center justify-center text-text-strong hover:text-primary cursor-pointer"
                >
                  <ZoomOut className="w-5 h-5" />
                </button>
              </div>
            )}
          </div>
        </div>,
        document.body
      )}
    </>
  );
});

export default ListingMap;