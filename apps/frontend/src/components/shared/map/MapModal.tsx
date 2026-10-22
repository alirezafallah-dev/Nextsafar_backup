"use client";

import { useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { MapPin, ZoomIn, ZoomOut, X } from "lucide-react";
import { loadLeaflet, waitForSize } from "@/lib/map-loader";
import { getMapConfig } from "@/lib/api/map";
import { addTiles, makeMarkerIcon, colorOf } from "@/components/map/map-icons";
import { faDigits } from "@/lib/api/hotels";
import type { ListingMapPoint } from "./types";

interface MapModalProps {
  isOpen: boolean;
  onClose: () => void;
  points: ListingMapPoint[];
  onPointClick?: (key: string) => void;
}

export default function MapModal({
  isOpen,
  onClose,
  points,
  onPointClick,
}: MapModalProps) {
  const modalMapRef = useRef<HTMLDivElement>(null);
  const modalMapInstance = useRef<any>(null);
  const modalMarkersRef = useRef<Map<string, any>>(new Map());
  const [isLoaded, setIsLoaded] = useState(false);

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
            .on("click", () => onPointClick?.(point.key));

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
    };
  }, [isOpen, points, onPointClick]);

  if (!isOpen || typeof document === "undefined") return null;

  return createPortal(
    <div className="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-200">
      <div className="relative w-full max-w-7xl h-[90vh] bg-white rounded-lg overflow-hidden shadow-2xl flex flex-col">
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b border-border bg-white">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <MapPin className="w-5 h-5 text-primary" />
            </div>
            <div>
              <h3 className="font-extrabold text-text-strong">نقشه هتل‌ها</h3>
              <p className="text-xs text-text-muted">
                {faDigits(points.length)} هتل یافت شد
              </p>
            </div>
          </div>
          <button
            type="button"
            onClick={onClose}
            className="w-10 h-10 rounded-lg bg-bg-sec hover:bg-danger/10 hover:text-danger flex items-center justify-center transition-colors cursor-pointer"
            aria-label="بستن"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Map */}
        <div ref={modalMapRef} className="flex-1 bg-bg-sec" />

        {/* Zoom Controls */}
        {isLoaded && (
          <div className="absolute start-6 top-20 z-[400] flex flex-col gap-2">
            <button
              type="button"
              onClick={() => {
                try {
                  modalMapInstance.current?.zoomIn();
                } catch (err) {}
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
                } catch (err) {}
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
  );
}