"use client";
import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import { Search } from "lucide-react";
import {
  CityPicker,
  CityValue,
  DateRangePicker,
  DateRangeValue,
  Field,
  PassengerPicker,
  SwapButton,
  parseJalaliDate,
  todayJalali,
} from "../fields";

interface Props {
  cabin?: string;
  initial?: {
    origin?: CityValue | null;
    dest?: CityValue | null;
    from?: string;
    to?: string;
    adults?: number;
    kids?: number;
  };
}

export default function TourForm({ cabin = "economy", initial }: Props) {
  const router = useRouter();
  const [origin, setOrigin] = useState<CityValue | null>(initial?.origin ?? null);
  const [dest, setDest] = useState<CityValue | null>(initial?.dest ?? null);
  const [dates, setDates] = useState<DateRangeValue>(
    initial?.from ? { from: initial.from, to: initial.to } : {},
  );
  const [adults, setAdults] = useState(initial?.adults ?? 1);
  const [kids, setKids] = useState(initial?.kids ?? 0);
  const [error, setError] = useState("");

  /* ✅ آپدیت فرم وقتی initial تغییر کند */
  useEffect(() => {
    if (initial) {
      setOrigin(initial.origin ?? null);
      setDest(initial.dest ?? null);
      setDates(initial.from ? { from: initial.from, to: initial.to } : {});
      setAdults(initial.adults ?? 1);
      setKids(initial.kids ?? 0);
    }
  }, [initial]);

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    
    if (!origin) { setError("مبدا را انتخاب کنید"); return; }
    if (!dest) { setError("مقصد را انتخاب کنید"); return; }
    if (origin.code && dest?.code && origin.code === dest.code) { 
      setError("مبدا و مقصد یکسان است"); 
      return; 
    }
    if (!dates.from) { setError("تاریخ رفت الزامی است"); return; }
    
    // ✅ مقایسه با Date به جای رشته
    const today = parseJalaliDate(todayJalali());
    const fromDate = parseJalaliDate(dates.from);
    
    if (!fromDate) { setError("تاریخ رفت نامعتبر است"); return; }
    if (today && fromDate < today) { 
      setError("تاریخ رفت نمی‌تواند در گذشته باشد"); 
      return; 
    }
    
    if (!dates.to) { setError("تاریخ برگشت الزامی است"); return; }
    
    const toDate = parseJalaliDate(dates.to);
    if (!toDate) { setError("تاریخ برگشت نامعتبر است"); return; }
    if (toDate <= fromDate) { 
      setError("برگشت نمی‌تواند قبل از رفت باشد"); 
      return; 
    }
    
    if (adults < 1) { setError("حداقل یک بزرگسال الزامی است"); return; }

    const p = new URLSearchParams({
      cabin,
      origin: origin.label,
      originCode: origin.code || "",
      destination: dest.label,
      destCode: dest.code || "",
      departDate: dates.from,
      returnDate: dates.to,
      adults: String(adults),
      children: String(kids),
    });
    router.push(`/tours?${p.toString()}`);
  };

  return (
    <form onSubmit={submit} className="flex flex-col gap-3">
      <div className="flex flex-wrap items-end gap-3">
        <Field label="مبدا" className="flex-1 min-w-[170px]">
          <CityPicker
            source="airports"
            value={origin}
            onChange={setOrigin}
            placeholder="مبدا (شهر / فرودگاه)"
          />
        </Field>

        <SwapButton
          onSwap={() => {
            setOrigin(dest);
            setDest(origin);
          }}
        />

        <Field label="مقصد" className="flex-1 min-w-[170px]">
          <CityPicker
            source="airports"
            value={dest}
            onChange={setDest}
            placeholder="مقصد (شهر / فرودگاه)"
          />
        </Field>

        <div className="flex-1 min-w-[280px]">
          <DateRangePicker
            value={dates}
            onChange={setDates}
            allowRange={true}
            fromName="تاریخ رفت"
            toName="تاریخ برگشت"
          />
        </div>

        <Field label="مسافران" className="w-full md:w-[190px]">
          <PassengerPicker
            adults={adults}
            children={kids}
            onChange={(a, c) => {
              setAdults(a);
              setKids(c);
            }}
          />
        </Field>

        <button
          type="submit"
          className="ns-btn ns-btn-primary h-12 w-full md:w-auto md:min-w-[140px]"
        >
          <Search className="w-4 h-4" />
          جستجو
        </button>
      </div>

      {error && (
        <p 
          className="text-sm text-danger bg-danger/5 border border-danger/20 rounded-lg px-4 py-2"
          role="alert"
          aria-live="assertive"
        >
          {error}
        </p>
      )}
    </form>
  );
}