<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Notifications\Notification;

class LeaveStatusNotification extends Notification
{
    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message'  => "Your leave request has been {$this->leave->status}.",
            'leave_id' => $this->leave->id,
            'status'   => $this->leave->status,
        ];
    }
}
