"use client";
import { useState, useEffect } from "react";
import Link from "next/link";
import Image from "next/image";
import {
  Menu,
  X,
  Sparkles,
  Home,
  Plane,
  PlaneTakeoff,
  BedDouble,
  Backpack,
  Stamp,
  MapPin,
  UtensilsCrossed,
  Newspaper,
  BookOpen,
  Stethoscope,
  ChevronDown,
  User,
  Phone,
  Mail,
  Compass,
  LogOut,
  UserCircle,
  CreditCard,
  Settings,
  ArrowLeft,
  Headphones,
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { MenuItem } from "@/types/menu";
import { useAuth } from "@/lib/auth/AuthProvider";
import { useAuthModal } from "@/lib/auth/AuthModalProvider";
import UserAvatar from "@/components/ui/UserAvatar";
import Logo from "./Logo";

interface MobileMenuProps {
  items: MenuItem[];
  onAiTripClick?: () => void;
}

/* ═══════════════════════════════════════════════════════
   آیکون‌های شبکه‌های اجتماعی (SVG استاندارد)
═══════════════════════════════════════════════════════ */
function InstagramIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
    </svg>
  );
}

function XIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
    </svg>
  );
}

function LinkedInIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
    </svg>
  );
}

/* ═══════════════════════════════════════════════════════
   نگاشت آیکون: اول عنوان فارسی، بعد URL
═══════════════════════════════════════════════════════ */
function iconFor(url: string, title: string) {
  const t = (title || "").trim();
  const u = (url || "").toLowerCase();

  if (t.includes("فرودگاه")) return PlaneTakeoff;
  if (t.includes("بیمارستان")) return Stethoscope;
  if (t.includes("رستوران")) return UtensilsCrossed;
  if (t.includes("راهنما")) return BookOpen;
  if (t.includes("اخبار") || t.includes("خبر")) return Newspaper;
  if (t.includes("مقصد")) return MapPin;
  if (t.includes("ویزا")) return Stamp;
  if (t.includes("هتل")) return BedDouble;
  if (t.includes("پرواز")) return Plane;
  if (t.includes("تور")) return Backpack;

  if (u.startsWith("/airports")) return PlaneTakeoff;
  if (u.startsWith("/hospitals")) return Stethoscope;
  if (u.startsWith("/restaurants")) return UtensilsCrossed;
  if (u.startsWith("/travel-guides") || u.startsWith("/travelguide"))
    return BookOpen;
  if (u.startsWith("/travel-news") || u.startsWith("/news")) return Newspaper;
  if (u.startsWith("/destinations")) return MapPin;
  if (u.startsWith("/visa")) return Stamp;
  if (u.startsWith("/hotels")) return BedDouble;
  if (u.startsWith("/flights")) return Plane;
  if (u.startsWith("/tours")) return Backpack;
  if (u === "/" || u === "") return Home;

  return Compass;
}

const FALLBACK: MenuItem[] = [
  { id: 1, title: "صفحه اصلی", url: "/", children: [] },
  { id: 2, title: "پروازها", url: "/flights", children: [] },
  { id: 3, title: "هتل‌ها", url: "/hotels", children: [] },
  { id: 4, title: "تورها", url: "/tours", children: [] },
  { id: 5, title: "ویزا", url: "/visas", children: [] },
  { id: 6, title: "مقاصد گردشگری", url: "/destinations", children: [] },
  { id: 7, title: "راهنمای سفر", url: "/travel-guides", children: [] },
  { id: 8, title: "رستوران‌ها", url: "/restaurants", children: [] },
  { id: 9, title: "بیمارستان‌ها", url: "/hospitals", children: [] },
  { id: 10, title: "اخبار گردشگری", url: "/travel-news", children: [] },
  { id: 11, title: "فرودگاه‌ها", url: "/airports", children: [] },
];

/* ═══ ردیف منو با زیرمنوی آکاردئونی ═══ */
function MenuRow({
  item,
  onNavigate,
  index,
}: {
  item: MenuItem;
  onNavigate: () => void;
  index: number;
}) {
  const [open, setOpen] = useState(false);
  const Icon = iconFor(item.url, item.title);
  const kids = item.children ?? [];
  const hasKids = kids.length > 0;

  // badge برای آیتم‌های جدید
  const isNew =
    item.title.includes("ویزا") ||
    item.title.includes("بیمارستان") ||
    item.url.includes("visa");

  return (
    <motion.div
      initial={{ opacity: 0, x: -20 }}
      animate={{ opacity: 1, x: 0 }}
      transition={{ delay: index * 0.04, duration: 0.3 }}
    >
      <div className="flex items-center gap-1">
        <Link
          href={item.url}
          onClick={onNavigate}
          className="flex flex-1 min-w-0 items-center gap-3 px-3 py-3 rounded-xl hover:bg-white hover:shadow-sm transition-all group"
        >
          <span className="w-10 h-10 rounded-xl bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/20 text-primary flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:shadow-md group-hover:from-primary/20 group-hover:to-primary/10 transition-all">
            <Icon className="w-5 h-5" />
          </span>
          <span className="flex-1 text-sm font-semibold text-text-strong group-hover:text-primary transition-colors truncate">
            {item.title}
          </span>
          {isNew && (
            <span className="px-2 py-0.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-[9px] font-bold shadow-sm">
              جدید
            </span>
          )}
        </Link>
        {hasKids && (
          <button
            type="button"
            onClick={() => setOpen((o) => !o)}
            aria-expanded={open}
            aria-label={`زیرمنوی ${item.title}`}
            className="w-10 h-10 rounded-xl flex items-center justify-center text-text-muted hover:bg-white hover:text-primary transition shrink-0 cursor-pointer"
          >
            <ChevronDown
              className={`w-4 h-4 transition-transform duration-300 ${
                open ? "rotate-180" : ""
              }`}
            />
          </button>
        )}
      </div>

      <AnimatePresence initial={false}>
        {hasKids && open && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: "auto", opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            transition={{ duration: 0.25, ease: "easeInOut" }}
            className="overflow-hidden"
          >
            <ul className="ms-6 ps-4 border-s-2 border-primary/25 space-y-0.5 py-2 mb-2">
              {kids.map((c, i) => (
                <motion.li
                  key={c.id}
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: i * 0.03 }}
                >
                  <Link
                    href={c.url}
                    onClick={onNavigate}
                    className="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] text-text-muted hover:bg-white hover:text-primary-dark transition"
                  >
                    <span className="w-1.5 h-1.5 rounded-full bg-primary/50 shrink-0" />
                    <span className="truncate">{c.title}</span>
                  </Link>
                </motion.li>
              ))}
            </ul>
          </motion.div>
        )}
      </AnimatePresence>
    </motion.div>
  );
}

export default function MobileMenu({ items, onAiTripClick }: MobileMenuProps) {
  const [open, setOpen] = useState(false);
  const { user, logout } = useAuth();
  const { open: openModal } = useAuthModal();

  const menuItems = items.length > 0 ? items : FALLBACK;
  const handleClose = () => setOpen(false);
  const handleAiTripClick = () => {
    handleClose();
    onAiTripClick?.();
  };

  /* قفل اسکرول */
  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [open]);

  /* ESC */
  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => e.key === "Escape" && setOpen(false);
    document.addEventListener("keydown", onKey);
    return () => document.removeEventListener("keydown", onKey);
  }, [open]);

  /* Back button مرورگر */
  useEffect(() => {
    if (!open) return;
    window.history.pushState({ menuOpen: true }, "");
    const onPop = () => setOpen(false);
    window.addEventListener("popstate", onPop);
    return () => window.removeEventListener("popstate", onPop);
  }, [open]);

  // دسته‌بندی منو: اولین ۵ مورد = خدمات، بقیه = راهنما
  const serviceItems = menuItems.slice(0, 5);
  const guideItems = menuItems.slice(5);

  return (
    <>
      {/* ═══ دکمه همبرگر — فقط موبایل ═══ */}
      <button
        type="button"
        onClick={() => setOpen(true)}
        className="md:hidden flex items-center justify-center w-10 h-10 rounded-lg text-text-strong hover:bg-primary-lightest/50 transition cursor-pointer"
        aria-label="منو"
        aria-expanded={open}
      >
        <Menu className="w-5 h-5" />
      </button>

      {/* ═══ کشوی تمام‌عرض ═══ */}
      <AnimatePresence>
        {open && (
          <>
            {/* Overlay */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.25 }}
              onClick={handleClose}
              className="fixed inset-0 z-[600] bg-black/60 backdrop-blur-sm"
            />

            {/* Drawer */}
              <motion.div
                initial={{ x: "100%" }}
                animate={{ x: 0 }}
                exit={{ x: "100%" }}
                transition={{ duration: 0.32, ease: [0.22, 1, 0.36, 1] }}
                role="dialog"
                aria-modal="true"
                aria-label="منوی اصلی"
                className="fixed inset-y-0 start-0 z-[601] w-full max-w-sm bg-white shadow-2xl overflow-y-auto"
              >
              {/* ═══ هدر کشو با لوگو ═══ */}
              <div className="sticky top-0 bg-white/95 backdrop-blur-md border-b border-border/50 z-10">
                <div className="flex items-center justify-between px-4 h-16">
                  <Link
                    href="/"
                    onClick={handleClose}
                    className="flex items-center gap-2.5 group"
                  >
                  <div className="flex-shrink-0">
                    <Logo/>
                  </div>
                    <div>
                      <div className="font-extrabold text-sm text-text-strong leading-tight mb-1">
                        سفر بعدی ایرانیان
                      </div>
                      <div className="text-[10px] text-text-muted">
                        Nextsafr Iranian
                      </div>
                    </div>
                  </Link>
                  <button
                    type="button"
                    onClick={handleClose}
                    className="flex items-center justify-center w-10 h-10 rounded-lg text-text-strong hover:bg-bg-sec transition cursor-pointer"
                    aria-label="بستن"
                  >
                    <X className="w-5 h-5" />
                  </button>
                </div>
              </div>

              <div className="p-4 pb-8 space-y-4">
                {/* ═══ وضعیت کاربر ═══ */}
                {user ? (
                  <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.1 }}
                    className="rounded-2xl bg-gradient-to-br from-primary via-primary-dark to-primary overflow-hidden shadow-xl relative"
                  >
                    {/* پترن تزئینی */}
                    <div className="absolute top-0 end-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16" />
                    <div className="absolute bottom-0 start-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12" />

                    <div className="relative p-4">
                      <div className="flex items-center gap-3 mb-4">
                        <UserAvatar
                          src={user.avatar}
                          className="w-14 h-14 ring-4 ring-white/30 shadow-lg"
                        />
                        <div className="min-w-0 flex-1">
                          <div className="font-bold text-base text-white truncate">
                            {user.display_name}
                          </div>
                          {user.phone && (
                            <div
                              className="text-xs text-white/80 truncate"
                              dir="ltr"
                            >
                              {user.phone}
                            </div>
                          )}
                          {user.email && !user.phone && (
                            <div
                              className="text-xs text-white/80 truncate"
                              dir="ltr"
                            >
                              {user.email}
                            </div>
                          )}
                        </div>
                      </div>
                      <div className="grid grid-cols-3 gap-2 mb-3">
                        <Link
                          href="/account"
                          onClick={handleClose}
                          className="flex flex-col items-center gap-1 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-sm transition-all"
                        >
                          <UserCircle className="w-5 h-5 text-white" />
                          <span className="text-[11px] text-white font-medium">
                            داشبورد
                          </span>
                        </Link>
                        <Link
                          href="/account/bookings"
                          onClick={handleClose}
                          className="flex flex-col items-center gap-1 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-sm transition-all"
                        >
                          <CreditCard className="w-5 h-5 text-white" />
                          <span className="text-[11px] text-white font-medium">
                            رزروها
                          </span>
                        </Link>
                        <Link
                          href="/account/settings"
                          onClick={handleClose}
                          className="flex flex-col items-center gap-1 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-sm transition-all"
                        >
                          <Settings className="w-5 h-5 text-white" />
                          <span className="text-[11px] text-white font-medium">
                            تنظیمات
                          </span>
                        </Link>
                      </div>
                      <button
                        type="button"
                        onClick={async () => {
                          await logout();
                          handleClose();
                        }}
                        className="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-sm text-white font-medium text-sm transition cursor-pointer"
                      >
                        <LogOut className="w-4 h-4" />
                        خروج از حساب
                      </button>
                    </div>
                  </motion.div>
                ) : (
                  <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.1 }}
                  >
                    <button
                      type="button"
                      onClick={() => {
                        handleClose();
                        openModal("phone");
                      }}
                      className="w-full flex items-center justify-center gap-2 py-4 rounded-2xl bg-gradient-to-l from-primary to-primary-dark text-white font-bold text-sm hover:shadow-xl hover:shadow-primary/30 transition-all cursor-pointer group relative overflow-hidden"
                    >
                      <div className="absolute inset-0 bg-white/20 translate-x-full group-hover:translate-x-0 transition-transform duration-500" />
                      <User className="w-5 h-5 relative z-10" />
                      <span className="relative z-10">ورود / ثبت‌نام</span>
                      <ArrowLeft className="w-4 h-4 rtl:rotate-180 relative z-10 group-hover:-translate-x-1 transition-transform" />
                    </button>
                    <p className="text-center text-[11px] text-text-muted mt-2">
                      برای دسترسی به رزروها و پیشنهادهای ویژه وارد شوید
                    </p>
                  </motion.div>
                )}

                {/* ✨ سفر هوشمند */}
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.15 }}
                >
                  <button
                    type="button"
                    onClick={handleAiTripClick}
                    className="w-full flex items-center gap-3 px-4 py-4 rounded-2xl bg-gradient-to-br from-amber-50 via-orange-50 to-rose-50 border-2 border-amber-200 hover:shadow-xl hover:shadow-amber-500/20 hover:border-amber-300 transition-all cursor-pointer group relative overflow-hidden"
                  >
                    <div className="absolute inset-0 bg-gradient-to-l from-amber-200/0 via-amber-200/20 to-amber-200/0 translate-x-full group-hover:translate-x-0 transition-transform duration-700" />
                    <span className="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:rotate-12 transition-all relative z-10">
                      <Sparkles className="w-6 h-6" />
                    </span>
                    <span className="flex-1 text-start relative z-10">
                      <div className="text-sm font-bold text-amber-900 mb-0.5">
                        برنامه سفر با AI
                      </div>
                      <div className="text-[11px] text-amber-700 leading-4">
                        در کمتر از ۱ دقیقه برنامه سفر بساز
                      </div>
                    </span>
                    <span className="px-2.5 py-1 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[10px] font-bold shadow-md relative z-10 animate-pulse">
                      جدید
                    </span>
                  </button>
                </motion.div>

                {/* ═══ منوی خدمات ═══ */}
                <motion.nav
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  transition={{ delay: 0.2 }}
                  className="rounded-2xl bg-white/80 backdrop-blur-sm border border-border/50 p-2 shadow-sm"
                  aria-label="منوی خدمات"
                >
                  <div className="flex items-center gap-2 px-3 py-2 border-b border-border/50 mb-1">
                    <div className="w-1 h-4 bg-primary rounded-full" />
                    <h3 className="text-xs font-bold text-text-strong uppercase tracking-wide">
                      خدمات
                    </h3>
                  </div>
                  {serviceItems.map((item, idx) => (
                    <MenuRow
                      key={item.id}
                      item={item}
                      onNavigate={handleClose}
                      index={idx}
                    />
                  ))}
                </motion.nav>

                {/* ═══ راهنما و اطلاعات ═══ */}
                {guideItems.length > 0 && (
                  <motion.nav
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ delay: 0.25 }}
                    className="rounded-2xl bg-white/80 backdrop-blur-sm border border-border/50 p-2 shadow-sm"
                    aria-label="راهنما و اطلاعات"
                  >
                    <div className="flex items-center gap-2 px-3 py-2 border-b border-border/50 mb-1">
                      <div className="w-1 h-4 bg-secondary rounded-full" />
                      <h3 className="text-xs font-bold text-text-strong uppercase tracking-wide">
                        راهنما و اطلاعات
                      </h3>
                    </div>
                    {guideItems.map((item, idx) => (
                      <MenuRow
                        key={item.id}
                        item={item}
                        onNavigate={handleClose}
                        index={idx + serviceItems.length}
                      />
                    ))}
                  </motion.nav>
                )}

                {/* ═══ پشتیبانی ═══ */}
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.3 }}
                  className="rounded-2xl bg-gradient-to-br from-sky-50 via-blue-50 to-cyan-50 border border-sky-200 p-4"
                >
                  <div className="flex items-center gap-2 mb-3">
                    <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center shadow-md">
                      <Headphones className="w-4.5 h-4.5 text-white" />
                    </div>
                    <div>
                      <h3 className="text-sm font-bold text-sky-900">
                        پشتیبانی ۲۴/۷
                      </h3>
                      <p className="text-[10px] text-sky-700">
                        همیشه در کنار شما هستیم
                      </p>
                    </div>
                  </div>
                  <div className="space-y-2">
                    <a
                      href="tel:02182801754"
                      dir="ltr"
                      className="flex items-center justify-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/70 hover:bg-white text-sky-800 hover:text-sky-900 font-medium text-sm transition shadow-sm"
                    >
                      <Phone className="w-4 h-4 text-sky-600" />
                      <span className="font-bold">021-8280 1754</span>
                    </a>
                    <a
                      href="mailto:info@nextsafar.com"
                      className="flex items-center justify-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/70 hover:bg-white text-sky-800 hover:text-sky-900 font-medium text-sm transition shadow-sm"
                    >
                      <Mail className="w-4 h-4 text-sky-600" />
                      <span className="truncate">info@nextsafar.com</span>
                    </a>
                  </div>
                </motion.div>

                {/* ═══ شبکه‌های اجتماعی ═══ */}
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.35 }}
                  className="text-center"
                >
                  <p className="text-xs text-text-muted mb-3">
                    ما را در شبکه‌های اجتماعی دنبال کنید
                  </p>
                  <div className="flex items-center justify-center gap-3">
                    <a
                      href="https://instagram.com/nextsafar"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="w-11 h-11 rounded-full bg-gradient-to-br from-pink-500 via-rose-500 to-purple-600 text-white flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-pink-500/30 transition-all"
                      aria-label="اینستاگرام"
                    >
                      <InstagramIcon className="w-5 h-5" />
                    </a>
                    <a
                      href="https://twitter.com/nextsafar"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="w-11 h-11 rounded-full bg-gradient-to-br from-slate-800 to-black text-white flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-slate-500/30 transition-all"
                      aria-label="X (توییتر)"
                    >
                      <XIcon className="w-4 h-4" />
                    </a>
                    <a
                      href="https://linkedin.com/company/nextsafar"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="w-11 h-11 rounded-full bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-blue-500/30 transition-all"
                      aria-label="لینکدین"
                    >
                      <LinkedInIcon className="w-5 h-5" />
                    </a>
                  </div>
                </motion.div>

                {/* ═══ فوتر ═══ */}
                <motion.div
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  transition={{ delay: 0.4 }}
                  className="text-center pt-4 border-t border-border/50 space-y-1"
                >
                  <p className="text-[11px] text-text-muted">
                    نسخه ۱.۰.۰ • سفر بعدی ایرانیان
                  </p>
                  <p className="text-[10px] text-text-subtle">
                    © ۱۴۰۴ تمامی حقوق محفوظ است
                  </p>
                </motion.div>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </>
  );
}