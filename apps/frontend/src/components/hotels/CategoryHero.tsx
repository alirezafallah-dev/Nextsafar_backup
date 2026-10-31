// src/components/hotels/CategoryHero.tsx
"use client";

import Image from "next/image";
import { MapPin, Building2 } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";

interface Props {
  title: string;
  image: string;
  hotelCount: number;
  type: "country" | "city";
  subtitle?: string;
}

export default function CategoryHero({
  title,
  image,
  hotelCount,
  type,
  subtitle,
}: Props) {
  return (
    <section className="pt-5 md:pt-0">
      <div className="ns-container">
        <div className="relative w-full aspect-[16/9] md:aspect-[2172/650] rounded-2xl md:rounded-none overflow-hidden">
          <Image
            src={image}
            alt={`هتل‌های ${title}`}
            fill
            sizes="(max-width: 1280px) 100vw, 1280px"
            quality={85}
            priority
            fetchPriority="high"
            className="object-cover object-center md:object-contain md:object-top"
          />

          {/* Overlay تیره برای خوانایی متن */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-black/10" />

          {/* محتوا */}
          <div className="absolute inset-0 flex flex-col justify-end p-5 md:p-8 lg:p-10">
            <div className="max-w-2xl">
              {/* Badge نوع */}
              <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-white text-[11px] md:text-xs font-bold mb-3">
                {type === "country" ? (
                  <MapPin className="w-3.5 h-3.5" />
                ) : (
                  <Building2 className="w-3.5 h-3.5" />
                )}
                <span>{type === "country" ? "کشور" : "شهر"}</span>
              </div>

              {/* عنوان */}
              <h1 className="text-2xl md:text-4xl lg:text-5xl font-black text-white drop-shadow-lg mb-2">
                {title}
              </h1>

              {/* توضیح */}
              {subtitle && (
                <p className="text-sm md:text-base text-white/90 mb-4 drop-shadow-md">
                  {subtitle}
                </p>
              )}

              {/* آمار */}
              <div className="flex items-center gap-4">
                <div className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/15 backdrop-blur-md border border-white/20">
                  <Building2 className="w-4 h-4 text-white" />
                  <span className="text-white text-xs md:text-sm font-bold">
                    {faDigits(hotelCount)} هتل
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}