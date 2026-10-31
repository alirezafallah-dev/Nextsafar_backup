"use client";

import { MapPin, ZoomIn, ZoomOut, LocateFixed } from "lucide-react";

interface MapControlsProps {
  onResetView: () => void;
  onLocate: () => void;
  onZoomIn: () => void;
  onZoomOut: () => void;
  compact?: boolean;
}

export default function MapControls({
  onResetView,
  onLocate,
  onZoomIn,
  onZoomOut,
  compact = false,
}: MapControlsProps) {
  const btnSize = compact ? "w-8 h-8" : "w-10 h-10";
  const btnRadius = compact ? "rounded-md" : "rounded-md";
  const iconSize = compact ? "w-3.5 h-3.5" : "w-4 h-4";
  const controlGap = compact ? "gap-1.5" : "gap-2";
  const controlPos = compact ? "start-3 top-3" : "start-4 top-4";

  const btnClass = `${btnSize} ${btnRadius} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`;

  return (
    <div className={`absolute ${controlPos} z-[400] flex flex-col ${controlGap}`}>
      {/* نمایش همه */}
      <button
        type="button"
        onClick={onResetView}
        className={btnClass}
        title="نمایش همه"
      >
        <MapPin className={`${iconSize} group-hover:scale-110 transition-transform`} />
      </button>

      {/* موقعیت من */}
      <button
        type="button"
        onClick={onLocate}
        className={btnClass}
        title="موقعیت من"
      >
        <LocateFixed className={`${iconSize} group-hover:scale-110 transition-transform`} />
      </button>

      {/* جداکننده */}
      <div className="h-px bg-border/50 my-1" />

      {/* زوم */}
      <button
        type="button"
        onClick={onZoomIn}
        className={btnClass}
        title="بزرگ‌نمایی"
      >
        <ZoomIn className={`${iconSize} group-hover:scale-110 transition-transform`} />
      </button>
      <button
        type="button"
        onClick={onZoomOut}
        className={btnClass}
        title="کوچک‌نمایی"
      >
        <ZoomOut className={`${iconSize} group-hover:scale-110 transition-transform`} />
      </button>
    </div>
  );
}