<?php

namespace App\Http\Controllers;

use App\Models\{PerformanceReview, Employee};
use App\Http\Requests\StorePerformanceReviewRequest;
use App\Notifications\PerformanceReviewNotification;
use Illuminate\Http\{RedirectResponse};
use Illuminate\View\View;

class PerformanceReviewController extends Controller
{
    public function index(): View
    {
        $reviews = PerformanceReview::with(['employee.user', 'reviewer'])
            ->latest('reviewed_at')
            ->paginate(20);

        return view('performance.index', compact('reviews'));
    }

    public function create(): View
    {
        $employees = Employee::with('user')->where('status', 'active')->get();

        return view('performance.create', compact('employees'));
    }

    public function store(StorePerformanceReviewRequest $request): RedirectResponse
    {
        $review = PerformanceReview::create([
            'employee_id'  => $request->employee_id,
            'reviewer_id'  => auth()->id(),
            'score'        => $request->score,
            'period'       => $request->period,
            'strengths'    => $request->strengths,
            'improvements' => $request->improvements,
            'comments'     => $request->comments,
            'reviewed_at'  => $request->reviewed_at ?? today(),
        ]);

        $review->employee->user->notify(new PerformanceReviewNotification($review));

        return redirect()->route('performance-reviews.index')
            ->with('success', 'Performance review submitted.');
    }

    public function show(PerformanceReview $performanceReview): View
    {
        $performanceReview->load(['employee.user', 'reviewer']);

        return view('performance.show', compact('performanceReview'));
    }
}
