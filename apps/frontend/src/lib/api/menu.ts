// src/lib/api/menu.ts
import { buildApiUrl } from "./config";
import type { MenuItem } from "@/types/menu";
import mockMenus from "@/data/mock/menus.json";

/* ═══ Cache سراسری — فقط یک بار در کل عمر برنامه ═══ */
const cache = new Map<string, MenuItem[]>();

/* ═══ Fallback: داده‌های محلی وقتی API در دسترس نیست ═══ */
function getLocalFallback(slug: string): MenuItem[] {
  const local = (mockMenus as Record<string, MenuItem[]>)[slug];
  if (local && local.length > 0) {
    console.info(`[menus] ${slug} → using local fallback (${local.length} items)`);
    return local;
  }
  return [];
}

export async function getMenus(slug: string): Promise<MenuItem[]> {
  // ۱. بررسی cache
  if (cache.has(slug)) {
    return cache.get(slug)!;
  }

  // ۲. اگر API URL تنظیم نشده، مستقیم به fallback برو
  const apiUrl = process.env.NEXT_PUBLIC_API_URL;
  if (!apiUrl) {
    const fallback = getLocalFallback(slug);
    cache.set(slug, fallback);
    return fallback;
  }

  // ۳. تلاش برای fetch از API
  try {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 3000); // 3 ثانیه timeout
    
    const res = await fetch(buildApiUrl(`/nextsafar/v1/menus/${slug}`), {
      cache: "no-store",
      signal: controller.signal,
    });
    clearTimeout(timeout);

    if (!res.ok) {
      // خطای HTTP → fallback
      const fallback = getLocalFallback(slug);
      cache.set(slug, fallback);
      return fallback;
    }

    const data = await res.json();
    const items: MenuItem[] = Array.isArray(data) ? data : (data?.items ?? []);
    
    if (items.length === 0) {
      // API خالی برگرداند → fallback
      const fallback = getLocalFallback(slug);
      cache.set(slug, fallback);
      return fallback;
    }
    
    cache.set(slug, items);
    return items;
  } catch (e) {
    // Network error یا timeout → fallback
    const fallback = getLocalFallback(slug);
    cache.set(slug, fallback);
    return fallback;
  }
}