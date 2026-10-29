// src/components/hotels/single/HotelHeroGallery.tsx
"use client";

import { useState } from "react";
import Image from "next/image";
import { Camera } from "lucide-react";
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

  const top = images.slice(0, 3);
  const bottom = images.slice(3, 8);
  while (top.length < 3) top.push(top[top.length - 1] || "/placeholder.jpg");
  while (bottom.length < 5) bottom.push(bottom[bottom.length - 1] || top[0]);

  const remaining = images.length - 7;

  const openModal = (index: number) => {
    setInitialIndex(index);
    setModalOpen(true);
  };

  const galleryImages: GalleryImage[] = images.map((src, i) => ({
    src,
    alt: `${title} - تصویر ${i + 1}`,
  }));

  if (images.length === 0) return null;

  return (
    <>
      <div className="space-y-2">
        {/* ردیف بالا */}
        <div className="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-2 md:h-[320px]">
          <button
            onClick={() => openModal(0)}
            className="relative md:col-span-2 md:row-span-2 h-[220px] md:h-full rounded-[10px] md:rounded-lg overflow-hidden group border border-border hover:border-primary-light transition-all duration-500"
            aria-label={`مشاهده تصویر ۱ ${title}`}
          >
            <Image
              src={top[0]}
              alt={`${title} - تصویر ۱`}
              fill
              priority
              loading="eager"
              className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
              sizes="(max-width: 768px) 100vw, 66vw"
            />
          </button>

          <button
            onClick={() => openModal(1)}
            className="relative h-[140px] md:h-full rounded-[10px] md:rounded-lg overflow-hidden group border border-border hover:border-primary-light transition-all duration-500"
          >
            <Image
              src={top[1]}
              alt={`${title} - تصویر ۲`}
              fill
              className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
              sizes="(max-width: 768px) 100vw, 33vw"
            />
          </button>

          <button
            onClick={() => openModal(2)}
            className="relative h-[140px] md:h-full rounded-[10px] md:rounded-lg overflow-hidden group border border-border hover:border-primary-light transition-all duration-500"
          >
            <Image
              src={top[2]}
              alt={`${title} - تصویر ۳`}
              fill
              className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
              sizes="(max-width: 768px) 100vw, 33vw"
            />
          </button>
        </div>

        {/* ردیف پایین - 5 عکس */}
        <div className="flex gap-2 overflow-x-auto scrollbar-hide md:grid md:grid-cols-5 md:overflow-visible h-[90px] md:h-[110px]">
          {bottom.map((img, i) => (
            <button
              key={i}
              onClick={() => openModal(3 + i)}
              className="relative rounded-[10px] md:rounded-lg overflow-hidden group shrink-0 w-[120px] md:w-auto h-full border border-border hover:border-primary-light transition-all duration-500"
            >
              <Image
                src={img}
                alt={`${title} - تصویر ${i + 4}`}
                fill
                className="object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
                sizes="(max-width: 768px) 120px, 20vw"
              />

              {i === 4 && remaining > 0 && (
                <span className="absolute inset-0 bg-black/55 group-hover:bg-black/65 transition-colors flex flex-col items-center justify-center gap-1 text-white">
                  <Camera className="w-5 h-5" />
                  <span className="text-xs font-black">{faDigits(images.length)} تصویر</span>
                </span>
              )}
            </button>
          ))}
        </div>
      </div>

      {/* مودال گلوبال */}
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