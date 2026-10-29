"use client";
import { useState } from "react";
import { useMediaQuery } from "@/hooks/use-media-query";
import ListingMap from "./ListingMap";
import MapModal from "./MapModal";
import type { ListingMapPoint } from "./types";

interface ResponsiveMapProps {
  points: ListingMapPoint[];
  title?: string;
  /** برچسب آیتم‌ها برای مودال - مثال: "هتل"، "مکان"، "پرواز" */
  itemLabel?: string;
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
 * در موبایل: دکمه شناور وسط صفحه + مودال تمام صفحه نمایش می‌دهد
 *
 * @example
 * <ResponsiveMap
 *   points={mapPoints}
 *   title="نقشه هتل‌ها"
 *   itemLabel="هتل"
 *   desktopHeight={300}
 * />
 */
export default function ResponsiveMap({
  points,
  title = "نقشه",
  itemLabel = "مکان",
  desktopHeight = 300,
  showDesktopControls = true,
  mobileButtonLabel = "مشاهده روی نقشه",
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

  // ═══ حالت موبایل: دکمه شناور وسط + مودال ═══
  if (isMobile) {
    return (
      <>
        {/* دکمه شناور وسط صفحه */}
        <button
          onClick={() => setIsModalOpen(true)}
          className="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 flex items-center px-4 py-2.5 bg-primary text-white rounded-full shadow-xl shadow-primary/30 hover:scale-105 hover:shadow-2xl hover:shadow-primary/40 active:scale-95 transition-all duration-200"
          aria-label={`نمایش ${title}`}
        >
          <span className="font-bold text-sm whitespace-nowrap">
            {mobileButtonLabel}
          </span>
        </button>

        {/* مودال تمام صفحه - با itemLabel ماژولار */}
        <MapModal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          points={points}
          title={title}
          itemLabel={itemLabel}
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