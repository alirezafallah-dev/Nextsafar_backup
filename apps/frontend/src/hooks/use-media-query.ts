import { useState, useEffect } from "react";

/**
 * Hook برای تشخیص سایز صفحه (Media Query)
 * بهینه برای پرفورمنس - فقط در کلاینت اجرا می‌شود
 * 
 * @example
 * const isDesktop = useMediaQuery('(min-width: 768px)');
 * const isMobile = useMediaQuery('(max-width: 767px)');
 * const prefersReducedMotion = useMediaQuery('(prefers-reduced-motion: reduce)');
 */
export function useMediaQuery(query: string): boolean {
  const [matches, setMatches] = useState<boolean>(false);

  useEffect(() => {
    // ✅ فقط در مرورگر اجرا شود (SSR-safe)
    if (typeof window === "undefined") return;

    const mediaQuery = window.matchMedia(query);
    
    // تنظیم اولیه
    setMatches(mediaQuery.matches);

    // گوش دادن به تغییرات
    const handleChange = (event: MediaQueryListEvent) => {
      setMatches(event.matches);
    };

    // پشتیبانی از مرورگرهای قدیمی‌تر
    if (mediaQuery.addEventListener) {
      mediaQuery.addEventListener("change", handleChange);
      return () => mediaQuery.removeEventListener("change", handleChange);
    } else {
      // @ts-ignore - fallback برای مرورگرهای خیلی قدیمی
      mediaQuery.addListener(handleChange);
      // @ts-ignore
      return () => mediaQuery.removeListener(handleChange);
    }
  }, [query]);

  return matches;
}