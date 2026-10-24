import { useState, useCallback } from 'react';

/**
 * Hook برای مدیریت fallback امن در صورت خطای دریافت دیتا
 * @example
 * const { data, loading, error, retry } = useStaticFallback(
 *   () => fetchSomeData(),
 *   fallbackData
 * );
 */
export function useStaticFallback<T>(
  fetcher: () => Promise<T>,
  fallback: T
) {
  const [data, setData] = useState<T>(fallback);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const retry = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const result = await fetcher();
      setData(result);
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setData(fallback); // بازگشت به fallback در صورت خطا
    } finally {
      setLoading(false);
    }
  }, [fetcher, fallback]);

  return { data, loading, error, retry };
}