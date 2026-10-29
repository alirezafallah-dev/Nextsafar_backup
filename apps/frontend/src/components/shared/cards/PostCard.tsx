"use client";

import Link from "next/link";
import Image from "next/image";
import { BookOpen, ArrowLeft } from "lucide-react";

export interface PostCardProps {
  slug: string;
  title: string;
  excerpt: string;
  image: string;
  category: string;
  readTime: string;
  href?: string;
}

export default function PostCard({
  slug,
  title,
  excerpt,
  image,
  category,
  readTime,
  href,
}: PostCardProps) {
  const finalHref = href || `/travel-guides/${slug}`;

  return (
    <Link
      href={finalHref}
      className="group rounded-[10px] sm:rounded-lg border border-border bg-white overflow-hidden hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 flex-shrink-0 w-[280px] md:w-auto snap-start cursor-pointer"
    >
      <div className="relative aspect-[16/10] bg-bg-sec overflow-hidden">
        <Image
          src={image}
          alt={title}
          fill
          sizes="(max-width: 768px) 280px, (max-width: 1024px) 50vw, 25vw"
          className="object-cover transition-transform duration-500 group-hover:scale-110"
        />
        <span className="absolute top-2 start-2 px-2 py-1 rounded-md bg-black/60 backdrop-blur text-white text-[9px] font-bold z-10">
          {category}
        </span>
      </div>

      <div className="p-3 md:p-4 flex flex-col gap-2">
        <h3 className="font-bold text-sm md:text-[15px] leading-6 line-clamp-2 group-hover:text-primary transition-colors">
          {title}
        </h3>
        <p className="text-[11px] md:text-xs text-text-muted leading-6 line-clamp-2">
          {excerpt}
        </p>

        <div className="flex items-center justify-between mt-2 pt-2 border-t border-divider">
          <div className="flex items-center gap-1 text-[10px] text-text-muted">
            <BookOpen className="w-3 h-3" />
            <span>{readTime} مطالعه</span>
          </div>
          <ArrowLeft className="w-4 h-4 text-primary opacity-0 group-hover:opacity-100 group-hover:-translate-x-1 transition-all" />
        </div>
      </div>
    </Link>
  );
}