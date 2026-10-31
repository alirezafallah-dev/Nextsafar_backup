// src/components/hotels/single/HotelReviewsSection.tsx
"use client";

import { useState } from "react";
import { Star, Send, BadgeCheck, ThumbsUp, MessageSquarePlus, X, Quote } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import { faDigits } from "@/lib/api/hotels";

const CATEGORIES = [
  { label: "نظافت", score: 9.4 },
  { label: "موقعیت مکانی", score: 9.1 },
  { label: "خدمات", score: 9.3 },
  { label: "ارزش قیمت", score: 8.8 },
];

const DISTRIBUTION = [
  { stars: 5, percent: 72 },
  { stars: 4, percent: 18 },
  { stars: 3, percent: 6 },
  { stars: 2, percent: 2 },
  { stars: 1, percent: 2 },
];

const AVATAR_GRADIENTS = [
  "from-primary to-primary-dark",
  "from-warning to-tour",
  "from-success to-info",
  "from-danger to-warning",
];

interface Review {
  id: number;
  name: string;
  stars: number;
  text: string;
  date: string;
  helpful: number;
}

const INITIAL_REVIEWS: Review[] = [
  { id: 1, name: "علی محمدی", stars: 5, text: "همه چیز عالی بود؛ خصوصاً پارک آبی و رستوران‌ها. برخورد پرسنل فوق‌العاده محترمانه بود.", date: "۲ روز پیش", helpful: 12 },
  { id: 2, name: "زهرا کریمی", stars: 4, text: "ساحل اختصاصی و خدمات هتل فوق‌العاده بود. فقط زمان چک‌این کمی طولانی شد.", date: "۱ هفته پیش", helpful: 7 },
];

interface Props { hotel: Hotel }

export default function HotelReviewsSection({ hotel }: Props) {
  const [reviews, setReviews] = useState(INITIAL_REVIEWS);
  const [showForm, setShowForm] = useState(false);
  const [text, setText] = useState("");
  const [stars, setStars] = useState(5);
  const [helpfulVotes, setHelpfulVotes] = useState<Record<number, boolean>>({});

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!text.trim()) return;
    setReviews([
      { id: Date.now(), name: "شما", stars, text, date: "همین الان", helpful: 0 },
      ...reviews,
    ]);
    setText("");
    setStars(5);
    setShowForm(false);
  };

  const toggleHelpful = (id: number) =>
    setHelpfulVotes((v) => ({ ...v, [id]: !v[id] }));

  return (
    <section id="reviews">
      {/* Section Header هم‌استایل */}
      <div className="flex items-center justify-between gap-3 mb-5 md:mb-6">
        <div className="min-w-0">
          <h2 className="ns-section-title !mb-1">نظرات مهمانان</h2>
          <p className="text-xs md:text-sm text-text-muted">
            تجربه واقعی مسافران این هتل
          </p>
        </div>

        <button
          onClick={() => setShowForm(!showForm)}
          className={`flex items-center gap-1.5 shrink-0 whitespace-nowrap px-3 py-1.5 md:px-4 md:py-2 rounded-lg border text-[11px] md:text-xs font-bold transition ${
            showForm
              ? "bg-primary text-white border-primary hover:bg-primary-dark"
              : "border-border bg-white text-text-muted hover:border-primary hover:text-primary-dark hover:bg-primary-lightest/30"
          }`}
        >
          {showForm ? <X className="w-3.5 h-3.5" /> : <MessageSquarePlus className="w-3.5 h-3.5" />}
          {showForm ? "بستن فرم" : "ثبت نظر شما"}
        </button>
      </div>

      {/* فرم ثبت نظر */}
      {showForm && (
        <form
          onSubmit={submit}
          className="rounded-[10px] md:rounded-lg border border-primary/30 bg-primary-lightest/30 p-4 md:p-5 space-y-3 mb-6 animate-ns-fade"
        >
          <div className="flex items-center justify-between flex-wrap gap-3">
            <h3 className="text-sm md:text-base font-extrabold text-text-strong">تجربه خود را بنویسید</h3>
            <div className="flex items-center gap-2">
              <span className="text-[11px] md:text-xs text-text-muted">امتیاز شما:</span>
              <div className="flex items-center gap-0.5 p-1 rounded-lg bg-white border border-border">
                {[1, 2, 3, 4, 5].map((s) => (
                  <button
                    key={s}
                    type="button"
                    onClick={() => setStars(s)}
                    aria-label={`امتیاز ${s}`}
                    className="p-0.5 transition-transform hover:scale-125"
                  >
                    <Star
                      className={`w-4 h-4 transition ${
                        s <= stars ? "fill-warning text-warning" : "text-border hover:text-warning"
                      }`}
                    />
                  </button>
                ))}
              </div>
              <span className="text-[11px] md:text-xs font-black text-warning w-8">
                {faDigits(stars)}/۵
              </span>
            </div>
          </div>

          <textarea
            value={text}
            onChange={(e) => setText(e.target.value)}
            placeholder="نقاط قوت و ضعف اقامت خود را بنویسید..."
            className="ns-textarea text-xs md:text-sm bg-white"
            required
          />

          <div className="flex items-center justify-end gap-2">
            <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 rounded-lg border border-border bg-white text-xs md:text-sm font-bold text-text hover:bg-bg-sec transition">
              انصراف
            </button>
            <button type="submit" className="inline-flex items-center gap-1.5 px-4 md:px-5 py-2 md:py-2.5 rounded-lg bg-primary hover:bg-primary-dark text-white text-xs md:text-sm font-bold shadow-md hover:shadow-lg transition-all">
              <Send className="w-3.5 h-3.5" />
              ارسال نظر
            </button>
          </div>
        </form>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-[300px_1fr] gap-4 md:gap-6">

        {/* کارت امتیاز کل */}
        <div className="rounded-[10px] md:rounded-lg bg-gradient-to-br from-primary to-primary-dark text-white p-5 md:p-6 flex flex-col items-center text-center shadow-card-hover relative overflow-hidden">
          <div className="absolute -top-12 -start-12 w-36 h-36 rounded-full bg-white/10 pointer-events-none" />
          <div className="absolute -bottom-16 -end-10 w-40 h-40 rounded-full bg-white/5 pointer-events-none" />

          <span className="text-[10px] md:text-xs font-bold bg-white/15 px-3 py-1 rounded-full backdrop-blur relative">
            امتیاز کل
          </span>

          <div className="flex items-end gap-1 mt-3 relative">
            <span className="text-5xl md:text-6xl font-black leading-none">
              {faDigits(hotel.rating.toFixed(1))}
            </span>
            <span className="text-xs md:text-sm font-bold text-white/70 mb-1">/۱۰</span>
          </div>

          <span className="mt-3 px-4 py-1 rounded-sm bg-white text-primary text-[11px] md:text-xs font-black relative">
            عالی
          </span>

          <div className="flex gap-0.5 mt-3 relative">
            {Array.from({ length: 5 }).map((_, i) => (
              <Star key={i} className="w-4 h-4 fill-white text-white" />
            ))}
          </div>

          <p className="text-[10px] md:text-[11px] text-white/80 mt-2 relative">
            بر اساس {faDigits((hotel.reviews || 2348).toLocaleString())} نظر ثبت‌شده
          </p>

          <div className="w-full mt-5 space-y-2 relative">
            {DISTRIBUTION.map((d) => (
              <div key={d.stars} className="flex items-center gap-2">
                <span className="text-[10px] md:text-[11px] font-bold w-6 flex items-center gap-0.5 shrink-0">
                  {faDigits(d.stars)}
                  <Star className="w-2.5 h-2.5 fill-white" />
                </span>
                <div className="flex-1 h-1.5 rounded-full bg-white/20 overflow-hidden">
                  <div
                    className="h-full bg-white rounded-full transition-all duration-700"
                    style={{ width: `${d.percent}%` }}
                  />
                </div>
                <span className="text-[9px] md:text-[10px] text-white/70 w-8 text-end shrink-0">
                  ٪{faDigits(d.percent)}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* ستون محتوا */}
        <div className="space-y-4 md:space-y-5 min-w-0">

          {/* دسته‌بندی‌ها */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            {CATEGORIES.map((c) => (
              <div
                key={c.label}
                className="p-3 md:p-4 rounded-[10px] md:rounded-lg border border-border bg-white hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out"
              >
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[11px] md:text-xs font-bold text-text">{c.label}</span>
                  <span className="px-2 py-0.5 rounded-md bg-primary/10 text-primary text-[11px] md:text-xs font-black">
                    {faDigits(c.score.toFixed(1))}
                  </span>
                </div>
                <div className="h-1.5 rounded-full bg-bg-sec overflow-hidden">
                  <div
                    className="h-full rounded-full bg-gradient-to-l from-primary to-primary-light transition-all duration-700"
                    style={{ width: `${c.score * 10}%` }}
                  />
                </div>
              </div>
            ))}
          </div>

          {/* کارت نظرات */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            {reviews.map((r, idx) => (
              <article
                key={r.id}
                className="relative p-4 md:p-5 rounded-[10px] md:rounded-lg border border-border bg-white hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out overflow-hidden group"
              >
                <Quote className="absolute top-3 end-3 w-6 h-6 md:w-8 md:h-8 text-primary/10 group-hover:text-primary/25 transition" />

                <div className="flex items-center gap-2.5 md:gap-3 mb-3 md:mb-4">
                  <div
                    className={`w-10 h-10 md:w-11 md:h-11 rounded-[10px] md:rounded-lg bg-gradient-to-br ${AVATAR_GRADIENTS[idx % AVATAR_GRADIENTS.length]} text-white flex items-center justify-center text-sm md:text-base font-black shrink-0 shadow-md`}
                  >
                    {r.name.charAt(0)}
                  </div>
                  <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-1.5">
                      <span className="text-xs md:text-sm font-extrabold text-text-strong truncate">
                        {r.name}
                      </span>
                      <BadgeCheck className="w-3.5 h-3.5 md:w-4 md:h-4 text-primary shrink-0" />
                    </div>
                    <span className="text-[10px] md:text-xs text-text-muted">{r.date}</span>
                  </div>
                </div>

                <div className="flex gap-0.5 mb-2 md:mb-3">
                  {Array.from({ length: 5 }).map((_, i) => (
                    <Star
                      key={i}
                      className={`w-4 h-4 md:w-5 md:h-5 ${i < r.stars ? "fill-warning text-warning" : "text-border"}`}
                    />
                  ))}
                </div>

                <p className="text-xs md:text-sm text-text leading-6 md:leading-7 line-clamp-3 md:line-clamp-4 mb-3">
                  {r.text}
                </p>

                <div className="flex items-center justify-between pt-3 border-t border-border/50">
                  <button
                    onClick={() => toggleHelpful(r.id)}
                    className={`inline-flex items-center gap-1.5 text-[11px] md:text-xs font-bold transition ${
                      helpfulVotes[r.id] ? "text-primary" : "text-text-muted hover:text-primary"
                    }`}
                  >
                    <ThumbsUp className={`w-4 h-4 md:w-5 md:h-5 ${helpfulVotes[r.id] ? "fill-primary/20" : ""}`} />
                    مفید بود ({faDigits(r.helpful + (helpfulVotes[r.id] ? 1 : 0))})
                  </button>
                  <span className="text-[10px] md:text-[11px] text-text-subtle">اقامت تأییدشده</span>
                </div>
              </article>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}