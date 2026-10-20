/* ═══════════════════════════════════════════════════════════
   تنظیمات هیرو: تصویر + متن هر تب
   برای چرخش ۶ ثانیه‌ای، عکس دوم رو به آرایه اضافه کن
═══════════════════════════════════════════════════════════ */
export interface HeroTabConfig {
  title: string;
  subtitle: string;
  images: string[];
}

export const HERO_TABS: Record<string, HeroTabConfig> = {
  flight: {
    title: "پروازت رو هوشمندانه انتخاب کن",
    subtitle: "مقایسه لحظه‌ای قیمت همه ایرلاین‌ها",
    images: ["/images/hero/Flights.png"],
  },
  hotel: {
    title: "اقامتی که لایقته",
    subtitle: "هتل‌های داخلی و خارجی با تضمین بهترین قیمت",
    images: ["/images/hero/Hotel.png"],
  },
  tour: {
    title: "ماجراجویی بعدی از اینجا شروع می‌شه",
    subtitle: "تورهای داخلی و خارجی با برنامه‌ریزی کامل",
    images: ["/images/hero/Tour.png"],
  },
  destination: {
    title: "به کجا می‌خواهید بروید؟",
    subtitle: "صدها مقصد منتظر کشف شدن هستند",
    images: ["/images/hero/Travel-Destination.png"],
  },
  visa: {
    title: "ویزا بدون دردسر",
    subtitle: "چک‌لیست مدارک و پیگیری مرحله‌به‌مرحله",
    images: ["/images/hero/Visa.png"],
  },
  restaurant: {
    title: "شکم‌گردی در سفر",
    subtitle: "بهترین رستوران‌ها و کافه‌های هر مقصد",
    images: ["/images/hero/Restaurant.png"],
  },
  airport: {
    title: "فرودگاه رو مثل حرفه‌ای‌ها بشناس",
    subtitle: "اطلاعات پرواز، خدمات و تشریفات فرودگاهی",
    images: ["/images/hero/Airport.png"],
  },
  hospital: {
    title: "سفری برای سلامتی",
    subtitle: "بیمارستان‌های معتبر با پکیج کامل درمان و اقامت",
    images: ["/images/hero/Hospital.png"],
  },
  travelguide: {
    title: "مثل یک محلی سفر کن",
    subtitle: "راهنماهای کامل سفر به سراسر جهان",
    images: ["/images/hero/Travel-Guide.png"],
  },
};

export const HERO_FALLBACK: HeroTabConfig = {
  title: "به کجا می‌خواهید بروید؟",
  subtitle: "پرواز، هتل، تور و ویزا؛ همه یکجا در سفر بعدی",
  images: ["/images/hero/destination-1.jpg"],
};