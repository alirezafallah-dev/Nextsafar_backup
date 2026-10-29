// src/components/hotels/single/HotelMiniMap.tsx
"use client";

import { useEffect, useRef } from "react";

interface Props {
  coordinates: { lat: number; lng: number };
  title: string;
  address: string;
}

export default function HotelMiniMap({ coordinates, title, address }: Props) {
  const mapRef = useRef<HTMLDivElement>(null);
  const mapInstance = useRef<any>(null);

  useEffect(() => {
    if (!mapRef.current || mapInstance.current || typeof window === "undefined") return;

    const loadMap = async () => {
      const L = (await import("leaflet")).default;
      await import("leaflet/dist/leaflet.css");

      const customIcon = L.divIcon({
        className: "custom-hotel-marker",
        html: `
          <div style="
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #43abff, #3384c6);
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(67, 171, 255, 0.4);
            display: flex; align-items: center; justify-content: center;
          ">
            <div style="transform: rotate(45deg); color: white; font-size: 16px;">★</div>
          </div>
        `,
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -36],
      });

      const map = L.map(mapRef.current, {
        zoomControl: false,
        attributionControl: false,
        scrollWheelZoom: false,
        dragging: false,
      }).setView([coordinates.lat, coordinates.lng], 15);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
      }).addTo(map);

      L.marker([coordinates.lat, coordinates.lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(`
          <div style="text-align: center; font-family: IRANSansX, sans-serif; padding: 4px;">
            <strong style="font-size: 12px;">${title}</strong>
          </div>
        `);

      mapInstance.current = map;
    };

    loadMap();

    return () => {
      if (mapInstance.current) {
        mapInstance.current.remove();
        mapInstance.current = null;
      }
    };
  }, [coordinates, title, address]);

  return <div ref={mapRef} className="w-full h-full bg-gray-100" />;
}