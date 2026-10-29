// src/components/hotels/single/HotelAbout.tsx
"use client";

import { useState } from "react";
import { ChevronDown, ChevronUp } from "lucide-react";
import type { Hotel } from "@/types/hotel";

interface Props {
  hotel: Hotel;
}

export default function HotelAbout({ hotel }: Props) {
  const [expanded, setExpanded] = useState(false);

  const long = hotel.description.length > 400;
  const text = expanded || !long ? hotel.description : hotel.description.slice(0, 400) + "...";

  return (
    <section>
      <div className="flex items-center justify-between gap-3 mb-5">
        <div className="min-w-0">
          <h2 className="ns-section-title !mb-1">درباره هتل</h2>
          <p className="text-xs md:text-sm text-text-muted">
            اطلاعات کامل و جزئیات هتل {hotel.title}
          </p>
        </div>
      </div>

      <div className="rounded-[10px] md:rounded-lg border border-border bg-white p-5 md:p-6">
        <p className="text-xs md:text-sm text-text leading-7 md:leading-8 whitespace-pre-line">
          {text}
        </p>
        {long && (
          <button
            onClick={() => setExpanded(!expanded)}
            className="inline-flex items-center gap-1 mt-3 text-[11px] md:text-xs font-bold text-primary hover:text-primary-dark transition"
          >
            {expanded ? (
              <>
                مشاهده کمتر <ChevronUp className="w-3.5 h-3.5" />
              </>
            ) : (
              <>
                ادامه مطلب <ChevronDown className="w-3.5 h-3.5" />
              </>
            )}
          </button>
        )}
      </div>
    </section>
  );
}