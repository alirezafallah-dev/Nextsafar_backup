import Image from "next/image";

const HOTEL_HERO_IMAGE = "/images/hero/Hotel.png";

export default function HotelHero() {
  return (
    <section className="pt-5">
      {/* ✅ عنوان برای SEO (screen-reader) */}
      <h1 className="sr-only">
        رزرو هتل در بهترین مقاصد داخلی و خارجی - سفر بعدی ایرانیان
      </h1>

      {/* ═══ هیرو دسکتاپ ═══ */}
      <div className="ns-container">
        <div className="relative hidden md:block w-full aspect-[2172/650]">
          <Image
            src={HOTEL_HERO_IMAGE}
            alt="هتل‌های لوکس و باکیفیت در سراسر جهان"
            fill
            sizes="(max-width: 1336px) 100vw, 1336px"
            quality={85}
            priority
            fetchPriority="high"
            className="object-contain object-top"
          />
        </div>
      </div>

      {/* ═══ هیرو موبایل (نسخه ساده‌تر) ═══ */}
      <div className="ns-container md:hidden">
        <div className="relative w-full aspect-[16/9] rounded-2xl overflow-hidden">
          <Image
            src={HOTEL_HERO_IMAGE}
            alt="هتل‌های لوکس"
            fill
            sizes="100vw"
            quality={80}
            className="object-cover"
          />
          {/* Gradient overlay برای خوانایی */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
          
          {/* عنوان کوچک روی موبایل */}
          <div className="absolute bottom-4 inset-x-4 text-white">
            <h2 className="text-xl font-extrabold mb-1">
              اقامتی که لایقته
            </h2>
            <p className="text-xs text-white/90">
              بهترین هتل‌ها با تضمین قیمت
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}