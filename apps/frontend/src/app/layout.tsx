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
  { id: 1, title: 'صفحه اصلی', url: '/', children: [] },
  { id: 2, title: 'پرواز', url: '/flights', children: [] },
  { id: 3, title: 'تورها', url: '/tours', children: [] },
  { id: 4, title: 'ویزا', url: '/visas', children: [] },
  { id: 5, title: 'درباره ما', url: '/about', children: [] },
  { id: 6, title: 'تماس با ما', url: '/contact', children: [] },
  { id: 7, title: 'قوانین', url: '/terms', children: [] },
];

const TEST_SEC_MENU: MenuItem[] = [
  { id: 8, title: 'هتل‌ها', url: '/hotels', children: [] },
  { id: 9, title: 'مقاصد گردشگری', url: '/travel-destination', children: [] },
  { id: 10, title: 'راهنمای سفر', url: '/travel-guides', children: [] },
  { id: 11, title: 'رستوران', url: '/restaurants', children: [] },
  { id: 12, title: 'اخبار گردشگری', url: '/travel-news', children: [] },
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