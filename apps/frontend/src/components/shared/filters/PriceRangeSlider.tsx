"use client";
import { useState, useRef, useEffect } from "react";
import { faDigits } from "@/lib/api/hotels";

interface Props {
  min: number;
  max: number;
  value: [number, number];
  onChange: (value: [number, number]) => void;
  step?: number;
}

export default function PriceRangeSlider({
  min, max, value, onChange, step = 500_000,
}: Props) {
  const trackRef = useRef<HTMLDivElement>(null);
  const [dragging, setDragging] = useState<"min" | "max" | null>(null);

  const pct = (v: number) => ((v - min) / (max - min)) * 100;

  const getValue = (clientX: number) => {
    if (!trackRef.current) return min;
    const rect = trackRef.current.getBoundingClientRect();
    // در RTL: سمت راست = min (p=0)، سمت چپ = max (p=1)
    const p = Math.max(0, Math.min(1, (rect.right - clientX) / rect.width));
    const raw = min + p * (max - min);
    return Math.round(raw / step) * step;
  };

  useEffect(() => {
    if (!dragging) return;
    const move = (x: number) => {
      const v = getValue(x);
      if (dragging === "min") {
        onChange([Math.min(v, value[1] - step), value[1]]);
      } else {
        onChange([value[0], Math.max(v, value[0] + step)]);
      }
    };
    const onMove = (e: MouseEvent) => move(e.clientX);
    const onTouch = (e: TouchEvent) => move(e.touches[0].clientX);
    const end = () => setDragging(null);

    window.addEventListener("mousemove", onMove);
    window.addEventListener("mouseup", end);
    window.addEventListener("touchmove", onTouch);
    window.addEventListener("touchend", end);
    return () => {
      window.removeEventListener("mousemove", onMove);
      window.removeEventListener("mouseup", end);
      window.removeEventListener("touchmove", onTouch);
      window.removeEventListener("touchend", end);
    };
    // eslint-disable-next-line
  }, [dragging, value, onChange, min, max, step]);

  const format = (n: number) => {
    if (n >= 1_000_000) return faDigits((n / 1_000_000).toFixed(1).replace(/\.0$/, "")) + " م";
    return faDigits(n.toLocaleString());
  };

  return (
    <div className="pt-4 pb-2">
      {/* Display Values - حداقل سمت راست، حداکثر سمت چپ در RTL */}
      <div className="flex items-center justify-between mb-4">
        <div className="px-2 py-1.5 rounded-md bg-bg-sec border border-border min-w-[80px] text-center">
          <div className="text-[9px] text-text-muted mb-0.5">حداقل</div>
          <div className="text-[11px] font-black text-text-strong">{format(value[0])}</div>
        </div>
        <div className="flex-1 mx-2 h-px bg-border" />
        <div className="px-2 py-1.5 rounded-md bg-bg-sec border border-border min-w-[80px] text-center">
          <div className="text-[9px] text-text-muted mb-0.5">حداکثر</div>
          <div className="text-[11px] font-black text-text-strong">{format(value[1])}</div>
        </div>
      </div>

      {/* Track */}
      <div
        ref={trackRef}
        className="relative h-1.5 bg-border rounded-full cursor-pointer"
      >
        {/* Active Track */}
        <div
          className="absolute h-full bg-primary rounded-full"
          style={{
            left: `${100 - pct(value[1])}%`,
            width: `${pct(value[1]) - pct(value[0])}%`,
          }}
        />
        
        {/* Min Handle - سمت راست در RTL */}
        <div
          onMouseDown={() => setDragging("min")}
          onTouchStart={() => setDragging("min")}
          className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 cursor-grab active:cursor-grabbing z-10"
          style={{ left: `${100 - pct(value[0])}%` }}
        >
          <div className="w-5 h-5 rounded-full bg-white border-2 border-primary shadow-lg hover:scale-110 transition-transform" />
        </div>
        
        {/* Max Handle - سمت چپ در RTL */}
        <div
          onMouseDown={() => setDragging("max")}
          onTouchStart={() => setDragging("max")}
          className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 cursor-grab active:cursor-grabbing z-10"
          style={{ left: `${100 - pct(value[1])}%` }}
        >
          <div className="w-5 h-5 rounded-full bg-white border-2 border-primary shadow-lg hover:scale-110 transition-transform" />
        </div>
      </div>
    </div>
  );
}