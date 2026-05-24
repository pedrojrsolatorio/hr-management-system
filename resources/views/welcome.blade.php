<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HRMS — Human Resource Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:     #0d0d0f;
            --ink-2:   #1c1c20;
            --ink-3:   #2e2e35;
            --muted:   #6b6b78;
            --faint:   #a8a8b3;
            --line:    rgba(255,255,255,0.08);
            --accent:  #5b6af0;
            --accent2: #8b5cf6;
            --glow:    rgba(91,106,240,0.35);
            --white:   #ffffff;
            --surface: rgba(255,255,255,0.04);
            --surface2:rgba(255,255,255,0.07);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--ink);
            color: var(--white);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Background ── */
        .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 64px 64px;
        }

        .bg-orb-1 {
            position: absolute;
            width: 700px; height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(91,106,240,0.18) 0%, transparent 70%);
            top: -200px; right: -100px;
            animation: drift1 12s ease-in-out infinite alternate;
        }

        .bg-orb-2 {
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139,92,246,0.14) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            animation: drift2 15s ease-in-out infinite alternate;
        }

        .bg-orb-3 {
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(91,106,240,0.10) 0%, transparent 70%);
            top: 40%; left: 30%;
            animation: drift3 18s ease-in-out infinite alternate;
        }

        @keyframes drift1 { from { transform: translate(0,0) scale(1); } to { transform: translate(40px, 60px) scale(1.1); } }
        @keyframes drift2 { from { transform: translate(0,0) scale(1); } to { transform: translate(-30px, -50px) scale(1.08); } }
        @keyframes drift3 { from { transform: translate(0,0); } to { transform: translate(50px, -40px); } }

        /* ── Noise overlay ── */
        .bg-layer::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            opacity: 0.4;
        }

        /* ── Layout ── */
        .page { position: relative; z-index: 1; }

        /* ── Nav ── */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 28px 48px;
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(13,13,15,0.7);
        }

        .nav-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-logo-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            color: var(--faint);
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            padding: 8px 16px;
            border-radius: 8px;
            transition: color 0.2s, background 0.2s;
        }

        .nav-link:hover { color: var(--white); background: var(--surface2); }

        .btn-nav {
            background: var(--white);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            padding: 9px 20px;
            border-radius: 9px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .btn-nav:hover { opacity: 0.88; transform: translateY(-1px); }

        /* ── Hero ── */
        .hero {
            min-height: calc(100vh - 89px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 48px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 100px;
            padding: 6px 14px 6px 8px;
            font-size: 12px;
            color: var(--faint);
            margin-bottom: 36px;
            animation: fadeUp 0.6s ease both;
        }

        .hero-badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 8px #4ade80;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.85); }
        }

        .hero-title {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(48px, 7vw, 88px);
            line-height: 1.0;
            letter-spacing: -3px;
            max-width: 900px;
            margin-bottom: 28px;
            animation: fadeUp 0.6s 0.1s ease both;
        }

        .hero-title-accent {
            background: linear-gradient(135deg, #818cf8, #a78bfa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: 18px;
            font-weight: 300;
            color: var(--faint);
            max-width: 520px;
            line-height: 1.7;
            margin-bottom: 48px;
            animation: fadeUp 0.6s 0.2s ease both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 0 32px var(--glow);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 0 48px var(--glow);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--surface);
            color: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 400;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            border: 1px solid var(--line);
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-secondary:hover {
            background: var(--surface2);
            transform: translateY(-2px);
        }

        .btn-arrow {
            font-size: 16px;
            transition: transform 0.2s;
        }

        .btn-primary:hover .btn-arrow,
        .btn-secondary:hover .btn-arrow { transform: translateX(3px); }

        /* ── Stats strip ── */
        .stats-strip {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-top: 80px;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            animation: fadeUp 0.6s 0.4s ease both;
        }

        .stat-item {
            flex: 1;
            max-width: 200px;
            padding: 32px 24px;
            text-align: center;
            border-right: 1px solid var(--line);
        }

        .stat-item:last-child { border-right: none; }

        .stat-number {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 32px;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--white), var(--faint));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── Features ── */
        .section {
            padding: 120px 48px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-label {
            display: inline-block;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--accent);
            margin-bottom: 16px;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: clamp(32px, 4vw, 48px);
            letter-spacing: -1.5px;
            line-height: 1.1;
            max-width: 560px;
            margin-bottom: 16px;
        }

        .section-sub {
            font-size: 16px;
            color: var(--faint);
            max-width: 480px;
            line-height: 1.7;
            font-weight: 300;
            margin-bottom: 64px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 32px;
            transition: background 0.3s, border-color 0.3s, transform 0.2s;
            cursor: default;
        }

        .feature-card:hover {
            background: var(--surface2);
            border-color: rgba(91,106,240,0.3);
            transform: translateY(-4px);
        }

        .feature-card.featured {
            background: linear-gradient(135deg, rgba(91,106,240,0.15), rgba(139,92,246,0.10));
            border-color: rgba(91,106,240,0.25);
            grid-column: span 2;
        }

        .feature-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, rgba(91,106,240,0.2), rgba(139,92,246,0.2));
            border: 1px solid rgba(91,106,240,0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .feature-title {
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            font-size: 17px;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .feature-desc {
            font-size: 14px;
            color: var(--faint);
            line-height: 1.65;
            font-weight: 300;
        }

        /* ── Roles section ── */
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 0;
        }

        .role-card {
            border-radius: 16px;
            padding: 36px 32px;
            border: 1px solid var(--line);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .role-card:hover { transform: translateY(-4px); }

        .role-card.admin {
            background: linear-gradient(135deg, rgba(91,106,240,0.12), rgba(91,106,240,0.04));
            border-color: rgba(91,106,240,0.2);
        }

        .role-card.hr {
            background: linear-gradient(135deg, rgba(139,92,246,0.12), rgba(139,92,246,0.04));
            border-color: rgba(139,92,246,0.2);
        }

        .role-card.employee {
            background: linear-gradient(135deg, rgba(20,184,166,0.12), rgba(20,184,166,0.04));
            border-color: rgba(20,184,166,0.2);
        }

        .role-tag {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 4px 10px;
            border-radius: 100px;
            margin-bottom: 20px;
        }

        .role-card.admin .role-tag { background: rgba(91,106,240,0.2); color: #818cf8; }
        .role-card.hr .role-tag { background: rgba(139,92,246,0.2); color: #c084fc; }
        .role-card.employee .role-tag { background: rgba(20,184,166,0.2); color: #5eead4; }

        .role-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 22px;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .role-perms {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .role-perms li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--faint);
            font-weight: 300;
        }

        .perm-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .role-card.admin .perm-dot { background: #818cf8; }
        .role-card.hr .perm-dot { background: #c084fc; }
        .role-card.employee .perm-dot { background: #5eead4; }

        /* ── CTA ── */
        .cta-section {
            padding: 80px 48px 120px;
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(91,106,240,0.12), rgba(139,92,246,0.08));
            border: 1px solid rgba(91,106,240,0.2);
            border-radius: 24px;
            padding: 72px 48px;
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -60px; left: 50%;
            transform: translateX(-50%);
            width: 400px; height: 200px;
            background: radial-gradient(ellipse, rgba(91,106,240,0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-title {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(32px, 4vw, 52px);
            letter-spacing: -2px;
            margin-bottom: 16px;
        }

        .cta-sub {
            font-size: 16px;
            color: var(--faint);
            margin-bottom: 40px;
            font-weight: 300;
        }

        .cta-credentials {
            display: inline-flex;
            gap: 24px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 16px 28px;
            margin-top: 32px;
            font-size: 13px;
            color: var(--faint);
        }

        .cred-item strong {
            color: var(--white);
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
        }

        .cred-divider {
            width: 1px;
            background: var(--line);
            align-self: stretch;
        }

        /* ── Footer ── */
        footer {
            border-top: 1px solid var(--line);
            padding: 28px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 15px;
            color: var(--muted);
        }

        .footer-copy {
            font-size: 13px;
            color: var(--muted);
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeup { animation: fadeUp 0.6s ease both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            nav { padding: 20px 24px; }
            .nav-links .nav-link { display: none; }
            .hero { padding: 60px 24px; }
            .section { padding: 80px 24px; }
            .features-grid { grid-template-columns: 1fr; }
            .feature-card.featured { grid-column: span 1; }
            .roles-grid { grid-template-columns: 1fr; }
            .stats-strip { flex-wrap: wrap; }
            .stat-item { border-right: none; border-bottom: 1px solid var(--line); max-width: 100%; }
            .cta-section { padding: 40px 24px 80px; }
            .cta-credentials { flex-direction: column; gap: 12px; }
            .cred-divider { display: none; }
            footer { flex-direction: column; gap: 8px; text-align: center; }
        }
    </style>
</head>
<body>

<!-- Background -->
<div class="bg-layer">
    <div class="bg-grid"></div>
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
                <a href="{{ route('login') }}" class="nav-link">Sign in</a>
                <a href="{{ route('login') }}" class="btn-nav">Get started →</a>
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