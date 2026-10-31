import CascadingCategoryExplorer, {
  type CategoryLevel1,
  type CategoryLevel2,
  type CategoryLevel3,
} from "@/components/shared/categories/CascadingCategoryExplorer";

export interface Region {
  id: number;
  name: string;
  slug: string;
  image: string;
}

export interface Country {
  id: number;
  regionId: number;
  name: string;
  slug: string;
  image: string;
}

export interface City {
  id: number;
  countryId: number;
  name: string;
  slug: string;
  image: string;
  count: number;
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
  const normalizedCountries: CategoryLevel2[] = countries.map((c) => ({
    id: c.id,
    parent_id: c.regionId,
    name: c.name,
    slug: c.slug,
    image: c.image,
  }));

  const normalizedCities: CategoryLevel3[] = cities.map((c) => {
    // پیدا کردن slug کشور والد
    const parentCountry = countries.find((country) => country.id === c.countryId);
    return {
      id: c.id,
      parent_id: c.countryId,
      name: c.name,
      slug: c.slug,
      image: c.image,
      count: c.count,
      countrySlug: parentCountry?.slug, // ← اضافه کن
    };
  });

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
      basePathByLevel={{
        level2: "/hotels",
        level3: "",
      }}
      level1Label="منطقه"
      level2Label="کشور"
      level3Label="شهر"
      className={className}
    />
  );
}