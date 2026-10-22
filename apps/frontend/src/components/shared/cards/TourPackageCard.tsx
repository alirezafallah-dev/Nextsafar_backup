"use client";

import Link from "next/link";
import Image from "next/image";
import { Plane, MapPin, Calendar } from "lucide-react";
import { formatNumber } from "@/lib/utils";

export interface TourPackageCardProps {
  id: number;
  title: string;
  destination: string;
  image: string;
  nights: number;
  hotelStars: number;
  price: number;
  discount: number;
  oldPrice: number;
  includes: string[];
  href?: string;
}

export default function TourPackageCard({
  id,
  title,
  destination,
  image,
  nights,
  hotelStars,
  price,
  discount,
  oldPrice,
  includes,
  href = "/tours",
}: TourPackageCardProps) {
  return (
    <Link
      href={href}
      className="group relative w-[280px] md:w-auto flex-shrink-0 rounded-xl overflow-hidden bg-white border border-border hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 snap-start cursor-pointer"
    >
      {/* تصویر */}
      <div className="relative aspect-[4/3] overflow-hidden">
        <Image
          src={image}
          alt={title}
          fill
          sizes="(max-width: 768px) 280px, 33vw"
          className="object-cover transition-transform duration-500 group-hover:scale-110"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />

        {/* بج تخفیف */}
        <span className="absolute top-2.5 start-2.5 px-2.5 py-1 rounded-md bg-danger text-white text-[10px] font-black z-10">
          {discount}٪ تخفیف
        </span>

        {/* بج شب */}
        <span className="absolute top-2.5 end-2.5 flex items-center gap-1 px-2 py-1 rounded-md bg-black/70 backdrop-blur text-white text-[10px] font-bold z-10">
          <Calendar className="w-3 h-3" />
          {nights} شب
        </span>

        {/* عنوان روی تصویر */}
        <div className="absolute bottom-0 inset-x-0 p-3 z-10">
          <div className="flex items-center gap-1 text-white/90 text-[10px] mb-1">
            <MapPin className="w-3 h-3" />
            <span>{destination}</span>
          </div>
          <h3 className="text-white text-sm md:text-[15px] font-bold line-clamp-1">
            {title}
          </h3>
        </div>
      </div>

      {/* بدنه */}
      <div className="p-3 md:p-4 flex flex-col gap-3">
        {/* ستاره هتل */}
        <div className="flex items-center gap-1 text-[10px] text-text-muted">
          <span>هتل:</span>
          {Array.from({ length: hotelStars }).map((_, i) => (
            <span key={i} className="text-warning">⭐</span>
          ))}
        </div>

        {/* خدمات */}
        <ul className="space-y-1">
          {includes.slice(0, 2).map((item, i) => (
            <li key={i} className="flex items-center gap-1 text-[10px] text-text-muted">
              <div className="w-1 h-1 rounded-full bg-success" />
              <span>{item}</span>
            </li>
          ))}
        </ul>

        {/* قیمت */}
        <div className="pt-3 border-t border-divider flex items-end justify-between">
          <div className="flex flex-col">
            <span className="text-[10px] text-text-muted line-through">
              {formatNumber(oldPrice)}
            </span>
            <span className="text-primary font-black text-base">
              {formatNumber(price)}
            </span>
            <span className="text-[9px] text-text-muted">تومان / نفر</span>
          </div>
          <span className="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-180">
            <Plane className="w-4 h-4" />
          </span>
        </div>
      </div>
    </Link>
  );
}