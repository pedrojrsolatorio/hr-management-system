<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRequestNotification extends Notification
{
    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message'       => "{$this->leave->employee->user->name} submitted a leave request.",
            'leave_id'      => $this->leave->id,
            'employee_name' => $this->leave->employee->user->name,
            'days'          => $this->leave->total_days,
            'status'        => 'pending',
        ];
    }
}
