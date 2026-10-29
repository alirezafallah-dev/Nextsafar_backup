/* ═══════════════════════════════════════════════════════
   تایپ‌های عمومی برای تمام صفحات دسته‌بندی
   (هتل، فرودگاه، بیمارستان، رستوران، مقاصد)
   ═══════════════════════════════════════════════════════ */

/** نقطه روی نقشه */
export interface MapPoint {
  key: string;
  lat: number;
  lng: number;
  title: string;
  subtitle?: string;
  price?: number | null;
  url?: string;
}

/** آیتم لیست (generic) */
export interface ListingItem {
  id: string | number;
  title: string;
  subtitle?: string;
  image: string | null;
  url: string;
  coordinates: { lat: number; lng: number } | null;
  featured?: boolean;
  badges?: string[];
  // اطلاعات اضافی (اختیاری - بسته به نوع)
  meta?: Record<string, string | number | boolean | null>;
}

/** وضعیت فیلترها (generic) */
export interface BaseFilterState {
  searchQuery: string;
  sortBy: string;
  category?: string | null;
  subcategory?: string | null;
}

/** گزینه مرتب‌سازی */
export interface SortOption {
  key: string;
  label: string;
  icon?: React.ComponentType<{ className?: string }>;
}

/** آمار نتایج */
export interface ListingStats {
  total: number;
  filtered: number;
}

// اضافه کردن listingType به SplitViewLayoutProps
export interface SplitViewLayoutProps {
  mapPoints: MapPoint[];
  mapHeight?: number;
  defaultView?: "split" | "list" | "map";
  children: React.ReactNode;
  onItemHover?: (key: string | null) => void;
  stats?: ListingStats;
  title?: string;
}

// اضافه کردن metaContent به ListingCardProps
export interface ListingCardProps {
  item: ListingItem;
  content?: React.ReactNode;
  metaContent?: React.ReactNode;
  badges?: React.ReactNode;
  onHover?: (key: string | null) => void;
}

/** Props برای SearchFiltersBar */
export interface SearchFiltersBarProps {
  /** مقدار جستجو */
  searchQuery: string;
  /** callback تغییر جستجو */
  onSearchChange: (query: string) => void;
  /** placeholder جستجو */
  searchPlaceholder?: string;
  /** گزینه‌های مرتب‌سازی */
  sortOptions: SortOption[];
  /** مقدار فعلی مرتب‌سازی */
  sortValue: string;
  /** callback تغییر مرتب‌سازی */
  onSortChange: (value: string) => void;
  /** تعداد نتایج */
  resultCount: number;
  /** label برای تعداد (مثلاً "هتل" یا "فرودگاه") */
  resultLabel?: string;
  /** دکمه‌های اضافی */
  extraActions?: React.ReactNode;
}

/** Props برای FilterPanel */
export interface FilterPanelProps {
  /** تعداد فیلترهای فعال */
  activeCount?: number;
  /** callback پاک کردن فیلترها */
  onClear?: () => void;
  /** محتوای فیلترها (children) */
  children: React.ReactNode;
}

/** Props برای CategorySelector (بروزرسانی شده) */
export interface CategorySelectorProps {
  categories: Category[];
  selectedCategory: string | null;
  selectedChild: string | null;
  onCategorySelect: (slug: string | null) => void;
  onChildSelect: (slug: string | null) => void;
  title?: string;
  subtitle?: string;
  categoryLabel?: string;
  childLabel?: string;
  icon?: React.ReactNode;
  heroHeight?: string;
  showCounts?: boolean;
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  image: string;
  children: CategoryChild[];
  count?: number;
}

export interface CategoryChild {
  id: string;
  name: string;
  slug: string;
  image: string;
  count?: number;
}