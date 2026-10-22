"use client";

import { useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, SlidersHorizontal } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import type { MobileFilterSheetProps } from "./types";

export default function MobileFilterSheet({
  open,
  onClose,
  title = "فیلترها",
  resultCount,
  resultLabel = "نمایش نتایج",
  children,
}: MobileFilterSheetProps) {
  useEffect(() => {
    if (open) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
    return () => {
      document.body.style.overflow = "";
    };
  }, [open]);

  return (
    <AnimatePresence>
      {open && (
        <>
          {/* Overlay */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="fixed inset-0 bg-black/50 backdrop-blur-sm z-[900]"
          />

          {/* Sheet */}
          <motion.div
            initial={{ y: "100%" }}
            animate={{ y: 0 }}
            exit={{ y: "100%" }}
            transition={{ type: "spring", damping: 30, stiffness: 300 }}
            className="fixed inset-x-0 bottom-0 z-[901] bg-bg-sec rounded-t-3xl max-h-[90vh] overflow-y-auto shadow-2xl"
          >
            {/* Header */}
            <div className="sticky top-0 bg-bg-sec border-b border-border flex items-center justify-between px-5 py-4 z-10">
              <div className="flex items-center gap-2">
                <SlidersHorizontal className="w-5 h-5 text-primary" />
                <span className="font-bold text-text-strong">{title}</span>
              </div>
              <button
                onClick={onClose}
                className="w-9 h-9 rounded-full bg-white border border-border flex items-center justify-center hover:bg-danger/5 transition cursor-pointer"
                aria-label="بستن"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Content */}
            <div className="p-4">{children}</div>

            {/* Footer */}
            <div className="sticky bottom-0 bg-white border-t border-border p-4">
              <button
                onClick={onClose}
                className="w-full py-3.5 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition cursor-pointer"
              >
                {resultCount !== undefined
                  ? `نمایش ${faDigits(resultCount)} ${resultLabel}`
                  : resultLabel}
              </button>
            </div>
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
}