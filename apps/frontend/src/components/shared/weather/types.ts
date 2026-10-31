// src/components/shared/weather/types.ts
export interface WeatherWidgetProps {
  lat: number;
  lng: number;
  cityName?: string;
  showForecast?: boolean;
  compact?: boolean;
  className?: string;
}