"use client";
import { useEffect, useState } from "react";
import { ShieldCheck, Zap, Headphones } from "lucide-react";
import SearchWidget from "./SearchWidget";
import HeroBackground from "./HeroBackground";
import { HERO_TABS, HERO_FALLBACK } from "@/lib/constants/hero-images";

export default function HomeHero() {
  const [tabId, setTabId] = useState("flight");
  const [isDesktop, setIsDesktop] = useState(false);

  /* ✅ فقط در دسکتاپ (>=768px) تصاویر هیرو mount می‌شن */
  useEffect(() => {
    const mq = window.matchMedia("(min-width: 768px)");
    const update = () => setIsDesktop(mq.matches);
    update();
    mq.addEventListener("change", update);
    return () => mq.removeEventListener("change", update);
  }, []);

  /* ✅ fallback امن اگر تب در HERO_TABS نبود */
  const tab = HERO_TABS[tabId] ?? HERO_FALLBACK;

  return (
    <section className="pt-6 md:pt-0">
      {/* ✅ عنوان فقط برای SEO (screen-reader) — هیچ متن видимی روی هیرو نیست */}
      <h1 className="sr-only">{tab.title}</h1>

      {/* ═══ هیرو دسکتاپ: با فاصله از لبه‌های صفحه ═══
          تصویر کامل فیت می‌شه (object-contain) و با پس‌زمینه سفید صفحه ادغام می‌شه */}
      <div className="ns-container">
        <div className="relative hidden md:block w-full aspect-[2172/650]">
          {isDesktop && <HeroBackground activeTab={tabId} />}
        </div>
      </div>

      <div className="ns-container">
        {/* ═══ کارت جستجو — کمی روی محوشدگی پایین تصویر ═══
            اگه خواستید کارت کاملاً زیر تصویر باشه: md:-mt-12 رو به md:mt-4 تغییر بدید */}
        <div className="relative z-20 md:-mt-12">
          <SearchWidget activeTab={tabId} onTabChange={setTabId} float />
        </div>

        {/* ═══ ردیف اعتماد ═══ */}
        <div className="mt-5 md:mt-8 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-text-muted text-xs md:text-sm">
          <span className="flex items-center gap-2">
            <ShieldCheck className="w-4 h-4 text-primary" /> پرداخت امن
          </span>
          <span className="flex items-center gap-2">
            <Zap className="w-4 h-4 text-primary" /> صدور آنی واچر
          </span>
          <span className="flex items-center gap-2">
            <Headphones className="w-4 h-4 text-primary" /> پشتیبانی ۲۴/۷
          </span>
        </div>
      </div>
    </section>
  );
}