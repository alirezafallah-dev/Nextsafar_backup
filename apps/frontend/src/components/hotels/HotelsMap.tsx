"use client";

import NextSafarMap from "@/components/map/NextSafarMap";

export interface HotelMapPoint {
  key: string;
  lat: number;
  lng: number;
  title: string;
  price: number | null;
  isSite: boolean;
  href: string | null;
}

export default function HotelsMap({
  points,
  activeKey = null,
  height = 210,
  flyOnActive = false,
  refitSignal = 0,
  className = "",
  scrollWheelZoom = false,
}: {
  points: HotelMapPoint[];
  activeKey?: string | null;
  height?: number | string;
  flyOnActive?: boolean;
  refitSignal?: number;
  className?: string;
  scrollWheelZoom?: boolean;
}) {
  return (
    <NextSafarMap
      preset="hotels"
      points={points}
      activeKey={activeKey}
      height={height}
      flyOnActive={flyOnActive}
      refitSignal={refitSignal}
      className={className}
      scrollWheelZoom={scrollWheelZoom}
    />
  );
}