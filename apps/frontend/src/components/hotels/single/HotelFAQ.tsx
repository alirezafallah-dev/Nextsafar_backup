// src/components/hotels/single/HotelFAQ.tsx
"use client";

import { useState } from "react";
import { ChevronDown } from "lucide-react";

const FAQS = [
  { q: "ساعت ورود و خروج هتل چیست؟", a: "ورود از ساعت ۱۴:۰۰ و خروج تا ساعت ۱۲:۰۰ روز بعد امکان‌پذیر است." },
  { q: "آیا امکان رزرو بدون پیش‌پرداخت وجود دارد؟", a: "بله، برای برخی اتاق‌ها رزرو با پرداخت در محل نیز فراهم است." },
  { q: "آیا هتل ترانسفر فرودگاهی ارائه می‌دهد؟", a: "بله، با هماهنگی قبلی (حداقل ۲۴ ساعت) ترانسفر رفت و برگشت ارائه می‌شود." },
];

export default function HotelFAQ() {
  const [open, setOpen] = useState<number | null>(0);
  return (
    <section id="faq" className="space-y-3">
      <h2 className="text-base font-extrabold text-text-strong">سؤالات متداول</h2>
      {FAQS.map((f, i) => (
        <div key={i} className={`rounded-xl border transition ${open === i ? "border-primary/40 bg-primary/5" : "border-border/60 bg-white"}`}>
          <button onClick={() => setOpen(open === i ? null : i)} className="w-full flex items-center justify-between gap-3 p-3.5 text-start">
            <span className="text-xs font-bold text-text-strong">{f.q}</span>
            <ChevronDown className={`w-4 h-4 text-text-muted shrink-0 transition-transform ${open === i ? "rotate-180 text-primary" : ""}`} />
          </button>
          {open === i && <p className="px-3.5 pb-3.5 text-[11px] text-text leading-6">{f.a}</p>}
        </div>
      ))}
    </section>
  );
}