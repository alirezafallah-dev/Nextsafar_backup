// src/components/shared/weather/WeatherWidget.tsx
"use client";

import { useEffect, useState } from "react";
import {
  Cloud,
  Droplets,
  Wind,
  Thermometer,
  MapPin,
  RefreshCw,
} from "lucide-react";
import { getWeather, type WeatherData } from "@/lib/api/weather";
import { faDigits } from "@/lib/api/hotels";
import type { WeatherWidgetProps } from "./types";
import WeatherForecast from "./WeatherForecast";

export default function WeatherWidget({
  lat,
  lng,
  cityName,
  showForecast = true,
  compact = false,
  className = "",
}: WeatherWidgetProps) {
  const [weather, setWeather] = useState<WeatherData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    let cancelled = false;

    const load = async () => {
      setLoading(true);
      setError(false);
      const data = await getWeather(lat, lng);
      if (!cancelled) {
        setWeather(data);
        setLoading(false);
        if (!data) setError(true);
      }
    };

    load();
    return () => {
      cancelled = true;
    };
  }, [lat, lng]);

  const handleRefresh = async () => {
    setLoading(true);
    const data = await getWeather(lat, lng);
    setWeather(data);
    setLoading(false);
  };

  /* ═══ حالت loading ═══ */
  if (loading) {
    return (
      <div
        className={`rounded-[10px] md:rounded-lg border border-border bg-white overflow-hidden ${className}`}
      >
        <div className="p-4 md:p-5">
          <div className="flex items-center gap-2 mb-3">
            <div className="w-8 h-8 rounded-lg bg-bg-sec animate-pulse" />
            <div className="h-4 w-24 rounded bg-bg-sec animate-pulse" />
          </div>
          <div className="h-16 rounded-lg bg-bg-sec animate-pulse" />
          {showForecast && !compact && (
            <div className="grid grid-cols-5 gap-1.5 mt-4">
              {[...Array(5)].map((_, i) => (
                <div key={i} className="h-20 rounded-lg bg-bg-sec animate-pulse" />
              ))}
            </div>
          )}
        </div>
      </div>
    );
  }

  /* ═══ حالت خطا ═══ */
  if (error || !weather) {
    return (
      <div
        className={`rounded-[10px] md:rounded-lg border border-border bg-white p-4 md:p-5 ${className}`}
      >
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2 text-text-muted">
            <Cloud className="w-5 h-5" />
            <span className="text-xs md:text-sm">
              آب و هوا در دسترس نیست
            </span>
          </div>
          <button
            onClick={handleRefresh}
            className="w-8 h-8 rounded-lg hover:bg-bg-sec flex items-center justify-center transition"
            aria-label="تلاش مجدد"
          >
            <RefreshCw className="w-4 h-4" />
          </button>
        </div>
      </div>
    );
  }

  const { current, forecast } = weather;

  return (
    <div
      className={`rounded-[10px] md:rounded-lg border border-border bg-white hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out space-y-3 ${className}`}
    >
      {/* Header */}
      <div className="flex items-center justify-between px-4 md:px-5 py-3 border-b border-border/60 bg-gradient-to-l from-primary/5 to-transparent">
        <div className="flex items-center gap-2">
          <div className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <Cloud className="w-4 h-4 text-primary" />
          </div>
          <div>
            <h3 className="text-sm font-extrabold text-text-strong">
              آب و هوای {cityName || "منطقه"}
            </h3>
            {cityName && (
              <div className="flex items-center gap-1 text-[10px] md:text-[11px] text-text-muted mt-0.5">
                <MapPin className="w-3 h-3" />
                <span>آب و هوای فعلی</span>
              </div>
            )}
          </div>
        </div>

        <button
          onClick={handleRefresh}
          className="w-8 h-8 rounded-lg hover:bg-bg-sec flex items-center justify-center text-text-muted hover:text-primary transition"
          aria-label="به‌روزرسانی"
        >
          <RefreshCw className="w-3.5 h-3.5" />
        </button>
      </div>

      {/* Body */}
      <div className="p-4 md:p-5">
        <div className="flex items-center gap-4">
          {/* آیکون + دما */}
          <div className="flex items-center gap-3">
            <span className="text-5xl md:text-6xl leading-none">
              {current.weatherIcon}
            </span>
            <div>
              <div className="flex items-baseline gap-1">
                <span className="text-3xl md:text-4xl font-black text-text-strong">
                  {faDigits(current.temperature)}
                </span>
                <span className="text-lg md:text-xl text-text-muted">°C</span>
              </div>
              <p className="text-xs md:text-sm font-bold text-text mt-0.5">
                {current.weatherDescription}
              </p>
            </div>
          </div>

          {/* اطلاعات تکمیلی */}
          <div className="flex-1 grid grid-cols-3 gap-2 md:gap-3">
            <div className="flex flex-col items-center gap-1 p-2 rounded-lg bg-bg-sec/50">
              <Thermometer className="w-3.5 h-3.5 text-warning" />
              <span className="text-[10px] text-text-muted leading-none">
                احساس
              </span>
              <span className="text-[11px] md:text-xs font-black text-text-strong">
                {faDigits(current.apparentTemperature)}°
              </span>
            </div>

            <div className="flex flex-col items-center gap-1 p-2 rounded-lg bg-bg-sec/50">
              <Droplets className="w-3.5 h-3.5 text-info" />
              <span className="text-[10px] text-text-muted leading-none">
                رطوبت
              </span>
              <span className="text-[11px] md:text-xs font-black text-text-strong">
                {faDigits(current.humidity)}٪
              </span>
            </div>

            <div className="flex flex-col items-center gap-1 p-2 rounded-lg bg-bg-sec/50">
              <Wind className="w-3.5 h-3.5 text-primary" />
              <span className="text-[10px] text-text-muted leading-none">
                باد
              </span>
              <span className="text-[11px] md:text-xs font-black text-text-strong">
                {faDigits(current.windSpeed)}
              </span>
            </div>
          </div>
        </div>

        {/* پیش‌بینی */}
        {showForecast && !compact && forecast.length > 0 && (
          <WeatherForecast forecast={forecast} />
        )}
      </div>
    </div>
  );
}