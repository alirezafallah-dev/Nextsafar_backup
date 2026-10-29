import { NextResponse } from 'next/server';

const featuredHotels = [
  {
    id: 1,
    title: 'هتل اسپیناس پالاس تهران',
    slug: 'espinas-palace-tehran',
    image: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
    stars: 5,
    rating: 4.8,
    city: 'تهران',
    country: 'ایران',
    amenities: ['وای‌فای', 'استخر', 'رستوران', 'باشگاه', 'اسپا'],
    url: '/hotels/espinas-palace-tehran',
  },
  {
    id: 2,
    title: 'هتل پارسیان آزادی',
    slug: 'parsian-azadi',
    image: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
    stars: 5,
    rating: 4.7,
    city: 'تهران',
    country: 'ایران',
    amenities: ['وای‌فای', 'رستوران', 'بار', 'باشگاه'],
    url: '/hotels/parsian-azadi',
  },
  {
    id: 3,
    title: 'هتل داریوش کیش',
    slug: 'dariush-kish',
    image: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800',
    stars: 5,
    rating: 4.9,
    city: 'کیش',
    country: 'ایران',
    amenities: ['وای‌فای', 'استخر', 'اسپا', 'رستوران'],
    url: '/hotels/dariush-kish',
  },
  {
    id: 4,
    title: 'هتل بزرگ مشهد',
    slug: 'grand-hotel-mashhad',
    image: 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800',
    stars: 5,
    rating: 4.6,
    city: 'مشهد',
    country: 'ایران',
    amenities: ['وای‌فای', 'رستوران', 'باشگاه', 'اسپا'],
    url: '/hotels/grand-hotel-mashhad',
  },
  {
    id: 5,
    title: 'هتل المپیک تهران',
    slug: 'olympic-hotel-tehran',
    image: 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
    stars: 4,
    rating: 4.5,
    city: 'تهران',
    country: 'ایران',
    amenities: ['وای‌فای', 'رستوران', 'بار', 'باشگاه'],
    url: '/hotels/olympic-hotel-tehran',
  },
];

export async function GET() {
  return NextResponse.json(featuredHotels);
}