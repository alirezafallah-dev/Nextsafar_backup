// src/components/shared/common/ModalCloseButton.tsx
"use client";

import { X } from "lucide-react";

interface ModalCloseButtonProps {
  onClick: () => void;
  className?: string;
  ariaLabel?: string;
}

/**
 * دکمه بستن استاندارد برای همه مودال‌های سایت
 * استایل مرجع: مودال امکانات هتل
 */
export default function ModalCloseButton({
  onClick,
  className = "",
  ariaLabel = "بستن",
}: ModalCloseButtonProps) {
  return (
    <button
      type="button"
      onClick={onClick}
      className={`w-9 h-9 rounded-md bg-white hover:bg-danger/10 hover:text-danger flex items-center justify-center transition border border-border cursor-pointer shrink-0 ${className}`}
      aria-label={ariaLabel}
    >
      <X className="w-4 h-4" />
    </button>
  );
}