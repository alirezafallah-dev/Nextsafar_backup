import PostListSection from "@/components/shared/sections/PostListSection";

const guides = [
  {
    slug: "how-to-choose-hotel",
    title: "چگونه بهترین هتل را انتخاب کنیم؟",
    excerpt: "راهنمای کامل انتخاب هتل بر اساس بودجه، موقعیت مکانی و نوع سفر شما",
    image: "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600",
    category: "راهنمای انتخاب",
    readTime: "۵ دقیقه",
  },
  {
    slug: "hotel-star-rating",
    title: "راهنمای جامع ستاره‌های هتل",
    excerpt: "تفاوت واقعی هتل‌های ۳ تا ۵ ستاره چیست؟ امکانات هر کدام را بشناسید",
    image: "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600",
    category: "آموزشی",
    readTime: "۴ دقیقه",
  },
  {
    slug: "best-neighborhoods",
    title: "بهترین محله‌ها برای اقامت",
    excerpt: "محبوب‌ترین محله‌های توریستی شهرهای پرطرفدار جهان را بشناسید",
    image: "https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600",
    category: "مقاصد",
    readTime: "۶ دقیقه",
  },
  {
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
    <PostListSection
      title="راهنمای انتخاب هتل"
      subtitle="همه چیزهایی که قبل از رزرو باید بدانید"
      posts={guides}
      href="/travel-guides"
      linkLabel="همه راهنماها"
    />
  );
}