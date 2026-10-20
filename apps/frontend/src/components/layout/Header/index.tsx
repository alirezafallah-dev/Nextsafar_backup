"use client";

import { useState, useEffect } from "react";
import { usePathname } from "next/navigation";
import Logo from "./Logo";
import MainMenu from "./MainMenu";
import HeaderSearch from "./HeaderSearch";
import UserMenu from "./UserMenu";
import MobileMenu from "./MobileMenu";
import dynamic from "next/dynamic";
import type { MenuItem } from "@/types/menu";

const AssistantDrawer = dynamic(
  () => import("@/components/assistant/AssistantDrawer"),
  { ssr: false, loading: () => null }
);

interface HeaderProps {
  mainMenuItems: MenuItem[];
  secMenuItems: MenuItem[];
  mobileMenuItems?: MenuItem[];
}

export default function Header({
  mainMenuItems,
  secMenuItems,
  mobileMenuItems = [],
}: HeaderProps) {
  const pathname = usePathname();
  const [scrolled, setScrolled] = useState(false);
  const [isMobile, setIsMobile] = useState(false);
  const [showAssistant, setShowAssistant] = useState(false);

  const isHome = pathname === "/";

  // ✅ تشخیص موبایل با CSS media query (بدون state)
  useEffect(() => {
    const mq = window.matchMedia("(max-width: 1023px)");
    setIsMobile(mq.matches);

    const update = () => setIsMobile(mq.matches);
    mq.addEventListener("change", update);
    return () => mq.removeEventListener("change", update);
  }, []);

  // ✅ اسکرول
  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 260);
    handleScroll();
    window.addEventListener("scroll", handleScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const showSecondRow =
    secMenuItems.length > 0 && !isMobile && (!isHome || scrolled);

  const handleAiTripClick = () => setShowAssistant(true);
  const handleCloseModal = () => setShowAssistant(false);

  return (
    <>
      <header className="sticky top-3 z-50">
        {/* ✅ ns-container با padding ثابت از ابتدا */}
        <div className="ns-container">
          {/* سطر اول */}
          <div
            className={`border border-border bg-white transition-all duration-300 px-4 ${
              showSecondRow ? "rounded-t-lg rounded-b-none" : "rounded-lg"
            }`}
          >
            <div className="flex items-center justify-between h-16 lg:h-[72px] gap-3 lg:gap-4">
              {/* راست: لوگو */}
              <div className="flex-shrink-0">
                <Logo size={scrolled ? "sm" : "md"} />
              </div>

              {/* وسط: منوی اصلی (فقط دسکتاپ) */}
              <div
                className={`hidden lg:flex flex-1 min-w-0 transition-all duration-300 ${
                  scrolled ? "justify-start" : "justify-center"
                }`}
              >
                <MainMenu
                  items={mainMenuItems}
                  variant="primary"
                  onAiTripClick={handleAiTripClick}
                />
              </div>

              {/* چپ: جستجو + کاربر + منوی موبایل */}
              <div className="flex items-center gap-2 shrink-0">
                <HeaderSearch desktopBoxVisible={scrolled} />
                <UserMenu />
                <MobileMenu
                  items={
                    mobileMenuItems.length > 0 ? mobileMenuItems : mainMenuItems
                  }
                  onAiTripClick={handleAiTripClick}
                />
              </div>
            </div>
          </div>

          {/* سطر دوم: منوی دسته‌ها */}
          {showSecondRow && (
            <div className="border border-t-0 border-border bg-surface rounded-b-lg">
              <nav className="flex items-center gap-1 py-2 overflow-x-auto scrollbar-hide px-2">
                {secMenuItems.map((item) => (
                  <a
                    key={item.id}
                    href={item.url}
                    className="px-3 py-1.5 rounded-md text-sm font-medium text-text-muted hover:text-primary-dark hover:bg-primary-lightest transition-colors whitespace-nowrap"
                  >
                    {item.title}
                  </a>
                ))}
              </nav>
            </div>
          )}
        </div>
      </header>

      {showAssistant && <AssistantDrawer onClose={handleCloseModal} />}
    </>
  );
}