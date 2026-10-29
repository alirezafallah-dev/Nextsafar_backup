"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import SectionHeader from "../home/SectionHeader";
import TourPackageCard from "@/components/shared/cards/TourPackageCard";

const packages = [
  {
    id: 1,
    title: "تور آنتالیا + هتل ۵ ستاره",
    destination: "آنتالیا، ترکیه",
    image: "https://images.unsplash.com/photo-1589561454226-796a8c445760?w=600",
    nights: 7,
    hotelStars: 5,
    price: 18500000,
    discount: 15,
    oldPrice: 21760000,
    includes: ["پرواز رفت و برگشت", "هتل با صبحانه", "ترانسفر فرودگاهی"],
  },
  {
    id: 2,
    title: "تور دبی + هتل ساحلی",
    destination: "دبی، امارات",
    image: "https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600",
    nights: 5,
    hotelStars: 5,
    price: 32000000,
    discount: 20,
    oldPrice: 40000000,
    includes: ["پرواز رفت و برگشت", "هتل لوکس", "ویزای امارات"],
  },
  {
    id: 3,
    title: "تور استانبول + هتل ۴ ستاره",
    destination: "استانبول، ترکیه",
    image: "https://images.unsplash.com/photo-1527838832700-5059252407fa?w=600",
    nights: 4,
    hotelStars: 4,
    price: 12500000,
    discount: 10,
    oldPrice: 13880000,
    includes: ["پرواز رفت و برگشت", "هتل مرکز شهر", "گشت شهری"],
  },
  {
    id: 4,
    title: "تور تفلیس + هتل ۴ ستاره",
    destination: "تفلیس، گرجستان",
    image: "https://images.unsplash.com/photo-1565008447742-97f6f38c985c?w=600",
    nights: 3,
    hotelStars: 4,
    price: 8900000,
    discount: 12,
    oldPrice: 10110000,
    includes: ["پرواز رفت و برگشت", "هتل", "ترانسفر"],
  },
  {
    id: 5,
    title: "تور باتومی + هتل ساحلی",
    destination: "باتومی، گرجستان",
    image: "https://images.unsplash.com/photo-1540541338287-41700207dee6?w=600",
    nights: 4,
    hotelStars: 4,
    price: 9500000,
    discount: 8,
    oldPrice: 10320000,
    includes: ["پرواز", "هتل ساحلی", "صبحانه"],
  },
  {
    id: 6,
    title: "تور کیش + هتل ۵ ستاره",
    destination: "جزیره کیش",
    image: "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600",
    nights: 3,
    hotelStars: 5,
    price: 7800000,
    discount: 18,
    oldPrice: 9510000,
    includes: ["پرواز رفت و برگشت", "هتل مارینا", "ناهار و شام"],
  },
];

export default function TourHotelPackages() {
  const ref = useRef<HTMLDivElement>(null);
  const [canPrev, setCanPrev] = useState(false);
  const [canNext, setCanNext] = useState(false);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const update = useCallback(() => {
    const el = ref.current;
    if (!el) return;
    const max = el.scrollWidth - el.clientWidth;
    const pos = Math.abs(el.scrollLeft);
    setCanPrev(pos > 8);
    setCanNext(max - pos > 8);
  }, []);

  useEffect(() => {
    update();
    const el = ref.current;
    if (!el) return;
    el.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    return () => {
      el.removeEventListener("scroll", update);
      window.removeEventListener("resize", update);
    };
  }, [update]);

  const scroll = (dir: 1 | -1) => {
    const el = ref.current;
    if (!el) return;
    const isRTL = document.documentElement.dir === "rtl";
    const delta = isRTL ? -dir : dir;
    el.scrollBy({ left: delta * el.clientWidth * 0.98, behavior: "smooth" });
  };

  return (
    <section className="ns-container py-8 md:py-12 bg-bg-sec/40">
      <SectionHeader
        title="پکیج‌های تور + هتل"
        subtitle="پکیج‌های ترکیبی با بهترین قیمت و بیشترین امکانات"
        href="/tours"
        linkLabel="همه پکیج‌ها"
      />

      <div className="relative">
        {mounted && canPrev && (
          <button
            onClick={() => scroll(-1)}
            aria-label="قبلی"
            className="hidden md:flex absolute -start-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/95 backdrop-blur shadow-lg border border-border items-center justify-center text-text hover:text-primary hover:scale-110 transition"
          >
            <ChevronRight className="w-5 h-5" />
          </button>
        )}

        <div
          ref={ref}
          className="flex gap-4 md:gap-5 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-3 md:grid md:grid-cols-2 lg:grid-cols-3 md:overflow-visible md:snap-none md:pb-0"
        >
          {packages.map((pkg) => (
            <TourPackageCard key={pkg.id} {...pkg} />
          ))}
        </div>

        {mounted && canNext && (
          <button
            onClick={() => scroll(1)}
            aria-label="بعدی"
            className="hidden md:flex absolute -end-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/95 backdrop-blur shadow-lg border border-border items-center justify-center text-text hover:text-primary hover:scale-110 transition"
          >
            <ChevronLeft className="w-5 h-5" />
          </button>
        )}
      </div>
    </section>
  );
}