export interface FlightResult {
  id: number;
  flight_number: string;
  airline: string;
  airline_logo: string | null;
  origin_code: string;
  origin_city: string;
  dest_code: string;
  dest_city: string;
  date: string;
  depart_time: string;
  arrive_time: string;
  next_day: boolean;
  duration_min: number;
  stops: number;
  price: number;
  currency: string;
  cabin: string;
  aircraft: string;
}

export interface FlightSearchParams {
  origin?: string;
  originCode?: string;
  destination?: string;
  destCode?: string;
  date?: string;
  returnDate?: string;
  tripType?: string;
  cabin?: string;
  adults?: number;
  children?: number;
}