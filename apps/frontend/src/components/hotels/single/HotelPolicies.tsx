// src/components/hotels/single/HotelPolicies.tsx
"use client";

import { Clock, CheckCircle, XCircle } from "lucide-react";

const RULES_OK = ["رزرو قطعی پس از پرداخت بیعانه", "کنسلی رایگان تا ۴۸ ساعت قبل", "پذیرش ۲۴ ساعته"];
const RULES_NO = ["ورود حیوانات خانگی مجاز نیست", "سیگار کشیدن در اتاق‌ها ممنوع", "برگزاری مراسم بدون هماهنگی ممنوع"];

export default function HotelPolicies() {
  return (
    <section id="policies">
      <div className="flex items-center justify-between gap-3 mb-5 md:mb-6">
        <div className="min-w-0">
          <h2 className="ns-section-title !mb-1">قوانین هتل</h2>
          <p className="text-xs md:text-sm text-text-muted">
            قوانین ورود، خروج و اقامت در این هتل
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="p-5 md:p-6 rounded-[10px] md:rounded-lg border border-border bg-white hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out space-y-3">
          <div className="flex items-center gap-2 pb-3 border-b border-border/60">
            <Clock className="w-5 h-5 text-primary" />
            <span className="text-sm md:text-base font-extrabold text-text-strong">ساعات ورود و خروج</span>
          </div>
          <div className="flex justify-between text-xs md:text-sm">
            <span className="text-text-muted">ورود از</span>
            <span className="font-bold text-text-strong">۱۴:۰۰</span>
          </div>
          <div className="flex justify-between text-xs md:text-sm">
            <span className="text-text-muted">خروج تا</span>
            <span className="font-bold text-text-strong">۱۲:۰۰</span>
          </div>
        </div>

        <div className="p-5 md:p-6 rounded-[10px] md:rounded-lg border border-border bg-white hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out space-y-3">
          {RULES_OK.map((r) => (
            <div key={r} className="flex items-center gap-2 text-xs md:text-sm text-text">
              <CheckCircle className="w-4 h-4 md:w-5 md:h-5 text-success shrink-0" /> {r}
            </div>
          ))}
          {RULES_NO.map((r) => (
            <div key={r} className="flex items-center gap-2 text-xs md:text-sm text-text">
              <XCircle className="w-4 h-4 md:w-5 md:h-5 text-danger shrink-0" /> {r}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}