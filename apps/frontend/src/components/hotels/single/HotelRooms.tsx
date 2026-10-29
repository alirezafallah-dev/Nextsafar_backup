// src/components/hotels/single/HotelRooms.tsx
"use client";

import { useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { Users, BedDouble, Ruler, Check, Plus } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import { faDigits } from "@/lib/api/hotels";

const ROOMS = [
  {
    id: 1,
    name: "اتاق استاندارد",
    image: "/images/rooms/standard.jpg",
    guests: 2, beds: "۱ تخت دو نفره", size: "۲۶-۳۶",
    perks: ["صبحانه رایگان", "Wi-Fi", "کنسلی رایگان"],
    price: 6890000,
  },
  {
    id: 2,
    name: "اتاق خانوادگی",
    image: "/images/rooms/family.jpg",
    guests: 4, beds: "۲ تخت دو نفره", size: "۴۰-۵۲",
    perks: ["صبحانه رایگان", "Wi-Fi", "کنسلی رایگان"],
    price: 9450000,
  },
];

interface Props { hotel: Hotel }

export default function HotelRooms({ hotel }: Props) {
  const [selected, setSelected] = useState<number | null>(null);

  return (
    <section id="rooms" className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-base font-extrabold text-text-strong">اتاق‌های هتل</h2>
        <Link href="#rooms" className="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:underline">
          <Plus className="w-3.5 h-3.5" />
          مشاهده همه اتاق‌ها
        </Link>
      </div>

      <div className="space-y-3">
        {ROOMS.map((room) => (
          <article
            key={room.id}
            className={`flex gap-3 p-3 rounded-xl border transition ${
              selected === room.id ? "border-primary ring-2 ring-primary/20 bg-primary/5" : "border-border bg-white hover:border-primary/40"
            }`}
          >
            {/* Details (right in RTL) */}
            <div className="flex-1 min-w-0 flex flex-col">
              <h3 className="text-sm font-extrabold text-text-strong mb-1.5">{room.name}</h3>

              <div className="flex items-center gap-3 text-[10px] text-text-muted mb-2 flex-wrap">
                <span className="inline-flex items-center gap-1"><Users className="w-3 h-3" /> {faDigits(room.guests)} بزرگسال</span>
                <span className="inline-flex items-center gap-1"><BedDouble className="w-3 h-3" /> {room.beds}</span>
                <span className="inline-flex items-center gap-1"><Ruler className="w-3 h-3" /> {faDigits(room.size)} متر</span>
              </div>

              <div className="flex items-center gap-1.5 flex-wrap mb-3">
                {room.perks.map((p) => (
                  <span key={p} className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-success/10 text-success text-[9px] font-bold">
                    <Check className="w-2.5 h-2.5" /> {p}
                  </span>
                ))}
              </div>

              <div className="mt-auto flex items-end justify-between gap-2">
                <div>
                  <div className="text-sm font-black text-text-strong">
                    {faDigits(room.price.toLocaleString())}
                    <span className="text-[9px] font-bold text-warning ms-1">تومان</span>
                  </div>
                  <div className="text-[9px] text-text-muted">برای ۲ شب</div>
                </div>
                <button
                  onClick={() => setSelected(room.id)}
                  className={`ns-btn ns-btn-sm ${selected === room.id ? "bg-success text-white" : "bg-primary text-white hover:bg-primary/90"}`}
                >
                  {selected === room.id ? "انتخاب شد ✓" : "انتخاب اتاق"}
                </button>
              </div>
            </div>

            {/* Image (left in RTL) */}
            <div className="relative w-[110px] md:w-[140px] h-[110px] md:h-auto rounded-lg overflow-hidden shrink-0">
              <Image src={room.image} alt={room.name} fill className="object-cover" sizes="140px" />
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}