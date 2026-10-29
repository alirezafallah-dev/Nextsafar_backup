"use client";

import { useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { MapPin, ZoomIn, ZoomOut, X } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import { loadLeaflet, waitForSize } from "@/lib/map-loader";
import { getMapConfig } from "@/lib/api/map";
import { addTiles } from "./map-icons";
import type { MapPoint } from "./renderers";
import { renderMarker, renderPopup } from "./renderers";

interface MapFullscreenModalProps {
  open: boolean;
  onClose: () => void;
  points: MapPoint[];
  markerStyle: "pill" | "numbered" | "category";
  title?: string;
  maxZoom?: number;
}

export function MapFullscreenModal({
  open,
  onClose,
  points,
  markerStyle,
  title = "نقشه",
  maxZoom = 14,
}: MapFullscreenModalProps) {
  const modalMapRef = useRef<HTMLDivElement>(null);
  const mapInstanceRef = useRef<any>(null);
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    if (!open) return;
    let cancelled = false;

    (async () => {
      const [L, cfg] = await Promise.all([loadLeaflet(), getMapConfig()]);
      if (cancelled || !modalMapRef.current) return;
      await new Promise((r) => setTimeout(r, 100));
      await waitForSize(modalMapRef.current);
      if (cancelled || !modalMapRef.current) return;

      const map = L.map(modalMapRef.current, {
        zoomControl: false,
        attributionControl: true,
      });
      addTiles(L, map, cfg);
      mapInstanceRef.current = map;

      points.forEach((point, idx) => {
        const icon = renderMarker(L, markerStyle, point, idx, false);
        L.marker([point.lat, point.lng], { icon })
          .addTo(map)
          .bindPopup(renderPopup(markerStyle, point), { closeButton: false });
      });

      if (points.length > 0) {
        const bounds = L.latLngBounds(points.map((p) => [p.lat, p.lng]));
        map.fitBounds(bounds, { padding: [80, 80], maxZoom });
      }

      setLoaded(true);
    })();

    return () => {
      cancelled = true;
      mapInstanceRef.current?.remove();
      mapInstanceRef.current = null;
      setLoaded(false);
    };
  }, [open, points, markerStyle, maxZoom]);

  if (!open || typeof document === "undefined") return null;

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
              <h3 className="font-extrabold text-text-strong">{title}</h3>
              <p className="text-xs text-text-muted">
                {faDigits(points.length)} مکان یافت شد
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

        {/* Controls */}
        {loaded && (
          <div className="absolute start-6 top-20 z-[400] flex flex-col gap-2">
            <button
              type="button"
              onClick={() => mapInstanceRef.current?.zoomIn()}
              className="w-11 h-11 rounded-lg bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white flex items-center justify-center text-text-strong hover:text-primary cursor-pointer"
            >
              <ZoomIn className="w-5 h-5" />
            </button>
            <button
              type="button"
              onClick={() => mapInstanceRef.current?.zoomOut()}
              className="w-11 h-11 rounded-lg bg-white/95 backdrop-blur border border-border/50 shadow-lg hover:bg-white flex items-center justify-center text-text-strong hover:text-primary cursor-pointer"
            >
              <ZoomOut className="w-5 h-5" />
            </button>
          </div>
        )}
      </div>
    </div>,
    document.body,
  );
}