// src/components/shared/weather/WeatherForecast.tsx
"use client";

import type { WeatherData } from "@/lib/api/weather";
import { faDigits } from "@/lib/api/hotels";

interface Props {
  forecast: WeatherData["forecast"];
}

export default function WeatherForecast({ forecast }: Props) {
  return (
    <div className="grid grid-cols-5 gap-1.5 md:gap-2 mt-4">
      {forecast.map((day, i) => (
        <div
          key={i}
          className={`flex flex-col items-center gap-1 p-2 md:p-2.5 rounded-[10px] md:rounded-lg border transition ${
            i === 0
              ? "bg-primary/10 border-primary/30"
              : "bg-white border-border/60"
          }`}
        >
          <span className="text-[10px] md:text-[11px] font-bold text-text-muted">
            {i === 0 ? "امروز" : day.dayName}
          </span>
          <span className="text-xl md:text-2xl my-0.5">{day.weatherIcon}</span>
          <div className="flex items-center gap-1 text-[10px] md:text-[11px]">
            <span className="font-black text-text-strong">
              {faDigits(day.tempMax)}°
            </span>
            <span className="text-text-muted">/</span>
            <span className="text-text-muted">{faDigits(day.tempMin)}°</span>
          </div>
        </div>
      ))}
    </div>
  );
}