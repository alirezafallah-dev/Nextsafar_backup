import type { Metadata } from "next";
import { notFound } from "next/navigation";
import hotelsData from "@/data/hotels.json";
import HotelDetailPage from "@/components/hotels/single/HotelDetailPage";
import type { Hotel } from "@/types/hotel";

export const revalidate = 3600;

interface PageProps {
  params: Promise<{ slug: string }>;
}

const getHotel = (slug: string) =>
  (hotelsData.hotels as Hotel[]).find((h) => h.slug === slug) || null;

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const hotel = getHotel(slug);
  if (!hotel) return { title: "هتل یافت نشد" };
  return {
    title: `${hotel.title} | رزرو هتل ${hotel.city}`,
    description: hotel.description.slice(0, 160),
    openGraph: { title: hotel.title, images: hotel.gallery?.[0] || hotel.image },
    alternates: { canonical: `/hotels/${slug}` },
  };
}

export async function generateStaticParams() {
  return (hotelsData.hotels as Hotel[]).map((h) => ({ slug: h.slug }));
}

export default async function Page({ params }: PageProps) {
  const { slug } = await params;
  const hotel = getHotel(slug);
  if (!hotel) notFound();
  return <HotelDetailPage hotel={hotel} />;
}