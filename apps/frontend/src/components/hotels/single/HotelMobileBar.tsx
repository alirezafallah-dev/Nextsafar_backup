// src/components/hotels/single/HotelMobileBar.tsx
"use client";

import { useState, useEffect } from "react";
import { Heart, Share2, Phone, ChevronUp, X, MessageCircle } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import { useFavorites } from "@/lib/favorites";
import type { Hotel } from "@/types/hotel";

interface Props {
  hotel: Hotel;
}

export default function HotelMobileBar({ hotel }: Props) {
  const { has, toggle } = useFavorites();
  const [isVisible, setIsVisible] = useState(false);
  const [showActions, setShowActions] = useState(false);
  const isFavorite = has(`hotel-${hotel.id}`);

  useEffect(() => {
    const handleScroll = () => {
      setIsVisible(window.scrollY > 400);
    };
    window.addEventListener("scroll", handleScroll, { passive: true });
    handleScroll();
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const handleShare = async () => {
    if (navigator.share) {
      try {
        await navigator.share({ title: hotel.title, url: window.location.href });
      } catch {}
    } else {
      await navigator.clipboard.writeText(window.location.href);
      alert("لینک کپی شد");
    }
  };

  return (
    <>
      {/* ═══ Floating Action Bar — فقط موبایل ═══ */}
      <div
        className={`fixed bottom-0 inset-x-0 z-40 md:hidden transition-all duration-300 ${
          isVisible
            ? "translate-y-0 opacity-100"
            : "translate-y-full opacity-0"
        }`}
      >
        {/* Backdrop برای actions */}
        {showActions && (
          <div
            className="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"
            onClick={() => setShowActions(false)}
          />
        )}

        {/* Actions Menu */}
        <div
          className={`relative z-50 bg-white border-t border-border shadow-2xl transition-all duration-300 ${
            showActions ? "translate-y-0" : "translate-y-full"
          }`}
        >
          <div className="ns-container py-4">
            <div className="flex items-center justify-between mb-3">
              <h3 className="text-sm font-extrabold text-text-strong">
                اقدامات بیشتر
              </h3>
              <button
                onClick={() => setShowActions(false)}
                className="w-8 h-8 rounded-full hover:bg-bg-sec flex items-center justify-center"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="grid grid-cols-4 gap-3">
              <button
                onClick={() => {
                  toggle(`hotel-${hotel.id}`, {
                    source: "hotels",
                    title: hotel.title,
                    image: hotel.image,
                    url: `/hotels/${hotel.slug}`,
                  });
                }}
                className="flex flex-col items-center gap-1.5 p-3 rounded-[10px] bg-bg-sec/50 hover:bg-primary/5 transition"
              >
                <Heart
                  className={`w-5 h-5 ${
                    isFavorite ? "fill-danger text-danger" : "text-text"
                  }`}
                />
                <span className="text-[10px] font-bold text-text-strong">
                  {isFavorite ? "ذخیره شده" : "ذخیره"}
                </span>
              </button>

              <button
                onClick={handleShare}
                className="flex flex-col items-center gap-1.5 p-3 rounded-[10px] bg-bg-sec/50 hover:bg-primary/5 transition"
              >
                <Share2 className="w-5 h-5 text-text" />
                <span className="text-[10px] font-bold text-text-strong">
                  اشتراک
                </span>
              </button>

              <a
                href={`tel:+982191001234`}
                className="flex flex-col items-center gap-1.5 p-3 rounded-[10px] bg-bg-sec/50 hover:bg-primary/5 transition"
              >
                <Phone className="w-5 h-5 text-text" />
                <span className="text-[10px] font-bold text-text-strong">
                  تماس
                </span>
              </a>

              <a
                href="https://wa.me/989123456789"
                className="flex flex-col items-center gap-1.5 p-3 rounded-[10px] bg-bg-sec/50 hover:bg-primary/5 transition"
              >
                <MessageCircle className="w-5 h-5 text-text" />
                <span className="text-[10px] font-bold text-text-strong">
                  واتس‌اپ
                </span>
              </a>
            </div>
          </div>
        </div>

        {/* Main Bar */}
        <div className="bg-white border-t border-border shadow-2xl relative z-50">
          <div className="ns-container py-3">
            <div className="flex items-center gap-3">
              {/* دکمه Actions */}
              <button
                onClick={() => setShowActions(!showActions)}
                className="flex flex-col items-center justify-center w-12 h-12 rounded-[10px] bg-bg-sec/50 hover:bg-bg-sec transition"
                aria-label="اقدامات بیشتر"
              >
                <ChevronUp
                  className={`w-4 h-4 text-text transition-transform ${
                    showActions ? "rotate-180" : ""
                  }`}
                />
              </button>

              {/* اطلاعات قیمت (یا متن دلخواه) */}
              <div className="flex-1 min-w-0">
                <div className="text-[10px] text-text-muted">شروع قیمت از</div>
                <div className="flex items-baseline gap-1">
                  <span className="text-base font-black text-primary">
                    {faDigits("۲,۵۰۰,۰۰۰")}
                  </span>
                  <span className="text-[10px] text-text-muted">تومان</span>
                </div>
              </div>

              {/* دکمه اصلی CTA */}
              <button className="flex items-center justify-center gap-2 px-6 py-3 rounded-[10px] bg-primary hover:bg-primary-dark text-white text-sm font-black shadow-lg hover:shadow-xl transition-all">
                رزرو
              </button>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}