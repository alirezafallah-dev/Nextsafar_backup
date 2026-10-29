"use client";

import { useEffect, useRef, useState } from "react";
import { loadLeaflet, waitForSize, scheduleFix } from "@/lib/map-loader";
import { getMapConfig } from "@/lib/api/map";
import { addTiles } from "./map-icons";
import { MAP_PRESETS, type MapPreset } from "./presets";
import { renderMarker, renderPopup, type MapPoint } from "./renderers";
import { MapControls } from "./MapControls";
import { MapFullscreenModal } from "./MapFullscreenModal";

export interface NextSafarMapProps {
  preset?: MapPreset;
  points: MapPoint[];
  center?: { lat: number; lng: number } | null;
  activeKey?: string | null;
  hoverKey?: string | null;
  height?: number | string;
  className?: string;
  zoom?: number;
  maxZoom?: number;
  padding?: [number, number];
  fitBounds?: boolean;
  initialCenter?: [number, number];
  scrollWheelZoom?: boolean;
  dragging?: boolean;
  interactive?: boolean;
  showControls?: boolean;
  showFullscreen?: boolean;
  markerStyle?: "pill" | "numbered" | "category";
  flyOnActive?: boolean;
  hoverOnlyStyle?: boolean;
  refitSignal?: number;
  onPointClick?: (key: string) => void;
  onPointHover?: (key: string | null) => void;
}

const MAP_CSS = `
.ns-map .leaflet-bar{border:none!important;box-shadow:none!important;}
.ns-map .leaflet-control-zoom{display:none!important;}
.ns-map .leaflet-control-attribution{font-size:9px!important;background:rgba(255,255,255,.7)!important;border-radius:6px 0 0 0;}
`;

export default function NextSafarMap(props: NextSafarMapProps) {
  const presetConfig = props.preset ? MAP_PRESETS[props.preset] : {};
  const config = { ...presetConfig, ...props };

  const {
    points,
    center,
    activeKey = null,
    hoverKey = null,
    height = 400,
    className = "",
    zoom = 5,
    maxZoom = 14,
    padding = [50, 50],
    fitBounds = true,
    initialCenter,
    scrollWheelZoom = false,
    dragging = true,
    interactive = true,
    showControls = false,
    showFullscreen = false,
    markerStyle = "category",
    flyOnActive = false,
    hoverOnlyStyle = false,
    refitSignal = 0,
    onPointClick,
    onPointHover,
  } = config;

  const ref = useRef<HTMLDivElement>(null);
  const LRef = useRef<any>(null);
  const mapRef = useRef<any>(null);
  const markersRef = useRef<Map<string, any>>(new Map());
  const pointsRef = useRef<MapPoint[]>(points);
  const [isLoaded, setIsLoaded] = useState(false);
  const [isFullscreen, setIsFullscreen] = useState(false);

  useEffect(() => {
    pointsRef.current = points;
  }, [points]);

  /* ═══ Init نقشه (یک‌بار) ═══ */
  useEffect(() => {
    let cancelled = false;
    let cancelFix: (() => void) | null = null;
    let ro: ResizeObserver | null = null;

    (async () => {
      const [L, cfg] = await Promise.all([loadLeaflet(), getMapConfig()]);
      if (cancelled || !ref.current) return;
      await waitForSize(ref.current);
      if (cancelled || !ref.current) return;

      const validPoints = pointsRef.current.filter((p) => p.lat && p.lng);
      const defaultCenter: [number, number] =
        initialCenter ||
        (center ? [center.lat, center.lng] : [35.7, 51.4]);

      const map = L.map(ref.current, {
        center: defaultCenter,
        zoom,
        attributionControl: true,
        scrollWheelZoom: interactive ? scrollWheelZoom : false,
        zoomControl: false,
        dragging: interactive ? dragging : false,
        doubleClickZoom: interactive,
        boxZoom: interactive,
        touchZoom: interactive,
      });

      addTiles(L, map, cfg);
      LRef.current = L;
      mapRef.current = map;

      if (fitBounds && validPoints.length > 0) {
        const bounds = L.latLngBounds(
          validPoints.map((p) => [p.lat, p.lng]),
        );
        map.fitBounds(bounds, { padding, maxZoom, animate: true });
      }

      const onResize = () => {
        try {
          map.invalidateSize();
        } catch {}
      };
      if (typeof ResizeObserver !== "undefined" && ref.current) {
        ro = new ResizeObserver(onResize);
        ro.observe(ref.current);
      }

      cancelFix = scheduleFix(map);
      setIsLoaded(true);
    })();

    return () => {
      cancelled = true;
      cancelFix?.();
      ro?.disconnect();
      mapRef.current?.remove();
      mapRef.current = null;
      markersRef.current.clear();
      setIsLoaded(false);
    };
  }, []);

  /* ═══ مدیریت مارکرها - ✅ بهبود cleanup ═══ */
  useEffect(() => {
    const L = LRef.current;
    const map = mapRef.current;
    if (!L || !map || !isLoaded) return;

    const validPoints = points.filter((p) => p.lat && p.lng);
    const validPointKeys = new Set(validPoints.map(p => p.key));

    // ✅ حذف مارکرهای قدیمی که دیگر در لیست نیستند
    const markersToRemove: string[] = [];
    markersRef.current.forEach((marker, key) => {
      if (!validPointKeys.has(key)) {
        marker.remove();
        markersToRemove.push(key);
      }
    });
    markersToRemove.forEach(key => markersRef.current.delete(key));

    // اضافه یا آپدیت مارکرها
    validPoints.forEach((point, idx) => {
      const isActive = point.key === activeKey;
      const isCenterMarker = center && point.lat === center.lat && point.lng === center.lng;
      const icon = renderMarker(L, markerStyle, point, idx, isActive, !!isCenterMarker);
      
      let marker = markersRef.current.get(point.key);
      if (marker) {
        marker.setIcon(icon);
      } else {
        marker = L.marker([point.lat, point.lng], { icon }).addTo(map);
        const popupContent = renderPopup(markerStyle, point);
        if (popupContent) {
          marker.bindPopup(popupContent, { closeButton: false });
        }
        marker.on("click", () => onPointClick?.(point.key));
        marker.on("mouseover", function (this: any) {
          this.openPopup();
          onPointHover?.(point.key);
        });
        marker.on("mouseout", function (this: any) {
          this.closePopup();
          onPointHover?.(null);
        });
        markersRef.current.set(point.key, marker);
      }
    });

    if (fitBounds && validPoints.length > 0) {
      const bounds = L.latLngBounds(validPoints.map((p) => [p.lat, p.lng]));
      map.fitBounds(bounds, { padding, maxZoom, animate: true });
    }
  }, [points, isLoaded, markerStyle, activeKey, center, fitBounds, padding, maxZoom, onPointClick, onPointHover]);

  /* ═══ هایلایت / hover ═══ */
  useEffect(() => {
    const L = LRef.current;
    const map = mapRef.current;
    if (!L || !map || !isLoaded) return;

    markersRef.current.forEach((marker, key) => {
      const point = pointsRef.current.find((p) => p.key === key);
      if (!point) return;

      const isActive = key === activeKey;
      const isHovered = key === hoverKey;

      if (markerStyle === "pill") {
        const icon = renderMarker(L, markerStyle, point, 0, isActive);
        marker.setIcon(icon);
        marker.setZIndexOffset(isActive ? 1000 : 0);
      } else {
        const el = marker.getElement();
        if (!el) return;

        if (isActive) {
          el.style.transform = "scale(1.5) translateY(-5px)";
          el.style.zIndex = "1000";
          el.style.filter = "drop-shadow(0 8px 12px rgba(0,0,0,0.25))";
          el.style.transition = "all 0.25s cubic-bezier(0.4, 0, 0.2, 1)";
          if (!hoverOnlyStyle) marker.openPopup();
        } else if (isHovered) {
          el.style.transform = "scale(1.15)";
          el.style.zIndex = "500";
          el.style.filter = "drop-shadow(0 3px 8px rgba(0,0,0,0.45)) saturate(1.2) brightness(1.05)";
          el.style.transition = "all 0.15s ease";
        } else {
          el.style.transform = "scale(1)";
          el.style.zIndex = "1";
          el.style.filter = "drop-shadow(0 2px 4px rgba(0,0,0,0.1))";
          el.style.transition = "all 0.25s cubic-bezier(0.4, 0, 0.2, 1)";
        }
      }
    });

    if (flyOnActive && activeKey && !hoverOnlyStyle) {
      const marker = markersRef.current.get(activeKey);
      const point = pointsRef.current.find((p) => p.key === activeKey);
      if (marker && point) {
        map.flyTo([point.lat, point.lng], Math.max(map.getZoom(), 15), {
          duration: 0.8,
        });
        marker.openPopup();
      }
    }
  }, [activeKey, hoverKey, isLoaded, markerStyle, flyOnActive, hoverOnlyStyle]);

  /* ═══ Refit signal ═══ */
  useEffect(() => {
    const L = LRef.current;
    const map = mapRef.current;
    if (!L || !map || !isLoaded || refitSignal === 0) return;

    const validPoints = pointsRef.current.filter((p) => p.lat && p.lng);
    if (validPoints.length > 0) {
      const bounds = L.latLngBounds(validPoints.map((p) => [p.lat, p.lng]));
      map.fitBounds(bounds, { padding, maxZoom });
    }
  }, [refitSignal, isLoaded, padding, maxZoom]);

  /* ═══ کنترل‌ها ═══ */
  const handleZoomIn = () => mapRef.current?.zoomIn();
  const handleZoomOut = () => mapRef.current?.zoomOut();
  const handleLocate = () => {
    if (!mapRef.current || !navigator.geolocation) return;
    const L = LRef.current;
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const { latitude, longitude } = pos.coords;
        mapRef.current.setView([latitude, longitude], 13);
        const icon = L.divIcon({
          className: "",
          html: `<div style="width:20px;height:20px;background:#ef4444;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>`,
          iconSize: [20, 20],
          iconAnchor: [10, 10],
        });
        L.marker([latitude, longitude], { icon })
          .addTo(mapRef.current)
          .bindPopup("موقعیت شما")
          .openPopup();
      },
      () => {},
    );
  };
  const handleResetView = () => {
    if (!mapRef.current) return;
    const L = LRef.current;
    const validPoints = pointsRef.current.filter((p) => p.lat && p.lng);
    if (validPoints.length > 0) {
      const bounds = L.latLngBounds(validPoints.map((p) => [p.lat, p.lng]));
      mapRef.current.fitBounds(bounds, { padding, maxZoom });
    }
  };

  const compact = typeof height === "number" && height < 250;

  return (
    <>
      <style>{MAP_CSS}</style>
      <div className={`relative ${className}`} style={{ height }}>
        <div
          ref={ref}
          className="ns-map w-full h-full relative z-0 bg-bg-sec rounded-md"
          aria-label="نقشه"
        />

        {!isLoaded && (
          <div className="absolute inset-0 flex items-center justify-center bg-bg-sec rounded-md">
            <div className="flex flex-col items-center gap-3">
              <div className="w-10 h-10 border-3 border-primary border-t-transparent rounded-full animate-spin" />
              <span className="text-sm text-text-muted font-medium">
                در حال بارگذاری نقشه...
              </span>
            </div>
          </div>
        )}

        {isLoaded && showControls && (
          <MapControls
            compact={compact}
            onZoomIn={handleZoomIn}
            onZoomOut={handleZoomOut}
            onLocate={handleLocate}
            onResetView={handleResetView}
            onFullscreen={() => setIsFullscreen(true)}
            showFullscreen={showFullscreen}
          />
        )}
      </div>

      {showFullscreen && (
        <MapFullscreenModal
          open={isFullscreen}
          onClose={() => setIsFullscreen(false)}
          points={points}
          markerStyle={markerStyle}
          maxZoom={maxZoom}
        />
      )}
    </>
  );
}