// src/components/shared/gallery/GalleryModal.tsx
"use client";

import { useEffect, useState, useRef, useMemo } from "react";
import { createPortal } from "react-dom";
import Image from "next/image";
import { X, ChevronLeft, ChevronRight, Grid3x3, Download, Share2, Heart, ArrowLeft, ArrowRight } from "lucide-react";
import type { GalleryModalProps, GalleryViewMode } from "./types";
import ModalCloseButton from "../common/ModalCloseButton";

const aspectPatterns = [
  "aspect-[4/3]", "aspect-[3/4]", "aspect-[16/9]", "aspect-square",
  "aspect-[3/4]", "aspect-[4/3]", "aspect-[16/9]", "aspect-[3/4]",
  "aspect-square", "aspect-[4/3]",
];

const safeIndex = (index: number, length: number): number => {
  if (length === 0) return 0;
  if (index < 0) return length - 1;
  if (index >= length) return 0;
  return index;
};

export default function GalleryModal({
  images,
  title = "گالری تصاویر",
  isOpen,
  initialIndex,
  onClose,
}: GalleryModalProps) {
  const [view, setView] = useState<GalleryViewMode>("grid");
  const [rawActiveIndex, setRawActiveIndex] = useState(initialIndex);
  const [isFavorite, setIsFavorite] = useState(false);
  const touchStartX = useRef<number>(0);

  const activeIndex = useMemo(
    () => safeIndex(rawActiveIndex, images.length),
    [rawActiveIndex, images.length]
  );

  const currentImage = images[activeIndex];

  /* ═══ Body scroll lock ═══ */
  useEffect(() => {
    if (isOpen) {
      setRawActiveIndex(initialIndex);
      setView(initialIndex === 0 ? "grid" : "single");
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "unset";
    }
    return () => { document.body.style.overflow = "unset"; };
  }, [isOpen, initialIndex]);

  /* ═══ Keyboard Navigation ═══ */
  useEffect(() => {
    if (!isOpen || images.length === 0) return;
    const handleKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") {
        if (view === "single") setView("grid");
        else onClose();
      } else if (e.key === "ArrowLeft" && view === "single") {
        setRawActiveIndex((prev) => (prev + 1) % images.length);
      } else if (e.key === "ArrowRight" && view === "single") {
        setRawActiveIndex((prev) => (prev - 1 + images.length) % images.length);
      }
    };
    window.addEventListener("keydown", handleKey);
    return () => window.removeEventListener("keydown", handleKey);
  }, [isOpen, view, images.length, onClose]);

  const nextImage = () => setRawActiveIndex((prev) => (prev + 1) % Math.max(images.length, 1));
  const prevImage = () => setRawActiveIndex((prev) => (prev - 1 + images.length) % Math.max(images.length, 1));

  const handleTouchStart = (e: React.TouchEvent) => {
    touchStartX.current = e.touches[0].clientX;
  };
  const handleTouchEnd = (e: React.TouchEvent) => {
    if (view !== "single") return;
    const diff = touchStartX.current - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) {
      if (diff > 0) nextImage();
      else prevImage();
    }
  };

  const handleDownload = () => {
    if (!currentImage) return;
    const link = document.createElement("a");
    link.href = currentImage.src;
    link.download = `${title}-${activeIndex + 1}.jpg`;
    link.click();
  };

  const handleShare = async () => {
    if (!currentImage) return;
    if (navigator.share) {
      try {
        await navigator.share({ title, url: currentImage.src });
      } catch {}
    } else {
      await navigator.clipboard.writeText(currentImage.src);
      alert("لینک کپی شد");
    }
  };

  if (!isOpen || typeof document === "undefined") return null;
  if (images.length === 0) return null;

  /* ═══ محتوای مودال (برای Portal) ═══ */
  const modalContent = (
    <div className="fixed inset-0 z-[9999]">

      {/* ═══ Backdrop مدرن: گرادیان طوسی + blur ═══ */}
      <div
        className="absolute inset-0 bg-gradient-to-br from-slate-900/75 via-slate-800/60 to-slate-900/75 backdrop-blur-xl animate-ns-fade"
        onClick={onClose}
      />

      {/* ═══ Container با فاصله دقیق ۲۰px از هر طرف ═══ 
           inset-5 = top/right/bottom/left: 20px (5 × 4 = 20)
           موبایل: inset-2 = 8px فاصله (کمتر برای صفحه کوچک)
      */}
      <div className="absolute inset-2 md:inset-5 flex flex-col">
        <div className="w-full h-full bg-white overflow-hidden flex flex-col shadow-2xl rounded-xl animate-ns-fade">

          {/* ═══ Header — چیدمان نهایی ═══ */}
          <div className="flex items-center justify-between px-4 md:px-5 py-3 md:py-3 border-b border-border bg-white shrink-0">

            {/* ══ سمت راست (در RTL) — Actions + Back button ══ */}
            <div className="flex items-center shrink-0 gap-1 md:gap-1.5">

              {/* دکمه بازگشت به گالری — فقط در single، کنار actions */}
              {view === "single" && (
                <button
                  onClick={() => setView("grid")}
                  className="flex items-center gap-1.5 px-2.5 md:px-3 py-2.5 rounded-md bg-bg-sec hover:bg-border transition"
                >
                  <ArrowRight className="w-3.5 h-3.5 text-text-strong" />
                  <span className="text-[11px] md:text-xs font-bold text-text-strong hidden sm:inline">
                    بازگشت به گالری
                  </span>
                </button>
              )}

              {/* Actions: Heart + Share + Download — فقط در single */}
              {view === "single" && (
                <>
                  <button
                    onClick={() => setIsFavorite(!isFavorite)}
                    className={`w-9 h-9 rounded-md items-center justify-center transition hidden md:flex ${
                      isFavorite ? "bg-danger/10 text-danger" : "hover:bg-bg-sec text-text-muted"
                    }`}
                    aria-label="ذخیره"
                  >
                    <Heart className={`w-4 h-4 ${isFavorite ? "fill-current" : ""}`} />
                  </button>
                  <button
                    onClick={handleShare}
                    className="w-9 h-9 rounded-md hover:bg-bg-sec items-center justify-center text-text-muted transition hidden md:flex"
                    aria-label="اشتراک"
                  >
                    <Share2 className="w-4 h-4" />
                  </button>
                  <button
                    onClick={handleDownload}
                    className="w-9 h-9 rounded-md hover:bg-bg-sec items-center justify-center text-text-muted transition hidden md:flex"
                    aria-label="دانلود"
                  >
                    <Download className="w-4 h-4" />
                  </button>
                </>
              )}
              {view === "single" ? (
                <div className="flex items-center gap-1 px-2.5 py-1 rounded-sm bg-bg-sec text-[10px] md:text-[11px] font-bold text-text">
                  <span className="text-primary">{activeIndex + 1}</span>
                  <span className="text-text-subtle mx-0.5">از</span>
                  <span>{images.length}</span>
                </div>
              ) : (
                title && (
                  <h2 className="text-xs md:text-sm font-bold text-text-strong truncate max-w-full">
                    {title}
                  </h2>
                )
              )}
            </div>

            {/* ══ سمت چپ (در RTL) — Badge (grid) + Close ══ */}
            <div className="flex items-center gap-1 md:gap-1.5 shrink-0">
              {/* Badge "X عکس" — فقط در grid */}
              {view === "grid" && (
                <div className="flex items-center gap-1 px-2.5 py-1 rounded-sm bg-bg-sec text-[10px] md:text-[11px] font-bold text-text">
                  <Grid3x3 className="w-3 h-3" />
                  <span>{images.length} عکس</span>
                </div>
              )}

              {/* دکمه بستن — همیشه */}
              {onClose && <ModalCloseButton onClick={onClose} />}
            </div>
          </div>

          {/* ═══ Body ═══ */}
          <div className="flex-1 overflow-hidden relative bg-bg-sec min-h-0">
            {/* Grid View */}
            {view === "grid" && (
              <div className="h-full overflow-y-auto p-3 md:p-5 lg:p-6">
                <div className="columns-2 md:columns-3 lg:columns-4 xl:columns-5 gap-2 md:gap-3 space-y-2 md:space-y-3">
                  {images.map((img, i) => {
                    const aspect = aspectPatterns[i % aspectPatterns.length];
                    return (
                      <button
                        key={i}
                        onClick={() => {
                          setRawActiveIndex(i);
                          setView("single");
                        }}
                        className="relative block w-full overflow-hidden rounded-[10px] md:rounded-lg group cursor-pointer break-inside-avoid hover:ring-2 hover:ring-primary hover:ring-offset-2 hover:ring-offset-bg-sec transition-all ${aspect}"
                        style={{ breakInside: "avoid" }}
                      >
                        <div className={`w-full ${aspect} relative`}>
                          <Image
                            src={img.src}
                            alt={img.alt || `${title} - عکس ${i + 1}`}
                            fill
                            sizes="(max-width: 768px) 50vw, (max-width: 1024px) 33vw, 25vw"
                            className="object-cover group-hover:scale-105 transition-transform duration-500"
                          />
                          <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <div className="opacity-0 group-hover:opacity-100 transition-opacity">
                              <div className="w-10 h-10 md:w-12 md:h-12 rounded-md bg-white/95 backdrop-blur-sm flex items-center justify-center shadow-xl">
                                <ChevronLeft className="w-5 h-5 md:w-6 md:h-6 text-text-strong" />
                              </div>
                            </div>
                          </div>
                          <div className="absolute top-2 right-2 px-2 py-0.5 rounded-sm bg-black/60 backdrop-blur-sm text-white text-[10px] font-semibold">
                            {i + 1}
                          </div>
                          {img.caption && (
                            <div className="absolute bottom-0 inset-x-0 p-2 bg-gradient-to-t from-black/70 to-transparent">
                              <p className="text-white text-[10px] md:text-xs line-clamp-1">{img.caption}</p>
                            </div>
                          )}
                        </div>
                      </button>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Single View */}
            {view === "single" && currentImage && (
              <div
                className="h-full flex items-center justify-center relative p-4 md:p-8"
                onTouchStart={handleTouchStart}
                onTouchEnd={handleTouchEnd}
              >
                <div className="relative w-full h-full flex items-center justify-center p-2 md:p-4">
                <img
                    key={activeIndex}
                    src={currentImage.src}
                    alt={currentImage.alt || `${title} - عکس ${activeIndex + 1}`}
                    className="max-w-full max-h-full w-auto h-auto object-contain rounded-[10px] shadow-2xl ring-1 ring-black/10 animate-ns-fade select-none"
                    draggable={false}
                />
                </div>

                <button
                  onClick={prevImage}
                  className="absolute right-2 md:right-5 top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 rounded-md bg-white/95 hover:bg-white backdrop-blur-sm shadow-xl flex items-center justify-center text-text-strong transition-all hover:scale-110 z-10"
                  aria-label="عکس قبلی"
                >
                  <ChevronRight className="w-5 h-5 md:w-6 md:h-6" />
                </button>

                <button
                  onClick={nextImage}
                  className="absolute left-2 md:left-5 top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 rounded-md bg-white/95 hover:bg-white backdrop-blur-sm shadow-xl flex items-center justify-center text-text-strong transition-all hover:scale-110 z-10"
                  aria-label="عکس بعدی"
                >
                  <ChevronLeft className="w-5 h-5 md:w-6 md:h-6" />
                </button>

                <div className="absolute bottom-3 left-1/2 -translate-x-1/2 max-w-[90%] md:max-w-5xl">
                  <div className="flex gap-1.5 md:gap-2 overflow-x-auto px-2 py-2 rounded-2xl bg-black/40 backdrop-blur-md scrollbar-hide">
                    {images.map((img, i) => (
                      <button
                        key={i}
                        onClick={() => setRawActiveIndex(i)}
                        className={`relative w-12 h-12 md:w-14 md:h-14 rounded-lg overflow-hidden shrink-0 transition-all ${
                          activeIndex === i
                            ? "ring-2 ring-white opacity-100 scale-105"
                            : "opacity-50 hover:opacity-80"
                        }`}
                      >
                        <Image src={img.src} alt="" fill sizes="56px" className="object-cover" />
                      </button>
                    ))}
                  </div>
                </div>

                {currentImage.caption && (
                  <div className="absolute top-4 left-1/2 -translate-x-1/2 px-4 py-2 rounded-full bg-black/50 backdrop-blur-sm text-white text-[11px] md:text-xs max-w-[80%] text-center">
                    {currentImage.caption}
                  </div>
                )}
              </div>
            )}

            {view === "single" && !currentImage && (
              <div className="h-full flex items-center justify-center">
                <p className="text-text-muted text-sm">تصویری برای نمایش وجود ندارد</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );

  /* ═══ رندر با Portal — مستقیم در body ═══ */
  return createPortal(modalContent, document.body);
}