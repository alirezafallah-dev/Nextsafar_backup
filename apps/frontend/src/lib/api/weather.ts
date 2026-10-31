// src/lib/api/weather.ts
export interface WeatherData {
  current: {
    temperature: number;
    apparentTemperature: number;
    humidity: number;
    windSpeed: number;
    weatherCode: number;
    weatherDescription: string;
    weatherIcon: string;
    isDay: boolean;
  };
  forecast: Array<{
    date: string;
    dayName: string;
    tempMax: number;
    tempMin: number;
    weatherCode: number;
    weatherIcon: string;
    weatherDescription: string;
  }>;
  location: {
    timezone: string;
  };
}

/* ═══ نگاشت کد آب و هوا به آیکون و توضیحات ═══ */
const WEATHER_CODES: Record<number, { icon: string; desc: string }> = {
  0: { icon: "☀️", desc: "آفتابی" },
  1: { icon: "🌤️", desc: "عمدتاً آفتابی" },
  2: { icon: "⛅", desc: "نیمه‌ابری" },
  3: { icon: "☁️", desc: "ابری" },
  45: { icon: "🌫️", desc: "مه" },
  48: { icon: "🌫️", desc: "مه یخ‌زده" },
  51: { icon: "🌦️", desc: "نم‌نم باران خفیف" },
  53: { icon: "🌦️", desc: "نم‌نم باران" },
  55: { icon: "🌦️", desc: "نم‌نم باران شدید" },
  61: { icon: "🌧️", desc: "باران خفیف" },
  63: { icon: "🌧️", desc: "باران" },
  65: { icon: "🌧️", desc: "باران شدید" },
  71: { icon: "🌨️", desc: "برف خفیف" },
  73: { icon: "🌨️", desc: "برف" },
  75: { icon: "❄️", desc: "برف شدید" },
  80: { icon: "🌦️", desc: "رگبار خفیف" },
  81: { icon: "🌧️", desc: "رگبار" },
  82: { icon: "⛈️", desc: "رگبار شدید" },
  95: { icon: "⛈️", desc: "رعد و برق" },
  96: { icon: "⛈️", desc: "رعد و برق با تگرگ" },
  99: { icon: "⛈️", desc: "رعد و برق شدید" },
};

const DAY_NAMES_FA = [
  "یکشنبه", "دوشنبه", "سه‌شنبه", "چهارشنبه",
  "پنج‌شنبه", "جمعه", "شنبه"
];

const getWeatherInfo = (code: number) => {
  return WEATHER_CODES[code] || { icon: "🌡️", desc: "نامشخص" };
};

/* ═══ دریافت آب و هوا از Open-Meteo (رایگان - بدون API key) ═══ */
export async function getWeather(
  lat: number,
  lng: number
): Promise<WeatherData | null> {
  try {
    if (!lat || !lng || isNaN(lat) || isNaN(lng)) return null;

    const url = new URL("https://api.open-meteo.com/v1/forecast");
    url.searchParams.set("latitude", lat.toString());
    url.searchParams.set("longitude", lng.toString());
    url.searchParams.set(
      "current",
      "temperature_2m,apparent_temperature,relative_humidity_2m,wind_speed_10m,weather_code,is_day"
    );
    url.searchParams.set(
      "daily",
      "temperature_2m_max,temperature_2m_min,weather_code"
    );
    url.searchParams.set("timezone", "auto");
    url.searchParams.set("forecast_days", "5");

    const res = await fetch(url.toString(), { next: { revalidate: 1800 } }); // 30 دقیقه cache
    if (!res.ok) return null;

    const data = await res.json();

    const currentCode = data.current?.weather_code ?? 0;
    const currentInfo = getWeatherInfo(currentCode);

    const forecast = (data.daily?.time || []).map((date: string, i: number) => {
      const code = data.daily.weather_code[i] ?? 0;
      const info = getWeatherInfo(code);
      const d = new Date(date);
      return {
        date,
        dayName: DAY_NAMES_FA[d.getDay()],
        tempMax: Math.round(data.daily.temperature_2m_max[i]),
        tempMin: Math.round(data.daily.temperature_2m_min[i]),
        weatherCode: code,
        weatherIcon: info.icon,
        weatherDescription: info.desc,
      };
    });

    return {
      current: {
        temperature: Math.round(data.current.temperature_2m),
        apparentTemperature: Math.round(data.current.apparent_temperature),
        humidity: data.current.relative_humidity_2m,
        windSpeed: Math.round(data.current.wind_speed_10m),
        weatherCode: currentCode,
        weatherDescription: currentInfo.desc,
        weatherIcon: currentInfo.icon,
        isDay: data.current.is_day === 1,
      },
      forecast,
      location: {
        timezone: data.timezone || "UTC",
      },
    };
  } catch (err) {
    console.error("[Weather API] Error:", err);
    return null;
  }
}