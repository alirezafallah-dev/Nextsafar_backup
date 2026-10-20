"use client";

import { useState } from "react";
import ListingMap from "./ListingMap";
import type { ListingMapPoint } from "./ListingMap";

interface Props {
  mapPoints: ListingMapPoint[];
  mapHeight?: number;
  children: React.ReactNode;
  onItemHover?: (key: string | null) => void;
  viewMode?: "split" | "list";
}

export default function SplitViewLayout({
  mapPoints,
  mapHeight = 650,
  children,
  onItemHover,
  viewMode = "split",
}: Props) {
  const [hoveredKey, setHoveredKey] = useState<string | null>(null);

  const handleHover = (key: string | null) => {
    setHoveredKey(key);
    onItemHover?.(key);
  };

  return (
    <>
      {viewMode === "split" ? (
        /* ═══ حالت Split: نقشه + لیست ═══ */
        <div className="grid lg:grid-cols-[500px_1fr] gap-6 items-start">
          {/* نقشه (sticky - زیر فیلتر) */}
          <div className="hidden lg:block sticky top-[240px] self-start">
            <div className="rounded-2xl border border-border bg-white shadow-lg overflow-hidden">
              <ListingMap
                points={mapPoints}
                activeKey={hoveredKey}
                height={mapHeight}
                className="!rounded-none"
              />
            </div>
          </div>

          {/* لیست */}
          <div
            className="space-y-4"
            onMouseLeave={() => handleHover(null)}
          >
            {children}
          </div>
        </div>
      ) : (
        /* ═══ حالت فقط لیست ═══ */
        <div
          className="grid md:grid-cols-2 gap-4"
          onMouseLeave={() => handleHover(null)}
        >
          {children}
        </div>
      )}
    </>
  );
}