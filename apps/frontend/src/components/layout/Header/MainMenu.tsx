"use client";

import { memo } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { Sparkles } from "lucide-react";
import { MenuItem } from "@/types/menu";

interface MainMenuProps {
  items: MenuItem[];
  variant?: "primary" | "secondary";
  onAiTripClick?: () => void;
}

function MainMenuComponent({ items, variant = "primary", onAiTripClick }: MainMenuProps) {
  const pathname = usePathname();

  const isActive = (url: string) => pathname === url || pathname?.startsWith(url + "/");

  return (
    <nav className="flex items-center gap-1">
      {items.map((item) => (
        <Link
          key={item.id}
          href={item.url}
          className={`px-4 py-2 rounded-md text-sm font-medium transition-colors whitespace-nowrap ${
            isActive(item.url)
              ? "text-primary-dark"
              : "text-text-muted hover:text-primary-dark hover:bg-primary-lightest/50"
          }`}
        >
          {item.title}
        </Link>
      ))}

      {/* تب سفر هوشمند */}
      <button
        type="button"
        onClick={onAiTripClick}
        className={`flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-medium transition-colors whitespace-nowrap ${
          pathname === "/ai-trip"
            ? "text-primary-dark"
            : "text-text-muted hover:text-primary-dark hover:bg-primary-lightest/50"
        }`}
      >
        <Sparkles className="w-4 h-4" />
        <span>سفر هوشمند</span>
      </button>
    </nav>
  );
}

// ✅ Memoize برای جلوگیری از re-render غیرضروری
export default memo(MainMenuComponent, (prevProps, nextProps) => {
  // فقط اگر items تغییر کرده باشد re-render شود
  return (
    prevProps.items === nextProps.items &&
    prevProps.variant === nextProps.variant
  );
});