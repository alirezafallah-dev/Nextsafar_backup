"use client";
import Link from "next/link";
import Image from "next/image";
import { useState } from "react";
import { MapPin } from "lucide-react";

interface StoryCardProps {
  slug: string;
  title: string;
  image: string;
  place?: string;
}

export default function StoryCard({ slug, title, image, place }: StoryCardProps) {
  const [loaded, setLoaded] = useState(false);

  return (
    <Link
      href={`/destinations/${slug}`}
      className="group relative snap-start shrink-0 w-[200px] md:w-[240px] aspect-[3/4] rounded-[10px] md:rounded-lg overflow-hidden border border-border bg-bg-sec cursor-pointer"
    >
      {!loaded && (
        <div className="absolute inset-0 animate-pulse bg-gradient-to-br from-primary-lightest via-white to-primary-light" />
      )}
      <Image
        src={image}
        alt={title}
        fill
        sizes="(max-width: 768px) 200px, 240px"
        quality={85}
        loading="lazy"
        onLoad={() => setLoaded(true)}
        className={`object-cover transition-all duration-300 group-hover:scale-105 ${loaded ? "opacity-100" : "opacity-0"}`}
      />
      <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/5 to-transparent" />

      {place && (
        <span className="absolute top-3 start-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-black/30 backdrop-blur-md border border-white/20 text-white text-[11px] font-semibold z-10">
          <MapPin className="w-3 h-3" />
          {place}
        </span>
      )}

      <div className="absolute bottom-0 inset-x-0 px-4 py-2 z-10">
        <div className="text-white font-bold text-sm md:text-base leading-6 line-clamp-2 drop-shadow">
          {title}
        </div>
      </div>
    </Link>
  );
}