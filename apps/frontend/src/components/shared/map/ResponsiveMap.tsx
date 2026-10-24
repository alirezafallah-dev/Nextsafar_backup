"use client";

import { useState } from "react";
import { MapPin } from "lucide-react";
import { useMediaQuery } from "@/hooks/use-media-query";
import ListingMap from "./ListingMap";
import MapModal from "./MapModal";
import { faDigits } from "@/lib/api/hotels";
import type { ListingMapPoint } from "./types";

interface ResponsiveMapProps {
  points: ListingMapPoint[];
  title?: string;
  
  // Desktop props
  desktopHeight?: number;
  showDesktopControls?: boolean;
  
  // Mobile props
  mobileButtonLabel?: string;
  
  // Common props
  activeKey?: string | null;
  onPointClick?: (key: string) => void;
  onPointHover?: (key: string | null) => void;
  className?: string;
}

/**
 * کامپوننت ماژولار نقشه با پشتیبانی از دسکتاپ و موبایل
 * 
 * در دسکتاپ: نقشه معمولی را نمایش می‌دهد
 * در موبایل: دکمه شناور + مودال تمام صفحه نمایش می‌دهد
 * 
 * @example
 * <ResponsiveMap
 *   points={mapPoints}
 *   title="نقشه هتل‌ها"
 *   desktopHeight={300}
 *   mobileButtonLabel="هتل روی نقشه"
 * />
 */
export default function ResponsiveMap({
  points,
  title = "نقشه",
  desktopHeight = 300,
  showDesktopControls = true,
  mobileButtonLabel = "روی نقشه",
  activeKey,
  onPointClick,
  onPointHover,
  className = "",
}: ResponsiveMapProps) {
  const [isModalOpen, setIsModalOpen] = useState(false);
  
  // تشخیص موبایل/دسکتاپ
  const isMobile = useMediaQuery("(max-width: 1023px)");

  // اگر نقطه‌ای برای نمایش نیست، چیزی رندر نکن
  if (!points || points.length === 0) return null;

  // ═══ حالت موبایل: دکمه شناور + مودال ═══
  if (isMobile) {
    return (
      <>
        {/* دکمه شناور */}
        <button
          onClick={() => setIsModalOpen(true)}
          className="fixed bottom-6 right-6 z-40 flex items-center gap-2 px-5 py-3.5 bg-primary text-white rounded-full shadow-xl shadow-primary/30 hover:scale-105 hover:shadow-2xl hover:shadow-primary/40 active:scale-95 transition-all duration-200"
          aria-label={`نمایش ${title}`}
        >
          <MapPin className="w-5 h-5" />
          <span className="font-bold text-sm">
            {faDigits(points.length)} {mobileButtonLabel}
          </span>
        </button>

        {/* مودال تمام صفحه */}
        <MapModal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          points={points}
          title={title}
          onPointClick={onPointClick}
        />
      </>
    );
  }

  // ═══ حالت دسکتاپ: نقشه معمولی ═══
  return (
    <div className={className}>
      <ListingMap
        points={points}
        height={desktopHeight}
        activeKey={activeKey}
        onPointClick={onPointClick}
        onPointHover={onPointHover}
      />
    </div>
  );
}