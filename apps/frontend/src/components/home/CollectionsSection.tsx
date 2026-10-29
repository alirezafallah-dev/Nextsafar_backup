import { getTourismTerms } from "@/lib/api/home";
import SectionHeader from "./SectionHeader";
import CollectionsCarousel from "./CollectionsCarousel";

export default async function CollectionsSection() {
  // ✅ نهایت 10 شهر نمایش داده می‌شود
  const terms = await getTourismTerms(10);
  if (terms.length === 0) return null;

  return (
    <section className="ns-container py-8 md:py-12">
      <SectionHeader
        title="شهرهای پرطرفدار"
        subtitle="محبوب‌ترین شهرها بر اساس محتوا و بازدید کاربران سفر بعدی"
        href="/tourism"
        linkLabel="همه شهرها"
      />

      <CollectionsCarousel items={terms} />
    </section>
  );
}