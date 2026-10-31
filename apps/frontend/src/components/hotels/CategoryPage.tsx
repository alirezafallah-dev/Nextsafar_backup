// src/components/hotels/CategoryPage.tsx
"use client";

import type { HotelsData } from "@/types/hotel";
import HotelsPage from "./HotelsPage";
import CategoryHero from "./CategoryHero";

interface Props {
  data: HotelsData;
  title: string;
  image: string;
  type: "country" | "city";
  subtitle?: string;
}

export default function CategoryPage({
  data,
  title,
  image,
  type,
  subtitle,
}: Props) {
  return (
    <>
      <CategoryHero
        title={title}
        image={image}
        hotelCount={data.hotels.length}
        type={type}
        subtitle={subtitle}
      />
      <HotelsPage data={data} />
    </>
  );
}