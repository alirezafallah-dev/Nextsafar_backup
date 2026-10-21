<?php

namespace App\Modules\TripPlanner\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\TripPlanner\Services\TripPlannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TripPlannerController extends Controller
{
    public function __construct(private TripPlannerService $service) {}

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'destination' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'days' => 'required|integer|min:1|max:30',
            'budget_level' => 'required|in:economy,medium,luxury',
            'interests' => 'nullable|array',
            'interests.*' => 'string|max:50',
            'travelers' => 'required|integer|min:1|max:20',
            'start_date' => 'nullable|date|after_or_equal:today',
            'session_id' => 'nullable|string|max:64',
        ]);

        try {
            $userId = $request->user()?->id;
            
            // Fallback برای session: اول از input، بعد از session، در نهایت UUID تولید می‌کنیم
            $sessionId = $validated['session_id'] 
                ?? ($request->hasSession() ? $request->session()->getId() : null)
                ?? Str::uuid()->toString();
            
            $ip = $request->ip();

            $tripPlan = $this->service->generate($validated, $userId, $sessionId, $ip);

            return response()->json([
                'success' => true,
                'data' => $tripPlan->toArray(),
                'message' => 'برنامه سفر با موفقیت ایجاد شد',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TRIP_GENERATION_FAILED',
                    'message' => 'خطا در ایجاد برنامه سفر',
                    'technical' => config('app.debug') ? $e->getMessage() : null,
                ],
            ], 500);
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $sessionId = $request->input('session_id');
        $tripPlan = $this->service->get($id, $userId, $sessionId);

        if (!$tripPlan) {
            return response()->json([
                'success' => false, 
                'error' => ['message' => 'برنامه سفر یافت نشد']
            ], 404);
        }

        $tripPlan->incrementViewCount();

        return response()->json(['success' => true, 'data' => $tripPlan->toArray()]);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'available' => $this->service->hasActiveProviders(),
                'providers' => $this->service->testProviders(),
            ],
        ]);
    }
}
