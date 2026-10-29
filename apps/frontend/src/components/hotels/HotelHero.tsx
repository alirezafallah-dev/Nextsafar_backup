import Image from "next/image";

const HOTEL_HERO_IMAGE = "/images/hero/Hotel.png";

export default function HotelHero() {
  return (
    <section className="pt-5">
      {/* ✅ عنوان برای SEO (screen-reader) */}
      <h1 className="sr-only">
        رزرو هتل در بهترین مقاصد داخلی و خارجی - سفر بعدی ایرانیان
      </h1>

      <div className="ns-container">
        {/* ═══ هیرو یکپارچه (موبایل + دسکتاپ) ═══ */}
        <div className="relative w-full aspect-[21/9] md:aspect-[2172/650] rounded-2xl md:rounded-none overflow-hidden">
          <Image
            src={HOTEL_HERO_IMAGE}
            alt="هتل‌های لوکس و باکیفیت در سراسر جهان"
            fill
            sizes="(max-width: 1336px) 100vw, 1336px"
            quality={85}
            priority
            fetchPriority="high"
            className="object-cover object-center md:object-contain md:object-top"
          />
        </div>
      </div>
    </section>
  );
}