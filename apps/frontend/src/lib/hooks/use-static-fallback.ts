import { useState, useEffect } from "react";

/**
 * هوک برای استفاده از داده static به عنوان fallback
 * اگر API در دسترس نبود، داده static را نشان می‌دهد
 */
export function useStaticFallback<T>(
  apiFetcher: () => Promise<T>,
  staticData: T,
  cacheKey: string
) {
  const [data, setData] = useState<T>(staticData);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    let mounted = true;

    const cached = sessionStorage.getItem(cacheKey);
    if (cached) {
      try {
        setData(JSON.parse(cached));
        return;
      } catch {}
    }

    setLoading(true);
    apiFetcher()
      .then((result) => {
        if (mounted) {
          setData(result);
          sessionStorage.setItem(cacheKey, JSON.stringify(result));
        }
      })
      .catch(() => {
        if (mounted) setData(staticData);
      })
      .finally(() => {
        if (mounted) setLoading(false);
      });

    return () => { mounted = false; };
  }, [cacheKey]);

  return { data, loading };
}