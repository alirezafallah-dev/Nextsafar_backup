// src/components/shared/gallery/types.ts

export interface GalleryImage {
  src: string;
  alt?: string;
  caption?: string;
}

export interface ImageGalleryProps {
  images: (string | GalleryImage)[];
  title?: string;
  maxPreview?: number;
  desktopHeight?: number;
  mobileHeight?: number;
  rounded?: boolean;
  className?: string;
}

export interface GalleryModalProps {
  images: GalleryImage[];
  title?: string;
  isOpen: boolean;
  initialIndex: number;
  onClose: () => void;
}

export type GalleryViewMode = "grid" | "single";