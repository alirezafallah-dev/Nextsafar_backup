/**
 * SwapButton - جابجایی مبدا و مقصد
 */
import { ArrowLeftRight } from "lucide-react";

export function SwapButton({ onSwap }: { onSwap: () => void }) {
  return (
    <button
      type="button"
      onClick={onSwap}
      aria-label="جابجایی مبدا و مقصد"
      className="self-end mb-0.5 w-10 h-10 rounded-full border border-border bg-white flex items-center justify-center text-primary-600 hover:bg-primary-50 hover:rotate-180 transition-all cursor-pointer shrink-0"
    >
      <ArrowLeftRight className="w-4 h-4" />
    </button>
  );
}