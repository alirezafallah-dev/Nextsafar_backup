// src/components/shared/cards/types.ts

export type CardVariant = 
  | 'story'        // عمودی بلند - مقاصد
  | 'hotel'        // عمودی استاندارد - هتل
  | 'tour'         // با گرادیان - تور
  | 'visa'         // افقی - ویزا
  | 'collection'   // با rank badge - شهر
  | 'article';     // مقاله

export type AccentColor = 'primary' | 'tour' | 'visa' | 'success' | 'warning' | 'neutral';
export type BadgeColor = 'primary' | 'tour' | 'visa' | 'warning' | 'dark' | 'light' | 'gold' | 'silver' | 'bronze';
export type HoverEffect = 'scale' | 'lift' | 'shadow' | 'border' | 'ring';

export interface CardMeta {
  icon?: React.ComponentType<{ className?: string }>;
  text: string;
  className?: string;
}

export interface CardBadge {
  text: string;
  icon?: React.ComponentType<{ className?: string }>;
  color?: BadgeColor;
  position?: 'top-start' | 'top-end' | 'bottom-start' | 'bottom-end';
}

export interface CardProps {
  title: string;
  subtitle?: string;
  image: string | null;
  href: string;
  className?: string;
  children?: React.ReactNode;
  onClick?: () => void;
}