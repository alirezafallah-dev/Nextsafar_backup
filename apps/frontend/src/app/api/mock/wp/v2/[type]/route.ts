import { NextRequest, NextResponse } from 'next/server';
import mockVisas from '@/data/mock/visas.json';
import mockGuides from '@/data/mock/guides.json';
import mockNews from '@/data/mock/news.json';
import mockHotels from '@/data/mock/hotels.json';
import mockDestinations from '@/data/mock/destinations.json';
import mockTourism from '@/data/mock/tourism.json';
import mockTourCategories from '@/data/mock/tour-categories.json';

const mockData: Record<string, any> = {
  visa: mockVisas,
  travelguide: mockGuides,
  travelnews: mockNews,
  hotel: mockHotels,
  destination: mockDestinations,
  tourism: mockTourism,
  tour_category: mockTourCategories,
};

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ type: string }> } // ✅ تغییر به Promise
) {
  const { type } = await params; // ✅ await اضافه شود
  const data = mockData[type] || [];
  
  const headers = new Headers();
  headers.set('x-wp-total', String(data.length));
  headers.set('x-wp-totalpages', '1');
  
  return NextResponse.json(data, { headers });
}