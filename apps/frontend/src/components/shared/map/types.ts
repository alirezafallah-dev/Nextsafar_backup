export interface ListingMapPoint {
  key: string;
  lat: number;
  lng: number;
  title: string;
  type: string;
  locationType?: string;
}

export interface MapConfig {
  tilesUrl: string;
  attribution: string;
  center: [number, number];
  zoom: number;
}