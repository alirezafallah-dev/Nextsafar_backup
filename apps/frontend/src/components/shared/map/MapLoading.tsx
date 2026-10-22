"use client";

export default function MapLoading({ text = "در حال بارگذاری نقشه..." }: { text?: string }) {
  return (
    <div className="absolute inset-0 flex items-center justify-center bg-bg-sec rounded-xl">
      <div className="flex flex-col items-center gap-3">
        <div className="w-10 h-10 border-[3px] border-primary border-t-transparent rounded-full animate-spin" />
        <span className="text-sm text-text-muted font-medium">{text}</span>
      </div>
    </div>
  );
}