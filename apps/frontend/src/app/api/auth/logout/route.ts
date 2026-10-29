import { NextResponse } from "next/server";
import { cookies } from "next/headers";
import { buildApiUrl } from "@/lib/api/config";
import { WP_AUTH_BASE, SESSION_COOKIE } from "@/lib/api/auth-config";

export async function POST(req: Request) {
  try {
    const cookieStore = await cookies();
    const token = cookieStore.get(SESSION_COOKIE)?.value;

    // ✅ بررسی Origin header برای جلوگیری از CSRF
    const origin = req.headers.get("origin");
    const expectedOrigin = process.env.NEXT_PUBLIC_FRONTEND_URL || "http://localhost:3000";
    
    if (origin && origin !== expectedOrigin) {
      console.warn("[logout] Invalid origin:", origin);
      return NextResponse.json(
        { ok: false, error: "invalid_origin" },
        { status: 403 }
      );
    }

    if (token) {
      try {
        await fetch(buildApiUrl(`${WP_AUTH_BASE}/logout`), {
          method: "POST",
          headers: { 
            "x-ns-session": token,
            "Content-Type": "application/json"
          },
        });
      } catch (e) {
        console.error("[logout] Backend logout failed:", e);
      }
    }

    // حذف کوکی
    cookieStore.delete({
      name: SESSION_COOKIE,
      path: "/",
    });

    return NextResponse.json({ ok: true });
  } catch (e) {
    console.error("[logout]", e);
    return NextResponse.json(
      { ok: false, error: "server_error" },
      { status: 500 }
    );
  }
}