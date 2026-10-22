"use client";

import { X } from "lucide-react";
import ListingMap, { type ListingMapPoint } from "./ListingMap";

interface MapModalProps {
  open: boolean;
  onClose: () => void;
  title?: string;
  points: ListingMapPoint[];
  activeKey?: string | null;
  onPointClick?: (key: string) => void;
  onPointHover?: (key: string | null) => void;
  className?: string;
}

/**
 * مودال تمام‌صفحه نقشه
 * برای نمایش نقشه بزرگ‌تر از لیست‌ها
 */
export default function MapModal({
  open,
  onClose,
  title = "نقشه",
  points,
  activeKey,
  onPointClick,
  onPointHover,
  className = "",
}: MapModalProps) {
  if (!open) return null;

  return (
    <div className="fixed inset-0 z-[400] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div
        className={`relative w-full max-w-5xl bg-white rounded-xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh] ${className}`}
      >
        {/* Header */}
        <div className="flex items-center justify-between px-4 py-3 border-b border-border">
          <h3 className="font-bold text-text-strong">{title}</h3>
          <button
            onClick={onClose}
            className="p-2 rounded-lg hover:bg-bg-sec transition cursor-pointer"
            aria-label="بستن"
          >
            <X className="w-5 h-5 text-text-muted" />
          </button>
        </div>

        {/* Map */}
        <div className="flex-1 min-h-[400px] max-h-[70vh]">
          <ListingMap
            points={points}
            height="100%"
            className="!rounded-none"
            activeKey={activeKey}
            onPointClick={onPointClick}
            onPointHover={onPointHover}
            showControls={true}
            showFullscreen={false}
            fitBounds={true}
          />
        </div>
      </div>
    </div>
  );
}