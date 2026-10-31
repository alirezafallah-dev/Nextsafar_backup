"use client";

import {
  Map as MapIcon,
  LocateFixed,
  ZoomIn,
  ZoomOut,
  Maximize2,
} from "lucide-react";

interface MapControlsProps {
  compact?: boolean;
  onZoomIn: () => void;
  onZoomOut: () => void;
  onLocate: () => void;
  onResetView: () => void;
  onFullscreen: () => void;
  showFullscreen?: boolean;
}

export function MapControls({
  compact = false,
  onZoomIn,
  onZoomOut,
  onLocate,
  onResetView,
  onFullscreen,
  showFullscreen = true,
}: MapControlsProps) {
  const btn = compact ? "w-8 h-8 rounded-md" : "w-8 h-8 rounded-md";
  const icon = compact ? "w-3.5 h-3.5" : "w-4 h-4";
  const gap = compact ? "gap-1.5" : "gap-2";
  const pad = compact ? "start-3 top-3" : "start-4 top-4";

  return (
    <>
      <div className={`absolute ${pad} z-[400] flex flex-col ${gap}`}>
        <button
          type="button"
          onClick={onResetView}
          className={`${btn} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
          title="نمایش همه"
        >
          <MapIcon className={`${icon} group-hover:scale-110 transition-transform`} />
        </button>

        <button
          type="button"
          onClick={onLocate}
          className={`${btn} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
          title="موقعیت من"
        >
          <LocateFixed className={`${icon} group-hover:scale-110 transition-transform`} />
        </button>

        <div className="h-px bg-border/50 my-1" />

        <button
          type="button"
          onClick={onZoomIn}
          className={`${btn} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
          title="بزرگ‌نمایی"
        >
          <ZoomIn className={`${icon} group-hover:scale-110 transition-transform`} />
        </button>

        <button
          type="button"
          onClick={onZoomOut}
          className={`${btn} bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white hover:shadow-xl transition-all flex items-center justify-center text-text-strong hover:text-primary cursor-pointer group`}
          title="کوچک‌نمایی"
        >
          <ZoomOut className={`${icon} group-hover:scale-110 transition-transform`} />
        </button>
      </div>

      {showFullscreen && (
        <button
          type="button"
          onClick={onFullscreen}
          className={`absolute bottom-${compact ? "3" : "4"} end-${compact ? "3" : "4"} z-[400] flex items-center gap-2 px-${compact ? "3" : "4"} py-${compact ? "2" : "2.5"} rounded-${compact ? "lg" : "xl"} bg-primary hover:bg-primary-dark text-white shadow-lg hover:shadow-xl transition-all font-bold text-[${compact ? "10px" : "xs"}] cursor-pointer group`}
        >
          <Maximize2 className={`w-${compact ? "3.5" : "4"} h-${compact ? "3.5" : "4"} group-hover:scale-110 transition-transform`} />
          نقشه تمام‌صفحه
        </button>
      )}
    </>
  );
}