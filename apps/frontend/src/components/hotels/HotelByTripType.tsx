"use client";
import { useState } from "react";
import Link from "next/link";
import { Heart, Users, Briefcase, Gem, Umbrella, ArrowLeft } from "lucide-react";
import SectionHeader from "../home/SectionHeader";
import HotelGridCard from "@/components/shared/cards/HotelGridCard";
import type { Hotel } from "@/types/hotel";

const TRIP_TYPES = [
  {
    id: "family",
    label: "خانوادگی",
    icon: Users,
    color: "primary",
    hotels: [
      { id: 1, title: "هتل اسپیناس پالاس تهران", city: "تهران", image: "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400", rating: 9.2, stars: 5 },
      { id: 2, title: "هتل پارسیان آزادی", city: "تهران", image: "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400", rating: 8.8, stars: 5 },
      { id: 3, title: "هتل المپیک کیش", city: "کیش", image: "https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400", rating: 9.0, stars: 5 },
      { id: 4, title: "هتل داریوش کیش", city: "کیش", image: "https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400", rating: 9.1, stars: 5 },
    ],
  },
  {
    id: "honeymoon",
    label: "ماه عسل",
    icon: Heart,
    color: "rose",
    hotels: [
      { id: 5, title: "هتل مارینا کیش", city: "کیش", image: "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400", rating: 9.3, stars: 5 },
      { id: 6, title: "هتل سیمرغ مشهد", city: "مشهد", image: "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400", rating: 8.9, stars: 4 },
      { id: 7, title: "هتل پارسا آنتالیا", city: "آنتالیا", image: "https://images.unsplash.com/photo-1455587734955-0816b2681c8b?w=400", rating: 9.4, stars: 5 },
      { id: 8, title: "هتل ریکسوس دبی", city: "دبی", image: "https://images.unsplash.com/photo-1540541338287-41700207dee6?w=400", rating: 9.5, stars: 5 },
    ],
  },
  {
    id: "business",
    label: "تجاری",
    icon: Briefcase,
    color: "slate",
    hotels: [
      { id: 9, title: "هتل استقلال تهران", city: "تهران", image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400", rating: 8.7, stars: 5 },
      { id: 10, title: "هتل اسپیناس خلیج", city: "تهران", image: "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400", rating: 9.0, stars: 5 },
      { id: 11, title: "هتل هما شیراز", city: "شیراز", image: "https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400", rating: 8.8, stars: 5 },
      { id: 12, title: "هتل آریا اصفهان", city: "اصفهان", image: "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400", rating: 8.9, stars: 4 },
    ],
  },
  {
    id: "luxury",
    label: "لوکس",
    icon: Gem,
    color: "amber",
    hotels: [
      { id: 13, title: "هتل اسپیناس پالاس تهران", city: "تهران", image: "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400", rating: 9.6, stars: 5 },
      { id: 14, title: "هتل مارینا پارک کیش", city: "کیش", image: "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400", rating: 9.5, stars: 5 },
      { id: 15, title: "هتل برجم العرب دبی", city: "دبی", image: "https://images.unsplash.com/photo-1540541338287-41700207dee6?w=400", rating: 9.8, stars: 5 },
      { id: 16, title: "هتل فورسیزنز استانبول", city: "استانبول", image: "https://images.unsplash.com/photo-1455587734955-0816b2681c8b?w=400", rating: 9.7, stars: 5 },
    ],
  },
  {
    id: "beach",
    label: "ساحلی",
    icon: Umbrella,
    color: "teal",
    hotels: [
      { id: 17, title: "هتل مارینا کیش", city: "کیش", image: "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400", rating: 9.3, stars: 5 },
      { id: 18, title: "هتل سیمرغ کیش", city: "کیش", image: "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400", rating: 9.0, stars: 5 },
      { id: 19, title: "هتل لارا آنتالیا", city: "آنتالیا", image: "https://images.unsplash.com/photo-1455587734955-0816b2681c8b?w=400", rating: 9.2, stars: 5 },
      { id: 20, title: "هتل آتلانتیس دبی", city: "دبی", image: "https://images.unsplash.com/photo-1540541338287-41700207dee6?w=400", rating: 9.6, stars: 5 },
    ],
  },
];

const colorMap: Record<string, { bg: string; text: string }> = {
  primary: { bg: "bg-primary", text: "text-primary" },
  rose: { bg: "bg-rose-500", text: "text-rose-500" },
  slate: { bg: "bg-slate-600", text: "text-slate-600" },
  amber: { bg: "bg-amber-500", text: "text-amber-500" },
  teal: { bg: "bg-teal-500", text: "text-teal-500" },
};

interface SimpleHotel {
  id: number;
  title: string;
  city: string;
  image: string;
  rating: number;
  stars: number;
}

function convertToHotel(item: SimpleHotel): Hotel {
  return {
    id: item.id,
    title: item.title,
    title_en: "",
    image: item.image,
    gallery: [],
    address: item.city,
    stars: item.stars,
    rating: item.rating,
    price_per_night: 0,
    amenities: ["wifi", "pool", "restaurant"],
    featured: false,
    reviews: 0,
    slug: `hotel-${item.id}`,
    category: "",
    coordinates: { lat: 0, lng: 0 },
  };
}

export default function HotelByTripType() {
  const [activeTab, setActiveTab] = useState("family");
  const activeTrip = TRIP_TYPES.find((t) => t.id === activeTab)!;
  const colors = colorMap[activeTrip.color];

  return (
    <section className="ns-container py-8 md:py-12">
      <SectionHeader
        title="بهترین هتل‌ها بر اساس نوع سفر"
        subtitle="هتل مناسب سفر خود را پیدا کنید"
      />

      {/* تب‌ها */}
      <div className="flex overflow-x-auto md:flex-wrap gap-2 mb-6 scrollbar-hide pb-2 md:pb-0 snap-x snap-mandatory md:snap-none">
        {TRIP_TYPES.map((trip) => {
          const Icon = trip.icon;
          const isActive = activeTab === trip.id;
          const tripColors = colorMap[trip.color];
          return (
            <button
              key={trip.id}
              onClick={() => setActiveTab(trip.id)}
              className={`flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-xs md:text-sm font-bold transition-all duration-300 flex-shrink-0 snap-start whitespace-nowrap ${
                isActive
                  ? `${tripColors.bg} text-white shadow-md`
                  : "bg-white border border-border text-text-muted hover:text-text-strong"
              }`}
            >
              <Icon className="w-4 h-4" />
              <span>{trip.label}</span>
            </button>
          );
        })}
      </div>

      {/* کارت‌ها */}
      <div className="flex overflow-x-auto md:grid md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5 snap-x snap-mandatory md:snap-none scrollbar-hide pb-2 md:pb-0">
        {activeTrip.hotels.map((hotel) => (
          <HotelGridCard
            key={hotel.id}
            hotel={convertToHotel(hotel)}
            showPrice={false}
          />
        ))}
      </div>

      <div className="text-center mt-6 md:mt-8">
        <Link
          href={`/hotels?trip_type=${activeTab}`}
          className={`inline-flex items-center gap-1.5 px-6 py-3 rounded-lg ${colors.bg} text-white font-bold hover:opacity-90 transition`}
        >
          مشاهده همه هتل‌های {activeTrip.label}
          <ArrowLeft className="w-4 h-4 rtl:rotate-180" />
        </Link>
      </div>
    </section>
  );
}