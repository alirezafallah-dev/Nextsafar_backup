"use client";

import { useEffect, useRef, useState } from "react";
import Image from "next/image";
import { HERO_TABS } from "@/lib/constants/hero-images";

/* ✅ Helper امن برای preload */
function preloadImage(src: string) {
  if (typeof window === "undefined") return;
  const img = document.createElement("img");
  img.src = src;
}

export default function HeroBackground({ activeTab }: { activeTab: string }) {
  const [i, setI] = useState(0);
  const lastSrc = useRef<string>("");

  const activeImages = HERO_TABS[activeTab]?.images ?? [];
  const activeSrc = activeImages[i % (activeImages.length || 1)] ?? "";

  /* چرخش خودکار فقط وقتی تب فعال بیش از یک تصویر دارد */
  useEffect(() => {
    setI(0);
    if (activeImages.length < 2) return;
    const t = setInterval(
      () => setI((v) => (v + 1) % activeImages.length),
      6000,
    );
    return () => clearInterval(t);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [activeTab, activeImages.length]);

  /* preload تصویر بعدیِ چرخش */
  useEffect(() => {
    const next = activeImages[(i + 1) % activeImages.length];
    if (next && next !== activeSrc) preloadImage(next);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [i, activeTab]);

  /* idle: preload تصویر اول تب‌های دیگر */
  useEffect(() => {
    const idle: (cb: () => void) => number =
      "requestIdleCallback" in window
        ? (cb) => (window as any).requestIdleCallback(cb, { timeout: 4000 })
        : (cb) => window.setTimeout(cb, 2500) as unknown as number;

    const id = idle(() => {
      Object.entries(HERO_TABS).forEach(([tab, def]) => {
        if (tab === activeTab) return;
        const src = def?.images?.[0];
        if (src) preloadImage(src);
      });
    });
    return () => {
      if ("cancelIdleCallback" in window) (window as any).cancelIdleCallback(id);
      else clearTimeout(id);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [activeTab]);

  return (
    <div className="absolute inset-0 bg-white">
      {/* فقط لایه فعال — بدون افکت */}
      {activeSrc && (
        <Image
          key={activeSrc}
          src={activeSrc}
          alt=""
          fill
          sizes="100vw"
          quality={85}
          loading="eager"
          fetchPriority="high"
          className="object-contain object-top"
        />
      )}
    </div>
  );
}