"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { StoryCard } from "@/components/shared/cards";

export interface StoryItem {
  slug: string;
  title: string;
  image: string;
  place?: string;
}

export default function StoriesCarousel({ items }: { items: StoryItem[] }) {
  const ref = useRef<HTMLDivElement>(null);
  const [canPrev, setCanPrev] = useState(false);
  const [canNext, setCanNext] = useState(false);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const update = useCallback(() => {
    const el = ref.current;
    if (!el) return;
    const max = el.scrollWidth - el.clientWidth;
    const pos = Math.abs(el.scrollLeft);
    setCanPrev(pos > 4);
    setCanNext(pos < max - 4);
  }, []);

  useEffect(() => {
    update();
    const el = ref.current;
    if (!el) return;
    el.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    return () => {
      el.removeEventListener("scroll", update);
      window.removeEventListener("resize", update);
    };
  }, [update]);

  const scroll = (dir: 1 | -1) => {
    const el = ref.current;
    if (!el) return;
    const isRTL = document.documentElement.dir === "rtl";
    const amount = el.clientWidth * 0.8;
    el.scrollBy({ left: (isRTL ? -dir : dir) * amount, behavior: "smooth" });
  };

  return (
    <div className="relative">
      {mounted && (
        <>
          <button
            onClick={() => scroll(-1)}
            disabled={!canPrev}
            aria-label="قبلی"
            className="hidden md:flex absolute -start-5 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white shadow-card-hover border border-border items-center justify-center text-text hover:text-primary disabled:opacity-40 transition"
          >
            <ChevronRight className="w-5 h-5" />
          </button>
          <button
            onClick={() => scroll(1)}
            disabled={!canNext}
            aria-label="بعدی"
            className="hidden md:flex absolute -end-5 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white shadow-card-hover border border-border items-center justify-center text-text hover:text-primary disabled:opacity-40 transition"
          >
            <ChevronLeft className="w-5 h-5" />
          </button>
        </>
      )}

      <div
        ref={ref}
        className="ns-bleed-x flex gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory pt-1 pb-2"
      >
        {items.map((item) => (
          <StoryCard
            key={item.slug}
            slug={item.slug}
            title={item.title}
            image={item.image}
            place={item.place}
          />
        ))}
      </div>
    </div>
  );
}