"use client";
import Link from "next/link";
import Image from "next/image";
import { Clock3, FileText } from "lucide-react";
import { formatFa } from "@/lib/api/home";

const CURRENCY_FA: Record<string, string> = {
  USD: "دلار", EUR: "یورو", TRY: "لیر", AED: "درهم",
  GBP: "پوند", CAD: "دلار کانادا", IRR: "تومان", IRT: "تومان", TOMAN: "تومان",
};

interface VisaCardProps {
  slug: string;
  title: string;
  image: string | null;
  category?: string;
  price_min?: number | null;
  currency?: string | null;
  issue?: string;
}

export default function VisaCard({
  slug, title, image, category, price_min, currency, issue,
}: VisaCardProps) {
  const currencyLabel = currency ? (CURRENCY_FA[currency.toUpperCase()] || currency) : "";

  return (
    <Link
      href={`/visas/${slug}`}
      className="w-[70%] sm:w-[46%] md:w-auto shrink-0 snap-start overflow-hidden group rounded-[10px] sm:rounded-lg border border-border bg-white cursor-pointer hover:border-primary/40 hover:shadow-card-hover transition-all"
    >
      <div className="relative aspect-[16/10] bg-bg-sec">
        {image ? (
          <Image
            src={image}
            alt={title}
            fill
            sizes="(max-width: 768px) 70vw, (max-width: 1024px) 46vw, 25vw"
            className="object-cover transition-transform duration-500 group-hover:scale-105"
          />
        ) : (
          <div className="absolute inset-0 flex items-center justify-center">
            <FileText className="w-8 h-8 text-text-muted/40" />
          </div>
        )}
        {category && (
          <span className="absolute top-2 start-2 px-2 py-1 rounded-lg bg-black/50 backdrop-blur-sm text-white text-[9px] font-bold z-10">
            {category}
          </span>
        )}
      </div>

      <div className="p-3 md:p-4">
        <h3 className="text-xs md:text-sm font-extrabold leading-6 line-clamp-2 group-hover:text-primary transition">
          {title}
        </h3>
        <div className="flex items-center justify-between gap-2 mt-3">
          {price_min ? (
            <span className="flex items-center gap-1 px-2 py-1 rounded-lg bg-visa/10 text-visa text-[10px] md:text-[11px] font-black shrink-0">
              از {formatFa(Math.round(price_min))} {currencyLabel}
            </span>
          ) : (
            <span className="px-2 py-1 rounded-lg bg-bg-sec text-text-muted text-[10px] md:text-[11px] font-bold shrink-0">
              استعلام قیمت
            </span>
          )}
          {issue && (
            <span className="flex items-center gap-1 text-[9px] md:text-[10px] font-bold text-text-muted truncate">
              <Clock3 className="w-3 h-3 shrink-0" />
              {issue}
            </span>
          )}
        </div>
      </div>
    </Link>
  );
}