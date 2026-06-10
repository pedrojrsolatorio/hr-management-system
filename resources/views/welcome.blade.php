<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet"> --}}
    {{-- Use Bunny instead of Google Fonts for privacy --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:300,400,500|syne:400,500,600,700,800" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <!-- Background -->
    <div class="bg-layer">
        {{-- <div class="bg-grid"></div> --}}
        <div class="bg-orb-1"></div>
        <div class="bg-orb-2"></div>
        <div class="bg-orb-3"></div>
    </div>

    <div class="page">

        <!-- Nav -->
        <nav>
            <div class="nav-logo">
                <div class="nav-logo-icon">H</div>
                HRMS
            </div>
            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#roles" class="nav-link">Roles</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-nav">Go to Dashboard →</a>
                @else
                    {{-- <a href="{{ route('login') }}" class="nav-link">Sign in</a> --}}
                    {{-- <a href="{{ route('register') }}" class="btn-nav">Get started →</a> --}}
                    <a href="{{ route('login') }}" class="btn-nav">Sign in →</a>
                @endauth
            </div>
        </nav>

        <!-- Hero -->
        <section class="hero">
            <div class="hero-badge">
                <div class="hero-badge-dot"></div>
                Built with Laravel 11 + TailwindCSS
            </div>

            <h1 class="hero-title">
                Modern HR<br />
                <span class="hero-title-accent">Operations</span><br />
                Simplified
            </h1>

            <p class="hero-sub">
                A complete Human Resource Management System. Manage employees,
                track attendance, process payroll, and review performance — all in one place.
            </p>

            <div class="hero-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">
                        Open Dashboard <span class="btn-arrow">→</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">
                        Sign in <span class="btn-arrow">→</span>
                    </a>
                    <a href="#features" class="btn-secondary">
                        Explore features <span class="btn-arrow">→</span>
                    </a>
                @endauth
            </div>

            <div class="stats-strip">
                <div class="stat-item">
                    <div class="stat-number">9+</div>
                    <div class="stat-label">Core Modules</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Role Levels</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">REST</div>
                    <div class="stat-label">API Ready</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">PDF</div>
                    <div class="stat-label">Payslip Export</div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="section" id="features">
            <span class="section-label animate-fadeup">What's included</span>
            <h2 class="section-title animate-fadeup delay-1">Everything your HR team needs</h2>
            <p class="section-sub animate-fadeup delay-2">
                From onboarding to payroll, every workflow is covered with a clean,
                role-aware interface.
            </p>

            <div class="features-grid animate-fadeup delay-3">

                <div class="feature-card featured">
                    <div class="feature-icon">📊</div>
                    <div class="feature-title">Live Analytics Dashboard</div>
                    <div class="feature-desc">
                        Real-time metrics on attendance, headcount, leave statistics, and monthly
                        payroll costs visualised with Chart.js. At-a-glance KPIs so HR managers
                        always have the full picture without opening a single report.
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <div class="feature-title">Employee Management</div>
                    <div class="feature-desc">
                        Full employee profiles with department and position assignment,
                        profile photos, and soft-delete termination flow.
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🕐</div>
                    <div class="feature-title">Attendance Tracking</div>
                    <div class="feature-desc">
                        Daily check-in and check-out with automatic late detection.
                        Monthly reports with present, late, and absent counts.
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🏖️</div>
                    <div class="feature-title">Leave Management</div>
                    <div class="feature-desc">
                        Configurable leave types with balance tracking, approval workflows,
                        and instant notifications to HR managers.
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <div class="feature-title">Payroll System</div>
                    <div class="feature-desc">
                        Automated salary calculation with allowances, deductions, and
                        absence adjustments. One-click PDF payslip generation.
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <div class="feature-title">Performance Reviews</div>
                    <div class="feature-desc">
                        Scored evaluations with period tracking, strengths, and improvement
                        areas. Full review history per employee.
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📄</div>
                    <div class="feature-title">Reports & Exports</div>
                    <div class="feature-desc">
                        Employee, payroll, and attendance reports exportable as PDF or Excel
                        with a single click.
                    </div>
                </div>

            </div>
        </section>

        <!-- Roles -->
        <section class="section" id="roles" style="padding-top: 0;">
            <span class="section-label animate-fadeup">Access control</span>
            <h2 class="section-title animate-fadeup delay-1">Three roles, clear boundaries</h2>
            <p class="section-sub animate-fadeup delay-2">
                Every action is gated by role. Employees see only what they need.
                Admins control everything.
            </p>

            <div class="roles-grid animate-fadeup delay-3">
                <div class="role-card admin">
                    <div class="role-tag">Admin</div>
                    <div class="role-name">System Admin</div>
                    <ul class="role-perms">
                        <li><span class="perm-dot"></span>Full system access</li>
                        <li><span class="perm-dot"></span>Manage departments & positions</li>
                        <li><span class="perm-dot"></span>Generate & approve payroll</li>
                        <li><span class="perm-dot"></span>Permanent employee deletion</li>
                        <li><span class="perm-dot"></span>View all reports & audit logs</li>
                    </ul>
                </div>

                <div class="role-card hr">
                    <div class="role-tag">HR Manager</div>
                    <div class="role-name">HR Manager</div>
                    <ul class="role-perms">
                        <li><span class="perm-dot"></span>Manage employee profiles</li>
                        <li><span class="perm-dot"></span>Review attendance records</li>
                        <li><span class="perm-dot"></span>Approve or reject leave requests</li>
                        <li><span class="perm-dot"></span>Submit performance reviews</li>
                        <li><span class="perm-dot"></span>Export HR reports</li>
                    </ul>
                </div>

                <div class="role-card employee">
                    <div class="role-tag">Employee</div>
                    <div class="role-name">Employee</div>
                    <ul class="role-perms">
                        <li><span class="perm-dot"></span>View own profile</li>
                        <li><span class="perm-dot"></span>Check in & check out daily</li>
                        <li><span class="perm-dot"></span>Submit leave requests</li>
                        <li><span class="perm-dot"></span>Download own payslips</li>
                        <li><span class="perm-dot"></span>View attendance history</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="cta-section">
            <div class="cta-box">
                <h2 class="cta-title">Ready to get started?</h2>
                <p class="cta-sub">Sign in with one of the demo accounts below to explore the system.</p>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary" style="font-size: 16px; padding: 16px 36px;">
                        Open Dashboard <span class="btn-arrow">→</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary" style="font-size: 16px; padding: 16px 36px;">
                        Sign in to HRMS <span class="btn-arrow">→</span>
                    </a>
                @endauth

                <div class="cta-credentials">
                    <div class="cred-item">
                        <strong>Admin</strong><br />
                        admin@hrms.com / password
                    </div>
                    <div class="cred-divider"></div>
                    <div class="cred-item">
                        <strong>HR Manager</strong><br />
                        hr@hrms.com / password
                    </div>
                    <div class="cred-divider"></div>
                    <div class="cred-item">
                        <strong>Employee</strong><br />
                        emp1@hrms.com / password
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="footer-logo">HRMS</div>
            <div class="footer-copy">Built with Laravel 11 · PHP 8.2 · TailwindCSS · MySQL</div>
        </footer>

    </div>

</body>

</html>
