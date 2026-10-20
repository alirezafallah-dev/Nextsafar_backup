"use client";

import { useState, useRef, useEffect } from "react";
import Image from "next/image";
import { MapPin, ChevronDown, ChevronLeft, Globe, X } from "lucide-react";

/* ═══════════════════════════════════════════════════════
   تایپ‌های عمومی
   ═══════════════════════════════════════════════════════ */
export interface CategoryChild {
  id: string;
  name: string;
  slug: string;
  image: string;
  count?: number; // تعداد آیتم‌ها (مثلاً تعداد هتل‌ها)
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  image: string;
  children: CategoryChild[];
  count?: number;
}

export interface CategorySelectorProps {
  /** آرایه دسته‌بندی‌های اصلی (مثلاً کشورها) */
  categories: Category[];
  /** اسلاگ دسته‌بندی انتخاب شده */
  selectedCategory: string | null;
  /** اسلاگ زیردسته انتخاب شده */
  selectedChild: string | null;
  /** Callback وقتی دسته اصلی انتخاب شد */
  onCategorySelect: (slug: string | null) => void;
  /** Callback وقتی زیردسته انتخاب شد */
  onChildSelect: (slug: string | null) => void;
  /** عنوان بخش (مثلاً "مقصد"، "فرودگاه"، "رستوران") */
  title?: string;
  /** زیرعنوان */
  subtitle?: string;
  /** لیبل برای دسته‌بندی اصلی */
  categoryLabel?: string;
  /** لیبل برای زیردسته‌ها */
  childLabel?: string;
  /** آیکون اختصاصی (پیش‌فرض: MapPin) */
  icon?: React.ReactNode;
  /** ارتفاع بخش عکس (برای سفارشی‌سازی) */
  heroHeight?: string;
  /** نمایش تعداد آیتم‌ها */
  showCounts?: boolean;
}

export default function CategorySelector({
  categories,
  selectedCategory,
  selectedChild,
  onCategorySelect,
  onChildSelect,
  title = "مقصد",
  subtitle = "مقصد خود را انتخاب کنید",
  categoryLabel = "کشورها",
  childLabel = "شهرها",
  icon,
  heroHeight = "h-64 lg:h-[400px]",
  showCounts = true,
}: CategorySelectorProps) {
  const [open, setOpen] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  // بستن dropdown با کلیک خارج
  useEffect(() => {
    const handleClick = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    };
    document.addEventListener("mousedown", handleClick);
    return () => document.removeEventListener("mousedown", handleClick);
  }, []);

  const currentCategory = categories.find((c) => c.slug === selectedCategory);
  const children = currentCategory?.children || [];

  // عکس بزرگ (سمت راست)
  const heroImage = currentCategory?.image || categories[0]?.image;
  const heroName = currentCategory?.name || "همه مقاصد";

  // محاسبه تعداد کل
  const totalChildrenCount = children.length;
  const totalItemsCount = showCounts
    ? children.reduce((sum, child) => sum + (child.count || 0), 0)
    : 0;

  return (
    <section
      ref={containerRef}
      className="rounded-2xl overflow-hidden border border-border bg-white shadow-card relative"
    >
      <div className="grid lg:grid-cols-2 gap-0">
        {/* ═══ سمت راست: عکس بزرگ ═══ */}
        <div className={`relative ${heroHeight} overflow-hidden group`}>
          {heroImage && (
            <Image
              src={heroImage}
              alt={heroName}
              fill
              className="object-cover group-hover:scale-105 transition-transform duration-700"
              priority
            />
          )}
          {/* Overlay gradient */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />

          {/* محتوای روی عکس */}
          <div className="absolute inset-0 p-6 flex flex-col justify-end">
            <div className="flex items-center gap-2 text-white/90 text-sm mb-2">
              {icon || <Globe className="w-4 h-4" />}
              <span>{title} انتخابی</span>
            </div>
            <h2 className="text-2xl lg:text-3xl font-extrabold text-white">
              {heroName}
            </h2>
            {currentCategory && (
              <p className="text-white/80 text-sm mt-2">
                {totalChildrenCount} {childLabel}
                {showCounts && totalItemsCount > 0 && (
                  <> • {totalItemsCount} مورد</>
                )}
              </p>
            )}
          </div>

          {/* دکمه تغییر */}
          <button
            onClick={() => setOpen(!open)}
            className="absolute top-4 end-4 px-4 py-2 rounded-lg bg-white/95 backdrop-blur text-sm font-bold text-text-strong hover:bg-white transition cursor-pointer flex items-center gap-2"
          >
            {icon || <MapPin className="w-4 h-4 text-primary" />}
            تغییر {title}
            <ChevronDown
              className={`w-4 h-4 transition-transform ${open ? "rotate-180" : ""}`}
            />
          </button>
        </div>

        {/* ═══ سمت چپ: دسته‌بندی‌ها ═══ */}
        <div className="p-6 lg:p-8 flex flex-col justify-center">
          <h3 className="text-lg font-extrabold text-text-strong mb-4">
            {subtitle}
          </h3>

          {/* ═══ لاین ۱: دسته‌بندی‌های اصلی ═══ */}
          <div className="mb-6">
            <p className="text-xs font-bold text-text-muted uppercase tracking-wider mb-3">
              {categoryLabel}
            </p>
            <div className="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
              {categories.map((category) => (
                <button
                  key={category.id}
                  onClick={() => {
                    onCategorySelect(
                      category.slug === selectedCategory ? null : category.slug
                    );
                    onChildSelect(null); // ریست زیردسته
                  }}
                  className={`relative flex-shrink-0 group cursor-pointer ${
                    category.slug === selectedCategory
                      ? "ring-2 ring-primary ring-offset-2"
                      : ""
                  }`}
                >
                  <div className="relative w-28 h-20 rounded-xl overflow-hidden">
                    <Image
                      src={category.image}
                      alt={category.name}
                      fill
                      className="object-cover group-hover:scale-110 transition-transform duration-300"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                    <span className="absolute bottom-2 inset-x-2 text-center text-xs font-bold text-white">
                      {category.name}
                    </span>
                    {showCounts && category.count && (
                      <span className="absolute top-1 end-1 px-1.5 py-0.5 rounded-full bg-black/60 text-white text-[9px] font-bold">
                        {category.count}
                      </span>
                    )}
                  </div>
                </button>
              ))}
            </div>
          </div>

          {/* ═══ لاین ۲: زیردسته‌ها ═══ */}
          {currentCategory && children.length > 0 && (
            <div className="animate-in fade-in slide-in-from-top-2 duration-300">
              <p className="text-xs font-bold text-text-muted uppercase tracking-wider mb-3">
                {childLabel}ی {currentCategory.name}
              </p>
              <div className="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                {/* دکمه "همه" */}
                <button
                  onClick={() => onChildSelect(null)}
                  className={`relative flex-shrink-0 group cursor-pointer ${
                    selectedChild === null
                      ? "ring-2 ring-primary ring-offset-2"
                      : ""
                  }`}
                >
                  <div className="relative w-28 h-20 rounded-xl overflow-hidden bg-primary/10 flex items-center justify-center">
                    {icon || <MapPin className="w-8 h-8 text-primary" />}
                    <div className="absolute inset-0 bg-gradient-to-t from-primary/20 to-transparent" />
                    <span className="absolute bottom-2 inset-x-2 text-center text-xs font-bold text-text-strong">
                      همه {childLabel}
                    </span>
                  </div>
                </button>

                {children.map((child) => (
                  <button
                    key={child.id}
                    onClick={() =>
                      onChildSelect(
                        child.slug === selectedChild ? null : child.slug
                      )
                    }
                    className={`relative flex-shrink-0 group cursor-pointer ${
                      child.slug === selectedChild
                        ? "ring-2 ring-primary ring-offset-2"
                        : ""
                    }`}
                  >
                    <div className="relative w-28 h-20 rounded-xl overflow-hidden">
                      <Image
                        src={child.image}
                        alt={child.name}
                        fill
                        className="object-cover group-hover:scale-110 transition-transform duration-300"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                      <span className="absolute bottom-2 inset-x-2 text-center text-xs font-bold text-white">
                        {child.name}
                      </span>
                      {showCounts && child.count && (
                        <span className="absolute top-1 end-1 px-1.5 py-0.5 rounded-full bg-black/60 text-white text-[9px] font-bold">
                          {child.count}
                        </span>
                      )}
                    </div>
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* ═══ وضعیت انتخاب ═══ */}
          {(selectedCategory || selectedChild) && (
            <div className="mt-4 p-3 rounded-lg bg-primary-lightest border border-primary/20">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2 text-sm">
                  {icon || <MapPin className="w-4 h-4 text-primary" />}
                  <span className="font-bold text-text-strong">
                    {currentCategory?.name}
                    {selectedChild &&
                      children.find((c) => c.slug === selectedChild) && (
                        <>
                          <ChevronLeft className="w-3 h-3 inline mx-1" />
                          {children.find((c) => c.slug === selectedChild)?.name}
                        </>
                      )}
                  </span>
                </div>
                <button
                  onClick={() => {
                    onCategorySelect(null);
                    onChildSelect(null);
                  }}
                  className="text-xs text-danger hover:text-danger-dark font-bold cursor-pointer flex items-center gap-1"
                >
                  <X className="w-3 h-3" />
                  پاک کردن
                </button>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* ═══ Dropdown کامل (برای موبایل) ═══ */}
      {open && (
        <div className="absolute inset-x-0 top-full z-50 bg-white border-t border-border shadow-lg animate-in slide-in-from-top-2 duration-200 lg:hidden">
          <div className="p-4 max-h-[60vh] overflow-y-auto">
            {categories.map((category) => (
              <div key={category.id} className="mb-4">
                <p className="text-xs font-bold text-text-muted mb-2">
                  {category.name}
                </p>
                <div className="flex gap-2 overflow-x-auto">
                  {category.children.map((child) => (
                    <button
                      key={child.id}
                      onClick={() => {
                        onCategorySelect(category.slug);
                        onChildSelect(child.slug);
                        setOpen(false);
                      }}
                      className="flex-shrink-0 px-3 py-2 rounded-lg bg-bg-sec hover:bg-primary-lightest text-xs font-bold text-text-strong cursor-pointer"
                    >
                      {child.name}
                    </button>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </section>
  );
}