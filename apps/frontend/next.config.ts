import type { NextConfig } from 'next';

const nextConfig: NextConfig = {
  // ═══════════════════════════════════════════════════════════
  // 1. Dev Origins — رفع خطای cross-origin (فقط hostname!)
  // ═══════════════════════════════════════════════════════════
  allowedDevOrigins: ['nextsafar.local', 'localhost', '127.0.0.1'],

  // ═══════════════════════════════════════════════════════════
  // 2. Image Optimization
  // ═══════════════════════════════════════════════════════════
  images: {
    remotePatterns: [
      { protocol: 'https', hostname: '**.nextsafar.com' },
      { protocol: 'https', hostname: 'images.unsplash.com' },
      { protocol: 'http', hostname: 'localhost' },
      { protocol: 'http', hostname: '127.0.0.1' },
      { protocol: 'http', hostname: 'nextsafar.local' },
      { protocol: 'https', hostname: 'nextsafar.local' },
    ],
    formats: ['image/avif', 'image/webp'],
    minimumCacheTTL: 60 * 60 * 24 * 30, // 30 روز
    dangerouslyAllowLocalIP: true,
    deviceSizes: [640, 750, 828, 1080, 1200, 1920, 2048, 3840],
    imageSizes: [16, 32, 48, 64, 96, 128, 256, 384],
    unoptimized: process.env.NODE_ENV === 'development',
  },

  // ═══════════════════════════════════════════════════════════
  // 3. Rewrites — پروکسی API وردپرس و لاراول
  // ═══════════════════════════════════════════════════════════
  async rewrites() {
    return [
      {
        source: '/api/wp/:path*',
        destination: `${process.env.NEXT_PUBLIC_WP_API_URL || 'http://nextsafar.local/wp-json'}/:path*`,
      },
      {
        source: '/wp-content/:path*',
        destination: `${process.env.NEXT_PUBLIC_WP_URL || 'http://nextsafar.local'}/wp-content/:path*`,
      },
    ];
  },

  // ═══════════════════════════════════════════════════════════
  // 4. Security Headers
  // ═══════════════════════════════════════════════════════════
  async headers() {
    return [
      {
        source: '/(.*)',
        headers: [
          { key: 'X-Frame-Options', value: 'DENY' },
          { key: 'X-Content-Type-Options', value: 'nosniff' },
          { key: 'Referrer-Policy', value: 'strict-origin-when-cross-origin' },
          {
            key: 'Permissions-Policy',
            value: 'camera=(), microphone=(), geolocation=(self)',
          },
        ],
      },
    ];
  },

  // ═══════════════════════════════════════════════════════════
  // 5. Experimental
  // ═══════════════════════════════════════════════════════════
  experimental: {
    serverActions: {
      bodySizeLimit: '2mb',
    },
    optimizePackageImports: [
      'lucide-react',
      'date-fns',
      'date-fns-jalali',
      '@base-ui/react',
    ],
  },

  // ═══════════════════════════════════════════════════════════
  // 6. Build Optimization
  // ═══════════════════════════════════════════════════════════
  compiler: {
    removeConsole:
      process.env.NODE_ENV === 'production'
        ? { exclude: ['error', 'warn'] }
        : false,
  },

  // ═══════════════════════════════════════════════════════════
  // 7. General
  // ═══════════════════════════════════════════════════════════
  poweredByHeader: false,
  reactStrictMode: true,
};

export default nextConfig;