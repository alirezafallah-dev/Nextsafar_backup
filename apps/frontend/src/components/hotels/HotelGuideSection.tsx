// src/components/hotels/HotelGuideSection.tsx
import { GuideCard } from "@/components/shared/cards";
import SectionHeader from "../home/SectionHeader";

const guides = [
  {
    id: 1,
    slug: "how-to-choose-hotel",
    title: "چگونه بهترین هتل را انتخاب کنیم؟",
    excerpt: "راهنمای کامل انتخاب هتل بر اساس بودجه، موقعیت مکانی و نوع سفر شما",
    image: "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600",
    category: "راهنمای انتخاب",
    readTime: "۵ دقیقه",
  },
  {
    id: 2,
    slug: "hotel-star-rating",
    title: "راهنمای جامع ستاره‌های هتل",
    excerpt: "تفاوت واقعی هتل‌های ۳ تا ۵ ستاره چیست؟ امکانات هر کدام را بشناسید",
    image: "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600",
    category: "آموزشی",
    readTime: "۴ دقیقه",
  },
  {
    id: 3,
    slug: "best-neighborhoods",
    title: "بهترین محله‌ها برای اقامت",
    excerpt: "محبوب‌ترین محله‌های توریستی شهرهای پرطرفدار جهان را بشناسید",
    image: "https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600",
    category: "مقاصد",
    readTime: "۶ دقیقه",
  },
  {
    id: 4,
    slug: "hotel-booking-tips",
    title: "نکات طلایی رزرو هتل",
    excerpt: "چطور با کمترین هزینه بهترین هتل را رزرو کنیم؟ ترفندهای حرفه‌ای",
    image: "https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600",
    category: "ترفندها",
    readTime: "۳ دقیقه",
  },
];

export default function HotelGuideSection() {
  return (
    <section className="ns-container py-8 md:py-12">
      <SectionHeader
        title="راهنمای انتخاب هتل"
        subtitle="همه چیزهایی که قبل از رزرو باید بدانید"
        href="/travel-guides"
        linkLabel="همه راهنماها"
      />

      {/* موبایل: کاروسل افقی / دسکتاپ: گرید 4 ستونه */}
      <div className="flex overflow-x-auto md:grid md:grid-cols-4 gap-3 md:gap-5 snap-x snap-mandatory md:snap-none scrollbar-hide pb-2 md:pb-0">
        {guides.map((guide) => (
          <GuideCard
            key={guide.id}
            slug={guide.slug}
            title={guide.title}
            excerpt={guide.excerpt}
            image={guide.image}
            category={guide.category}
            readTime={guide.readTime}
          />
        ))}
      </div>
    </section>
  );
}