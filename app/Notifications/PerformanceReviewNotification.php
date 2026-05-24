<?php

namespace App\Notifications;

use App\Models\PerformanceReview;
use Illuminate\Notifications\Notification;

class PerformanceReviewNotification extends Notification
{
    public function __construct(public PerformanceReview $review) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "You have received a performance review for {$this->review->period}.",
            'score'   => $this->review->score,
            'period'  => $this->review->period,
        ];
    }
}
