// src/components/shared/cards/utils.tsx

import { LucideIcon } from 'lucide-react';
import type { BadgeColor, AccentColor } from './types';

// ═══ رنگ‌بندی badge ═══
export const getBadgeStyles = (color: BadgeColor = 'primary'): string => {
  const styles: Record<BadgeColor, string> = {
    primary: 'bg-primary text-white',
    tour: 'bg-tour text-white',
    visa: 'bg-visa text-white',
    warning: 'bg-warning text-white',
    dark: 'bg-black/50 backdrop-blur-sm text-white',
    light: 'bg-white/95 backdrop-blur-sm text-text-strong',
    gold: 'bg-gradient-to-l from-amber-300 to-yellow-500 text-amber-950',
    silver: 'bg-gradient-to-l from-slate-200 to-slate-400 text-slate-900',
    bronze: 'bg-gradient-to-l from-orange-300 to-amber-600 text-orange-950',
  };
  return styles[color];
};

// ═══ رنگ accent (برای border و hover) ═══
export const getAccentStyles = (color: AccentColor = 'primary'): {
  border: string;
  hoverBorder: string;
  hoverBg: string;
  text: string;
  bg: string;
  bgLight: string;
} => {
  const styles: Record<AccentColor, any> = {
    primary: {
      border: 'border-primary-light',
      hoverBorder: 'hover:border-primary-light',
      hoverBg: 'group-hover:bg-primary',
      text: 'text-primary',
      bg: 'bg-primary',
      bgLight: 'bg-primary-lightest',
    },
    tour: {
      border: 'border-tour',
      hoverBorder: 'hover:border-tour',
      hoverBg: 'group-hover:bg-tour',
      text: 'text-tour',
      bg: 'bg-tour',
      bgLight: 'bg-tour/10',
    },
    visa: {
      border: 'border-visa',
      hoverBorder: 'hover:border-visa',
      hoverBg: 'group-hover:bg-visa',
      text: 'text-visa',
      bg: 'bg-visa',
      bgLight: 'bg-visa/10',
    },
    success: {
      border: 'border-success',
      hoverBorder: 'hover:border-success',
      hoverBg: 'group-hover:bg-success',
      text: 'text-success',
      bg: 'bg-success',
      bgLight: 'bg-success/10',
    },
    warning: {
      border: 'border-warning',
      hoverBorder: 'hover:border-warning',
      hoverBg: 'group-hover:bg-warning',
      text: 'text-warning',
      bg: 'bg-warning',
      bgLight: 'bg-warning/10',
    },
    neutral: {
      border: 'border-border',
      hoverBorder: 'hover:border-border',
      hoverBg: 'group-hover:bg-text-muted',
      text: 'text-text-muted',
      bg: 'bg-text-muted',
      bgLight: 'bg-bg-sec',
    },
  };
  return styles[color];
};

// ═══ Overlay گرادیان ═══
export const getOverlayStyles = (intensity: 'light' | 'medium' | 'heavy' = 'medium'): string => {
  const styles = {
    light: 'from-black/40 via-transparent to-transparent',
    medium: 'from-black/70 via-black/20 to-transparent',
    heavy: 'from-black/85 via-black/40 to-transparent',
  };
  return `bg-gradient-to-t ${styles[intensity]}`;
};

// ═══ Badge component ═══
interface BadgeProps {
  text: string;
  icon?: LucideIcon;
  color?: BadgeColor;
  className?: string;
}

export const Badge = ({ text, icon: Icon, color = 'primary', className = '' }: BadgeProps) => (
  <span
    className={`inline-flex items-center gap-1 px-2 py-0.5 md:px-2.5 md:py-1 rounded-lg text-[9px] md:text-[10px] font-bold shadow-md ${getBadgeStyles(color)} ${className}`}
  >
    {Icon && <Icon className="w-3 h-3" />}
    {text}
  </span>
);

// ═══ Meta item ═══
interface MetaItemProps {
  icon?: LucideIcon;
  text: string;
  className?: string;
  accentColor?: AccentColor;
}

export const MetaItem = ({ icon: Icon, text, className = '', accentColor = 'primary' }: MetaItemProps) => {
  const accent = getAccentStyles(accentColor);
  return (
    <span className={`inline-flex items-center gap-1 text-[10px] md:text-[11px] ${className}`}>
      {Icon && <Icon className={`w-3 h-3 ${accent.text} shrink-0`} />}
      <span className="truncate">{text}</span>
    </span>
  );
};