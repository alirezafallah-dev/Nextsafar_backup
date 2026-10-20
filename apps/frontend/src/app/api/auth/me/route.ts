import { NextResponse } from "next/server";
import { cookies } from "next/headers";
import { buildApiUrl } from "@/lib/api/config";
import { WP_AUTH_BASE, SESSION_COOKIE } from "@/lib/api/auth-config";

export async function GET() {
  try {
    const cookieStore = await cookies();
    const token = cookieStore.get(SESSION_COOKIE)?.value;
    
    if (!token) {
      return NextResponse.json(
        { ok: false, error: "no_session" },
        { status: 401 }
      );
    }
    
    const res = await fetch(buildApiUrl(`${WP_AUTH_BASE}/me`), {
      headers: { "x-ns-session": token },
      cache: "no-store",
    });
    
    if (!res.ok) {
      // اگر session منقضی شده، کوکی را پاک کن
      if (res.status === 401) {
        cookieStore.delete(SESSION_COOKIE);
      }
      return NextResponse.json(
        { ok: false, error: "invalid_session" },
        { status: 401 }
      );
    }
    
    const data = await res.json();
    return NextResponse.json(data, { status: 200 });
  } catch (e) {
    console.error("[/api/auth/me]", e);
    return NextResponse.json(
      { ok: false, error: "server_error" },
      { status: 500 }
    );
  }
}