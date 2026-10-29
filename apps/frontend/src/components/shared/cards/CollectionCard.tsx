"use client";
import Link from "next/link";
import Image from "next/image";
import { useState } from "react";
import { Flame, Layers, MapPin } from "lucide-react";
import { formatFa } from "@/lib/api/home";

const GRADIENTS = [
  "from-indigo-600 via-purple-600 to-primary",
  "from-rose-600 via-orange-500 to-amber-500",
  "from-teal-600 via-cyan-600 to-sky-500",
  "from-fuchsia-600 via-pink-600 to-rose-500",
];

const RANK_STYLES = [
  "bg-gradient-to-l from-amber-300 to-yellow-500 text-amber-950",
  "bg-gradient-to-l from-slate-200 to-slate-400 text-slate-900",
  "bg-gradient-to-l from-orange-300 to-amber-600 text-orange-950",
];

interface CollectionCardProps {
  slug: string;
  name: string;
  image: string | null;
  count: number;
  country?: string;
  index: number;
}

export default function CollectionCard({
  slug, name, image, count, country, index,
}: CollectionCardProps) {
  const [loaded, setLoaded] = useState(false);

  return (
    <Link
      href={`/tourism/${slug}`}
      className="group relative block overflow-hidden rounded-[10px] sm:rounded-lg h-52 md:h-64 ring-1 ring-black/5 hover:ring-2 hover:ring-primary/60 transition-shadow shrink-0 w-[280px] md:w-[calc(25%-0.9375rem)] snap-start cursor-pointer"
    >
      {image ? (
        <Image
          src={image}
          alt={name}
          fill
          sizes="280px"
          quality={85}
          loading="lazy"
          onLoad={() => setLoaded(true)}
          className={`object-cover transition-transform duration-700 group-hover:scale-110 ${loaded ? "opacity-100" : "opacity-0"}`}
        />
      ) : (
        <div className={`absolute inset-0 bg-gradient-to-br ${GRADIENTS[index % GRADIENTS.length]}`}>
          <span className="absolute inset-0 flex items-center justify-center text-white/15 text-6xl md:text-7xl font-black select-none">
            {name.charAt(0)}
          </span>
        </div>
      )}

      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/5 transition-colors group-hover:from-black/90" />

      <div className="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1 z-10">
        <span className={`flex items-center gap-1 px-2 py-1 rounded-lg text-[9px] font-black shadow-md ${RANK_STYLES[index] ?? "bg-black/45 backdrop-blur-sm text-white"}`}>
          {index < 3 && <Flame className="w-3 h-3" />}
          رتبه {formatFa(index + 1)}
        </span>
        <span className="flex items-center gap-1 px-2 py-1 rounded-lg bg-black/45 backdrop-blur-sm text-white text-[9px] font-bold">
          <Layers className="w-3 h-3" />
          {formatFa(count)} مطلب
        </span>
      </div>

      <div className="absolute inset-x-0 bottom-0 p-3 md:p-4 z-10">
        {country && (
          <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary/90 text-white text-[8.5px] md:text-[9px] font-bold mb-1.5">
            <MapPin className="w-2.5 h-2.5" />
            {country}
          </span>
        )}
        <h3 className="text-white text-sm md:text-base leading-6 line-clamp-1">{name}</h3>
        <span className="block h-0.5 w-8 bg-primary rounded-full mt-2 transition-all duration-500 group-hover:w-full" />
      </div>
    </Link>
  );
}