<?php

namespace App\Services;

use App\Models\{Attendance, Department, Employee, LeaveRequest, LeaveType, Payroll, PerformanceReview, User};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ChatbotService
{
    // private Employee|null $employee;
    private ?Employee $employee;
    private User $user;
    private string $role;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->role = $this->resolveRole();
        $this->employee = $this->resolveEmployee();
    }

    // public entry point
    public function reply(string $message): array
    {
        $input = strtolower(trim($message));
        $intents = $this->getIntentsForRole();
        $matched = $this->matchIntent($input, $intents);

        if ($matched) {
            $response = ($matched['handler'])($input); // can be --$response = ($matched['handler'])();-- because ($input) is not yet used since it's not passed in handler function, so 
            return [
                'text' => $response['text'],
                'cards' => $response['cards'] ?? [],
                'buttons' => $response['buttons'] ?? [],
                'intent' => $matched['name'],
            ];
        }

        return [
            'text' => $this->fallback($input),
            'cards' => [],
            'buttons' => $this->suggestedTopics(),
            'intent' => 'fallback',
        ];
    }

    // Role detection
    private function resolveRole(): string
    {
        if ($this->user->hasRole('admin')) return 'admin';
        if ($this->user->hasRole('hr_manager')) return 'hr_manager';
        return 'employee';
    }

    private function resolveEmployee(): ?Employee
    {
        return Employee::where('user_id', $this->user->id)->with(['user', 'department', 'position'])->first();
    }

    public function getIntentsForRole(): array
    {
        $shared = $this->sharedIntents();

        return match ($this->role) {
            'admin' => array_merge($this->adminIntents(), $shared),
            'hr_manager' => array_merge($this->hrManagerIntents(),  $shared),
            default => array_merge($this->employeeIntents(),   $shared)
        };
    }

    // Intent matcher (word-boundary safe)
    private function matchIntent(string $input, array $intents): ?array
    {
        foreach ($intents as $intent) {
            foreach ($intent['patterns'] as $pattern) {
                // Use word-boundary regex so 'hi' does not match inside 'history'
                // and 'hey' does not match inside 'they'
                $escaped = preg_quote($pattern, '/');
                if (preg_match('/(?<![a-z])' . $escaped . '(?![a-z])/i', $input)) {
                    return $intent;
                }
            }
        }
        return null;
    }

    // Shared intents (all roles)
    public function sharedIntents(): array
    {
        return [
            // Patterns are exact greetings only — no short strings like 'hi' that would match inside 'history', 'this', 'white', etc.
            [
                'name'     => 'greeting',
                'patterns' => [
                    'hello',
                    'good morning',
                    'good afternoon',
                    'good evening',
                    'howdy',
                    'greetings',
                    'start',
                    'hi there',
                    'hey there',
                ],
                'handler' => fn($i) => $this->handleGreeting(), // can be --'handler' => fn() => $this->handleGreeting(),-- since it's not passing $input
            ],
            [
                'name'     => 'help',
                'patterns' => [
                    'help',
                    'what can you do',
                    'commands',
                    'topics',
                    'options',
                    'menu',
                    'capabilities',
                ],
                'handler' => fn($i) => $this->handleHelp(),
            ],
            [
                'name'     => 'faq_leave_policy',
                'patterns' => [
                    'leave policy',
                    'annual leave policy',
                    'sick leave policy',
                    'how does leave work',
                    'leave rules',
                    'leave entitlement',
                    'leave per year',
                    'view leave policy',
                ],
                'handler' => fn($i) => $this->handleFaqLeavePolicy(),
            ],
            [
                'name'     => 'faq_working_hours',
                'patterns' => [
                    'working hours',
                    'office hours',
                    'work hours',
                    'what time start',
                    'what time end',
                    'office time',
                    'work schedule',
                    'when do we start',
                    'when do we end',
                ],
                'handler' => fn($i) => $this->handleFaqWorkingHours(),
            ],
            [
                'name'     => 'faq_late_policy',
                'patterns' => [
                    'late policy',
                    'what happens if late',
                    'late to work',
                    'tardiness',
                    'late penalty',
                    'late mark',
                    'late deduction',
                    'late threshold',
                ],
                'handler' => fn($i) => $this->handleFaqLatePolicy(),
            ],
            [
                'name'     => 'faq_absent_policy',
                'patterns' => [
                    'absent policy',
                    'what if absent',
                    'no show',
                    'absence policy',
                    'unexcused absence',
                    'absent without leave',
                    'awol',
                ],
                'handler' => fn($i) => $this->handleFaqAbsentPolicy(),
            ],
            [
                'name'     => 'faq_contact_hr',
                'patterns' => [
                    'contact hr',
                    'hr contact',
                    'hr email',
                    'reach hr',
                    'talk to hr',
                    'hr number',
                    'hr phone',
                    'hr office',
                    'who is hr',
                    'hr manager contact',
                ],
                'handler' => fn($i) => $this->handleFaqContactHr(),
            ],
            [
                'name'     => 'departments',
                'patterns' => [
                    'departments',
                    'list departments',
                    'all departments',
                    'what departments',
                    'company departments',
                ],
                'handler' => fn($i) => $this->handleDepartments(),
            ],
            [
                'name'     => 'thanks',
                'patterns' => [
                    'thanks',
                    'thank you',
                    'thank you so much',
                    'appreciate it',
                    'cheers',
                ],
                'handler' => fn($i) => $this->handleThanks(),
            ],
            // Kept last so 'bye' does not shadow other patterns
            [
                'name'     => 'goodbye',
                'patterns' => [
                    'goodbye',
                    'see you',
                    'take care',
                    'thanks bye',
                    'thank you bye',
                    'bye bye',
                ],
                'handler' => fn($i) => $this->handleGoodbye(),
            ]
        ];
    }

    // Employee intents
    private function employeeIntents(): array
    {
        return [
            [
                'name'     => 'leave_history',
                'patterns' => [
                    'leave history',
                    'view leave history',
                    'past leave',
                    'previous leave',
                    'leave request',
                    'my leave requests',
                    'leave status',
                    'leave approved',
                    'leave rejected',
                ],
                'handler' => fn($i) => $this->handleLeaveHistory(),
            ],
            [
                'name'     => 'leave_balance',
                'patterns' => [
                    'leave balance',
                    'my leave',
                    'remaining leave',
                    'how many leave',
                    'leave days',
                    'days left',
                    'leave remaining',
                    'available leave',
                    'check leave',
                    'my leaves',
                ],
                'handler' => fn($i) => $this->handleLeaveBalance(),
            ],
            [
                'name'     => 'attendance_today',
                'patterns' => [
                    'attendance today',
                    'my attendance today',
                    'check in today',
                    'checked in',
                    'did i check in',
                    'today attendance',
                    'what time did i',
                    'check-in time',
                    'checkout time',
                ],
                'handler' => fn($i) => $this->handleAttendanceToday(),
            ],
            [
                'name'     => 'attendance_summary',
                'patterns' => [
                    'attendance this month',
                    'this month attendance',
                    'monthly attendance',
                    'attendance summary',
                    'attendance record',
                    'how many days present',
                    'present days',
                    'absent days',
                    'late days',
                    'my attendance',
                    'attendance for this month',
                ],
                'handler' => fn($i) => $this->handleAttendanceSummary(),
            ],
            [
                'name'     => 'payroll_history',
                'patterns' => [
                    'payroll history',
                    'past payslip',
                    'previous salary',
                    'salary history',
                    'old payslip',
                    'all payslips',
                    'payslip history',
                ],
                'handler' => fn($i) => $this->handlePayrollHistory(),
            ],
            [
                'name'     => 'faq_payroll_schedule',
                'patterns' => [
                    'payroll schedule',
                    'pay schedule',
                    'pay date',
                    'payday',
                    'when do we get paid',
                    'salary schedule',
                    'when is salary',
                    'salary date',
                    'payment date',
                    'when is payday',
                ],
                'handler' => fn($i) => $this->handleFaqPayrollSchedule(),
            ],
            [
                'name'     => 'payroll',
                'patterns' => [
                    'my payslip',
                    'latest payslip',
                    'last payslip',
                    'my pay',
                    'net pay',
                    'gross pay',
                    'pay this month',
                    'my payroll',
                    'view payslip',
                    'current payslip',
                    'how much salary',
                    'salary',
                    'payroll',
                ],
                'handler' => fn($i) => $this->handlePayroll(),
            ],
            [
                'name'     => 'faq_payslip_download',
                'patterns' => [
                    'download pdf',
                    'download payslip',
                    'get payslip',
                    'payslip pdf',
                    'where is my payslip',
                    'print payslip',
                    'how to download payslip',
                ],
                'handler' => fn($i) => $this->handleFaqPayslipDownload(),
            ],
            [
                'name'     => 'my_profile',
                'patterns' => [
                    'my profile',
                    'my info',
                    'my details',
                    'who am i',
                    'my department',
                    'my position',
                    'my role',
                    'employee code',
                    'my code',
                    'hire date',
                    'when did i join',
                    'my email',
                ],
                'handler' => fn($i) => $this->handleMyProfile(),
            ],
            [
                'name'     => 'faq_how_to_leave',
                'patterns' => [
                    'how to request leave',
                    'apply for leave',
                    'how do i request',
                    'how to apply leave',
                    'submit leave',
                    'file a leave',
                    'leave application',
                    'request leave',
                ],
                'handler' => fn($i) => $this->handleFaqHowToLeave(),
            ],
        ];
    }

    // Admin intents
    private function adminIntents(): array
    {
        return [
            [
                'name'     => 'admin_overview',
                'patterns' => [
                    'overview',
                    'system overview',
                    'summary',
                    'dashboard',
                    'company stats',
                    'company summary',
                    'total employees',
                    'how many employees',
                    'headcount',
                ],
                'handler' => fn($i) => $this->handleAdminOverview(),
            ],
            [
                'name'     => 'admin_all_attendance',
                'patterns' => [
                    'attendance today',
                    'who checked in',
                    'present today',
                    'absent today',
                    'late today',
                    'today attendance',
                    'attendance report',
                    'all attendance',
                ],
                'handler' => fn($i) => $this->handleAdminAttendanceToday(),
            ],
            [
                'name'     => 'admin_attendance_month',
                'patterns' => [
                    'attendance this month',
                    'monthly attendance',
                    'attendance summary',
                    'this month attendance',
                    'attendance for this month',
                ],
                'handler' => fn($i) => $this->handleAdminAttendanceMonth(),
            ],
            [
                'name'     => 'admin_pending_leaves',
                'patterns' => [
                    'pending leaves',
                    'leave requests',
                    'pending approvals',
                    'leaves to approve',
                    'unapproved leaves',
                    'leave queue',
                    'pending leave requests',
                ],
                'handler' => fn($i) => $this->handleAdminPendingLeaves(),
            ],
            [
                'name'     => 'admin_payroll_status',
                'patterns' => [
                    'payroll status',
                    'payroll this month',
                    'generated payroll',
                    'payroll summary',
                    'total payroll',
                    'payroll cost',
                    'salary cost',
                    'all payroll',
                    'payroll',
                ],
                'handler' => fn($i) => $this->handleAdminPayrollStatus(),
            ],
            [
                'name'     => 'admin_employees',
                'patterns' => [
                    'all employees',
                    'list employees',
                    'employee list',
                    'active employees',
                    'inactive employees',
                    'terminated employees',
                    'employees',
                ],
                'handler' => fn($i) => $this->handleAdminEmployees(),
            ],
            [
                'name'     => 'admin_department_stats',
                'patterns' => [
                    'department stats',
                    'department summary',
                    'employees per department',
                    'headcount per department',
                    'department headcount',
                    'department breakdown',
                ],
                'handler' => fn($i) => $this->handleAdminDepartmentStats(),
            ],
            [
                'name'     => 'admin_reviews',
                'patterns' => [
                    'performance reviews',
                    'recent reviews',
                    'review summary',
                    'all reviews',
                    'reviews this quarter',
                ],
                'handler' => fn($i) => $this->handleAdminReviews(),
            ],
        ];
    }

    // HR Manager intents
    private function hrManagerIntents(): array
    {
        return [
            [
                'name'     => 'hr_overview',
                'patterns' => [
                    'overview',
                    'summary',
                    'dashboard',
                    'my team',
                    'team summary',
                    'hr summary',
                    'hr overview',
                    'today summary',
                    'what is happening',
                ],
                'handler' => fn($i) => $this->handleHrOverview(),
            ],
            [
                'name'     => 'hr_attendance_today',
                'patterns' => [
                    'attendance today',
                    'who checked in',
                    'present today',
                    'absent today',
                    'late today',
                    'today attendance',
                    'all attendance',
                    'attendance report',
                ],
                'handler' => fn($i) => $this->handleAdminAttendanceToday(),
            ],
            [
                'name'     => 'hr_attendance_month',
                'patterns' => [
                    'attendance this month',
                    'monthly attendance',
                    'attendance summary',
                    'this month attendance',
                    'attendance for this month',
                ],
                'handler' => fn($i) => $this->handleAdminAttendanceMonth(),
            ],
            [
                'name'     => 'hr_pending_leaves',
                'patterns' => [
                    'pending leaves',
                    'leave requests',
                    'pending approvals',
                    'leaves to approve',
                    'unapproved leaves',
                    'leave queue',
                    'pending leave requests',
                    'who applied for leave',
                ],
                'handler' => fn($i) => $this->handleAdminPendingLeaves(),
            ],
            [
                'name'     => 'hr_employees',
                'patterns' => [
                    'all employees',
                    'list employees',
                    'employee list',
                    'active employees',
                    'new employees',
                    'employees',
                    'how many employees',
                    'total employees',
                    'headcount',
                ],
                'handler' => fn($i) => $this->handleAdminEmployees(),
            ],
            [
                'name'     => 'hr_department_stats',
                'patterns' => [
                    'department stats',
                    'department summary',
                    'employees per department',
                    'department headcount',
                    'department breakdown',
                ],
                'handler' => fn($i) => $this->handleAdminDepartmentStats(),
            ],
            [
                'name'     => 'hr_reviews',
                'patterns' => [
                    'performance reviews',
                    'recent reviews',
                    'review summary',
                    'all reviews',
                    'reviews this quarter',
                    'pending reviews',
                ],
                'handler' => fn($i) => $this->handleAdminReviews(),
            ],
            [
                'name'     => 'hr_leave_summary',
                'patterns' => [
                    'leave summary',
                    'approved leaves',
                    'all leave requests',
                    'leave this month',
                    'who is on leave',
                    'on leave today',
                    'leave overview',
                ],
                'handler' => fn($i) => $this->handleHrLeaveSummary(),
            ],
        ];
    }

    // Employee handlers
    private function handleLeaveBalance(): array
    {
        if (!$this->employee) {
            return ['text' => $this->noEmployeeMessage()];
        }

        $leaveTypes = LeaveType::all();
        $year       = now()->year;
        $cards      = [];

        foreach ($leaveTypes as $type) {
            $used = LeaveRequest::where('employee_id', $this->employee->id)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('total_days');

            $remaining = max(0, $type->days_allowed - $used);

            $cards[] = [
                'title' => $type->name,
                'value' => "{$remaining} days left",
                'sub'   => "Used {$used} of {$type->days_allowed} days",
                'color' => $remaining > 5 ? 'green' : ($remaining > 0 ? 'amber' : 'red'),
            ];
        }

        return [
            'text'    => "Here's your leave balance for **{$year}**:",
            'cards'   => $cards,
            'buttons' => [
                ['label' => '📋 Leave history',     'value' => 'leave history'],
                ['label' => '📝 How to request',    'value' => 'how to request leave'],
            ],
        ];
    }

    private function handleLeaveHistory(): array
    {
        if (!$this->employee) {
            return ['text' => $this->noEmployeeMessage()];
        }

        $leaves = LeaveRequest::where('employee_id', $this->employee->id)
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get();

        if ($leaves->isEmpty()) {
            return [
                'text'    => 'You have not submitted any leave requests yet.',
                'buttons' => [
                    ['label' => '📝 How to request leave', 'value' => 'how to request leave'],
                ],
            ];
        }

        $cards = $leaves->map(fn($l) => [
            'title' => $l->leaveType->name,
            'value' => $l->start_date->format('M d') . ' – ' . $l->end_date->format('M d, Y'),
            'sub'   => "{$l->total_days} day(s) · " . ucfirst($l->status),
            'color' => match ($l->status) {
                'approved' => 'green',
                'rejected' => 'red',
                default    => 'amber',
            },
        ])->toArray();

        return [
            'text'  => "Your **5 most recent** leave requests:",
            'cards' => $cards,
        ];
    }

    private function handleAttendanceToday(): array
    {
        if (!$this->employee) {
            return ['text' => $this->noEmployeeMessage()];
        }

        $record = Attendance::where('employee_id', $this->employee->id)
            ->whereDate('date', today())
            ->first();

        if (!$record) {
            return [
                'text'    => "You have **not checked in** today (" . today()->format('l, F j') . ").",
                'buttons' => [
                    ['label' => '📅 This month', 'value' => 'attendance this month'],
                ],
            ];
        }

        $checkIn  = $record->check_in  ? '⏰ Check-in: **'  . substr($record->check_in, 0, 5)  . '**' : '⏰ Check-in: —';
        $checkOut = $record->check_out ? '🏁 Check-out: **' . substr($record->check_out, 0, 5) . '**' : '🏁 Check-out: not yet';
        $statusMap = [
            'present'  => '✅ Present',
            'late'     => '🟡 Late',
            'absent'   => '❌ Absent',
            'half-day' => '🔶 Half-day',
        ];
        $status = $statusMap[$record->status] ?? ucfirst($record->status);

        return [
            'text' => "**Today's attendance** (" . today()->format('l, F j') . ")\n\n{$checkIn}\n{$checkOut}\nStatus: {$status}",
            'buttons' => [
                ['label' => '📅 This month summary', 'value' => 'attendance this month'],
            ],
        ];
    }

    private function handleAttendanceSummary(): array
    {
        if (!$this->employee) {
            return ['text' => $this->noEmployeeMessage()];
        }

        $start   = now()->startOfMonth()->toDateString();
        $end     = now()->endOfMonth()->toDateString();
        $records = Attendance::where('employee_id', $this->employee->id)
            ->whereBetween('date', [$start, $end])
            ->get();

        $cards = [
            ['title' => 'Present',  'value' => $records->where('status', 'present')->count()  . ' days', 'sub' => 'On time',            'color' => 'green'],
            ['title' => 'Late',     'value' => $records->where('status', 'late')->count()     . ' days', 'sub' => 'After 09:00',        'color' => 'amber'],
            ['title' => 'Half-day', 'value' => $records->where('status', 'half-day')->count() . ' days', 'sub' => 'Out before 13:00',   'color' => 'purple'],
            ['title' => 'Absent',   'value' => $records->where('status', 'absent')->count()   . ' days', 'sub' => 'No check-in',        'color' => 'red'],
        ];

        return [
            'text'  => "Your attendance for **" . now()->format('F Y') . "**:",
            'cards' => $cards,
            'buttons' => [
                ['label' => '🕐 Today', 'value' => 'attendance today'],
            ],
        ];
    }

    private function handlePayroll(): array
    {
        if (!$this->employee) {
            return ['text' => $this->noEmployeeMessage()];
        }

        $payroll = Payroll::where('employee_id', $this->employee->id)->latest()->first();

        if (!$payroll) {
            return ['text' => 'No payroll records found yet. Payroll is generated by the admin at the end of each month.'];
        }

        $month = Carbon::parse($payroll->month . '-01')->format('F Y');
        $cards = [
            ['title' => 'Basic Salary',     'value' => number_format($payroll->basic_salary, 2),     'sub' => 'Base pay',           'color' => 'blue'],
            ['title' => 'Gross Salary',     'value' => number_format($payroll->gross_salary, 2),     'sub' => 'With allowances',    'color' => 'green'],
            ['title' => 'Total Deductions', 'value' => number_format($payroll->total_deductions, 2), 'sub' => 'Tax + SSS + others', 'color' => 'red'],
            ['title' => 'Net Salary',       'value' => number_format($payroll->net_salary, 2),       'sub' => 'Take-home pay',      'color' => 'indigo'],
        ];

        return [
            'text'    => "Latest payslip — **{$month}** (Status: " . ucfirst($payroll->status) . "):",
            'cards'   => $cards,
            'buttons' => [
                ['label' => '📄 Download PDF',   'value' => 'download pdf'],
                ['label' => '📋 Payroll history', 'value' => 'payroll history'],
            ],
        ];
    }

    private function handlePayrollHistory(): array
    {
        if (!$this->employee) {
            return ['text' => $this->noEmployeeMessage()];
        }

        $payrolls = Payroll::where('employee_id', $this->employee->id)->latest()->take(6)->get();

        if ($payrolls->isEmpty()) {
            return ['text' => 'No payroll records found yet.'];
        }

        $cards = $payrolls->map(fn($p) => [
            'title' => Carbon::parse($p->month . '-01')->format('F Y'),
            'value' => 'Net: ' . number_format($p->net_salary, 2),
            'sub'   => 'Gross: ' . number_format($p->gross_salary, 2) . ' · ' . ucfirst($p->status),
            'color' => $p->status === 'paid' ? 'green' : 'amber',
        ])->toArray();

        return [
            'text'  => "Your **last 6 payroll records**:",
            'cards' => $cards,
        ];
    }

    private function handleMyProfile(): array
    {
        if (!$this->employee) {
            return ['text' => $this->noEmployeeMessage()];
        }

        $this->employee->load(['department', 'position', 'user']);

        $cards = [
            ['title' => 'Name',       'value' => $this->employee->user->name,                           'sub' => 'Full name',       'color' => 'blue'],
            ['title' => 'Code',       'value' => $this->employee->employee_code,                        'sub' => 'Employee ID',     'color' => 'blue'],
            ['title' => 'Email',      'value' => $this->employee->user->email,                          'sub' => 'Work email',      'color' => 'blue'],
            ['title' => 'Department', 'value' => $this->employee->department?->name ?? '—',             'sub' => 'Your department', 'color' => 'purple'],
            ['title' => 'Position',   'value' => $this->employee->position?->title ?? '—',              'sub' => 'Your role',       'color' => 'purple'],
            ['title' => 'Hire Date',  'value' => $this->employee->hire_date?->format('M d, Y') ?? '—', 'sub' => 'Date joined',     'color' => 'green'],
        ];

        return [
            'text'  => "Here is **your profile**:",
            'cards' => $cards,
        ];
    }

    private function handleFaqPayslipDownload(): array
    {
        return [
            'text'    => "**How to Download Your Payslip** 📄\n\n1. Go to **My Payslips** from the sidebar\n2. Find the month you need\n3. Click **Download PDF** next to it\n4. The payslip opens as a PDF in a new tab\n\nPayslips are available once the admin generates and approves payroll for that month.",
            'buttons' => [
                ['label' => '💰 View my payslips', 'value' => 'payroll history'],
            ],
        ];
    }

    private function handleFaqHowToLeave(): array
    {
        return [
            'text'    => "**How to Request Leave** 📝\n\n1. Go to **My Leaves** from the sidebar\n2. Fill in the leave type, start date, end date, and reason\n3. Click **Submit Request**\n4. Your HR manager will approve or reject it\n5. You will receive a notification on the decision\n\nMake sure you have enough balance before submitting.",
            'buttons' => [
                ['label' => '📊 Check my balance', 'value' => 'leave balance'],
                ['label' => '📖 Leave policy',     'value' => 'leave policy'],
            ],
        ];
    }

    // Admin handlers
    private function handleAdminOverview(): array
    {
        $totalActive     = Employee::where('status', 'active')->count();
        $totalInactive   = Employee::where('status', 'inactive')->count();
        $totalTerminated = Employee::where('status', 'terminated')->count();
        $presentToday    = Attendance::whereDate('date', today())->whereIn('status', ['present', 'late'])->count();
        $absentToday     = Attendance::whereDate('date', today())->where('status', 'absent')->count();
        $pendingLeaves   = LeaveRequest::where('status', 'pending')->count();
        $totalDepts      = Department::count();

        $month           = now()->format('Y-m');
        $payrollCost     = Payroll::where('month', $month)->sum('net_salary');

        $cards = [
            ['title' => 'Active Employees',  'value' => $totalActive,                        'sub' => "{$totalInactive} inactive · {$totalTerminated} terminated", 'color' => 'green'],
            ['title' => 'Present Today',     'value' => $presentToday . ' / ' . $totalActive, 'sub' => "{$absentToday} absent today",                               'color' => 'blue'],
            ['title' => 'Pending Leaves',    'value' => $pendingLeaves,                      'sub' => 'Awaiting approval',                                         'color' => $pendingLeaves > 0 ? 'amber' : 'green'],
            ['title' => 'Departments',       'value' => $totalDepts,                         'sub' => 'Active departments',                                        'color' => 'purple'],
            ['title' => 'Payroll This Month', 'value' => number_format($payrollCost, 2),      'sub' => now()->format('F Y') . ' net payout',                        'color' => 'indigo'],
        ];

        return [
            'text'    => "**System Overview** 📊 — " . now()->format('l, F j Y'),
            'cards'   => $cards,
            'buttons' => [
                ['label' => '👥 All employees',     'value' => 'all employees'],
                ['label' => '📋 Pending leaves',    'value' => 'pending leaves'],
                ['label' => '🕐 Attendance today',  'value' => 'attendance today'],
                ['label' => '💰 Payroll status',    'value' => 'payroll status'],
            ],
        ];
    }

    private function handleAdminAttendanceToday(): array
    {
        $today   = today()->toDateString();
        $present = Attendance::whereDate('date', $today)->where('status', 'present')->count();
        $late    = Attendance::whereDate('date', $today)->where('status', 'late')->count();
        $absent  = Attendance::whereDate('date', $today)->where('status', 'absent')->count();
        $halfDay = Attendance::whereDate('date', $today)->where('status', 'half-day')->count();
        $total   = Employee::where('status', 'active')->count();

        $cards = [
            ['title' => 'Present',  'value' => $present,  'sub' => 'Checked in on time',    'color' => 'green'],
            ['title' => 'Late',     'value' => $late,     'sub' => 'After 09:00',           'color' => 'amber'],
            ['title' => 'Half-day', 'value' => $halfDay,  'sub' => 'Out before 13:00',      'color' => 'purple'],
            ['title' => 'Absent',   'value' => $absent,   'sub' => 'No check-in recorded',  'color' => 'red'],
        ];

        $notRecorded = $total - $present - $late - $halfDay - $absent;

        return [
            'text'    => "**Today's Attendance** — " . today()->format('l, F j') . "\n\nTotal active employees: **{$total}**" . ($notRecorded > 0 ? " · {$notRecorded} not yet recorded" : ""),
            'cards'   => $cards,
            'buttons' => [
                ['label' => '📅 This month summary', 'value' => 'attendance this month'],
            ],
        ];
    }

    private function handleAdminAttendanceMonth(): array
    {
        $start = now()->startOfMonth()->toDateString();
        $end   = now()->endOfMonth()->toDateString();

        $present  = Attendance::whereBetween('date', [$start, $end])->where('status', 'present')->count();
        $late     = Attendance::whereBetween('date', [$start, $end])->where('status', 'late')->count();
        $halfDay  = Attendance::whereBetween('date', [$start, $end])->where('status', 'half-day')->count();
        $absent   = Attendance::whereBetween('date', [$start, $end])->where('status', 'absent')->count();
        $total    = Attendance::whereBetween('date', [$start, $end])->count();

        $cards = [
            ['title' => 'Present',  'value' => $present,  'sub' => 'All employees, on time', 'color' => 'green'],
            ['title' => 'Late',     'value' => $late,     'sub' => 'All employees, late',    'color' => 'amber'],
            ['title' => 'Half-day', 'value' => $halfDay,  'sub' => 'All employees',          'color' => 'purple'],
            ['title' => 'Absent',   'value' => $absent,   'sub' => 'All employees, absent',  'color' => 'red'],
        ];

        return [
            'text'  => "**Company-wide attendance** for **" . now()->format('F Y') . "** ({$total} total records):",
            'cards' => $cards,
            'buttons' => [
                ['label' => '🕐 Today only', 'value' => 'attendance today'],
            ],
        ];
    }

    private function handleAdminPendingLeaves(): array
    {
        $pending = LeaveRequest::where('status', 'pending')
            ->with(['employee.user', 'leaveType'])
            ->latest()
            ->take(8)
            ->get();

        if ($pending->isEmpty()) {
            return [
                'text' => "✅ No pending leave requests right now. All caught up!",
            ];
        }

        $cards = $pending->map(fn($l) => [
            'title' => $l->employee?->user?->name ?? 'Unknown',
            'value' => $l->leaveType->name . ' — ' . $l->total_days . ' day(s)',
            'sub'   => $l->start_date->format('M d') . ' – ' . $l->end_date->format('M d, Y'),
            'color' => 'amber',
        ])->toArray();

        return [
            'text'    => "**{$pending->count()} pending leave request(s)** awaiting approval:",
            'cards'   => $cards,
            'buttons' => [
                ['label' => '📋 Leave summary', 'value' => 'leave summary'],
            ],
        ];
    }

    private function handleAdminPayrollStatus(): array
    {
        $month    = now()->format('Y-m');
        $payrolls = Payroll::where('month', $month)->get();
        $active   = Employee::where('status', 'active')->count();
        $generated = $payrolls->count();
        $paid      = $payrolls->where('status', 'paid')->count();
        $approved  = $payrolls->where('status', 'approved')->count();
        $draft     = $payrolls->where('status', 'draft')->count();

        $cards = [
            ['title' => 'Generated',   'value' => $generated . ' / ' . $active, 'sub' => 'Payslips for ' . now()->format('F Y'), 'color' => $generated === $active ? 'green' : 'amber'],
            ['title' => 'Draft',       'value' => $draft,                        'sub' => 'Not yet approved',                    'color' => $draft > 0 ? 'amber' : 'green'],
            ['title' => 'Approved',    'value' => $approved,                     'sub' => 'Ready to pay',                        'color' => $approved > 0 ? 'blue' : 'green'],
            ['title' => 'Paid',        'value' => $paid,                         'sub' => 'Disbursed',                           'color' => 'green'],
            ['title' => 'Total Gross', 'value' => number_format($payrolls->sum('gross_salary'), 2),  'sub' => 'Gross payout',  'color' => 'indigo'],
            ['title' => 'Total Net',   'value' => number_format($payrolls->sum('net_salary'), 2),    'sub' => 'Net payout',    'color' => 'indigo'],
        ];

        return [
            'text'  => "**Payroll Status** for **" . now()->format('F Y') . "**:",
            'cards' => $cards,
            'buttons' => $this->suggestedTopics(),
        ];
    }

    private function handleAdminEmployees(): array
    {
        $active     = Employee::where('status', 'active')->count();
        $inactive   = Employee::where('status', 'inactive')->count();
        $terminated = Employee::withTrashed()->where('status', 'terminated')->count();
        $newThisMonth = Employee::whereMonth('hire_date', now()->month)
            ->whereYear('hire_date', now()->year)
            ->count();

        $cards = [
            ['title' => 'Active',          'value' => $active,       'sub' => 'Currently employed', 'color' => 'green'],
            ['title' => 'Inactive',         'value' => $inactive,     'sub' => 'On hold',            'color' => 'amber'],
            ['title' => 'Terminated',       'value' => $terminated,   'sub' => 'Soft-deleted',       'color' => 'red'],
            ['title' => 'New This Month',   'value' => $newThisMonth, 'sub' => now()->format('F Y'), 'color' => 'blue'],
        ];

        return [
            'text'    => "**Employee Overview**:",
            'cards'   => $cards,
            'buttons' => [
                ['label' => '🏢 Department stats', 'value' => 'department stats'],
            ],
        ];
    }

    private function handleAdminDepartmentStats(): array
    {
        $departments = Department::withCount([
            'employees' => fn($q) => $q->where('status', 'active'),
        ])->get();

        $cards = $departments->map(fn($d) => [
            'title' => $d->name,
            'value' => $d->employees_count . ' active',
            'sub'   => 'Manager: ' . ($d->manager?->name ?? 'Not assigned'),
            'color' => 'blue',
        ])->toArray();

        return [
            'text'  => "**Headcount by Department**:",
            'cards' => $cards,
        ];
    }

    private function handleAdminReviews(): array
    {
        $reviews = PerformanceReview::with(['employee.user'])
            ->latest('reviewed_at')
            ->take(5)
            ->get();

        if ($reviews->isEmpty()) {
            return ['text' => 'No performance reviews have been submitted yet.'];
        }

        $cards = $reviews->map(fn($r) => [
            'title' => $r->employee?->user?->name ?? 'Unknown',
            'value' => 'Score: ' . $r->score . '/100',
            'sub'   => $r->period . ' · ' . $r->reviewed_at->format('M d, Y'),
            'color' => $r->score >= 80 ? 'green' : ($r->score >= 60 ? 'amber' : 'red'),
        ])->toArray();

        return [
            'text'  => "**5 Most Recent Performance Reviews**:",
            'cards' => $cards,
        ];
    }

    // HR Manager handlers
    private function handleHrOverview(): array
    {
        $active        = Employee::where('status', 'active')->count();
        $presentToday  = Attendance::whereDate('date', today())->whereIn('status', ['present', 'late'])->count();
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $onLeaveToday  = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();

        $cards = [
            ['title' => 'Active Employees', 'value' => $active,        'sub' => 'Total workforce',        'color' => 'green'],
            ['title' => 'Present Today',    'value' => $presentToday,  'sub' => 'Checked in',             'color' => 'blue'],
            ['title' => 'On Leave Today',   'value' => $onLeaveToday,  'sub' => 'Approved leave',         'color' => 'purple'],
            ['title' => 'Pending Leaves',   'value' => $pendingLeaves, 'sub' => 'Needs your approval',    'color' => $pendingLeaves > 0 ? 'amber' : 'green'],
        ];

        return [
            'text'    => "**HR Overview** — " . now()->format('l, F j Y'),
            'cards'   => $cards,
            'buttons' => [
                ['label' => '📋 Pending leaves',    'value' => 'pending leaves'],
                ['label' => '🕐 Attendance today',  'value' => 'attendance today'],
                ['label' => '👥 All employees',     'value' => 'all employees'],
            ],
        ];
    }

    private function handleHrLeaveSummary(): array
    {
        $month    = now()->format('Y-m');
        $approved = LeaveRequest::where('status', 'approved')->whereYear('start_date', now()->year)->count();
        $rejected = LeaveRequest::where('status', 'rejected')->whereYear('start_date', now()->year)->count();
        $pending  = LeaveRequest::where('status', 'pending')->count();

        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->with('employee.user')
            ->get();

        $cards = [
            ['title' => 'Approved This Year', 'value' => $approved, 'sub' => now()->year . ' total', 'color' => 'green'],
            ['title' => 'Rejected This Year', 'value' => $rejected, 'sub' => now()->year . ' total', 'color' => 'red'],
            ['title' => 'Pending Now',        'value' => $pending,  'sub' => 'Awaiting approval',   'color' => $pending > 0 ? 'amber' : 'green'],
            ['title' => 'On Leave Today',     'value' => $onLeaveToday->count(), 'sub' => today()->format('M d, Y'), 'color' => 'purple'],
        ];

        $extraText = '';
        if ($onLeaveToday->isNotEmpty()) {
            $names = $onLeaveToday->map(fn($l) => $l->employee?->user?->name ?? 'Unknown')->join(', ');
            $extraText = "\n\nOn leave today: **{$names}**";
        }

        return [
            'text'  => "**Leave Summary**{$extraText}",
            'cards' => $cards,
            'buttons' => [
                ['label' => '📋 Pending approvals', 'value' => 'pending leaves'],
            ],
        ];
    }

    // Shared handlers
    private function handleGreeting(): array
    {
        $name  = $this->user->name ?? 'there';
        $hour  = now()->hour;
        $time  = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $roleLabel = match ($this->role) {
            'admin'      => 'System Admin',
            'hr_manager' => 'HR Manager',
            default      => 'Employee',
        };

        return [
            'text'    => "{$time}, **{$name}**! 👋 You are logged in as **{$roleLabel}**. How can I help you today?",
            'buttons' => $this->suggestedTopics(),
        ];
    }

    private function handleHelp(): array
    {
        $buttons = match ($this->role) {
            'admin' => [
                ['label' => '📊 System overview',     'value' => 'overview'],
                ['label' => '👥 All employees',        'value' => 'all employees'],
                ['label' => '🕐 Attendance today',     'value' => 'attendance today'],
                ['label' => '📅 Attendance this month', 'value' => 'attendance this month'],
                ['label' => '📋 Pending leaves',       'value' => 'pending leaves'],
                ['label' => '💰 Payroll status',       'value' => 'payroll status'],
                ['label' => '🏢 Department stats',     'value' => 'department stats'],
                ['label' => '⭐ Performance reviews',  'value' => 'performance reviews'],
                ['label' => '📖 Leave policy',         'value' => 'leave policy'],
                ['label' => '📞 Contact HR',           'value' => 'contact hr'],
            ],
            'hr_manager' => [
                ['label' => '📊 HR overview',          'value' => 'overview'],
                ['label' => '👥 All employees',        'value' => 'all employees'],
                ['label' => '🕐 Attendance today',     'value' => 'attendance today'],
                ['label' => '📅 Attendance this month', 'value' => 'attendance this month'],
                ['label' => '📋 Pending leaves',       'value' => 'pending leaves'],
                ['label' => '📄 Leave summary',        'value' => 'leave summary'],
                ['label' => '🏢 Department stats',     'value' => 'department stats'],
                ['label' => '⭐ Performance reviews',  'value' => 'performance reviews'],
                ['label' => '📖 Leave policy',         'value' => 'leave policy'],
            ],
            default => [
                ['label' => '📋 Leave history',        'value' => 'leave history'],
                ['label' => '🕐 Attendance today',     'value' => 'attendance today'],
                ['label' => '📅 Attendance this month', 'value' => 'attendance this month'],
                ['label' => '💰 My payslip',           'value' => 'my payslip'],
                ['label' => '📄 Payroll history',      'value' => 'payroll history'],
                ['label' => '👤 My profile',           'value' => 'my profile'],
                ['label' => '📖 Leave policy',         'value' => 'leave policy'],
                ['label' => '📅 Payroll schedule',     'value' => 'payroll schedule'],
                ['label' => '🕐 Working hours',        'value' => 'working hours'],
                ['label' => '📞 Contact HR',           'value' => 'contact hr'],
            ],
        };

        return [
            'text'    => "**Here is what I can help you with** 🤖\nChoose a topic or type your question:",
            'buttons' => $buttons,
        ];
    }

    private function handleFaqLeavePolicy(): array
    {
        $types = LeaveType::all();
        $list  = $types->map(
            fn($t) =>
            "• **{$t->name}** — {$t->days_allowed} days/year (" . ($t->is_paid ? 'paid' : 'unpaid') . ")"
        )->join("\n");

        return [
            'text'    => "**Leave Policy** 📋\n\nLeave types available per year:\n\n{$list}\n\nPlanned leaves must be submitted at least **24 hours in advance**. Sick leave may be applied retrospectively with a medical certificate.",
            'buttons' => $this->suggestedTopics(),
        ];
    }

    private function handleFaqPayrollSchedule(): array
    {
        return [
            'text' => "**Payroll Schedule** 💰\n\nSalaries are processed on the **last working day of each month**. Payslips are available for download on the same day.\n\nPayroll components:\n• Basic salary\n• Housing allowance (+20%)\n• Transport allowance (+10%)\n• Income tax (−10%)\n• Social security (−5%)\n• Absence deductions (if any)",
            'buttons' => [
                ['label' => '💰 My payslip',    'value' => 'my payslip'],
                ['label' => '📄 Download PDF',  'value' => 'download pdf'],
            ],
        ];
    }

    private function handleFaqWorkingHours(): array
    {
        return [
            'text'    => "**Working Hours** 🕐\n\n• Start: **8:00 AM**\n• End: **5:00 PM**\n• Days: **Monday – Friday**\n• Lunch: **12:00 PM – 1:00 PM**\n• Total: **8 hours/day**\n\nCheck-in after **9:00 AM** is marked as late.",
            'buttons' => [
                ['label' => '🟡 Late policy',           'value' => 'late policy'],
                ['label' => '🕐 My attendance today',   'value' => 'attendance today'],
            ],
        ];
    }

    private function handleFaqLatePolicy(): array
    {
        return [
            'text'    => "**Late Policy** 🟡\n\nLate threshold: **9:00 AM**\n\n• After 9:00 AM → marked **Late**\n• 3+ late records in a month may incur a deduction\n• Late arrivals appear on attendance reports\n\nNotify HR in advance if you have a valid reason.",
            'buttons' => [
                ['label' => '🕐 My attendance today',   'value' => 'attendance today'],
                ['label' => '📅 This month summary',    'value' => 'attendance this month'],
            ],
        ];
    }

    private function handleFaqAbsentPolicy(): array
    {
        return [
            'text'    => "**Absence Policy** ❌\n\nEmployees with no check-in by **6:00 PM** are marked **Absent** automatically.\n\n• Absences are deducted from salary\n• Sick leave should be filed ASAP\n• 3 consecutive unexcused absences require written explanation",
            'buttons' => [
                ['label' => '📊 Leave balance',     'value' => 'leave balance'],
                ['label' => '📝 How to request',    'value' => 'how to request leave'],
            ],
        ];
    }

    private function handleFaqContactHr(): array
    {
        $hrManagers = User::whereHas('roles', fn($q) => $q->where('slug', 'hr_manager'))->get();
        $list = $hrManagers->isNotEmpty()
            ? $hrManagers->map(fn($u) => "• **{$u->name}** — {$u->email}")->join("\n")
            : '• Check with your admin for HR contact details.';

        return [
            'text' => "**HR Contact** 📞\n\n{$list}\n\nVisit the HR office during working hours (8:00 AM – 5:00 PM) for urgent matters.",
        ];
    }

    private function handleDepartments(): array
    {
        $departments = Department::withCount([
            'employees' => fn($q) => $q->where('status', 'active'),
        ])->get();

        $cards = $departments->map(fn($d) => [
            'title' => $d->name,
            'value' => $d->employees_count . ' employee(s)',
            'sub'   => 'Manager: ' . ($d->manager?->name ?? 'Not assigned'),
            'color' => 'blue',
        ])->toArray();

        return [
            'text'  => "**Company Departments**:",
            'cards' => $cards,
        ];
    }

    private function handleThanks(): array
    {
        $responses = [
            "You're welcome! 😊 Anything else I can help with?",
            "Happy to help! Let me know if you need anything else.",
            "Glad I could assist! Feel free to ask anytime.",
        ];
        return [
            'text'    => $responses[array_rand($responses)],
            'buttons' => $this->suggestedTopics(),
        ];
    }

    private function handleGoodbye(): array
    {
        return [
            'text' => "Goodbye, **{$this->user->name}**! 👋 Have a great day.",
        ];
    }

    private function fallback(string $input): string
    {
        return "I'm not sure I understand **\"{$input}\"**. Try one of the suggestions below or rephrase your question.";
    }

    // Helpers
    private function suggestedTopics(): array
    {
        return match ($this->role) {
            'admin' => [
                ['label' => '📊 Overview',          'value' => 'overview'],
                ['label' => '📋 Pending leaves',    'value' => 'pending leaves'],
                ['label' => '🕐 Attendance today',  'value' => 'attendance today'],
                ['label' => '❓ Help',              'value' => 'help'],
            ],
            'hr_manager' => [
                ['label' => '📊 HR overview',       'value' => 'overview'],
                ['label' => '📋 Pending leaves',    'value' => 'pending leaves'],
                ['label' => '🕐 Attendance today',  'value' => 'attendance today'],
                ['label' => '❓ Help',              'value' => 'help'],
            ],
            default => [
                ['label' => '📊 Leave balance',     'value' => 'leave balance'],
                ['label' => '🕐 Attendance today',  'value' => 'attendance today'],
                ['label' => '💰 My payslip',        'value' => 'my payslip'],
                ['label' => '❓ Help',              'value' => 'help'],
            ],
        };
    }

    private function noEmployeeMessage(): string
    {
        return "I couldn't find an employee profile linked to your account. Please contact HR if you think this is an error.";
    }
}
