/**
 * Renderer های مختلف برای مارکرهای نقشه
 */

import { compactToman, faDigits } from "@/lib/api/hotels";
import { colorOf, makeMarkerIcon, makeNumberedMarker, TYPE_FA } from "./map-icons";

const esc = (s: string) =>
  s.replace(/[&<>"']/g, (c) =>
    ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]!,
  );

/* ═══════════════════════════════════════════════
   نقطه (برای همه preset ها)
═══════════════════════════════════════════════ */
export interface MapPoint {
  key: string;
  lat: number;
  lng: number;
  title: string;
  // برای hotels
  price?: number | null;
  isSite?: boolean;
  href?: string | null;
  // برای trip
  slug?: string;
  url?: string;
  type?: string;
  index?: number;
  // برای nearby/listing
  locationType?: string;
}

/* ═══════════════════════════════════════════════
   Pill (HotelsMap)
═══════════════════════════════════════════════ */
const C_SITE = "#0284c7";
const C_ONLINE = "#ffffff";
const C_ACTIVE = "#0b1e3a";

export function renderPillMarker(
  L: any,
  point: MapPoint,
  isActive: boolean,
): any {
  const label = point.price
    ? compactToman(point.price)
    : point.title.length > 14
      ? point.title.substring(0, 14) + "…"
      : point.title;
  const bg = isActive ? C_ACTIVE : point.isSite ? C_SITE : C_ONLINE;
  const fg = point.isSite || isActive ? "#fff" : "#0b1e3a";
  const border = point.isSite || isActive ? "none" : "1px solid #cbd5e1";

  const html = `
    <div style="display:flex;flex-direction:column;align-items:center;width:110px;">
      <span style="background:${bg};color:${fg};border:${border};border-radius:999px;padding:4px 10px;font-size:10px;font-weight:800;box-shadow:0 2px 8px rgba(0,0,0,.28);white-space:nowrap;transform:scale(${isActive ? 1.15 : 1});transition:transform .15s;">${esc(label)}</span>
      <span style="width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:6px solid ${bg};"></span>
    </div>`;

  return L.divIcon({
    className: "",
    html,
    iconSize: [110, 34],
    iconAnchor: [55, 34],
    popupAnchor: [0, -34],
  });
}

export function renderPillPopup(point: MapPoint): string {
  const price = point.price
    ? `<div style="color:#0284c7;font-weight:800;font-size:12px;margin-top:2px;">${faDigits(point.price.toLocaleString("en-US"))} تومان / شب</div>`
    : "";
  const link = point.href
    ? `<a href="${esc(point.href)}" ${point.isSite ? "" : 'target="_blank" rel="nofollow noopener"'} style="display:inline-block;margin-top:6px;color:#0284c7;font-weight:800;font-size:11px;">مشاهده هتل ←</a>`
    : "";
  return `<div style="direction:rtl;text-align:right;min-width:160px;font-size:12px;"><b>${esc(point.title)}</b>${price}${link}</div>`;
}

/* ═══════════════════════════════════════════════
   Numbered (TripMapPanel)
═══════════════════════════════════════════════ */
export function renderNumberedMarker(
  L: any,
  point: MapPoint,
  index: number,
): any {
  return makeNumberedMarker(L, colorOf(point.type || ""), index + 1);
}

export function renderNumberedPopup(point: MapPoint): string {
  return `<b>${point.title}</b><br/><a href="${point.url}" style="color:#0ea5e9">مشاهده در سایت</a>`;
}

/* ═══════════════════════════════════════════════
   Category (NearbyMap + ListingMap)
═══════════════════════════════════════════════ */
export function renderCategoryMarker(
  L: any,
  point: MapPoint,
  isCenter?: boolean,
): any {
  if (isCenter) {
    return makeMarkerIcon(L, colorOf("center"), "center");
  }
  return makeMarkerIcon(
    L,
    colorOf(point.type || ""),
    point.type || "",
    point.locationType || "",
  );
}

export function renderCategoryPopup(
  point: MapPoint,
  options?: { showDistance?: boolean },
): string {
  const distance = options?.showDistance && (point as any).distance_km
    ? `<br/>${(point as any).distance_km} کیلومتر`
    : "";
  return `<b>${point.title}</b>${distance}`;
}

/* ═══════════════════════════════════════════════
   Dispatcher
═══════════════════════════════════════════════ */
export function renderMarker(
  L: any,
  style: "pill" | "numbered" | "category",
  point: MapPoint,
  index: number,
  isActive: boolean,
  isCenter?: boolean,
): any {
  switch (style) {
    case "pill":
      return renderPillMarker(L, point, isActive);
    case "numbered":
      return renderNumberedMarker(L, point, index);
    case "category":
      return renderCategoryMarker(L, point, isCenter);
  }
}

export function renderPopup(
  style: "pill" | "numbered" | "category",
  point: MapPoint,
  options?: { showDistance?: boolean },
): string {
  switch (style) {
    case "pill":
      return renderPillPopup(point);
    case "numbered":
      return renderNumberedPopup(point);
    case "category":
      return renderCategoryPopup(point, options);
  }
}