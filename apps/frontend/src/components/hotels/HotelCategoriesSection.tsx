import CascadingCategoryExplorer from "@/components/shared/categories/CascadingCategoryExplorer";
import type {
  CategoryLevel1,
  CategoryLevel2,
  CategoryLevel3,
} from "@/components/shared/categories/CascadingCategoryExplorer";

/* ═══ Types (برای سازگاری با کد قبلی) ═══ */
export interface Region extends CategoryLevel1 {}
export interface Country extends CategoryLevel2 {
  regionId: number;
}
export interface City extends CategoryLevel3 {
  countryId: number;
}

export interface HotelCategoriesSectionProps {
  title?: string;
  subtitle?: string;
  regions: Region[];
  countries: Country[];
  cities: City[];
  initialRegionId?: number;
  initialCountryId?: number;
  videoSrc: string;
  videoPoster?: string;
  basePath?: string;
  className?: string;
}

export default function HotelCategoriesSection({
  title = "دسته‌بندی هتل‌ها",
  subtitle = "منطقه، کشور و شهر مورد نظر خود را انتخاب کنید",
  regions,
  countries,
  cities,
  initialRegionId,
  initialCountryId,
  videoSrc,
  videoPoster,
  basePath = "/hotels/category",
  className = "",
}: HotelCategoriesSectionProps) {
  // تبدیل regionId → parent_id و countryId → parent_id
  const normalizedCountries: CategoryLevel2[] = countries.map((c) => ({
    id: c.id,
    parent_id: c.regionId,
    name: c.name,
    slug: c.slug,
    image: c.image,
  }));

  const normalizedCities: CategoryLevel3[] = cities.map((c) => ({
    id: c.id,
    parent_id: c.countryId,
    name: c.name,
    slug: c.slug,
    image: c.image,
    count: c.count,
  }));

  return (
    <CascadingCategoryExplorer
      title={title}
      subtitle={subtitle}
      levels={regions}
      subLevels={normalizedCountries}
      subSubLevels={normalizedCities}
      initialLevel1Id={initialRegionId}
      initialLevel2Id={initialCountryId}
      videoSrc={videoSrc}
      videoPoster={videoPoster}
      basePath={basePath}
      level1Label="منطقه"
      level2Label="کشور"
      level3Label="شهر"
      className={className}
    />
  );
}