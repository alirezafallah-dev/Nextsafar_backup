import { NextRequest, NextResponse } from 'next/server';
import mockVisas from '@/data/mock/visas.json';
import mockGuides from '@/data/mock/guides.json';
import mockNews from '@/data/mock/news.json';
import mockHotels from '@/data/mock/hotels.json';
import mockDestinations from '@/data/mock/destinations.json';
import mockTourism from '@/data/mock/tourism.json';
import mockTourCategories from '@/data/mock/tour-categories.json';
import mockFeaturedHotels from '@/data/mock/featured-hotels.json';

const mockData: Record<string, any> = {
  visa: mockVisas,
  travelguide: mockGuides,
  travelnews: mockNews,
  hotel: mockHotels,
  destination: mockDestinations,
  tourism: mockTourism,
  tour_category: mockTourCategories,
};

// ✅ Mock Map Config
const MOCK_MAP_CONFIG = {
  tile_url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  fallback_url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  attribution: "© OpenStreetMap contributors",
  max_zoom: 19,
  default_center: { lat: 35.6892, lng: 51.3890 },
  default_zoom: 5,
};

// ✅ Mock Favorites
const mockFavorites: Record<string, any[]> = {};

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ path: string[] }> }
) {
  const { path } = await params;
  const fullPath = path.join('/');
  
  // ✅ Map Config endpoint
  if (fullPath.includes('nextsafar/v1/map/config')) {
    return NextResponse.json(MOCK_MAP_CONFIG);
  }
  
  // ✅ Hotel Favorites endpoint
  if (fullPath.includes('nextsafar/v1/account/hotel-favorites')) {
    return NextResponse.json({ items: [] });
  }
  
  // ✅ Stats endpoint
  if (fullPath.includes('nextsafar/v1/stats')) {
    return NextResponse.json({
      hotels_count: 1250,
      tours_count: 340,
      destinations_count: 85,
      customers_count: 15000,
    });
  }
  
  // ✅ Featured Hotels
  if (fullPath.includes('nextsafar/v1/featured-hotels')) {
    return NextResponse.json({
      hotels: mockFeaturedHotels.slice(0, 8),
    });
  }
  
  // ✅ Menus
  if (fullPath.includes('nextsafar/v1/menus/')) {
    const slug = path[path.length - 1];
    const menus: Record<string, any[]> = {
      mainmenu: [
        { id: 1, title: 'صفحه اصلی', url: '/', slug: 'home' },
        { id: 2, title: 'هتل‌ها', url: '/hotels', slug: 'hotels' },
        { id: 3, title: 'تورها', url: '/tours', slug: 'tours' },
        { id: 4, title: 'ویزا', url: '/visas', slug: 'visas' },
      ],
      secmenu: [
        { id: 5, title: 'درباره ما', url: '/about', slug: 'about' },
        { id: 6, title: 'تماس با ما', url: '/contact', slug: 'contact' },
      ],
      mobilemenu: [],
    };
    return NextResponse.json({ items: menus[slug] || [] });
  }
  
  // ✅ WP REST API endpoints
  const wpMatch = fullPath.match(/wp\/v2\/([^/?]+)/);
  if (wpMatch) {
    const type = wpMatch[1];
    const data = mockData[type] || [];
    
    const headers = new Headers();
    headers.set('x-wp-total', String(data.length));
    headers.set('x-wp-totalpages', '1');
    
    return NextResponse.json(data, { headers });
  }
  
  console.warn('[MOCK API] Unknown path:', fullPath);
  return NextResponse.json({ error: 'Not found' }, { status: 404 });
}

export async function POST(
  request: NextRequest,
  { params }: { params: Promise<{ path: string[] }> }
) {
  const { path } = await params;
  const fullPath = path.join('/');
  
  // ✅ Favorites sync
  if (fullPath.includes('account/hotel-favorites')) {
    try {
      const body = await request.json();
      return NextResponse.json({ ok: true, items: body.items || [] });
    } catch {
      return NextResponse.json({ ok: true, items: [] });
    }
  }
  
  return NextResponse.json({ ok: true });
}