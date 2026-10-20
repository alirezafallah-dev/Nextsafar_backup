import { BookOpen, MapPin, Star, Filter } from "lucide-react";

const features = [
  {
    icon: BookOpen,
    title: "اطلاعات جامع",
    description: "مشخصات کامل، امکانات و تصاویر واقعی",
  },
  {
    icon: MapPin,
    title: "موقعیت دقیق",
    description: "مشاهده روی نقشه با جزئیات کامل",
  },
  {
    icon: Star,
    title: "امتیاز مهمانان",
    description: "نظرات و امتیازهای واقعی مسافران",
  },
  {
    icon: Filter,
    title: "فیلتر پیشرفته",
    description: "یافتن هتل مناسب با معیارهای شما",
  },
];

export default function HotelHeroInfo() {
  return (
    <section className="ns-container pb-8">
      {/* ═══ عنوان اصلی ═══ */}
      <div className="text-center mb-8 md:mb-10">
        <div className="inline-flex items-center gap-2 mb-3">
          <div className="h-px w-8 bg-primary" />
          <span className="text-xs md:text-sm font-bold text-primary tracking-wide">
            سفر بعدی ایرانیان
          </span>
          <div className="h-px w-8 bg-primary" />
        </div>
        
        <h2 className="text-2xl md:text-4xl font-extrabold text-text-strong mb-3 md:mb-4 leading-tight">
          راهنمای جامع هتل‌های داخلی و خارجی
        </h2>
        
        <p className="text-sm md:text-lg text-text-muted max-w-2xl mx-auto leading-relaxed">
          اطلاعات کامل صدها هتل در سراسر جهان را بررسی کنید، موقعیت دقیق آن‌ها را روی نقشه ببینید،
          امتیاز و نظرات مهمانان را مطالعه کنید و بهترین گزینه را برای سفر خود پیدا کنید.
        </p>
      </div>

      {/* ═══ Grid ویژگی‌ها ═══ */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        {features.map((feature) => {
          const Icon = feature.icon;
          return (
            <div
              key={feature.title}
              className="group relative bg-white rounded-xl md:rounded-2xl border border-border p-4 md:p-6 hover:border-primary/40 hover:shadow-lg transition-all duration-300"
            >
              {/* آیکون */}
              <div className="w-10 h-10 md:w-12 md:h-12 rounded-lg md:rounded-xl bg-primary-lightest flex items-center justify-center mb-3 md:mb-4 group-hover:scale-110 transition-transform">
                <Icon className="w-5 h-5 md:w-6 md:h-6 text-primary" />
              </div>

              {/* عنوان */}
              <h3 className="text-sm md:text-base font-extrabold text-text-strong mb-1.5 md:mb-2">
                {feature.title}
              </h3>

              {/* توضیح */}
              <p className="text-xs md:text-sm text-text-muted leading-relaxed">
                {feature.description}
              </p>
            </div>
          );
        })}
      </div>
    </section>
  );
}