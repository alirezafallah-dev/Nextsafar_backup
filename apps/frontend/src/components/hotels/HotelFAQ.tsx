"use client";
import { useState } from "react";
import { ChevronDown, HelpCircle } from "lucide-react";
import SectionHeader from "../home/SectionHeader";

const faqs = [
  {
    q: "تفاوت هتل‌های ۳، ۴ و ۵ ستاره چیست؟",
    a: "هتل‌های ۳ ستاره امکانات پایه مثل اتاق تمیز، حمام اختصاصی و صبحانه را دارند. هتل‌های ۴ ستاره امکانات اضافه مانند استخر، باشگاه و رستوران دارند. هتل‌های ۵ ستاره خدمات لوکس، اسپا، چندین رستوران و خدمات ۲۴ ساعته ارائه می‌دهند.",
  },
  {
    q: "آیا امکان کنسل کردن رزرو هتل وجود دارد؟",
    a: "بستگی به قوانین هر هتل دارد. برخی هتل‌ها کنسل رایگان تا ۲۴ ساعت قبل از ورود را ارائه می‌دهند، برخی تا ۴۸ ساعت و برخی هیچ امکان کنسلی ندارند. این مورد در صفحه هر هتل ذکر می‌شود.",
  },
  {
    q: "ساعت ورود و خروج هتل چه زمانی است؟",
    a: "معمولاً ساعت ورود (Check-in) از ۱۴:۰۰ و ساعت خروج (Check-out) تا ۱۲:۰۰ ظهر است. درخواست ورود زودتر یا خروج دیرتر با هزینه اضافه امکان‌پذیر است.",
  },
  {
    q: "آیا در هتل‌ها اینترنت رایگان وجود دارد؟",
    a: "اکثر هتل‌های ۳ ستاره به بالا وای‌فای رایگان در لابی و اتاق‌ها ارائه می‌دهند. برخی هتل‌های لوکس اینترنت پر سرعت جداگانه با هزینه اضافه دارند.",
  },
  {
    q: "آیا امکان آوردن حیوان خانگی به هتل وجود دارد؟",
    a: "بسیاری از هتل‌ها حیوانات خانگی را نمی‌پذیرند، اما برخی هتل‌ها این امکان را با پرداخت هزینه اضافه و قوانین خاص فراهم می‌کنند. بهتر است قبل از رزرو تماس بگیرید.",
  },
  {
    q: "هتل‌های All Inclusive چه خدماتی ارائه می‌دهند؟",
    a: "هتل‌های All Inclusive (همه چیز شامل) صبحانه، ناهار، شام، میان‌وعده‌ها و نوشیدنی‌ها را در قیمت اتاق دارند. برخی حتی فعالیت‌های تفریحی و گشت‌ها را شامل می‌شوند.",
  },
];

function FAQItem({ q, a, index }: { q: string; a: string; index: number }) {
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

export default function HotelFAQ() {
  return (
    <section className="ns-container py-8 md:py-12">
      <SectionHeader
        title="سوالات متداول درباره هتل‌ها"
        subtitle="پاسخ به پرسش‌های رایج مسافران درباره اقامت در هتل"
      />
      <div className="max-w-3xl mx-auto">
        <div className="bg-white rounded-xl border border-border shadow-sm overflow-hidden">
          <div className="px-4 md:px-5 py-4 border-b border-border bg-bg-sec/30 flex items-center gap-2">
            <HelpCircle className="w-5 h-5 text-primary" />
            <h3 className="font-bold text-text-strong text-sm md:text-base">پرسش‌های متداول</h3>
          </div>
          <div className="px-3 md:px-5">
            {faqs.map((faq, i) => (
              <FAQItem key={i} q={faq.q} a={faq.a} index={i} />
            ))}
          </div>
          <div className="px-4 md:px-5 py-4 bg-bg-sec/30 border-t border-border">
            <p className="text-xs text-text-muted text-center">
              سوال دیگری دارید؟ با <strong className="text-primary">پشتیبانی ۲۴ ساعته</strong> ما تماس بگیرید
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}