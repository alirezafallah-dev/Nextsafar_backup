"use client";

import { useState, useMemo, useRef, useEffect, useCallback } from "react";
import Link from "next/link";
import Image from "next/image";
import {
  FolderOpen,
  ChevronLeft,
  ChevronRight,
  MapPin,
  Globe,
  Building2,
} from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import { useMediaQuery } from "@/hooks/use-media-query"; // ✅ اضافه شد

/* ═══ Types ═══ */
export interface CategoryLevel1 {
  id: number;
  name: string;
  slug: string;
  image: string;
}

export interface CategoryLevel2 {
  id: number;
  parent_id: number;
  name: string;
  slug: string;
  image: string;
}

export interface CategoryLevel3 {
  id: number;
  parent_id: number;
  name: string;
  slug: string;
  image: string;
  count: number;
}

export interface CascadingCategoryExplorerProps {
  title?: string;
  subtitle?: string;
  levels: CategoryLevel1[];
  subLevels: CategoryLevel2[];
  subSubLevels: CategoryLevel3[];
  initialLevel1Id?: number;
  initialLevel2Id?: number;
  videoSrc?: string;
  videoPoster?: string;
  basePath?: string;
  level1Label?: string;
  level2Label?: string;
  level3Label?: string;
  className?: string;
}

/* ═══ کاروسل با دکمه‌های هوشمند ═══ */
function Carousel({
  children,
  scrollerClassName = "",
}: {
  children: React.ReactNode;
  scrollerClassName?: string;
}) {
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
    setCanPrev(pos > 8);
    setCanNext(max - pos > 8);
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
    const delta = isRTL ? -dir : dir;
    const amount = el.clientWidth * 0.98;
    el.scrollBy({ left: delta * amount, behavior: "smooth" });
  };

  return (
    <div className="relative">
      {mounted && canPrev && (
        <button
          onClick={() => scroll(-1)}
          aria-label="قبلی"
          className="hidden md:flex absolute start-1 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/95 backdrop-blur shadow-lg border border-border items-center justify-center text-text hover:text-primary hover:scale-110 transition"
        >
          <ChevronRight className="w-5 h-5" />
        </button>
      )}

      <div ref={ref} className={scrollerClassName}>
        {children}
      </div>

      {mounted && canNext && (
        <button
          onClick={() => scroll(1)}
          aria-label="بعدی"
          className="hidden md:flex absolute end-1 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/95 backdrop-blur shadow-lg border border-border items-center justify-center text-text hover:text-primary hover:scale-110 transition"
        >
          <ChevronLeft className="w-5 h-5" />
        </button>
      )}
    </div>
  );
}

export default function CascadingCategoryExplorer({
  title = "دسته‌بندی‌ها",
  subtitle = "منطقه، کشور و شهر مورد نظر خود را انتخاب کنید",
  levels,
  subLevels,
  subSubLevels,
  initialLevel1Id,
  initialLevel2Id,
  videoSrc,
  videoPoster,
  basePath = "/category",
  level1Label = "منطقه",
  level2Label = "کشور",
  level3Label = "شهر",
  className = "",
}: CascadingCategoryExplorerProps) {
  // ✅ تشخیص موبایل برای عدم رندر ویدیو
  const isMobile = useMediaQuery("(max-width: 1023px)");

  const [selectedLevel1, setSelectedLevel1] = useState<number>(
    initialLevel1Id ?? levels[0]?.id ?? 0
  );

  const firstLevel2OfLevel1 = useMemo(
    () => subLevels.find((c) => c.parent_id === selectedLevel1),
    [subLevels, selectedLevel1]
  );

  const [selectedLevel2, setSelectedLevel2] = useState<number>(
    initialLevel2Id ?? firstLevel2OfLevel1?.id ?? 0
  );

  const filteredLevel2 = useMemo(
    () => subLevels.filter((c) => c.parent_id === selectedLevel1),
    [subLevels, selectedLevel1]
  );

  const filteredLevel3 = useMemo(
    () => subSubLevels.filter((c) => c.parent_id === selectedLevel2),
    [subSubLevels, selectedLevel2]
  );

  const handleLevel1Select = (level: CategoryLevel1) => {
    setSelectedLevel1(level.id);
    const firstLevel2 = subLevels.find((c) => c.parent_id === level.id);
    setSelectedLevel2(firstLevel2?.id ?? 0);
  };

  const handleLevel2Select = (level: CategoryLevel2) => {
    setSelectedLevel2(level.id);
  };

  const selectedLevel1Name = levels.find((r) => r.id === selectedLevel1)?.name ?? "";
  const selectedLevel2Name = subLevels.find((c) => c.id === selectedLevel2)?.name ?? "";

  return (
    <section className={`ns-container py-8 md:py-12 ${className}`}>
      {/* ═══ عنوان ═══ */}
      <div className="mb-6 md:mb-8">
        <div className="flex items-center gap-2 mb-0">
          <FolderOpen className="w-5 h-5 md:w-6 md:h-6 text-primary flex-shrink-0" />
          <h2 className="text-xl md:text-2xl font-extrabold text-text-strong">
            {title}
          </h2>
        </div>
        <p className="text-sm md:text-base text-text-muted mt-2">{subtitle}</p>
      </div>

      {/* ═══ Layout اصلی ═══ */}
      <div className="flex flex-col lg:flex-row gap-5 lg:gap-6 lg:h-[635px]">
        {/* ═══ ویدیو - فقط در دسکتاپ رندر می‌شود ═══ */}
        {videoSrc && !isMobile && (
          <div className="w-full lg:w-[340px] flex-shrink-0 order-2 lg:order-1">
            <div className="lg:sticky lg:top-24 lg:h-full">
              <div className="relative w-full h-[280px] sm:h-[320px] lg:h-full lg:aspect-auto rounded-2xl overflow-hidden bg-bg-sec shadow-lg">
                <video
                  src={videoSrc}
                  poster={videoPoster}
                  autoPlay
                  muted
                  loop
                  playsInline
                  controls
                  preload="metadata"
                  className="w-full h-full object-cover"
                >
                  مرورگر شما از ویدیو پشتیبانی نمی‌کند.
                </video>
                <div className="absolute top-3 start-3 flex items-center gap-1.5 px-2 py-1 rounded-md bg-black/60 backdrop-blur z-10 pointer-events-none">
                  <div className="w-1.5 h-1.5 rounded-full bg-danger animate-pulse" />
                  <span className="text-[10px] font-bold text-white">LIVE</span>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* ═══ 3 لیست ═══ */}
        <div className={`flex-1 min-w-0 ${videoSrc && !isMobile ? "order-1 lg:order-2" : ""} flex flex-col gap-4 lg:gap-5`}>
          {/* ═══ سطح 1 (منطقه) ═══ */}
          <div className="flex-shrink-0">
            <div className="flex items-center justify-between mb-0 gap-2">
              <div className="flex items-center gap-2 min-w-0">
                <Globe className="w-4 h-4 text-primary flex-shrink-0" />
                <h3 className="text-base font-extrabold text-text-strong truncate">
                  {level1Label}
                </h3>
              </div>
              <span className="text-xs text-text-muted font-bold flex-shrink-0">
                {faDigits(levels.length)} {level1Label}
              </span>
            </div>

            <div className="mt-3">
              <Carousel
                scrollerClassName="flex gap-2 md:gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory"
              >
                {levels.map((level) => (
                  <button
                    key={level.id}
                    onClick={() => handleLevel1Select(level)}
                    className={`relative w-[90px] md:w-[100px] aspect-square flex-shrink-0 rounded-xl overflow-hidden transition-all duration-300 snap-start ${
                      selectedLevel1 === level.id
                        ? "ring-2 ring-primary shadow-lg"
                        : "hover:shadow-md"
                    }`}
                  >
                    <Image
                      src={level.image}
                      alt={level.name}
                      fill
                      sizes="100px"
                      className="object-cover"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                    {selectedLevel1 === level.id && (
                      <div className="absolute top-1.5 end-1.5 w-4 h-4 rounded-full bg-primary flex items-center justify-center z-10">
                        <svg
                          className="w-2.5 h-2.5 text-white"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={3}
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </div>
                    )}
                    <div className="absolute bottom-0 inset-x-0 p-1.5 text-white">
                      <h4 className="text-[11px] md:text-xs font-extrabold line-clamp-1">
                        {level.name}
                      </h4>
                    </div>
                  </button>
                ))}
              </Carousel>
            </div>
          </div>

          {/* ═══ سطح 2 (کشور) ═══ */}
          <div className="flex-shrink-0">
            <div className="flex items-center justify-between mb-0 gap-2">
              <div className="flex items-center gap-2 min-w-0">
                <Building2 className="w-4 h-4 text-primary flex-shrink-0" />
                <h3 className="text-base font-extrabold text-text-strong truncate">
                  {level2Label}
                  {selectedLevel1Name && (
                    <span className="ms-2 text-xs font-bold text-text-muted">
                      در {selectedLevel1Name}
                    </span>
                  )}
                </h3>
              </div>
              <span className="text-xs text-text-muted font-bold flex-shrink-0">
                {faDigits(filteredLevel2.length)} {level2Label}
              </span>
            </div>

            <div className="mt-3">
              {filteredLevel2.length > 0 ? (
                <Carousel
                  scrollerClassName="flex gap-2 md:gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory"
                >
                  {filteredLevel2.map((level) => (
                    <button
                      key={level.id}
                      onClick={() => handleLevel2Select(level)}
                      className={`relative w-[100px] md:w-[120px] aspect-square flex-shrink-0 rounded-xl overflow-hidden transition-all duration-300 snap-start ${
                        selectedLevel2 === level.id
                          ? "ring-2 ring-primary shadow-lg"
                          : "hover:shadow-md"
                      }`}
                    >
                      <Image
                        src={level.image}
                        alt={level.name}
                        fill
                        sizes="120px"
                        className="object-cover"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
                      {selectedLevel2 === level.id && (
                        <div className="absolute top-1.5 end-1.5 w-4 h-4 rounded-full bg-primary flex items-center justify-center z-10">
                          <svg
                            className="w-2.5 h-2.5 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                          >
                            <path
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth={3}
                              d="M5 13l4 4L19 7"
                            />
                          </svg>
                        </div>
                      )}
                      <div className="absolute bottom-0 inset-x-0 p-1.5 text-white">
                        <h4 className="text-[11px] md:text-xs font-extrabold line-clamp-1">
                          {level.name}
                        </h4>
                      </div>
                    </button>
                  ))}
                </Carousel>
              ) : (
                <div className="p-4 rounded-xl bg-bg-sec border border-border text-center flex items-center justify-center h-[100px]">
                  <p className="text-xs text-text-muted">
                    {level2Label}ی برای این {level1Label} یافت نشد
                  </p>
                </div>
              )}
            </div>
          </div>

          {/* ═══ سطح 3 (شهر) ═══ */}
          <div className="flex-1 min-h-0 flex flex-col">
            <div className="flex items-center justify-between mb-0 gap-2">
              <div className="flex items-center gap-2 min-w-0">
                <MapPin className="w-4 h-4 text-primary flex-shrink-0" />
                <h3 className="text-base font-extrabold text-text-strong truncate">
                  {level3Label}
                  {selectedLevel2Name && (
                    <span className="ms-2 text-xs font-bold text-text-muted">
                      در {selectedLevel2Name}
                    </span>
                  )}
                </h3>
              </div>
              <span className="text-xs text-text-muted font-bold flex-shrink-0">
                {faDigits(filteredLevel3.length)} {level3Label}
              </span>
            </div>

            <div className="mt-3 flex-1 min-h-0">
              {filteredLevel3.length > 0 ? (
                <Carousel
                  scrollerClassName="flex gap-3 md:gap-4 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory"
                >
                  {filteredLevel3.map((level) => (
                    <Link
                      key={level.id}
                      href={`${basePath}/${level.slug}`}
                      className="group relative w-[200px] md:w-[calc(25%-0.75rem)] aspect-[4/5] md:aspect-auto md:h-[250px] flex-shrink-0 rounded-xl overflow-hidden hover:-translate-y-1 transition-all duration-300 snap-start"
                    >
                      <Image
                        src={level.image}
                        alt={level.name}
                        fill
                        sizes="(max-width: 768px) 200px, 25vw"
                        className="object-cover group-hover:scale-110 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                      <div className="absolute bottom-0 inset-x-0 p-3 text-white">
                        <h4 className="text-sm md:text-base font-extrabold mb-1 line-clamp-1 group-hover:text-primary-lightest transition-colors">
                          {level.name}
                        </h4>
                        <div className="flex items-center justify-between">
                          <div className="flex items-center gap-1 text-[10px] md:text-xs text-white/80">
                            <MapPin className="w-3 h-3" />
                            <span>{faDigits(level.count)} مورد</span>
                          </div>
                          <ChevronLeft className="w-4 h-4 text-white/80 group-hover:text-white group-hover:-translate-x-1 transition-all rtl:rotate-180" />
                        </div>
                      </div>
                    </Link>
                  ))}
                </Carousel>
              ) : (
                <div className="flex items-center justify-center p-8 rounded-xl bg-bg-sec border border-border h-full">
                  <div className="text-center">
                    <MapPin className="w-10 h-10 text-text-subtle mx-auto mb-2" />
                    <p className="text-sm text-text-muted">
                      {level3Label}ی برای این {level2Label} یافت نشد
                    </p>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}