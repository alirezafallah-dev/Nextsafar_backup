"use client";

import Image from "next/image";
import Link from "next/link";
import { Heart, ExternalLink, ChevronLeft } from "lucide-react";
import type { ListingCardProps } from "@/types/listing";

export default function ListingCard({
  item,
  content,
  metaContent,
  badges,
  onHover,
}: ListingCardProps & { metaContent?: React.ReactNode }) {
  const cardKey = `item-${item.id}`;

  return (
    <Link href={item.url}>
      <article
        onMouseEnter={() => onHover?.(cardKey)}
        onMouseLeave={() => onHover?.(null)}
        className="group relative bg-white rounded-xl border border-border overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer"
      >
        {/* ═══ تصویر ═══ */}
        <div className="relative aspect-[16/10] overflow-hidden bg-bg-sec">
          {item.image ? (
            <Image
              src={item.image}
              alt={item.title}
              fill
              className="object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center text-text-subtle/30">
              <svg className="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
              </svg>
            </div>
          )}

          {/* Gradient overlay */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />

          {/* بج‌ها */}
          {badges && (
            <div className="absolute top-3 start-3 flex flex-col gap-1.5 z-10">
              {badges}
            </div>
          )}

          {/* ❤️ علاقه‌مندی */}
          <button
            type="button"
            onClick={(e) => {
              e.preventDefault();
              e.stopPropagation();
            }}
            className="absolute top-3 end-3 w-9 h-9 rounded-full bg-white/95 backdrop-blur flex items-center justify-center shadow-md hover:scale-110 transition-transform cursor-pointer z-10"
          >
            <Heart className="w-4 h-4 text-text-muted hover:text-danger transition-colors" />
          </button>

          {/* دکمه مشاهده (hover) */}
          <div className="absolute bottom-3 end-3 opacity-0 group-hover:opacity-100 transition-opacity z-10">
            <span className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/95 backdrop-blur text-xs font-bold text-text-strong">
              مشاهده
              <ChevronLeft className="w-3 h-3" />
            </span>
          </div>
        </div>

        {/* ═══ محتوا ═══ */}
        <div className="p-4">
          {content ? (
            content
          ) : (
            <>
              <h3 className="font-extrabold text-base text-text-strong line-clamp-1 group-hover:text-primary transition-colors">
                {item.title}
              </h3>
              {item.subtitle && (
                <p className="text-sm text-text-muted mt-1 line-clamp-1">{item.subtitle}</p>
              )}
            </>
          )}

          {/* Meta content (اختیاری) */}
          {metaContent && <div className="mt-3">{metaContent}</div>}
        </div>
      </article>
    </Link>
  );
}