// src/components/hotels/single/BookingSidebar.tsx
"use client";

import { MapPin, Globe, Phone } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import HotelSidebarMap from "./HotelSidebarMap";

interface Props { hotel: Hotel }

const FACTS = [
  { label: "موقعیت", value: "بلک، آنتالیا، ترکیه" },
  { label: "نوع هتل", value: "۵ ستاره" },
  { label: "خدمات", value: "UALL (Ultra All Inclusive)" },
  { label: "فاصله تا ساحل", value: "۵۰۰ متر (با سرویس شاتل)" },
  { label: "اینترنت", value: "دارد" },
];

export default function BookingSidebar({ hotel }: Props) {
  return (
    <aside className="lg:sticky lg:top-[150px] lg:max-h-[calc(100vh-174px)] lg:overflow-y-auto scrollbar-hide space-y-4">

      {/* نقشه */}
      <div id="location" className="bg-white border border-border rounded-[10px] md:rounded-lg overflow-hidden shadow-sm hover:shadow-card-hover transition-shadow duration-500 ease-out">
        <HotelSidebarMap hotel={hotel} />
      </div>

      {/* اطلاعات کلیدی */}
      <div className="bg-white border border-border rounded-[10px] md:rounded-lg p-4 md:p-5 shadow-sm hover:shadow-card-hover transition-shadow duration-500 ease-out">
        <h3 className="text-sm md:text-base font-extrabold text-text-strong mb-3">اطلاعات کلیدی</h3>
        <div className="divide-y divide-border/60">
          {FACTS.map((f) => (
            <div key={f.label} className="flex items-start justify-between gap-3 py-2.5">
              <span className="text-[11px] md:text-xs text-text-muted shrink-0">{f.label}</span>
              <span className="text-[11px] md:text-xs font-bold text-text-strong text-end">{f.value}</span>
            </div>
          ))}
          <div className="flex items-start justify-between gap-3 py-2.5">
            <span className="text-[11px] md:text-xs text-text-muted shrink-0">تماس</span>
            <div className="flex items-center gap-2">
              <a href="tel:+902121234567" className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center hover:bg-primary/20 transition" aria-label="تلفن">
                <Phone className="w-4 h-4 text-primary" />
              </a>
              <a href="#" className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center hover:bg-primary/20 transition" aria-label="وب‌سایت">
                <Globe className="w-4 h-4 text-primary" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </aside>
  );
}