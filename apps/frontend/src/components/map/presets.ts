/**
 * Preset های از پیش تعریف شده برای NextSafarMap
 * هر preset یک پیکربندی خاص برای یک نوع نقشه است.
 */

import type { NextSafarMapProps } from "./NextSafarMap";

export type MapPreset = "hotels" | "trip" | "nearby" | "listing";

export const MAP_PRESETS: Record<MapPreset, Partial<NextSafarMapProps>> = {
  hotels: {
    markerStyle: "pill",
    showControls: false,
    showFullscreen: false,
    scrollWheelZoom: false,
    fitBounds: true,
    flyOnActive: true,
    padding: [50, 50],
    maxZoom: 14,
    zoom: 12,
  },
  trip: {
    markerStyle: "numbered",
    showControls: false,
    showFullscreen: false,
    scrollWheelZoom: false,
    fitBounds: true,
    flyOnActive: false,
    hoverOnlyStyle: true,
    padding: [60, 60],
    maxZoom: 15,
    zoom: 5,
  },
  nearby: {
    markerStyle: "category",
    showControls: false,
    showFullscreen: false,
    scrollWheelZoom: false,
    dragging: false,
    fitBounds: false,
    zoom: 14,
  },
  listing: {
    markerStyle: "category",
    showControls: true,
    showFullscreen: true,
    scrollWheelZoom: true,
    fitBounds: true,
    flyOnActive: true,
    padding: [50, 50],
    maxZoom: 14,
    zoom: 5,
  },
};