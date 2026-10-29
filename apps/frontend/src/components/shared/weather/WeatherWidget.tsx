"use client";

import { RefreshCw, Droplets, Wind, Thermometer } from "lucide-react";
import { useWeather } from "./WeatherProvider";
import { faDigits } from "@/lib/api/hotels";

interface WeatherWidgetProps {
  compact?: boolean;
}

export default function WeatherWidget({ compact = false }: WeatherWidgetProps) {
  const { weather, loading, error, refresh } = useWeather();

  // ═══ Loading State ═══
  if (loading) {
    return (
      <div className="h-full min-h-[200px] flex flex-col items-center justify-center bg-gradient-to-br from-sky-50 to-blue-50 rounded-2xl border border-sky-100">
        <div className="w-10 h-10 border-[3px] border-sky-500 border-t-transparent rounded-full animate-spin mb-3" />
        <span className="text-sm text-sky-700">دریافت آب و هوا...</span>
      </div>
    );
  }

  // ═══ Error State ═══
  if (error || !weather) {
    return (
      <div className="h-full min-h-[200px] flex flex-col items-center justify-center bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl border border-red-100 p-4 text-center">
        <Thermometer className="w-10 h-10 text-red-400 mb-2" />
        <p className="text-sm text-red-700 font-medium mb-2">
          {error || "اطلاعاتی در دسترس نیست"}
        </p>
        <button
          onClick={refresh}
          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-bold hover:bg-red-600 transition"
        >
          <RefreshCw className="w-3 h-3" />
          تلاش مجدد
        </button>
      </div>
    );
  }

  // ═══ Compact Mode ═══
  if (compact) {
    return (
      <div className="flex items-center justify-between p-4 bg-gradient-to-l from-sky-50 to-blue-50 rounded-xl border border-sky-100">
        <div className="flex items-center gap-3">
          <span className="text-3xl">{weather.current.icon}</span>
          <div>
            <div className="text-xl font-extrabold text-sky-900">
              {faDigits(weather.current.temp)}°C
            </div>
            <div className="text-xs text-sky-700">{weather.current.description}</div>
          </div>
        </div>
        <button
          onClick={refresh}
          className="w-8 h-8 rounded-full bg-white/60 hover:bg-white flex items-center justify-center transition"
          aria-label="بروزرسانی"
        >
          <RefreshCw className="w-3.5 h-3.5 text-sky-700" />
        </button>
      </div>
    );
  }

  // ═══ Full Mode ═══
  return (
    <div className="h-full bg-gradient-to-br from-sky-50 via-blue-50 to-cyan-50 rounded-2xl border border-sky-100 overflow-hidden flex flex-col">
      {/* Header */}
      <div className="p-4 border-b border-sky-100/60 bg-white/40 backdrop-blur-sm">
        <div className="flex items-center justify-between mb-2">
          <div className="flex items-center gap-2">
            <Thermometer className="w-4 h-4 text-sky-600" />
            <h3 className="text-sm font-extrabold text-sky-900">
              آب و هوای {weather.city}
            </h3>
          </div>
          <button
            onClick={refresh}
            className="w-8 h-8 rounded-full bg-white/70 hover:bg-white flex items-center justify-center transition"
            aria-label="بروزرسانی"
          >
            <RefreshCw className="w-3.5 h-3.5 text-sky-700" />
          </button>
        </div>
        <p className="text-xs text-sky-700">{weather.current.description}</p>
      </div>

      {/* Current Weather */}
      <div className="flex-1 p-4">
        <div className="flex items-center justify-between mb-4">
          <div>
            <div className="text-5xl font-extrabold text-sky-900 leading-none">
              {faDigits(weather.current.temp)}°
            </div>
            <div className="text-xs text-sky-700 mt-1">
              احساس: {faDigits(weather.current.feelsLike)}°
            </div>
          </div>
          <span className="text-6xl">{weather.current.icon}</span>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-2 gap-2 mb-4">
          <div className="flex items-center gap-2 p-2 bg-white/60 rounded-lg">
            <Droplets className="w-4 h-4 text-sky-600" />
            <div>
              <div className="text-[10px] text-sky-700">رطوبت</div>
              <div className="text-xs font-bold text-sky-900">
                {faDigits(weather.current.humidity)}٪
              </div>
            </div>
          </div>
          <div className="flex items-center gap-2 p-2 bg-white/60 rounded-lg">
            <Wind className="w-4 h-4 text-sky-600" />
            <div>
              <div className="text-[10px] text-sky-700">باد</div>
              <div className="text-xs font-bold text-sky-900">
                {faDigits(weather.current.wind)} km/h
              </div>
            </div>
          </div>
        </div>

        {/* Forecast */}
        <div>
          <div className="text-[10px] font-bold text-sky-900 uppercase tracking-wide mb-2">
            پیش‌بینی ۵ روزه
          </div>
          <div className="grid grid-cols-5 gap-1">
            {weather.forecast.slice(0, 5).map((day, idx) => (
              <div
                key={idx}
                className={`flex flex-col items-center py-2 rounded-lg transition ${
                  idx === 0 ? "bg-white/70 border border-sky-200" : "bg-white/40"
                }`}
              >
                <span className="text-[9px] font-bold text-sky-700 mb-1">
                  {day.day}
                </span>
                <span className="text-xl mb-0.5">{day.icon}</span>
                <span className="text-[11px] font-extrabold text-sky-900">
                  {faDigits(day.temp)}°
                </span>
                {day.tempMin && (
                  <span className="text-[9px] text-sky-600">
                    {faDigits(day.tempMin)}°
                  </span>
                )}
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}