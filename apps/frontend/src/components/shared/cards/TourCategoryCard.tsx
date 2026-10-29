"use client";

import Link from "next/link";
import Image from "next/image";
import { Backpack, Plane, ArrowLeft } from "lucide-react";
import { formatNumber } from "@/lib/utils";

export interface TourCategoryCardProps {
  slug: string;
  name: string;
  count: number;
  image: string | null;
  duration?: number | null;
}

export default function TourCategoryCard({
  slug,
  name,
  count,
  image,
  duration,
}: TourCategoryCardProps) {
  return (
    <Link
      href={`/tours?cat=${slug}`}
      className="group rounded-[10px] md:rounded-lg overflow-hidden border border-border hover:border-tour hover:shadow-card-hover transition-all duration-500 ease-out bg-white flex flex-col w-full cursor-pointer"
    >
      {/* ═══ بخش تصویر با گرادیان نارنجی-آبی ═══ */}
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

        {/* Overlay تیره برای خوانایی متن */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />

        {/* Badge مدت سفر */}
        {duration && (
          <span className="absolute top-2.5 start-2.5 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur text-[11px] text-tour-hover font-bold shadow z-10">
            {formatNumber(duration)} شب
          </span>
        )}

        {/* عنوان روی تصویر */}
        <div className="absolute bottom-2.5 inset-x-2.5 z-10">
          <h3 className="text-white text-base md:text-lg font-bold drop-shadow line-clamp-1">
            {name}
          </h3>
        </div>
      </div>

      {/* ═══ Footer ═══ */}
      <div className="p-3 flex items-center justify-between gap-2 flex-1">
        <div className="flex flex-col gap-1 min-w-0">
          <div className="flex items-center gap-1.5 text-[11px] text-text-muted">
            <Backpack className="w-3.5 h-3.5 text-tour" />
            <span>
              <span className="font-bold text-text-strong">
                {formatNumber(count)}
              </span>{" "}
              تور فعال
            </span>
          </div>
          <span className="text-[10px] text-text-subtle">
            مشاهده پکیج‌ها و قیمت‌ها
          </span>
        </div>
        <span className="w-9 h-9 rounded-full bg-tour/10 text-tour group-hover:bg-tour group-hover:text-white flex items-center justify-center shrink-0 transition-all duration-300">
          <ArrowLeft className="w-4 h-4" />
        </span>
      </div>
    </Link>
  );
}