"use client";

import { createContext, useContext, useEffect, useState, ReactNode } from "react";

// ═══ تایپ‌ها ═══
export interface WeatherData {
  city: string;
  current: {
    temp: number;
    feelsLike: number;
    condition: "sunny" | "partly-cloudy" | "cloudy" | "rainy" | "snowy" | "stormy" | "windy";
    description: string;
    humidity: number;
    wind: number;
    icon: string;
  };
  forecast: Array<{
    day: string;
    temp: number;
    tempMin?: number;
    condition: string;
    icon: string;
  }>;
}

interface WeatherContextType {
  weather: WeatherData | null;
  loading: boolean;
  error: string | null;
  refresh: () => void;
}

const WeatherContext = createContext<WeatherContextType | undefined>(undefined);

// ═══ Mock Data (فعلاً تستی) ═══
// بعداً با API واقعی جایگزین می‌شود
const MOCK_WEATHER_DATA: Record<string, WeatherData> = {
  dubai: {
    city: "دبی",
    current: {
      temp: 34,
      feelsLike: 38,
      condition: "sunny",
      description: "آفتابی",
      humidity: 45,
      wind: 15,
      icon: "☀️",
    },
    forecast: [
      { day: "امروز", temp: 34, tempMin: 26, condition: "sunny", icon: "☀️" },
      { day: "فردا", temp: 35, tempMin: 27, condition: "sunny", icon: "☀️" },
      { day: "پس‌فردا", temp: 33, tempMin: 25, condition: "partly-cloudy", icon: "⛅" },
      { day: "جمعه", temp: 32, tempMin: 24, condition: "cloudy", icon: "☁️" },
      { day: "شنبه", temp: 34, tempMin: 26, condition: "sunny", icon: "☀️" },
    ],
  },
  istanbul: {
    city: "استانبول",
    current: {
      temp: 18,
      feelsLike: 16,
      condition: "partly-cloudy",
      description: "نیمه ابری",
      humidity: 65,
      wind: 20,
      icon: "⛅",
    },
    forecast: [
      { day: "امروز", temp: 18, tempMin: 12, condition: "partly-cloudy", icon: "⛅" },
      { day: "فردا", temp: 16, tempMin: 10, condition: "rainy", icon: "🌧️" },
      { day: "پس‌فردا", temp: 15, tempMin: 9, condition: "rainy", icon: "🌧️" },
      { day: "جمعه", temp: 17, tempMin: 11, condition: "cloudy", icon: "☁️" },
      { day: "شنبه", temp: 19, tempMin: 13, condition: "sunny", icon: "☀️" },
    ],
  },
  bangkok: {
    city: "بانکوک",
    current: {
      temp: 32,
      feelsLike: 36,
      condition: "partly-cloudy",
      description: "نیمه ابری و گرم",
      humidity: 75,
      wind: 8,
      icon: "⛅",
    },
    forecast: [
      { day: "امروز", temp: 32, tempMin: 25, condition: "partly-cloudy", icon: "⛅" },
      { day: "فردا", temp: 33, tempMin: 26, condition: "rainy", icon: "🌧️" },
      { day: "پس‌فردا", temp: 31, tempMin: 24, condition: "stormy", icon: "⛈️" },
      { day: "جمعه", temp: 32, tempMin: 25, condition: "partly-cloudy", icon: "⛅" },
      { day: "شنبه", temp: 33, tempMin: 26, condition: "sunny", icon: "☀️" },
    ],
  },
  tbilisi: {
    city: "تفلیس",
    current: {
      temp: 15,
      feelsLike: 13,
      condition: "cloudy",
      description: "ابری",
      humidity: 70,
      wind: 12,
      icon: "☁️",
    },
    forecast: [
      { day: "امروز", temp: 15, tempMin: 8, condition: "cloudy", icon: "☁️" },
      { day: "فردا", temp: 14, tempMin: 7, condition: "rainy", icon: "🌧️" },
      { day: "پس‌فردا", temp: 12, tempMin: 5, condition: "snowy", icon: "🌨️" },
      { day: "جمعه", temp: 13, tempMin: 6, condition: "cloudy", icon: "☁️" },
      { day: "شنبه", temp: 16, tempMin: 9, condition: "sunny", icon: "☀️" },
    ],
  },
};

// ═══ Provider ═══
export function WeatherProvider({
  children,
  city,
}: {
  children: ReactNode;
  city: string;
}) {
  const [weather, setWeather] = useState<WeatherData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchWeather = async () => {
    setLoading(true);
    setError(null);
    
    try {
      // ⚠️ Mock: بعداً با API واقعی جایگزین شود
      // const response = await fetch(`/api/weather?city=${city}`);
      // const data = await response.json();
      
      // شبیه‌سازی تاخیر شبکه
      await new Promise((resolve) => setTimeout(resolve, 500));
      
      const normalizedCity = city.toLowerCase().trim();
      const mockData = MOCK_WEATHER_DATA[normalizedCity];
      
      if (mockData) {
        setWeather(mockData);
      } else {
        // Fallback: استفاده از داده پیش‌فرض
        setWeather(MOCK_WEATHER_DATA.dubai);
      }
    } catch (err) {
      setError("خطا در دریافت اطلاعات آب و هوا");
      console.error("[WeatherProvider] Error:", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (city) {
      fetchWeather();
    }
  }, [city]);

  return (
    <WeatherContext.Provider value={{ weather, loading, error, refresh: fetchWeather }}>
      {children}
    </WeatherContext.Provider>
  );
}

// ═══ Hook ═══
export function useWeather() {
  const context = useContext(WeatherContext);
  if (!context) {
    throw new Error("useWeather must be used within WeatherProvider");
  }
  return context;
}