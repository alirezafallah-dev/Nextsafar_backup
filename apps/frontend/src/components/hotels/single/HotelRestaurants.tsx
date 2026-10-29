// src/components/hotels/single/HotelRestaurants.tsx
"use client";

import { Utensils, Coffee } from "lucide-react";

const RESTAURANTS = [
  { icon: Utensils, label: "رستوران اصلی (بوفه)" },
  { icon: Utensils, label: "رستوران ایتالیایی" },
  { icon: Coffee, label: "بار کنار استخر" },
];

export default function HotelRestaurants() {
  return (
    <section id="restaurants">
      <div className="flex items-center justify-between gap-3 mb-5">
        <div className="min-w-0">
          <h2 className="ns-section-title !mb-1">رستوران‌ها و بارها</h2>
          <p className="text-xs md:text-sm text-text-muted">
            گزینه‌های متنوع غذایی در هتل
          </p>
        </div>
      </div>

      <div className="rounded-[10px] md:rounded-lg border border-border bg-white p-5 md:p-6">
        <div className="space-y-2">
          {RESTAURANTS.map((r, i) => {
            const Icon = r.icon;
            return (
              <div
                key={i}
                className="flex items-center gap-3 p-3 rounded-[10px] md:rounded-lg bg-white border border-border hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out"
              >
                <div className="w-9 h-9 md:w-10 md:h-10 rounded-lg bg-white border border-border flex items-center justify-center shrink-0">
                  <Icon className="w-4 h-4 md:w-5 md:h-5 text-primary" />
                </div>
                <span className="text-xs md:text-sm font-bold text-text">
                  {r.label}
                </span>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}