// src/components/shared/sections/FAQSection.tsx
"use client";

import { useState } from "react";
import { ChevronDown, Phone, MessageCircle, HelpCircle } from "lucide-react";

export interface FAQItem {
  q: string;
  a: string;
}

interface FAQSectionProps {
  title?: string;
  subtitle?: string;
  items: FAQItem[];
  supportText?: React.ReactNode;
  supportPhone?: string;
  className?: string;
  /** حالت فشرده برای داخل صفحات پست */
  compact?: boolean;
  /** نمایش دایره شماره کنار سؤال */
  showNumbers?: boolean;
}

/* ═══ آیتم آکاردئون (ترکیب استایل HotelFAQ + FAQSection) ═══ */
function FAQAccordionItem({
  q,
  a,
  index,
  showNumbers = true,
}: {
  q: string;
  a: string;
  index: number;
  showNumbers?: boolean;
}) {
  const [open, setOpen] = useState(index === 0);

  return (
    <div
      className={`rounded-xl border transition-all duration-200 ${
        open
          ? "border-primary/40 bg-primary/5 shadow-sm"
          : "border-border/60 bg-white hover:border-border"
      }`}
    >
      <button
        onClick={() => setOpen(!open)}
        className="w-full flex items-center gap-3 p-3.5 text-start group cursor-pointer"
        aria-expanded={open}
      >
        {showNumbers && (
          <span
            className={`flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-[11px] font-black transition ${
              open
                ? "bg-primary text-white shadow-sm"
                : "bg-primary/10 text-primary group-hover:bg-primary/20"
            }`}
          >
            {index + 1}
          </span>
        )}

        <span
          className={`flex-1 text-sm font-bold leading-relaxed transition-colors ${
            open ? "text-primary" : "text-text-strong group-hover:text-primary"
          }`}
        >
          {q}
        </span>

        <ChevronDown
          className={`flex-shrink-0 w-4 h-4 transition-transform duration-300 ${
            open ? "rotate-180 text-primary" : "text-text-muted"
          }`}
        />
      </button>

      {/* محتوا با انیمیشن max-height */}
      <div
        className={`grid transition-all duration-300 ease-out ${
          open ? "grid-rows-[1fr] opacity-100" : "grid-rows-[0fr] opacity-0"
        }`}
      >
        <div className="overflow-hidden">
          <p
            className={`px-3.5 pb-3.5 text-xs text-text leading-7 ${
              showNumbers ? "ps-14" : ""
            }`}
          >
            {a}
          </p>
        </div>
      </div>
    </div>
  );
}

export default function FAQSection({
  title,
  subtitle,
  items,
  supportText,
  supportPhone,
  className = "",
  compact = false,
  showNumbers = true,
}: FAQSectionProps) {
  if (!items || items.length === 0) return null;

  // تقسیم آیتم‌ها به دو ستون برای دسکتاپ
  const midIndex = Math.ceil(items.length / 2);
  const firstColumn = items.slice(0, midIndex);
  const secondColumn = items.slice(midIndex);

  return (
    <section className={className}>
      {/* ═══ Header (فقط اگر title داده شده) ═══ */}
      {title && (
        <div className="flex items-center gap-3 mb-5">
          <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
            <HelpCircle className="w-5 h-5 text-primary" />
          </div>
          <div>
            <h2 className="text-base font-extrabold text-text-strong">{title}</h2>
            {subtitle && (
              <p className="text-[11px] text-text-muted mt-0.5">{subtitle}</p>
            )}
          </div>
        </div>
      )}

      {/* ═══ باکس سوالات ═══ */}
      <div className="space-y-2.5">
        {/* موبایل: تک ستونه */}
        <div className="block lg:hidden space-y-2">
          {items.map((item, i) => (
            <FAQAccordionItem
              key={i}
              q={item.q}
              a={item.a}
              index={i}
              showNumbers={showNumbers}
            />
          ))}
        </div>

        {/* دسکتاپ: دو ستونه */}
        <div className="hidden lg:grid lg:grid-cols-2 lg:gap-3">
          <div className="space-y-2">
            {firstColumn.map((item, i) => (
              <FAQAccordionItem
                key={i}
                q={item.q}
                a={item.a}
                index={i}
                showNumbers={showNumbers}
              />
            ))}
          </div>
          <div className="space-y-2">
            {secondColumn.map((item, i) => (
              <FAQAccordionItem
                key={i}
                q={item.q}
                a={item.a}
                index={i + midIndex}
                showNumbers={showNumbers}
              />
            ))}
          </div>
        </div>
      </div>

      {/* ═══ فوتر با اطلاعات پشتیبانی ═══ */}
      {supportText && (
        <div className="mt-4 px-5 py-4 bg-gradient-to-l from-primary/8 to-primary/3 rounded-2xl border border-primary/10 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
              <MessageCircle className="w-4 h-4 text-primary" />
            </div>
            <p className="text-xs text-text leading-5">{supportText}</p>
          </div>

          {supportPhone && (
            <a
              href={`tel:${supportPhone}`}
              className="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white font-bold text-xs shadow-sm hover:shadow-md transition-all group whitespace-nowrap"
            >
              <Phone className="w-3.5 h-3.5 group-hover:rotate-12 transition-transform" />
              تماس با پشتیبانی
            </a>
          )}
        </div>
      )}
    </section>
  );
}