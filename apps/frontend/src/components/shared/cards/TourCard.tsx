"use client";
import Link from "next/link";
import Image from "next/image";
import { Backpack, Plane, ArrowLeft, Calendar, MapPin } from "lucide-react";
import { formatNumber } from "@/lib/utils";
import { faDigits } from "@/lib/api/hotels";

interface TourCategoryCardProps {
  slug: string;
  name: string;
  count: number;
  image: string | null;
  duration?: number | null;
}

interface TourPackageCardProps {
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
}

// ═══ کارت دسته‌بندی تور (داغ‌ترین‌ها) ═══
export function TourCategoryCard({ slug, name, count, image, duration }: TourCategoryCardProps) {
  return (
    <Link
      href={`/tours?cat=${slug}`}
      className="group rounded-[10px] md:rounded-lg overflow-hidden border border-border hover:border-tour hover:shadow-card-hover transition-all duration-500 ease-out bg-white flex flex-col w-full"
    >
      <div className="relative aspect-[4/3] bg-gradient-to-br from-tour/20 to-primary-lightest/30 overflow-hidden">
        {image ? (
          <Image
            src={image}
            alt={name}
            fill
            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 25vw"
            quality={85}
            loading="lazy"
            className="object-cover transition-all duration-700 ease-out group-hover:scale-[1.06]"
          />
        ) : (
          <div className="absolute inset-0 flex items-center justify-center">
            <Plane className="w-14 h-14 text-tour/40" />
          </div>
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
        {duration && (
          <span className="absolute top-2.5 start-2.5 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur text-[11px] text-tour-hover shadow z-10">
            {formatNumber(duration)} شب
          </span>
        )}
        <div className="absolute bottom-2.5 inset-x-2.5 z-10">
          <div className="text-white text-base md:text-lg font-bold drop-shadow line-clamp-1">
            {name}
          </div>
        </div>
      </div>
      <div className="p-3 flex items-center justify-between gap-2 flex-1">
        <div className="flex flex-col gap-1 min-w-0">
          <div className="flex items-center gap-1.5 text-[11px] text-text-muted">
            <Backpack className="w-3.5 h-3.5 text-tour" />
            <span>
              <span className="font-bold text-text-strong">{formatNumber(count)}</span> تور فعال
            </span>
          </div>
          <span className="text-[10px] text-text-subtle">مشاهده پکیج‌ها و قیمت‌ها</span>
        </div>
        <span className="w-9 h-9 rounded-full bg-tour/10 text-tour group-hover:bg-tour group-hover:text-white flex items-center justify-center shrink-0 transition-all duration-300">
          <ArrowLeft className="w-4 h-4" />
        </span>
      </div>
    </Link>
  );
}

// ═══ کارت پکیج تور + هتل ═══
export function TourPackageCard({ pkg }: { pkg: TourPackageCardProps }) {
  return (
    <Link
      href={`/tours/${pkg.id}`}
      className="group relative w-[280px] md:w-full flex-shrink-0 rounded-xl overflow-hidden bg-white border border-border hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 snap-start cursor-pointer"
    >
      <div className="relative aspect-[4/3] overflow-hidden">
        <Image
          src={pkg.image}
          alt={pkg.title}
          fill
          sizes="280px"
          className="object-cover transition-transform duration-500 group-hover:scale-110"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
        <span className="absolute top-2.5 start-2.5 px-2.5 py-1 rounded-md bg-danger text-white text-[10px] font-black z-10">
          {faDigits(pkg.discount)}٪ تخفیف
        </span>
        <span className="absolute top-2.5 end-2.5 flex items-center gap-1 px-2 py-1 rounded-md bg-black/70 backdrop-blur text-white text-[10px] font-bold z-10">
          <Calendar className="w-3 h-3" />
          {faDigits(pkg.nights)} شب
        </span>
        <div className="absolute bottom-0 inset-x-0 p-3 z-10">
          <div className="flex items-center gap-1 text-white/90 text-[10px] mb-1">
            <MapPin className="w-3 h-3" />
            <span>{pkg.destination}</span>
          </div>
          <h3 className="text-white text-sm md:text-[15px] font-bold line-clamp-1">
            {pkg.title}
          </h3>
        </div>
      </div>
      <div className="p-3 md:p-4 flex flex-col gap-3">
        <div className="flex items-center gap-1 text-[10px] text-text-muted">
          <span>هتل:</span>
          {Array.from({ length: pkg.hotelStars }).map((_, i) => (
            <span key={i} className="text-warning">⭐</span>
          ))}
        </div>
        <ul className="space-y-1">
          {pkg.includes.slice(0, 2).map((item, i) => (
            <li key={i} className="flex items-center gap-1 text-[10px] text-text-muted">
              <div className="w-1 h-1 rounded-full bg-success" />
              <span>{item}</span>
            </li>
          ))}
        </ul>
        <div className="pt-3 border-t border-divider flex items-end justify-between">
          <div className="flex flex-col">
            <span className="text-[10px] text-text-muted line-through">
              {formatNumber(pkg.oldPrice)}
            </span>
            <span className="text-primary font-black text-base">
              {formatNumber(pkg.price)}
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