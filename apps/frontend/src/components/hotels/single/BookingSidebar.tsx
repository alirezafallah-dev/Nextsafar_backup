// src/components/hotels/single/BookingSidebar.tsx
"use client";

import { MapPin, Star, Clock, Calendar, Users, Phone, Globe } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import { faDigits } from "@/lib/api/hotels";
import HotelSidebarMap from "./HotelSidebarMap";

interface Props {
  hotel: Hotel;
}

const FACTS = [
  { icon: Clock, label: "ساعت ورود", value: "۱۴:۰۰" },
  { icon: Calendar, label: "ساعت خروج", value: "۱۲:۰۰" },
  { icon: Users, label: "حداکثر ظرفیت", value: "۲ بزرگسال + ۱ کودک" },
  { icon: Star, label: "ستاره", value: "۵ ستاره" },
];

export default function BookingSidebar({ hotel }: Props) {
  return (
    <div className="space-y-4">
      {/* نقشه */}
      <div id="location" className="bg-white border border-border rounded-[10px] md:rounded-lg overflow-hidden shadow-sm hover:shadow-card-hover transition-shadow duration-500 ease-out">
        <HotelSidebarMap hotel={hotel} />
      </div>

      {/* اطلاعات کلیدی */}
      <div className="bg-white border border-border rounded-[10px] md:rounded-lg p-4 md:p-5 shadow-sm hover:shadow-card-hover transition-shadow duration-500 ease-out">
        <h3 className="text-sm md:text-base font-extrabold text-text-strong mb-3">
          اطلاعات کلیدی
        </h3>
        <div className="divide-y divide-border/60">
          {FACTS.map((f) => {
            const Icon = f.icon;
            return (
              <div key={f.label} className="flex items-start justify-between gap-3 py-2.5">
                <div className="flex items-center gap-2">
                  <Icon className="w-4 h-4 text-primary shrink-0" />
                  <span className="text-[11px] md:text-xs text-text-muted">
                    {f.label}
                  </span>
                </div>
                <span className="text-[11px] md:text-xs font-bold text-text-strong text-end">
                  {f.value}
                </span>
              </div>
            );
          })}
          <div className="flex items-start justify-between gap-3 py-2.5">
            <div className="flex items-center gap-2">
              <Phone className="w-4 h-4 text-primary shrink-0" />
              <span className="text-[11px] md:text-xs text-text-muted">تماس</span>
            </div>
            <div className="flex items-center gap-2">
              <a
                href="tel:+902121234567"
                className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center hover:bg-primary/20 transition"
                aria-label="تلفن"
              >
                <Phone className="w-4 h-4 text-primary" />
              </a>
              <a
                href="#"
                className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center hover:bg-primary/20 transition"
                aria-label="وب‌سایت"
              >
                <Globe className="w-4 h-4 text-primary" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}