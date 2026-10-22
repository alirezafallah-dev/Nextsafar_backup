"use client";
import Image from "next/image";
import Link from "next/link";
import {
  X,
  Star,
  MapPin,
  ChevronLeft,
  Utensils,
  Plane,
  Landmark,
  BedDouble,
  Building2,
} from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import type { ListingMapPoint } from "./types";

interface PlaceInfoPanelProps {
  place: ListingMapPoint | null;
  isOpen: boolean;
  onClose: () => void;
}

/* ═══ آیکون بر اساس نوع مکان ═══ */
function getPlaceIcon(type: string) {
  switch (type) {
    case "hotel":
      return <BedDouble className="w-4 h-4" />;
    case "restaurant":
      return <Utensils className="w-4 h-4" />;
    case "airport":
      return <Plane className="w-4 h-4" />;
    case "destination":
    case "attraction":
      return <Landmark className="w-4 h-4" />;
    default:
      return <Building2 className="w-4 h-4" />;
  }
}

/* ═══ برچسب فارسی نوع مکان ═══ */
function getPlaceTypeLabel(type: string): string {
  const labels: Record<string, string> = {
    hotel: "هتل",
    restaurant: "رستوران",
    airport: "فرودگاه",
    destination: "جاذبه گردشگری",
    attraction: "جاذبه گردشگری",
    hospital: "بیمارستان",
  };
  return labels[type] || "مکان";
}

/* ═══ رندر ستاره‌ها ═══ */
function StarRating({ stars }: { stars: number }) {
  return (
    <div className="flex items-center gap-0.5">
      {Array.from({ length: 5 }).map((_, i) => (
        <Star
          key={i}
          className={`w-3.5 h-3.5 ${
            i < stars
              ? "text-amber-400 fill-amber-400"
              : "text-gray-300"
          }`}
        />
      ))}
    </div>
  );
}

export default function PlaceInfoPanel({
  place,
  isOpen,
  onClose,
}: PlaceInfoPanelProps) {
  if (!place) return null;

  const typeLabel = getPlaceTypeLabel(place.type);
  const href = place.href || `/${place.type}s/${place.key}`;

  return (
    <div
      className={`absolute bottom-0 start-0 end-0 sm:end-auto sm:bottom-4 sm:start-4 sm:w-[340px] z-[450] transition-all duration-300 ease-out ${
        isOpen
          ? "translate-y-0 opacity-100"
          : "translate-y-full sm:translate-y-4 opacity-0 pointer-events-none"
      }`}
    >
      <div className="bg-white/95 backdrop-blur-xl rounded-t-2xl sm:rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
        {/* ═══ تصویر ═══ */}
        <div className="relative h-36 sm:h-40 overflow-hidden">
          {place.image ? (
            <Image
              src={place.image}
              alt={place.title}
              fill
              className="object-cover"
              sizes="(max-width: 640px) 100vw, 340px"
            />
          ) : (
            <div className="w-full h-full bg-gradient-to-br from-primary/20 to-primary/40 flex items-center justify-center">
              {getPlaceIcon(place.type)}
            </div>
          )}
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />

          {/* دکمه بستن */}
          <button
            type="button"
            onClick={onClose}
            className="absolute top-3 end-3 w-8 h-8 rounded-full bg-black/50 backdrop-blur flex items-center justify-center text-white hover:bg-black/70 transition cursor-pointer"
            aria-label="بستن پنل"
          >
            <X className="w-4 h-4" />
          </button>

          {/* برچسب نوع مکان */}
          <div className="absolute top-3 start-3 flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/50 backdrop-blur text-white text-[10px] font-bold">
            {getPlaceIcon(place.type)}
            {typeLabel}
          </div>

          {/* عنوان روی تصویر */}
          <div className="absolute bottom-3 start-3 end-3">
            <h4 className="text-white font-extrabold text-base sm:text-lg leading-tight line-clamp-1 drop-shadow">
              {place.title}
            </h4>
          </div>
        </div>

        {/* ═══ اطلاعات ═══ */}
        <div className="p-4 space-y-3">
          {/* ستاره‌ها و امتیاز (برای هتل و رستوران) */}
          {(place.stars || place.rating) && (
            <div className="flex items-center justify-between">
              {place.stars && place.stars > 0 && (
                <div className="flex items-center gap-2">
                  <StarRating stars={place.stars} />
                  <span className="text-xs text-text-muted">
                    {faDigits(place.stars)} ستاره
                  </span>
                </div>
              )}
              {place.rating && (
                <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary/10">
                  <span className="text-sm font-extrabold text-primary">
                    {faDigits(place.rating.toFixed(1))}
                  </span>
                  <span className="text-[10px] text-text-muted">از ۵</span>
                </div>
              )}
            </div>
          )}

          {/* کد فرودگاه (برای فرودگاه) */}
          {place.airportCode && (
            <div className="flex items-center gap-2 text-sm text-text-muted">
              <Plane className="w-4 h-4 text-primary" />
              <span className="font-bold text-text-strong">{place.airportCode}</span>
              {place.city && <span>• {place.city}</span>}
            </div>
          )}

          {/* نوع غذا (برای رستوران) */}
          {place.cuisine && (
            <div className="flex items-center gap-2 text-sm text-text-muted">
              <Utensils className="w-4 h-4 text-primary" />
              <span>{place.cuisine}</span>
            </div>
          )}

          {/* دسته‌بندی (برای جاذبه) */}
          {place.category && (
            <div className="flex items-center gap-2 text-sm text-text-muted">
              <Landmark className="w-4 h-4 text-primary" />
              <span>{place.category}</span>
            </div>
          )}

          {/* آدرس */}
          {place.address && (
            <div className="flex items-start gap-2 text-xs text-text-muted">
              <MapPin className="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-primary" />
              <span className="line-clamp-2">{place.address}</span>
            </div>
          )}

          {/* توضیحات کوتاه */}
          {place.description && (
            <p className="text-xs text-text-muted leading-5 line-clamp-2">
              {place.description}
            </p>
          )}

          {/* تعداد نظرات */}
          {place.reviewsCount && place.reviewsCount > 0 && (
            <p className="text-[11px] text-text-muted">
              {faDigits(place.reviewsCount)} نظر ثبت شده
            </p>
          )}

          {/* ═══ دکمه مشاهده جزئیات ═══ */}
          <Link
            href={href}
            className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold text-sm transition-all shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 cursor-pointer group mt-2"
          >
            مشاهده جزئیات {typeLabel}
            <ChevronLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform rtl:rotate-180" />
          </Link>
        </div>
      </div>
    </div>
  );
}