// src/components/hotels/single/HotelMapModal.tsx
"use client";

import { useEffect, useRef } from "react";
import { createPortal } from "react-dom";
import ListMapSplitView from "@/components/shared/map/ListMapSplitView";
import type { ListingMapPoint } from "@/components/shared/map/types";

interface HotelMapModalProps {
  isOpen: boolean;
  onClose: () => void;
  points: ListingMapPoint[];  // هتل فعلی باید اولین item باشد
  currentHotelKey: string;
  title?: string;
  itemLabel?: string;
}

export default function HotelMapModal({
  isOpen,
  onClose,
  points,
  currentHotelKey,
  title = "همه هتل‌ها",
  itemLabel = "هتل",
}: HotelMapModalProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  const clickedRef = useRef(false);

  /* ═══ قفل اسکرول بدنه ═══ */
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = "hidden";
      return () => { document.body.style.overflow = "unset"; };
    }
  }, [isOpen]);

  /* ═══ کلیک برنامه‌ای روی اولین کارت (هتل فعلی) برای هایلایت خودکار ═══ */
  useEffect(() => {
    if (!isOpen) {
      clickedRef.current = false;
      return;
    }
    if (clickedRef.current) return;

    // polling تا کارت‌ها رندر شوند
    let attempts = 0;
    const maxAttempts = 30;
    
    const interval = setInterval(() => {
      attempts++;
      const container = containerRef.current;
      if (!container) return;

      // پیدا کردن اولین PlaceCard (اولین عنصر با aria-label یا کارت)
      const cards = container.querySelectorAll<HTMLElement>('[data-place-key]');
      
      if (cards.length > 0) {
        // پیدا کردن کارت مربوط به هتل فعلی
        const targetCard = Array.from(cards).find(
          (card) => card.getAttribute("data-place-key") === currentHotelKey
        );
        
        if (targetCard) {
          targetCard.click();
          clickedRef.current = true;
          clearInterval(interval);
          return;
        }
        
        // اگر پیدا نشد، اولین کارت را کلیک کن
        cards[0].click();
        clickedRef.current = true;
        clearInterval(interval);
      }
      
      if (attempts >= maxAttempts) {
        clearInterval(interval);
      }
    }, 150);

    return () => clearInterval(interval);
  }, [isOpen, currentHotelKey]);

  if (!isOpen || typeof document === "undefined") return null;

  return createPortal(
    <div
      ref={containerRef}
      className="fixed inset-0 z-[9999] bg-white animate-in fade-in duration-200"
    >
      <ListMapSplitView
        items={points}
        title={title}
        itemLabel={itemLabel}
        showHeader={true}
        onClose={onClose}
        searchable={true}
        defaultMobileView="map"
      />
    </div>,
    document.body
  );
}