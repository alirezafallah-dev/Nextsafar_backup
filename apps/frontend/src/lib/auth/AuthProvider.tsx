"use client";
import {
  createContext,
  useContext,
  useState,
  useCallback,
  useEffect,
  useRef,
} from "react";
import type { AuthUser } from "./session";

interface AuthCtx {
  user: AuthUser | null;
  loading: boolean;
  refresh: () => Promise<void>;
  logout: () => Promise<void>;
}

const Ctx = createContext<AuthCtx | null>(null);

export function AuthProvider({
  initial,
  children,
}: {
  initial: AuthUser | null;
  children: React.ReactNode;
}) {
  const [user, setUser] = useState<AuthUser | null>(initial);
  const [loading, setLoading] = useState(false);
  
  // ✅ جلوگیری از درخواست تکراری در SSR
  const hasFetched = useRef(false);

  const refresh = useCallback(async () => {
    setLoading(true);
    try {
      const r = await fetch("/api/auth/me", { cache: "no-store" });
      if (!r.ok) {
        setUser(null);
        return;
      }
      const data = await r.json();
      setUser(data.ok ? data.user : null);
    } catch {
      setUser(null);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    // ✅ فقط یک‌بار در کلاینت چک کن، نه هر بار که user null شد
    if (!user && !hasFetched.current && !initial) {
      hasFetched.current = true;
      refresh();
    }
  }, [user, refresh, initial]);

  const logout = useCallback(async () => {
    await fetch("/api/auth/logout", { method: "POST" });
    setUser(null);
  }, []);

  return (
    <Ctx.Provider value={{ user, loading, refresh, logout }}>
      {children}
    </Ctx.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(Ctx);
  if (!ctx) throw new Error("useAuth must be used inside AuthProvider");
  return ctx;
}