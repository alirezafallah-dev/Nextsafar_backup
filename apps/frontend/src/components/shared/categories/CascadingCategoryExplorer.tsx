// src/components/shared/categories/CascadingCategoryExplorer.tsx
"use client";

import { useState, useMemo, useEffect, useRef, useCallback } from "react";
import Image from "next/image";
import Link from "next/link";
import { Globe, Building2, MapPin, ChevronLeft, Play, Pause } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";

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
  basePathByLevel?: {
    level2?: string;
    level3?: string;
  };
  level1Label?: string;
  level2Label?: string;
  level3Label?: string;
  className?: string;
}

/* ═══ Carousel ساده ═══ */
function Carousel({
  children,
  scrollerClassName,
}: {
  children: React.ReactNode;
  scrollerClassName?: string;
}) {
  return <div className={scrollerClassName}>{children}</div>;
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
  basePathByLevel,
  level1Label = "منطقه",
  level2Label = "کشور",
  level3Label = "شهر",
  className = "",
}: CascadingCategoryExplorerProps) {
  const [selectedLevel1, setSelectedLevel1] = useState<number>(
    initialLevel1Id ?? levels[0]?.id ?? 0
  );
  const [selectedLevel2, setSelectedLevel2] = useState<number>(
    initialLevel2Id ?? 0
  );
  const [isMobile, setIsMobile] = useState(false);
  const [videoPlaying, setVideoPlaying] = useState(false);
  const videoRef = useRef<HTMLVideoElement>(null);

  useEffect(() => {
    const check = () => setIsMobile(window.innerWidth < 1024);
    check();
    window.addEventListener("resize", check);
    return () => window.removeEventListener("resize", check);
  }, []);

  /* انتخاب خودکار اولین country بعد از انتخاب region */
  useEffect(() => {
    const first = subLevels.find((c) => c.parent_id === selectedLevel1);
    if (first && selectedLevel2 !== first.id) {
      setSelectedLevel2(first.id);
    } else if (!first) {
      setSelectedLevel2(0);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedLevel1]);

  const handleLevel1Select = useCallback((level: CategoryLevel1) => {
    setSelectedLevel1(level.id);
  }, []);

  const handleLevel2Select = useCallback((level: CategoryLevel2) => {
    setSelectedLevel2(level.id);
  }, []);

  const toggleVideo = () => {
    if (!videoRef.current) return;
    if (videoPlaying) {
      videoRef.current.pause();
      setVideoPlaying(false);
    } else {
      videoRef.current.play();
      setVideoPlaying(true);
    }
  };

  /* فیلترها */
  const selectedLevel1Name = useMemo(
    () => levels.find((l) => l.id === selectedLevel1)?.name ?? "",
    [levels, selectedLevel1]
  );

  const filteredLevel2 = useMemo(
    () => subLevels.filter((c) => c.parent_id === selectedLevel1),
    [subLevels, selectedLevel1]
  );

  const selectedLevel2Name = useMemo(
    () => subLevels.find((c) => c.id === selectedLevel2)?.name ?? "",
    [subLevels, selectedLevel2]
  );

  /* ✅ slug کشور انتخاب شده برای ساخت مسیر شهر */
  const selectedLevel2Slug = useMemo(
    () => subLevels.find((c) => c.id === selectedLevel2)?.slug ?? "",
    [subLevels, selectedLevel2]
  );

  const filteredLevel3 = useMemo(
    () => subSubLevels.filter((c) => c.parent_id === selectedLevel2),
    [subSubLevels, selectedLevel2]
  );

  /* ═══ مسیرهای لینک ═══ */
  const countryPath = basePathByLevel?.level2 || "/hotels";

  return (
    <section className={`ns-container py-8 md:py-12 ${className}`}>
      {/* Header */}
      <div className="flex items-center justify-between gap-3 mb-5 md:mb-6">
        <div className="min-w-0">
          <h2 className="ns-section-title !mb-1">{title}</h2>
          <p className="text-xs md:text-sm text-text-muted">{subtitle}</p>
        </div>
      </div>

      {/* Main layout: video + 3 lists */}
      <div className="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-5 lg:gap-6">
        {/* Video */}
        {videoSrc && (
          <div className={`${isMobile ? "order-2" : ""}`}>
            <div className="relative aspect-square rounded-[10px] md:rounded-lg overflow-hidden bg-bg-sec border border-border">
              <video
                ref={videoRef}
                src={videoSrc}
                poster={videoPoster}
                loop
                muted
                playsInline
                onPlay={() => setVideoPlaying(true)}
                onPause={() => setVideoPlaying(false)}
                className="w-full h-full object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none" />
              <button
                onClick={toggleVideo}
                className="absolute bottom-3 right-3 w-10 h-10 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center text-text-strong shadow-lg hover:bg-white transition z-10"
                aria-label={videoPlaying ? "توقف ویدیو" : "پخش ویدیو"}
              >
                {videoPlaying ? (
                  <Pause className="w-4 h-4" />
                ) : (
                  <Play className="w-4 h-4 mr-[-2px]" />
                )}
              </button>
            </div>
          </div>
        )}

        {/* ═══ 3 لیست ═══ */}
        <div
          className={`flex-1 min-w-0 ${
            videoSrc && !isMobile ? "order-1 lg:order-2" : ""
          } flex flex-col gap-4 lg:gap-5`}
        >
          {/* ═══ سطح 1 (منطقه) — دکمه انتخاب ═══ */}
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
                    className={`relative w-[90px] md:w-[100px] aspect-square flex-shrink-0 rounded-md overflow-hidden transition-all duration-300 snap-start ${
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

          {/* ═══ سطح 2 (کشور) — لینک مستقیم به صفحه کشور ═══ */}
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
                    <Link
                      key={level.id}
                      href={`${countryPath}/${level.slug}`}
                      onClick={() => handleLevel2Select(level)}
                      className={`group relative w-[100px] md:w-[120px] aspect-square flex-shrink-0 rounded-lg overflow-hidden transition-all duration-300 snap-start ${
                        selectedLevel2 === level.id
                          ? "ring-2 ring-primary shadow-lg"
                          : "hover:shadow-md hover:-translate-y-0.5"
                      }`}
                    >
                      <Image
                        src={level.image}
                        alt={level.name}
                        fill
                        sizes="120px"
                        className="object-cover group-hover:scale-105 transition-transform duration-500"
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
                        <h4 className="text-[11px] md:text-xs font-extrabold line-clamp-1 group-hover:text-primary-lightest transition-colors">
                          {level.name}
                        </h4>
                      </div>
                    </Link>
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

          {/* ═══ سطح 3 (شهر) — لینک شامل slug کشور ═══ */}
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
                      href={`${countryPath}/${selectedLevel2Slug}/${level.slug}`}
                      className="group relative w-[200px] md:w-[calc(25%-0.75rem)] aspect-[4/5] md:aspect-auto md:h-[250px] flex-shrink-0 rounded-lg overflow-hidden hover:-translate-y-1 transition-all duration-300 snap-start"
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