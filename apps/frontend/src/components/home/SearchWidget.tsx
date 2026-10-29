"use client";

import { useEffect, useState } from "react";
import dynamic from "next/dynamic";
import {
  Backpack,
  BedDouble,
  BookOpen,
  ChevronDown,
  MapPin,
  Plane,
  PlaneTakeoff,
  Stamp,
  Stethoscope,
  UtensilsCrossed,
  X,
} from "lucide-react";

// ✅ Dynamic imports برای فرم‌های سنگین
const FlightForm = dynamic(() => import("@/components/search/forms/FlightForm"), {
  ssr: false,
  loading: () => <FormSkeleton />,
});

const HotelForm = dynamic(() => import("@/components/search/forms/HotelForm"), {
  ssr: false,
  loading: () => <FormSkeleton />,
});

const TourForm = dynamic(() => import("@/components/search/forms/TourForm"), {
  ssr: false,
  loading: () => <FormSkeleton />,
});

const ContentForm = dynamic(() => import("@/components/search/forms/ContentForm"), {
  ssr: false,
  loading: () => <FormSkeleton />,
});

// ✅ Skeleton برای لودینگ فرم‌ها
function FormSkeleton() {
  return (
    <div className="animate-pulse space-y-4 py-2">
      <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
        {[1, 2, 3, 4].map((i) => (
          <div key={i} className="h-12 bg-bg-sec rounded-lg" />
        ))}
      </div>
      <div className="flex justify-end">
        <div className="h-11 w-32 bg-primary/20 rounded-lg" />
      </div>
    </div>
  );
}

type TabDef =
  | { id: string; label: string; icon: any; kind: "booking" }
  | {
      id: string;
      label: string;
      icon: any;
      kind: "content";
      postType: string;
      basePath: string;
      ph: string;
    };

const TABS: TabDef[] = [
  { id: "flight", label: "پرواز", icon: Plane, kind: "booking" },
  { id: "hotel", label: "هتل", icon: BedDouble, kind: "booking" },
  { id: "tour", label: "تور", icon: Backpack, kind: "booking" },
  {
    id: "destination", label: "مقاصد", icon: MapPin, kind: "content",
    postType: "destination", basePath: "/destinations", ph: "جستجوی مقاصد و جاذبه‌ها...",
  },
  {
    id: "visa", label: "ویزا", icon: Stamp, kind: "content",
    postType: "visa", basePath: "/visas", ph: "جستجوی شرایط و مدارک ویزا...",
  },
  {
    id: "restaurant", label: "رستوران", icon: UtensilsCrossed, kind: "content",
    postType: "restaurant", basePath: "/restaurants", ph: "جستجوی رستوران و کافه...",
  },
  {
    id: "airport", label: "فرودگاه", icon: PlaneTakeoff, kind: "content",
    postType: "airport", basePath: "/airports", ph: "جستجوی اطلاعات فرودگاه‌ها...",
  },
  {
    id: "hospital", label: "بیمارستان", icon: Stethoscope, kind: "content",
    postType: "hospital", basePath: "/hospitals", ph: "جستجوی بیمارستان و مراکز درمانی...",
  },
  {
    id: "travelguide", label: "راهنمای سفر", icon: BookOpen, kind: "content",
    postType: "travelguide", basePath: "/travel-guides", ph: "جستجوی راهنمای سفر...",
  },
];

const BOOKING_TABS = TABS.filter((t) => t.kind === "booking");
const CONTENT_TABS = TABS.filter((t) => t.kind === "content");

interface SearchWidgetProps {
  activeTab?: string;
  onTabChange?: (id: string) => void;
  float?: boolean;
}

export default function SearchWidget({
  activeTab,
  onTabChange,
  float = false,
}: SearchWidgetProps) {
  const [internalTab, setInternalTab] = useState("flight");
  const [sheetOpen, setSheetOpen] = useState(false);

  const tabId = activeTab ?? internalTab;
  const setTabId = (id: string) => {
    setInternalTab(id);
    onTabChange?.(id);
  };

  const tab = TABS.find((t) => t.id === tabId) ?? TABS[0];
  const activeContent = CONTENT_TABS.find((t) => t.id === tabId) ?? null;

  useEffect(() => {
    document.body.style.overflow = sheetOpen ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [sheetOpen]);

  return (
    <div className="w-full mx-auto">
      {/* ═══ سطر تب‌ها — دسکتاپ ═══ */}
      <div className="relative mb-3 hidden md:block">
        <div className="mx-auto w-fit max-w-full overflow-x-auto scrollbar-hide">
          <div
            role="tablist"
            aria-label="جستجوی خدمات سفر"
            className="flex items-center gap-0.5 bg-white rounded-lg shadow-sm border border-border p-1.5"
          >
            {TABS.map((t) => {
              const Icon = t.icon;
              const active = t.id === tabId;
              const isFirstContent = t.id === CONTENT_TABS[0]?.id;
              return (
                <span key={t.id} className="flex items-center">
                  {isFirstContent && (
                    <span className="w-px h-6 bg-border mx-1.5 shrink-0" />
                  )}
                  <button
                    type="button"
                    role="tab"
                    aria-selected={active}
                    onClick={() => setTabId(t.id)}
                    className={`flex items-center gap-1.5 px-3.5 md:px-4 py-2 rounded-lg text-xs md:text-sm font-semibold whitespace-nowrap transition-all cursor-pointer ${
                      active
                        ? "bg-primary text-white shadow-md"
                        : "text-text-muted hover:text-primary-dark hover:bg-primary-lightest"
                    }`}
                  >
                    <Icon className="w-4 h-4" />
                    {t.label}
                  </button>
                </span>
              );
            })}
          </div>
        </div>
      </div>

      {/* ═══ سطر تب‌ها — موبایل ═══ */}
      <div className="relative mb-3 md:hidden">
        <div
          role="tablist"
          aria-label="جستجوی خدمات سفر"
          className="flex items-center gap-1 bg-white rounded-lg shadow-sm border border-border p-1.5"
        >
          {BOOKING_TABS.map((t) => {
            const Icon = t.icon;
            const active = t.id === tabId;
            return (
              <button
                key={t.id}
                type="button"
                role="tab"
                aria-selected={active}
                onClick={() => setTabId(t.id)}
                className={`flex-1 min-w-0 flex items-center justify-center gap-1 px-1 py-2 rounded-md text-[11px] font-bold whitespace-nowrap transition-all cursor-pointer ${
                  active
                    ? "bg-primary text-white shadow-md"
                    : "text-text-muted hover:text-primary-dark"
                }`}
              >
                <Icon className="w-3.5 h-3.5 shrink-0" />
                <span className="truncate">{t.label}</span>
              </button>
            );
          })}
          <button
            type="button"
            onClick={() => setSheetOpen(true)}
            aria-haspopup="dialog"
            aria-expanded={sheetOpen}
            className={`flex-1 min-w-0 flex items-center justify-center gap-1 px-1 py-2 rounded-md text-[11px] font-bold transition-all cursor-pointer ${
              activeContent
                ? "bg-primary text-white shadow-md"
                : "text-text-muted hover:text-primary-dark"
            }`}
          >
            <span className="truncate">
              {activeContent ? activeContent.label : "خدمات +"}
            </span>
            <ChevronDown className="w-3 h-3 shrink-0" />
          </button>
        </div>
      </div>

      {/* ═══ Bottom Sheet خدمات — موبایل ═══ */}
      {sheetOpen && (
        <div
          className="fixed inset-0 z-[600] md:hidden"
          role="dialog"
          aria-modal="true"
        >
          <div
            className="absolute inset-0 bg-black/40 animate-ns-fade"
            onClick={() => setSheetOpen(false)}
          />
          <div className="absolute bottom-0 inset-x-0 bg-white rounded-lg p-4 pb-6 animate-ns-sheet">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-sm font-extrabold">جستجو در خدمات</h3>
              <button
                type="button"
                onClick={() => setSheetOpen(false)}
                className="p-2 rounded-lg hover:bg-bg-sec transition"
                aria-label="بستن"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
            <div className="grid grid-cols-2 min-[420px]:grid-cols-3 gap-2">
              {CONTENT_TABS.map((t) => {
                const Icon = t.icon;
                const active = t.id === tabId;
                return (
                  <button
                    key={t.id}
                    type="button"
                    onClick={() => {
                      setTabId(t.id);
                      setSheetOpen(false);
                    }}
                    className={`flex flex-col items-center gap-2 py-3.5 rounded-lg border transition cursor-pointer ${
                      active
                        ? "border-primary bg-primary/10 text-primary"
                        : "border-border text-text-muted hover:border-primary/50 hover:text-primary"
                    }`}
                  >
                    <Icon className="w-5 h-5" />
                    <span className="text-[10px] font-bold">{t.label}</span>
                  </button>
                );
              })}
            </div>
          </div>
        </div>
      )}

      {/* ═══ کارت فرم ═══ */}
      <div
        role="tabpanel"
        className={
          float
            ? "bg-white rounded-lg md:rounded-[16px] shadow-sm ring-1 ring-black/5 border border-border p-4 md:p-5"
            : "bg-white rounded-lg md:rounded-[16px] shadow-sm border border-border p-4 md:p-5"
        }
      >
        {tab.id === "flight" && <FlightForm />}
        {tab.id === "hotel" && <HotelForm />}
        {tab.id === "tour" && <TourForm />}
        {tab.kind === "content" && (
          <ContentForm
            postType={tab.postType}
            basePath={tab.basePath}
            placeholder={tab.ph}
            icon={tab.icon}
            typeLabel={tab.label}
          />
        )}
      </div>
    </div>
  );
}