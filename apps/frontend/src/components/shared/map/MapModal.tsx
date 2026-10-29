"use client";
import { useEffect } from "react";
import { createPortal } from "react-dom";
import ListMapSplitView from "./ListMapSplitView";
import type { ListingMapPoint } from "./types";

interface MapModalProps {
  isOpen: boolean;
  onClose: () => void;
  points: ListingMapPoint[];
  onPointClick?: (key: string) => void;
  title?: string;
  itemLabel?: string;
}

export default function MapModal({
  isOpen,
  onClose,
  points,
  onPointClick,
  title = "نقشه",
  itemLabel = "مورد",
}: MapModalProps) {
  // قفل اسکرول بدنه
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = "hidden";
      return () => {
        document.body.style.overflow = "unset";
      };
    }
  }, [isOpen]);

  if (!isOpen || typeof document === "undefined") return null;

  return createPortal(
    <div className="fixed inset-0 z-[9999] bg-white animate-in fade-in duration-200">
      <ListMapSplitView
        items={points}
        title={title}
        itemLabel={itemLabel}
        showHeader={true}
        onClose={onClose}
        onItemClick={(place) => onPointClick?.(place.key)}
        searchable={true}
      />
    </div>,
    document.body
  );
}