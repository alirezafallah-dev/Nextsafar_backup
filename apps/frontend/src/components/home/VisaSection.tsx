import { getVisaPosts } from "@/lib/api/home";
import SectionHeader from "./SectionHeader";
import { VisaCard } from "@/components/shared/cards";

export default async function VisaSection() {
  const visas = await getVisaPosts(8);
  const limitedVisas = visas.slice(0, 8);

  if (limitedVisas.length === 0) return null;

  return (
    <section className="py-10 md:py-14 bg-bg-sec/60">
      <div className="ns-container">
        <SectionHeader
          title="خدمات ویزا"
          subtitle="راهنما، مدارک و شرایط جدید انواع ویزا"
          href="/visas"
          linkLabel="همه ویزاها"
        />
      </div>

      <div className="ns-container">
        <div className="ns-bleed-x flex gap-3 overflow-x-auto snap-x snap-mandatory pb-2 md:grid md:grid-cols-3 lg:grid-cols-4 md:gap-4 md:overflow-visible md:pb-0 scrollbar-hide">
          {limitedVisas.map((v: any) => (
            <VisaCard
              key={v.id}
              slug={v.slug}
              title={v.title}
              image={v.image}
              category={v.category}
              price_min={v.price_min}
              currency={v.currency}
              issue={v.issue}
            />
          ))}
        </div>
      </div>
    </section>
  );
}