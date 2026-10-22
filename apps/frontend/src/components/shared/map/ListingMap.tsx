"use client";

import { useMemo, memo } from "react";
import NextSafarMap from "@/components/map/NextSafarMap";
import type { MapPoint } from "@/components/map/renderers";

export interface ListingMapPoint {
  key: string;
  lat: number;
  lng: number;
  title: string;
  price?: number | null;
  isSite?: boolean;
  href?: string | null;
  type?: string;
  locationType?: string;
}

interface ListingMapProps {
  points: ListingMapPoint[];
  height?: number | string;
  className?: string;
  activeKey?: string | null;
  onPointClick?: (key: string) => void;
  onPointHover?: (key: string | null) => void;
  showControls?: boolean;
  showFullscreen?: boolean;
  fitBounds?: boolean;
  markerStyle?: "pill" | "numbered" | "category";
}

/**
 * کامپوننت نقشه برای لیست‌ها (هتل‌ها، رستوران‌ها و ...)
 * از NextSafarMap استفاده می‌کند برای یکپارچگی کامل
 */
const ListingMap = memo(function ListingMap({
  points,
  height = 300,
  className = "",
  activeKey = null,
  onPointClick,
  onPointHover,
  showControls = false,
  showFullscreen = false,
  fitBounds = true,
  markerStyle = "pill",
}: ListingMapProps) {
  // تبدیل ListingMapPoint به MapPoint
  const mapPoints: MapPoint[] = useMemo(
    () =>
      points.map((p) => ({
        key: p.key,
        lat: p.lat,
        lng: p.lng,
        title: p.title,
        price: p.price,
        isSite: p.isSite,
        href: p.href,
        type: p.type || "hotel",
        locationType: p.locationType || "",
      })),
    [points]
  );

  if (mapPoints.length === 0) {
    return (
      <div
        className={`ns-map bg-bg-sec flex items-center justify-center ${className}`}
        style={{ height: typeof height === "number" ? height : height }}
      >
        <span className="text-sm text-text-muted">مکانی یافت نشد</span>
      </div>
    );
  }

  return (
    <NextSafarMap
      points={mapPoints}
      height={height}
      className={className}
      activeKey={activeKey}
      onPointClick={onPointClick}
      onPointHover={onPointHover}
      showControls={showControls}
      showFullscreen={showFullscreen}
      fitBounds={fitBounds}
      markerStyle={markerStyle}
      zoom={5}
      maxZoom={14}
      scrollWheelZoom={false}
      dragging={true}
    />
  );
});

export default ListingMap;