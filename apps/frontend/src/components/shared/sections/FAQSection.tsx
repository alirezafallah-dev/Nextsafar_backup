"use client";
import { useState } from "react";
import { ChevronDown, HelpCircle } from "lucide-react";
import SectionHeader from "@/components/home/SectionHeader";

export interface FAQItem {
  q: string;
  a: string;
}

interface FAQSectionProps {
  title: string;
  subtitle?: string;
  items: FAQItem[];
  supportText?: React.ReactNode;
  className?: string;
}

function FAQAccordionItem({ q, a, index }: { q: string; a: string; index: number }) {
  const [open, setOpen] = useState(index === 0);
  return (
    <div className="border-b border-border last:border-0">
      <button
        onClick={() => setOpen(!open)}
        className="w-full flex items-start gap-3 py-4 text-start group cursor-pointer"
      >
        <span className="flex-shrink-0 w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-black group-hover:bg-primary group-hover:text-white transition">
          {index + 1}
        </span>
        <div className="flex-1 min-w-0">
          <h3 className="font-bold text-sm md:text-base text-text-strong group-hover:text-primary transition-colors">
            {q}
          </h3>
        </div>
        <ChevronDown
          className={`flex-shrink-0 w-5 h-5 text-text-muted transition-transform duration-300 ${
            open ? "rotate-180" : ""
          }`}
        />
      </button>
      <div
        className={`overflow-hidden transition-all duration-300 ${
          open ? "max-h-96 pb-4" : "max-h-0"
        }`}
      >
        <p className="text-sm text-text-muted leading-7 pe-10 ps-10">
          {a}
        </p>
      </div>
    </div>
  );
}

export default function FAQSection({
  title,
  subtitle,
  items,
  supportText,
  className = "",
}: FAQSectionProps) {
  if (!items || items.length === 0) return null;

  return (
    <section className={`py-8 md:py-12 ${className}`}>
      <SectionHeader
        title={title}
        subtitle={subtitle || "پاسخ به پرسش‌های رایج شما"}
      />
      <div className="max-w-3xl mx-auto">
        <div className="bg-white rounded-xl border border-border shadow-sm overflow-hidden">
          <div className="px-4 md:px-5 py-4 border-b border-border bg-bg-sec/30 flex items-center gap-2">
            <HelpCircle className="w-5 h-5 text-primary" />
            <h3 className="font-bold text-text-strong text-sm md:text-base">پرسش‌های متداول</h3>
          </div>
          <div className="px-3 md:px-5">
            {items.map((item, i) => (
              <FAQAccordionItem key={i} q={item.q} a={item.a} index={i} />
            ))}
          </div>
          {supportText && (
            <div className="px-4 md:px-5 py-4 bg-bg-sec/30 border-t border-border">
              <p className="text-xs text-text-muted text-center">{supportText}</p>
            </div>
          )}
        </div>
      </div>
    </section>
  );
}