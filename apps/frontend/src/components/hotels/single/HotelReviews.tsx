// src/components/hotels/single/HotelReviews.tsx
"use client";

import { useState } from "react";
import { Star, Send, User } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import { faDigits } from "@/lib/api/hotels";

const BARS = [
  { label: "نظافت", score: 9.4 },
  { label: "موقعیت مکانی", score: 9.1 },
  { label: "خدمات", score: 9.3 },
  { label: "ارزش قیمت", score: 8.8 },
];

interface Review { id: number; name: string; stars: number; text: string; date: string }

const INITIAL_REVIEWS: Review[] = [
  { id: 1, name: "علی محمدی", stars: 5, text: "همه چیز عالی بود. خصوصاً پارک آبی و رستوران‌ها.", date: "۲ روز پیش" },
  { id: 2, name: "زهرا کریمی", stars: 4, text: "ساحل اختصاصی و خدمات فوق‌العاده بود.", date: "۱ هفته پیش" },
];

interface Props { hotel: Hotel }

export default function HotelReviews({ hotel }: Props) {
  const [reviews, setReviews] = useState(INITIAL_REVIEWS);
  const [showForm, setShowForm] = useState(false);
  const [text, setText] = useState("");
  const [stars, setStars] = useState(5);

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!text.trim()) return;
    setReviews([{ id: Date.now(), name: "شما", stars, text, date: "همین الان" }, ...reviews]);
    setText(""); setStars(5); setShowForm(false);
  };

  return (
    <section id="reviews" className="space-y-5">
      <h2 className="text-base font-extrabold text-text-strong">نظرات مهمانان</h2>

      <div className="flex flex-col lg:flex-row-reverse gap-6 lg:items-start">
        {/* Score */}
        <div className="lg:w-[150px] shrink-0 text-center lg:text-start">
          <div className="text-5xl font-black text-text-strong">{faDigits(hotel.rating.toFixed(1))}</div>
          <div className="text-sm font-extrabold text-primary mt-1">عالی</div>
          <div className="text-[10px] text-text-muted mt-1">بر اساس {faDigits((hotel.reviews || 2348).toLocaleString())} نظر</div>
          <div className="flex justify-center lg:justify-start gap-0.5 mt-2">
            {Array.from({ length: 5 }).map((_, i) => (
              <Star key={i} className="w-4 h-4 fill-warning text-warning" />
            ))}
          </div>
        </div>

        {/* Bars */}
        <div className="flex-1 w-full space-y-3">
          {BARS.map((b) => (
            <div key={b.label} className="flex items-center gap-3">
              <span className="text-[11px] font-bold text-text w-24 shrink-0">{b.label}</span>
              <div className="flex-1 h-1.5 bg-bg-sec rounded-full overflow-hidden">
                <div className="h-full bg-primary rounded-full" style={{ width: `${b.score * 10}%` }} />
              </div>
              <span className="text-[11px] font-black text-text-strong w-7 text-end">{faDigits(b.score.toFixed(1))}</span>
            </div>
          ))}
        </div>

        {/* Review Cards */}
        <div className="lg:w-[300px] shrink-0 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-2">
          {reviews.slice(0, 4).map((r) => (
            <div key={r.id} className="p-3 rounded-xl border border-border/60 bg-white">
              <div className="flex items-center gap-2 mb-1.5">
                <div className="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center">
                  <User className="w-3.5 h-3.5 text-primary" />
                </div>
                <div className="min-w-0">
                  <div className="text-[11px] font-extrabold text-text-strong truncate">{r.name}</div>
                  <div className="text-[9px] text-text-muted">{r.date}</div>
                </div>
              </div>
              <div className="flex gap-0.5 mb-1.5">
                {Array.from({ length: r.stars }).map((_, i) => (
                  <Star key={i} className="w-3 h-3 fill-warning text-warning" />
                ))}
              </div>
              <p className="text-[10px] text-text leading-5 line-clamp-3">{r.text}</p>
            </div>
          ))}
        </div>
      </div>

      {!showForm ? (
        <button onClick={() => setShowForm(true)} className="ns-btn ns-btn-sm border border-primary text-primary hover:bg-primary/5">
          ثبت نظر شما
        </button>
      ) : (
        <form onSubmit={submit} className="bg-bg-sec rounded-xl p-4 space-y-3">
          <div className="flex items-center gap-1">
            <span className="text-[11px] text-text-muted ms-1">امتیاز:</span>
            {[1, 2, 3, 4, 5].map((s) => (
              <button key={s} type="button" onClick={() => setStars(s)}>
                <Star className={`w-4 h-4 ${s <= stars ? "fill-warning text-warning" : "text-gray-300"}`} />
              </button>
            ))}
          </div>
          <textarea value={text} onChange={(e) => setText(e.target.value)} placeholder="تجربه خود را بنویسید..." className="ns-textarea text-xs" required />
          <div className="flex gap-2">
            <button type="submit" className="ns-btn ns-btn-sm bg-primary text-white">
              <Send className="w-3.5 h-3.5" /> ارسال
            </button>
            <button type="button" onClick={() => setShowForm(false)} className="ns-btn ns-btn-sm ns-btn-ghost">انصراف</button>
          </div>
        </form>
      )}
    </section>
  );
}