/**
 * توابع کاربردی برای تبدیل و کار با تاریخ شمسی (جلالی)
 */
import * as faCal from "date-fns-jalali";
import { isValid } from "date-fns-jalali";
import { differenceInCalendarDays } from "date-fns";

const JALALI_FMT = "yyyy/MM/dd";

/**
 * تبدیل رشته تاریخ شمسی (YYYY/MM/DD) به شی Date
 */
export function parseJalaliDate(s: string | undefined): Date | undefined {
  if (!s) return undefined;
  try {
    const d = faCal.parse(s, JALALI_FMT, new Date());
    return isValid(d) ? d : undefined;
  } catch {
    return undefined;
  }
}

/**
 * تبدیل شی Date به رشته تاریخ شمسی (YYYY/MM/DD)
 */
export function formatJalaliDate(d: Date | undefined): string {
  if (!d || !isValid(d)) return "";
  return faCal.format(d, JALALI_FMT);
}

/**
 * دریافت تاریخ امروز به صورت Date (بدون ساعت)
 */
function todayStart(): Date {
  const d = new Date();
  d.setHours(0, 0, 0, 0);
  return d;
}

/**
 * دریافت تاریخ امروز به صورت رشته شمسی (YYYY/MM/DD)
 */
export function todayJalali(): string {
  return formatJalaliDate(todayStart());
}

/**
 * محاسبه تعداد شب‌های بین دو تاریخ شمسی
 */
export function calcNights(from?: string, to?: string): number | null {
  if (!from || !to) return null;
  const f = parseJalaliDate(from);
  const t = parseJalaliDate(to);
  if (!f || !t) return null;
  const diff = differenceInCalendarDays(t, f);
  return diff > 0 ? diff : null;
}