// src/components/shared/cards/HotelCard.tsx

"use client";

import { BedDouble, MapPin, Star, ArrowLeft } from 'lucide-react';
import BaseCard, { CardImage } from './BaseCard';
import { Badge, MetaItem } from './utils';
import { faDigits } from '@/lib/api/hotels';

export interface HotelCardProps {
  title: string;
  image: string | null;
  href: string;
  city?: string;
  stars?: number;
  rating?: number;
  price?: number | null;
  priceLabel?: string;
  badge?: string;
  showPrice?: boolean;
  showFavorite?: boolean;
  isFavorite?: boolean;
  onFavoriteToggle?: () => void;
}

export default function HotelCard({
  title,
  image,
  href,
  city,
  stars,
  rating,
  price,
  priceLabel = 'هر شب از',
  badge = 'هتل',
  showPrice = true,
}: HotelCardProps) {
  return (
    <BaseCard
      href={href}
      accentColor="primary"
      hoverEffect="shadow"
      hasBorder
      className="w-[78%] sm:w-[46%] md:w-auto shrink-0 snap-start flex flex-col"
    >
      <CardImage
        src={image}
        alt={title}
        aspectRatio="4/3"
        sizes="(max-width: 640px) 78vw, (max-width: 1024px) 46vw, 25vw"
      >
        {/* Badge هتل */}
        <div className="absolute top-3 start-3 z-10">
          <Badge text={badge} icon={BedDouble} color="primary" />
        </div>
      </CardImage>

      <div className="p-3 md:p-4 flex flex-col gap-2 flex-1">
        {/* عنوان + rating */}
        <div className="flex items-start justify-between gap-2">
          <h3 className="font-bold text-sm md:text-[15px] text-text-strong line-clamp-1 group-hover:text-primary transition-colors">
            {title}
          </h3>
          {rating && rating > 0 && (
            <span className="px-1.5 py-0.5 rounded-md bg-success text-white text-[11px] font-extrabold shadow-sm shrink-0">
              {faDigits(rating.toFixed(1))}
            </span>
          )}
        </div>

        {/* ستاره + شهر */}
        <div className="flex items-center gap-3 text-xs text-text-muted">
          {stars && stars > 0 && (
            <span className="flex items-center gap-0.5" aria-label={`${stars} ستاره`}>
              {Array.from({ length: 5 }).map((_, i) => (
                <Star
                  key={i}
                  className={`w-3.5 h-3.5 ${i < stars ? 'text-primary fill-primary' : 'text-border'}`}
                />
              ))}
            </span>
          )}
          {city && <MetaItem icon={MapPin} text={city} accentColor="primary" />}
        </div>

        {/* Footer: قیمت + arrow */}
        {showPrice && (
          <div className="mt-auto pt-2 border-t border-divider flex items-center justify-between">
            {price ? (
              <span className="text-sm text-primary-dark">
                از {faDigits(price.toLocaleString())}{' '}
                <span className="text-[10px] font-medium text-text-muted">تومان</span>
              </span>
            ) : (
              <span className="text-xs font-semibold text-primary">استعلام قیمت</span>
            )}
            <span className="w-8 h-8 rounded-full border border-border flex items-center justify-center text-text-muted group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all duration-300">
              <ArrowLeft className="w-4 h-4" />
            </span>
          </div>
        )}
      </div>
    </BaseCard>
  );
}