import { NextRequest, NextResponse } from 'next/server';

const mockMenus = {
  mainmenu: [
    { id: 1, title: 'صفحه اصلی', url: '/', slug: 'home' },
    { id: 2, title: 'هتل‌ها', url: '/hotels', slug: 'hotels' },
    { id: 3, title: 'تورها', url: '/tours', slug: 'tours' },
    { id: 4, title: 'ویزا', url: '/visas', slug: 'visas' },
    { id: 5, title: 'راهنمای سفر', url: '/travel-guides', slug: 'travel-guides' },
    { id: 6, title: 'مجله', url: '/travel-news', slug: 'travel-news' },
  ],
  secmenu: [
    { id: 7, title: 'درباره ما', url: '/about', slug: 'about' },
    { id: 8, title: 'تماس با ما', url: '/contact', slug: 'contact' },
    { id: 9, title: 'قوانین', url: '/terms', slug: 'terms' },
    { id: 10, title: 'هتل', url: '/hotels', slug: 'terms' },
  ],
  mobilemenu: [],
};

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ slug: string }> } // ✅ تغییر به Promise
) {
  const { slug } = await params; // ✅ await اضافه شود
  const menu = mockMenus[slug as keyof typeof mockMenus] || [];
  return NextResponse.json({ items: menu });
}