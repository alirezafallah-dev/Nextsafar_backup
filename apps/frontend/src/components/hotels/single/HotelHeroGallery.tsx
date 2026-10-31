// src/components/hotels/single/HotelHeroGallery.tsx
"use client";

import { useState, useRef, useEffect } from "react";
import Image from "next/image";
import { Camera, ChevronLeft, ChevronRight } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import { GalleryModal } from "@/components/shared/gallery";
import type { GalleryImage } from "@/components/shared/gallery";

interface Props {
  images: string[];
  title: string;
}

export default function HotelHeroGallery({ images, title }: Props) {
  const [modalOpen, setModalOpen] = useState(false);
  const [initialIndex, setInitialIndex] = useState(0);
  const [mobileIndex, setMobileIndex] = useState(0);
  const touchStartX = useRef<number>(0);
  const [touchDelta, setTouchDelta] = useState(0);
  const [isDragging, setIsDragging] = useState(false);

  const galleryImages: GalleryImage[] = images.map((src, i) => ({
    src,
    alt: `${title} - تصویر ${i + 1}`,
  }));

  /* ═══ Swipe gesture با smooth animation ═══ */
  const handleTouchStart = (e: React.TouchEvent) => {
    touchStartX.current = e.touches[0].clientX;
    setIsDragging(true);
    setTouchDelta(0);
  };

  const handleTouchMove = (e: React.TouchEvent) => {
    if (!isDragging) return;
    const currentX = e.touches[0].clientX;
    const delta = currentX - touchStartX.current;
    setTouchDelta(delta);
  };

  const handleTouchEnd = () => {
    if (!isDragging) return;
    setIsDragging(false);
    
    const threshold = 50;
    if (touchDelta > threshold && mobileIndex > 0) {
      setMobileIndex((prev) => prev - 1);
    } else if (touchDelta < -threshold && mobileIndex < images.length - 1) {
      setMobileIndex((prev) => prev + 1);
    }
    setTouchDelta(0);
  };

  const openModal = (index: number) => {
    setInitialIndex(index);
    setModalOpen(true);
  };

  /* ═══ Desktop: پدینگ top/bottom ═══ */
  const top = images.slice(0, 3);
  const bottom = images.slice(3, 8);
  while (top.length < 3) top.push(top[top.length - 1] || "/placeholder.jpg");
  while (bottom.length < 5) bottom.push(bottom[bottom.length - 1] || top[0]);
  const remaining = images.length - 7;

  return (
    <>
      {/* ═══ موبایل: Carousel اسلایدر ═══ */}
      <div className="md:hidden relative -mx-4 mb-6">
        <div
          className="relative overflow-hidden bg-bg-sec"
          style={{ height: "340px" }}
          onTouchStart={handleTouchStart}
          onTouchMove={handleTouchMove}
          onTouchEnd={handleTouchEnd}
        >
          <div
            className="flex h-full transition-transform duration-300 ease-out"
            style={{
              transform: `translateX(calc(${mobileIndex * -100}% + ${touchDelta}px))`,
              transition: isDragging ? "none" : "transform 0.3s ease-out",
            }}
          >
            {images.map((img, i) => (
              <button
                key={i}
                onClick={() => openModal(i)}
                className="relative shrink-0 w-full h-full"
              >
                <Image
                  src={img}
                  alt={`${title} - تصویر ${i + 1}`}
                  fill
                  className="object-cover"
                  priority={i === 0}
                  sizes="100vw"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent pointer-events-none" />
              </button>
            ))}
          </div>

          {/* Counter Badge */}
          <div className="absolute top-4 right-4 flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-bold shadow-lg z-10">
            <Camera className="w-3.5 h-3.5" />
            <span>
              {faDigits(mobileIndex + 1)} از {faDigits(images.length)}
            </span>
          </div>

          {/* Arrows - فقط در تبلت کوچک */}
          {images.length > 1 && (
            <>
              <button
                onClick={(e) => {
                  e.stopPropagation();
                  setMobileIndex((prev) => Math.max(0, prev - 1));
                }}
                disabled={mobileIndex === 0}
                className="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/95 backdrop-blur-sm flex items-center justify-center text-text-strong shadow-lg disabled:opacity-30 disabled:cursor-not-allowed transition z-10"
                aria-label="عکس قبلی"
              >
                <ChevronRight className="w-5 h-5" />
              </button>
              <button
                onClick={(e) => {
                  e.stopPropagation();
                  setMobileIndex((prev) => Math.min(images.length - 1, prev + 1));
                }}
                disabled={mobileIndex === images.length - 1}
                className="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/95 backdrop-blur-sm flex items-center justify-center text-text-strong shadow-lg disabled:opacity-30 disabled:cursor-not-allowed transition z-10"
                aria-label="عکس بعدی"
              >
                <ChevronLeft className="w-5 h-5" />
              </button>
            </>
          )}

          {/* Dots Indicator */}
          {images.length > 1 && (
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-10">
              {images.map((_, i) => (
                <button
                  key={i}
                  onClick={(e) => {
                    e.stopPropagation();
                    setMobileIndex(i);
                  }}
                  className={`h-1.5 rounded-full transition-all ${
                    i === mobileIndex
                      ? "w-6 bg-white"
                      : "w-1.5 bg-white/60 hover:bg-white/80"
                  }`}
                  aria-label={`عکس ${i + 1}`}
                />
              ))}
            </div>
          )}
        </div>

        {/* دکمه نمایش همه عکس‌ها */}
        <div className="px-4 mt-3">
          <button
            onClick={() => openModal(0)}
            className="w-full flex items-center justify-center gap-2 py-3 rounded-[10px] border border-border bg-white hover:border-primary-light hover:bg-primary-lightest/30 transition-all"
          >
            <Camera className="w-4 h-4 text-primary" />
            <span className="text-xs font-bold text-text-strong">
              نمایش همه {faDigits(images.length)} عکس
            </span>
          </button>
        </div>
      </div>

      {/* ═══ دسکتاپ: Grid ═══ */}
      <div className="hidden md:block space-y-2">
        {/* ردیف بالا */}
        <div className="grid grid-cols-3 grid-rows-2 gap-2 h-[320px]">
          <button
            onClick={() => openModal(0)}
            className="relative col-span-2 row-span-2 rounded-[10px] lg:rounded-lg overflow-hidden group border border-border hover:border-primary-light transition-all duration-500"
            aria-label={`مشاهده تصویر ۱ ${title}`}
          >
            <Image
              src={top[0]}
              alt={`${title} - تصویر ۱`}
              fill
              priority
              loading="eager"
              className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
              sizes="66vw"
            />
          </button>

          <button
            onClick={() => openModal(1)}
            className="relative rounded-[10px] lg:rounded-lg overflow-hidden group border border-border hover:border-primary-light transition-all duration-500"
          >
            <Image
              src={top[1]}
              alt={`${title} - تصویر ۲`}
              fill
              className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
              sizes="33vw"
            />
          </button>

          <button
            onClick={() => openModal(2)}
            className="relative rounded-[10px] lg:rounded-lg overflow-hidden group border border-border hover:border-primary-light transition-all duration-500"
          >
            <Image
              src={top[2]}
              alt={`${title} - تصویر ۳`}
              fill
              className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
              sizes="33vw"
            />
          </button>
        </div>

        {/* ردیف پایین - 5 عکس */}
        <div className="grid grid-cols-5 gap-2 h-[110px]">
          {bottom.map((img, i) => (
            <button
              key={i}
              onClick={() => openModal(3 + i)}
              className="relative rounded-[10px] lg:rounded-lg overflow-hidden group border border-border hover:border-primary-light transition-all duration-500"
            >
              <Image
                src={img}
                alt={`${title} - تصویر ${i + 4}`}
                fill
                className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
                sizes="20vw"
              />

              {i === 4 && remaining > 0 && (
                <span className="absolute inset-0 bg-black/55 group-hover:bg-black/65 transition-colors flex flex-col items-center justify-center gap-1 text-white">
                  <Camera className="w-5 h-5" />
                  <span className="text-xs font-black">
                    {faDigits(images.length)} تصویر
                  </span>
                </span>
              )}
            </button>
          ))}
        </div>
      </div>

      {/* Gallery Modal */}
      <GalleryModal
        images={galleryImages}
        title={title}
        isOpen={modalOpen}
        initialIndex={initialIndex}
        onClose={() => setModalOpen(false)}
      />
    </>
  );
}