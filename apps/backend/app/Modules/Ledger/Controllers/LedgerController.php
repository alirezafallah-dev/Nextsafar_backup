<?php

namespace App\Modules\Ledger\Controllers;

use App\Core\ApiResponse;
use App\Modules\Ledger\Models\LedgerAccount;
use App\Modules\Ledger\Models\LedgerEntry;
use App\Modules\Ledger\Services\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LedgerController extends Controller
{
    public function __construct(
        protected LedgerService $ledgerService,
    ) {}

    /**
     * GET /api/v1/admin/ledger/entries
     * لیست تراکنش‌ها با فیلتر
     */
    public function index(Request $request): JsonResponse
    {
        $query = LedgerEntry::with(['order:id,code', 'payment:id,code'])
            ->latest('transaction_date');

        if ($type = $request->get('type')) {
            $query->ofType($type);
        }

        if ($orderId = $request->get('order_id')) {
            $query->forOrder((int) $orderId);
        }

        if ($paymentId = $request->get('payment_id')) {
            $query->forPayment((int) $paymentId);
        }

        if ($accountCode = $request->get('account_code')) {
            $query->where(function ($q) use ($accountCode) {
                $q->where('account_debit', $accountCode)
                    ->orWhere('account_credit', $accountCode);
            });
        }

        if ($from = $request->get('date_from')) {
            $query->where('transaction_date', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->where('transaction_date', '<=', $to);
        }

        if ($request->get('not_reversed')) {
            $query->notReversed();
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%")
                    ->orWhere('reference_code', 'ilike', "%{$search}%");
            });
        }

        $entries = $query->paginate($request->get('per_page', 50));

        return ApiResponse::success($entries->items())
            ->withPagination($entries)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/ledger/entries/{entry}
     * جزئیات تراکنش
     */
    public function show(LedgerEntry $entry): JsonResponse
    {
        $entry->load(['order:id,code', 'payment:id,code', 'debitAccount', 'creditAccount']);
        return ApiResponse::success($entry)->toResponse(request());
    }

    /**
     * GET /api/v1/admin/ledger/batch/{batchId}
     * همه تراکنش‌های یک دسته
     */
    public function batch(string $batchId): JsonResponse
    {
        $entries = LedgerEntry::getBatch($batchId);
        return ApiResponse::success($entries)->toResponse(request());
    }

    /**
     * POST /api/v1/admin/ledger/entries
     * ایجاد تراکنش دستی (تعدیل)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_debit' => ['required', 'string', 'max:50'],
            'account_credit' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'integer', 'min:1'],
            'currency' => ['nullable', 'string', 'max:10'],
            'description' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'type' => ['nullable', 'in:' . implode(',', LedgerEntry::ALL_TYPES)],
            'reference_type' => ['nullable', 'string', 'max:50'],
            'reference_code' => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ]);

        $entry = $this->ledgerService->recordManualEntry($validated);

        return ApiResponse::created($entry, 'تراکنش دستی ثبت شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/ledger/entries/{entry}/reverse
     * برگشت تراکنش
     */
    public function reverse(Request $request, LedgerEntry $entry): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $reversal = $this->ledgerService->reverseEntry($entry, $validated['reason']);

        return ApiResponse::success($reversal, 'تراکنش برگشت داده شد.')
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/ledger/accounts
     * لیست حساب‌ها
     */
    public function accounts(): JsonResponse
    {
        $accounts = LedgerAccount::active()
            ->with('parent:id,code,name')
            ->orderBy('code')
            ->get();

        return ApiResponse::success($accounts)->toResponse(request());
    }

    /**
     * GET /api/v1/admin/ledger/accounts/{code}/statement
     * گردش حساب
     */
    public function accountStatement(Request $request, string $code): JsonResponse
    {
        $statement = $this->ledgerService->getAccountStatement(
            $code,
            $request->get('date_from'),
            $request->get('date_to')
        );

        return ApiResponse::success($statement)->toResponse($request);
    }

    /**
     * GET /api/v1/admin/ledger/reports/trial-balance
     * تراز آزمایشی
     */
    public function trialBalance(Request $request): JsonResponse
    {
        $report = $this->ledgerService->getTrialBalance(
            $request->get('date_from'),
            $request->get('date_to')
        );

        return ApiResponse::success($report)->toResponse($request);
    }

    /**
     * GET /api/v1/admin/ledger/reports/income-statement
     * صورت سود و زیان
     */
    public function incomeStatement(Request $request): JsonResponse
    {
        $report = $this->ledgerService->getIncomeStatement(
            $request->get('date_from'),
            $request->get('date_to')
        );

        return ApiResponse::success($report)->toResponse($request);
    }

    /**
     * GET /api/v1/admin/ledger/reports/balance-sheet
     * صورت وضعیت مالی
     */
    public function balanceSheet(Request $request): JsonResponse
    {
        $report = $this->ledgerService->getBalanceSheet($request->get('as_of_date'));
        return ApiResponse::success($report)->toResponse($request);
    }
}
