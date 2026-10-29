"use client";
import Link from "next/link";
import Image from "next/image";
import { ArrowLeft, BookOpen } from "lucide-react";

// ═══ کارت ویژه (بزرگ - Featured) ═══
interface FeaturedArticleProps {
  slug: string;
  title: string;
  image: string | null;
  category?: string;
  excerpt?: string;
}

export function FeaturedArticle({ slug, title, image, category, excerpt }: FeaturedArticleProps) {
  return (
    <Link
      href={`/travel-guides/${slug}`}
      className="relative overflow-hidden rounded-[10px] sm:rounded-lg group aspect-[16/10] md:aspect-auto md:min-h-[320px] cursor-pointer"
    >
      {image ? (
        <Image
          src={image}
          alt={title}
          fill
          sizes="(max-width: 768px) 100vw, 50vw"
          className="object-cover transition-transform duration-500 group-hover:scale-105"
        />
      ) : (
        <div className="absolute inset-0 bg-gradient-to-br from-primary/60 to-primary-dark/50" />
      )}
      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent" />
      <div className="absolute inset-x-0 bottom-0 p-4 md:p-6 z-10">
        {category && (
          <span className="inline-block px-2 py-1 rounded-lg bg-primary text-white text-[9px] font-bold mb-2">
            {category}
          </span>
        )}
        <h3 className="text-white text-base md:text-xl leading-7 md:leading-8 line-clamp-2">
          {title}
        </h3>
        {excerpt && (
          <p className="text-white/70 text-[11px] md:text-xs leading-6 mt-2 line-clamp-2">
            {excerpt}
          </p>
        )}
      </div>
    </Link>
  );
}

// ═══ کارت فشرده (لیست) ═══
interface CompactArticleProps {
  slug: string;
  title: string;
  image: string | null;
  category?: string;
  date?: string;
  basePath?: string;
}

export function CompactArticle({
  slug, title, image, category = "راهنمای سفر", date, basePath = "/travel-guides",
}: CompactArticleProps) {
  return (
    <Link href={`${basePath}/${slug}`} className="flex items-center gap-3 py-3 group cursor-pointer">
      <div className="relative w-20 h-16 md:w-24 md:h-16 rounded-lg overflow-hidden shrink-0 bg-bg-sec">
        {image ? (
          <Image src={image} alt={title} fill sizes="96px" className="object-cover" />
        ) : (
          <div className="absolute inset-0 flex items-center justify-center">
            <BookOpen className="w-5 h-5 text-text-muted/40" />
          </div>
        )}
      </div>
      <div className="flex-1 min-w-0">
        <h4 className="text-xs md:text-sm font-bold leading-6 line-clamp-2 group-hover:text-primary transition">
          {title}
        </h4>
        <p className="text-[10px] text-text-muted mt-1">
          {category}
          {date && ` • ${date}`}
        </p>
      </div>
      <ArrowLeft className="w-4 h-4 text-text-muted opacity-0 group-hover:opacity-100 transition shrink-0 rtl:rotate-180" />
    </Link>
  );
}

// ═══ کارت خبر ═══
interface NewsCardProps {
  slug: string;
  title: string;
  category?: string;
  date?: string;
}

export function NewsCard({ slug, title, category = "خبر گردشگری", date }: NewsCardProps) {
  return (
    <Link
      href={`/travel-news/${slug}`}
      className="w-[72%] sm:w-[46%] md:w-auto shrink-0 snap-start rounded-[10px] sm:rounded-lg border border-border bg-white p-3 md:p-4 hover:border-primary/40 hover:shadow-card-hover transition group cursor-pointer"
    >
      <p className="text-[10px] font-bold text-primary mb-1.5">
        {category}
        {date && ` • ${date}`}
      </p>
      <h4 className="text-[11px] md:text-xs font-bold leading-6 line-clamp-2 group-hover:text-primary transition">
        {title}
      </h4>
    </Link>
  );
}