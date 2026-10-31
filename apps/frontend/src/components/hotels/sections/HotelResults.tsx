"use client";

import { useState, useEffect, useRef } from "react";
import { BedDouble, ChevronDown, Eye, CheckCircle2 } from "lucide-react";
import { HotelGridCard, HotelListItem } from "@/components/shared/cards";
import { faDigits } from "@/lib/api/hotels";
import type { Hotel } from "@/types/hotel";
import type { ViewMode } from "../constants";

interface HotelResultsProps {
  hotels: Hotel[];
  view: ViewMode;
  onHover: (key: string | null) => void;
}

/* ═══ اندازه‌های Pagination ═══ */
const LIST_PAGE_SIZE = 10;
const GRID_PAGE_SIZE = 12;

export default function HotelResults({
  hotels,
  view,
  onHover,
}: HotelResultsProps) {
  // ✅ تعداد آیتم‌های نمایش داده شده
  const [visibleCount, setVisibleCount] = useState<number>(
    view === "list" ? LIST_PAGE_SIZE : GRID_PAGE_SIZE
  );

  // ✅ Ref برای اسکرول به آیتم‌های جدید
  const newItemsRef = useRef<HTMLDivElement>(null);

  // ✅ Reset visibleCount وقتی view یا hotels تغییر می‌کند
  useEffect(() => {
    setVisibleCount(view === "list" ? LIST_PAGE_SIZE : GRID_PAGE_SIZE);
  }, [view, hotels.length]);

  if (hotels.length === 0) {
    return <EmptyState />;
  }

  // ✅ برش لیست
  const visibleHotels = hotels.slice(0, visibleCount);
  const hasMore = visibleCount < hotels.length;
  const remainingCount = hotels.length - visibleCount;
  const pageSize = view === "list" ? LIST_PAGE_SIZE : GRID_PAGE_SIZE;

  // ✅ بارگذاری بیشتر
  const handleLoadMore = () => {
    const nextCount = Math.min(visibleCount + pageSize, hotels.length);
    setVisibleCount(nextCount);
    // اسکرول نرم به آیتم‌های جدید
    setTimeout(() => {
      newItemsRef.current?.scrollIntoView({
        behavior: "smooth",
        block: "center",
      });
    }, 100);
  };

  // ✅ نمایش همه
  const handleShowAll = () => {
    setVisibleCount(hotels.length);
  };

  // ✅ نمایش کمتر (reset)
  const handleShowLess = () => {
    setVisibleCount(pageSize);
    // اسکرول به بالای لیست
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  // ═══ View: List ═══
  if (view === "list") {
    return (
      <div className="space-y-4 md:space-y-5">
        {visibleHotels.map((h, idx) => (
          <div
            key={h.id}
            ref={idx === visibleCount - pageSize ? newItemsRef : null}
          >
            <HotelListItem
              hotel={h}
              onHover={(key) => onHover(key)}
            />
          </div>
        ))}

        {/* ═══ دکمه‌های Pagination ═══ */}
        <PaginationControls
          hasMore={hasMore}
          visibleCount={visibleCount}
          totalCount={hotels.length}
          remainingCount={remainingCount}
          allShown={visibleCount >= hotels.length}
          onLoadMore={handleLoadMore}
          onShowAll={handleShowAll}
          onShowLess={handleShowLess}
        />
      </div>
    );
  }

  // ═══ View: Grid ═══
  return (
    <div>
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-5">
        {visibleHotels.map((h, idx) => (
          <div
            key={h.id}
            ref={idx === visibleCount - pageSize ? newItemsRef : null}
          >
            <HotelGridCard hotel={h} />
          </div>
        ))}
      </div>

      {/* ═══ دکمه‌های Pagination ═══ */}
      <PaginationControls
        hasMore={hasMore}
        visibleCount={visibleCount}
        totalCount={hotels.length}
        remainingCount={remainingCount}
        allShown={visibleCount >= hotels.length}
        onLoadMore={handleLoadMore}
        onShowAll={handleShowAll}
        onShowLess={handleShowLess}
      />
    </div>
  );
}

/* ═══ کامپوننت دکمه‌های Pagination ═══ */
function PaginationControls({
  hasMore,
  visibleCount,
  totalCount,
  remainingCount,
  allShown,
  onLoadMore,
  onShowAll,
  onShowLess,
}: {
  hasMore: boolean;
  visibleCount: number;
  totalCount: number;
  remainingCount: number;
  allShown: boolean;
  onLoadMore: () => void;
  onShowAll: () => void;
  onShowLess: () => void;
}) {
  return (
    <div className="flex flex-col items-center gap-3 mt-8">
      {/* نوار پیشرفت */}
      <div className="w-full max-w-sm">
        <div className="flex items-center justify-between mb-1.5 text-[11px] text-text-muted">
          <span>
            {faDigits(visibleCount)} از {faDigits(totalCount)} هتل
          </span>
          <span>{Math.round((visibleCount / totalCount) * 100)}٪</span>
        </div>
        <div className="h-1 bg-border rounded-full overflow-hidden">
          <div
            className="h-full bg-gradient-to-l from-primary to-primary-dark transition-all duration-500 rounded-full"
            style={{ width: `${(visibleCount / totalCount) * 100}%` }}
          />
        </div>
      </div>

      {/* دکمه‌ها */}
      <div className="flex items-center gap-2 flex-wrap justify-center">
        {/* دکمه مشاهده بیشتر (batch) */}
        {hasMore && (
          <button
            onClick={onLoadMore}
            className="flex items-center gap-2 px-5 py-2.5 rounded-md md:rounded-md bg-primary/10 hover:bg-primary/20 text-primary font-bold text-sm transition-all duration-500 ease-out cursor-pointer group hover:shadow-md"
          >
            <Eye className="w-4 h-4" />
            مشاهده بیشتر
            {remainingCount > 0 && (
              <span className="px-1.5 py-0.5 rounded-md bg-primary text-white text-[10px] font-bold">
                +{faDigits(Math.min(remainingCount, 10))}
              </span>
            )}
            <ChevronDown className="w-4 h-4 group-hover:translate-y-0.5 transition-transform" />
          </button>
        )}

        {/* دکمه نمایش همه */}
        {hasMore && remainingCount > 10 && (
          <button
            onClick={onShowAll}
            className="flex items-center gap-2 px-5 py-2.5 md:rounded-md border border-border hover:border-primary-light bg-white hover:bg-bg-sec text-text-strong font-bold text-sm transition-all duration-500 ease-out cursor-pointer hover:shadow-md"
          >
            نمایش همه {faDigits(totalCount)} هتل
          </button>
        )}

        {/* دکمه نمایش کمتر */}
        {allShown && visibleCount > 12 && (
          <button
            onClick={onShowLess}
            className="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-success/10 hover:bg-success/20 text-success font-bold text-sm transition-all cursor-pointer"
          >
            <CheckCircle2 className="w-4 h-4" />
            نمایش کمتر
          </button>
        )}
      </div>

      {/* پیام پایان */}
      {allShown && visibleCount > 12 && (
        <p className="text-xs text-text-muted text-center mt-1">
          تمام هتل‌ها نمایش داده شدند
        </p>
      )}
    </div>
  );
}

/* ═══ حالت خالی ═══ */
function EmptyState() {
  return (
    <div className="rounded-lg border-2 border-dashed border-border bg-bg-sec/30 p-12 text-center">
      <BedDouble className="w-12 h-12 text-primary/50 mx-auto mb-4" />
      <h3 className="text-lg font-extrabold text-text-strong mb-2">
        هتلی یافت نشد
      </h3>
      <p className="text-sm text-text-muted">
        فیلترهای خود را تغییر دهید یا مقصد دیگری را انتخاب کنید
      </p>
    </div>
  );
}