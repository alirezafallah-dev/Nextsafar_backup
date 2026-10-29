import { NextResponse } from "next/server";
import { buildApiUrl } from "@/lib/api/config";
import { WP_AUTH_BASE } from "@/lib/api/auth-config";
import { cookies } from "next/headers";

// ذخیره تعداد درخواست‌ها در حافظه (در production باید از Redis استفاده کرد)
const requestCounts = new Map<string, { count: number; resetTime: number }>();

const RATE_LIMIT_WINDOW = 60 * 1000; // 1 دقیقه
const MAX_REQUESTS = 3; // حداکثر 3 درخواست در دقیقه

export async function POST(req: Request) {
  try {
    const { phone } = await req.json();
    
    // دریافت IP یا identifier کاربر
    const cookieStore = await cookies();
    const sessionId = cookieStore.get("session_id")?.value || 
                      req.headers.get("x-forwarded-for") || 
                      "unknown";
    
    const now = Date.now();
    const record = requestCounts.get(sessionId);
    
    // بررسی rate limit
    if (record && record.resetTime > now) {
      if (record.count >= MAX_REQUESTS) {
        return NextResponse.json(
          { 
            ok: false, 
            error: "rate_limit",
            message: `حداکثر ${MAX_REQUESTS} درخواست در دقیقه مجاز است. ${Math.ceil((record.resetTime - now) / 1000)} ثانیه صبر کنید.`
          },
          { status: 429 }
        );
      }
      record.count++;
    } else {
      requestCounts.set(sessionId, { count: 1, resetTime: now + RATE_LIMIT_WINDOW });
    }
    
    // ارسال به backend
    const res = await fetch(buildApiUrl(`${WP_AUTH_BASE}/send-otp`), {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ phone }),
    });
    
    const data = await res.json();
    return NextResponse.json(data, { status: res.status });
  } catch (e) {
    console.error("[send-otp]", e);
    return NextResponse.json(
      { ok: false, error: "server_error" },
      { status: 500 }
    );
  }
}