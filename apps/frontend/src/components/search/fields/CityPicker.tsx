/**
 * CityPicker - انتخاب شهر/فرودگاه با دیتاست JSON + جستجوی وردپرس
 */
"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import { MapPin, Plane } from "lucide-react";

export interface CityValue {
  label: string;
  code?: string;
  sub?: string;
}

const WP =
  process.env.NEXT_PUBLIC_WP_API_URL || "http://nextsafar.local/wp-json";

let airportsCache: CityValue[] | null = null;
let citiesCache: CityValue[] | null = null;

async function loadAirports(): Promise<CityValue[]> {
  if (airportsCache) return airportsCache;
  try {
    const res = await fetch("/data/airports.json");
    if (!res.ok) throw new Error("Failed to load airports");
    const data: any[] = await res.json();
    airportsCache = data.map((a) => ({
      label: `${a.name_fa} (${a.iata})`,
      code: a.iata,
      sub: `${a.city_fa}، ${a.country_fa}`,
    }));
    return airportsCache!;
  } catch {
    airportsCache = [];
    return [];
  }
}

async function loadCities(): Promise<CityValue[]> {
  if (citiesCache) return citiesCache;
  try {
    const res = await fetch("/data/cities.json");
    if (!res.ok) throw new Error("Failed to load cities");
    const data: any[] = await res.json();
    citiesCache = data.map((c) => ({
      label: c.name_fa,
      sub: c.country_fa,
    }));
    return citiesCache!;
  } catch {
    citiesCache = [];
    return [];
  }
}

function dedupeCities(list: CityValue[]): CityValue[] {
  const seen = new Set<string>();
  const out: CityValue[] = [];
  for (const item of list) {
    const key = item.label
      .trim()
      .toLowerCase()
      .replace(/\s+/g, " ")
      .replace(/ي/g, "ی")
      .replace(/ك/g, "ک");
    if (seen.has(key)) continue;
    seen.add(key);
    out.push(item);
  }
  return out;
}

export function CityPicker({
  value,
  onChange,
  placeholder,
  source,
}: {
  value: CityValue | null;
  onChange: (v: CityValue) => void;
  placeholder: string;
  source: "airports" | "cities";
}) {
  const [open, setOpen] = useState(false);
  const [q, setQ] = useState("");
  const [allItems, setAllItems] = useState<CityValue[]>([]);
  const [wpItems, setWpItems] = useState<CityValue[]>([]);
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    (source === "airports" ? loadAirports() : loadCities()).then(setAllItems);
  }, [source]);

  useEffect(() => {
    if (!open || q.length < 2) {
      setWpItems([]);
      return;
    }
    const t = setTimeout(async () => {
      try {
        const ep = source === "airports" ? "airport" : "tourism";
        const res = await fetch(
          `${WP}/wp/v2/${ep}?search=${encodeURIComponent(
            q,
          )}&per_page=6&_fields=id,title,name,slug`,
        );
        if (!res.ok) throw 0;
        const data = await res.json();
        setWpItems(
          data.map((p: any) => ({
            label: decodeURIComponent(
              String(p.title?.rendered || p.name || "")
                .replace(/<[^>]+>/g, "")
                .trim(),
            ),
            code: decodeURIComponent(p.slug),
            sub: "از سایت",
          })),
        );
      } catch {
        setWpItems([]);
      }
    }, 300);
    return () => clearTimeout(t);
  }, [q, source, open]);

  useEffect(() => {
    if (!open) return;
    const close = (e: MouseEvent) => {
      if (!ref.current?.contains(e.target as Node)) setOpen(false);
    };
    document.addEventListener("mousedown", close);
    return () => document.removeEventListener("mousedown", close);
  }, [open]);

  const filtered = useMemo(() => {
    const term = q.trim().toLowerCase();
    const list = dedupeCities([...wpItems, ...allItems]);
    if (!term) return list.slice(0, 50);
    return list
      .filter(
        (i) =>
          i.label.toLowerCase().includes(term) ||
          i.code?.toLowerCase().includes(term) ||
          i.sub?.toLowerCase().includes(term),
      )
      .slice(0, 50);
  }, [q, allItems, wpItems]);

  const Icon = source === "airports" ? Plane : MapPin;

  return (
    <div ref={ref} className="relative w-full">
      <Icon className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary-500 pointer-events-none" />
      <input
        className="ns-input w-full ps-9"
        placeholder={placeholder}
        value={open ? q : value?.label || ""}
        onFocus={() => {
          setOpen(true);
          setQ("");
        }}
        onChange={(e) => {
          setQ(e.target.value);
          setOpen(true);
        }}
      />
      {open && (
        <ul className="absolute top-full inset-x-0 mt-2 bg-white rounded-lg border border-border shadow-card-hover overflow-hidden z-40 max-h-72 overflow-y-auto">
          {filtered.length === 0 ? (
            <li className="px-4 py-3 text-sm text-text-muted text-center">
              نتیجه‌ای یافت نشد
            </li>
          ) : (
            filtered.map((o, i) => (
              <li key={i}>
                <button
                  type="button"
                  onClick={() => {
                    onChange(o);
                    setOpen(false);
                    setQ("");
                  }}
                  className="w-full text-start px-4 py-2.5 text-sm hover:bg-primary-50 hover:text-primary-600 cursor-pointer flex items-center justify-between gap-2"
                >
                  <div className="flex flex-col min-w-0">
                    <span className="font-medium truncate">{o.label}</span>
                    {o.sub && (
                      <span className="text-xs text-text-subtle truncate">
                        {o.sub}
                      </span>
                    )}
                  </div>
                  {o.code && (
                    <span className="text-xs font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded shrink-0">
                      {o.code}
                    </span>
                  )}
                </button>
              </li>
            ))
          )}
        </ul>
      )}
    </div>
  );
}