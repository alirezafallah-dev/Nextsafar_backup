import type { Metadata } from 'next';
import './globals.css';

// ✅ همه default imports (بدون { })
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import AuthModal from '@/components/auth/AuthModal';

// ✅ احتمالاً named imports (با { }) — اگر خطا داد، این‌ها را هم default کنید
import { AuthProvider } from '@/lib/auth/AuthProvider';
import { AuthModalProvider } from '@/lib/auth/AuthModalProvider';
import { getMenus } from '@/lib/api/menu';
import { getSessionUser } from '@/lib/auth/session';

import type { MenuItem } from '@/types/menu';
import { Suspense } from 'react';

// ✅ Cache 1 ساعته
export const revalidate = 3600;

export const metadata: Metadata = {
  title: {
    default: 'سفر بعدی | رزرو هتل، تور، ویزا و بلیط',
    template: '%s | سفر بعدی ایرانیان',
  },
  description: 'بهترین قیمت هتل، تور، ویزا و بلیط پرواز در سفر بعدی ایرانیان.',
  keywords: ['هتل', 'تور', 'ویزا', 'بلیط پرواز', 'سفر', 'گردشگری'],
  openGraph: {
    type: 'website',
    locale: 'fa_IR',
    siteName: 'سفر بعدی ایرانیان',
  },
};

// ✅ منوهای fallback
const TEST_MAIN_MENU: MenuItem[] = [
  { id: 1, title: 'خانه', url: '/', children: [] },
  { id: 2, title: 'هتل', url: '/hotels', children: [] },
  { id: 3, title: 'ویزا', url: '/visas', children: [] },
  { id: 4, title: 'تور', url: '/tours', children: [] },
  { id: 5, title: 'پرواز', url: '/flights', children: [] },
];

const TEST_SEC_MENU: MenuItem[] = [
  { id: 101, title: 'مقاصد گردشگری', url: '/destinations', children: [] },
  { id: 102, title: 'راهنمای سفر', url: '/travel-guides', children: [] },
  { id: 103, title: 'اخبار گردشگری', url: '/travel-news', children: [] },
  { id: 104, title: 'هتل', url: '/hotels', children: [] },
];

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  // ✅ Parallel fetching با error handling
  const [mainMenuItems, secMenuItems, mobileMenuItems, user] = await Promise.all([
    getMenus('mainmenu').catch(() => [] as MenuItem[]),
    getMenus('secmenu').catch(() => [] as MenuItem[]),
    getMenus('mobilemenu').catch(() => [] as MenuItem[]),
    getSessionUser().catch(() => null),
  ]);

  const finalMainMenu = mainMenuItems.length > 0 ? mainMenuItems : TEST_MAIN_MENU;
  const finalSecMenu = secMenuItems.length > 0 ? secMenuItems : TEST_SEC_MENU;
  const finalMobileMenu = mobileMenuItems.length > 0 ? mobileMenuItems : finalMainMenu;

  return (
    <html lang="fa" dir="rtl" suppressHydrationWarning>
      <head>
        <link
          rel="preload"
          href="/fonts/IRANSansX-Regular.woff2"
          as="font"
          type="font/woff2"
          crossOrigin="anonymous"
        />
        <link
          rel="preload"
          href="/fonts/IRANSansX-Bold.woff2"
          as="font"
          type="font/woff2"
          crossOrigin="anonymous"
        />
      </head>
      <body className="antialiased bg-white text-text min-h-screen flex flex-col">
        <AuthProvider initial={user}>
          <AuthModalProvider>
            <Suspense fallback={null}>
              <Header
                mainMenuItems={finalMainMenu}
                secMenuItems={finalSecMenu}
                mobileMenuItems={finalMobileMenu}
              />
            </Suspense>
            <main className="flex-1">{children}</main>
            <Footer />
            <AuthModal />
          </AuthModalProvider>
        </AuthProvider>
      </body>
    </html>
  );
}