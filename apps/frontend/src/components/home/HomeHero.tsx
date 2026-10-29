"use client";
import { useState } from "react";
import { ShieldCheck, Zap, Headphones } from "lucide-react";
import SearchWidget from "./SearchWidget";
import HeroBackground from "./HeroBackground";
import { HERO_TABS, HERO_FALLBACK } from "@/lib/constants/hero-images";
import { useMediaQuery } from "@/hooks/use-media-query";

export default function HomeHero() {
  const [tabId, setTabId] = useState("flight");
  
  // ✅ استفاده از useMediaQuery با default true برای SSR
  // در SSR و hydration اولیه، true فرض می‌شود تا تصاویر لود شوند
  const isDesktop = useMediaQuery("(min-width: 768px)", { defaultMatches: true });

  const tab = HERO_TABS[tabId] ?? HERO_FALLBACK;

  return (
    <section className="pt-6 md:pt-0">
      <h1 className="sr-only">{tab.title}</h1>

      {/* ═══ هیرو دسکتاپ ═══ */}
      <div className="ns-container">
        <div className="relative hidden md:block w-full aspect-[2172/650]">
          {/* ✅ بدون شرط isDesktop - همیشه mount شود */}
          <HeroBackground activeTab={tabId} />
        </div>
      </div>

      <div className="ns-container">
        <div className="relative z-20 md:-mt-12">
          <SearchWidget activeTab={tabId} onTabChange={setTabId} float />
        </div>

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