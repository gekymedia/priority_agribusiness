<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name')); ?> - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --primary-light: #4caf50;
            --secondary: #ff9800;
            --accent: #8bc34a;
            --accent-light: #c5e1a5;
            --neutral: #f5f5f5;
            --neutral-dark: #e0e0e0;
            --text: #333333;
            --text-light: #666666;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --card-shadow: 0 10px 40px rgba(0,0,0,0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 16px;
            --sidebar-width: 280px;
            --header-height: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            color: var(--text);
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }


        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
            backdrop-filter: blur(20px);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            transition: var(--transition);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(46, 125, 50, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 0;
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
        }

        .sidebar-brand {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-brand-bottom {
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(46, 125, 50, 0.1);
        }

        .sidebar-menu {
            padding: 1.5rem 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-menu-item {
            margin: 0.5rem 1rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            color: var(--text);
            text-decoration: none;
            border-radius: 12px;
            transition: var(--transition);
            font-weight: 600;
            position: relative;
        }

        .sidebar-link i {
            width: 24px;
            text-align: center;
            font-size: 18px;
            color: var(--text-light);
            transition: var(--transition);
        }

        .sidebar-link:hover {
            background: rgba(46, 125, 50, 0.08);
            color: var(--primary);
            transform: translateX(5px);
        }

        .sidebar-link:hover i {
            color: var(--primary);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.15) 0%, rgba(139, 195, 74, 0.1) 100%);
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.15);
        }

        .sidebar-link.active i {
            color: var(--primary);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: linear-gradient(180deg, var(--primary), var(--accent));
            border-radius: 0 4px 4px 0;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(46, 125, 50, 0.1);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            background: rgba(46, 125, 50, 0.05);
        }

        .sidebar-user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }

        .sidebar-user-info {
            flex: 1;
        }

        .sidebar-user-name {
            font-weight: 700;
            color: var(--text);
            font-size: 14px;
        }

        .sidebar-user-role {
            font-size: 12px;
            color: var(--text-light);
        }

        /* Main Content */
        .main-container {
            margin-left: var(--sidebar-width);
            padding: 2rem 3rem;
            min-height: 100vh;
            transition: var(--transition);
        }

        .mobile-menu-toggle {
            display: none;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 4px 0 30px rgba(0, 0, 0, 0.15);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-container {
                margin-left: 0;
                padding: 1rem;
            }

            .mobile-menu-toggle {
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                width: 50px;
                height: 50px;
                background: white;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                cursor: pointer;
                transition: var(--transition);
            }

            .mobile-menu-toggle:hover {
                transform: scale(1.1);
            }

            .mobile-menu-toggle i {
                font-size: 20px;
                color: var(--primary);
            }

            /* Overlay for mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2.5rem;
            position: relative;
        }

        .page-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            font-weight: 500;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }

        .form-label i {
            color: var(--primary);
            margin-right: 8px;
        }

        .form-control {
            border: 2px solid var(--neutral-dark);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.15);
        }

        .form-control:disabled {
            background-color: var(--neutral);
            opacity: 0.7;
        }

        /* Cards */
        .agri-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Profile page specific card styling */
        .profile-page .agri-card {
            height: auto;
            min-height: unset;
        }

        .profile-page .agri-card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .profile-page .row {
            display: flex;
            align-items: stretch;
        }

        .profile-page .col-lg-8,
        .profile-page .col-lg-4 {
            display: flex;
            flex-direction: column;
        }

        .agri-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }

        .agri-card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: none;
        }

        .agri-card-header h3 {
            font-weight: 700;
            margin: 0;
            font-size: 1.3rem;
        }

        .agri-card-body {
            padding: 2rem;
        }

        /* Stats Cards */
        .stat-card {
            padding: 2rem;
            border-radius: var(--border-radius);
            background: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 1rem;
            margin-top: 0.5rem;
            font-weight: 600;
        }

        /* Buttons */
        .btn-agri {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none;
            color: white;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-agri::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-agri:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.4);
        }

        .btn-agri:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Alerts */
        .alert-modern {
            border: none;
            border-radius: var(--border-radius);
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.05) 100%);
            border-left: 5px solid var(--success);
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            backdrop-filter: blur(10px);
        }

        /* Table Styling */
        .table-modern {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table-modern thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent-light) 100%);
            color: var(--text);
            border: none;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-modern tbody tr {
            transition: var(--transition);
        }

        .table-modern tbody tr:hover {
            background: rgba(46, 125, 50, 0.05);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            color: white;
            font-size: 24px;
        }

        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .navbar-collapse {
                position: fixed;
                top: var(--header-height);
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                padding: 2rem;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                border-radius: 0 0 var(--border-radius) var(--border-radius);
                z-index: 999;
            }

            .main-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 1000;
            transition: var(--transition);
        }

        .theme-toggle:hover {
            transform: scale(1.1) rotate(180deg);
        }

        /* Pagination Styling */
        .pagination {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--primary);
            background-color: white;
            border: 1px solid var(--neutral-dark);
            border-radius: 8px;
            transition: var(--transition);
            min-width: 38px;
            text-align: center;
        }

        .pagination .page-link:hover {
            color: white;
            background-color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
        }

        .pagination .page-item.active .page-link {
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: var(--text-light);
            background-color: var(--neutral);
            border-color: var(--neutral-dark);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <!-- Theme Toggle -->
    <div class="theme-toggle" id="themeToggle">
        <i class="fas fa-moon"></i>
    </div>

    <!-- Mobile Menu Toggle -->
    <div class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-seedling"></i>
            </div>
            <div class="sidebar-brand"><?php echo e(config('app.name')); ?></div>
        </div>

        <nav class="sidebar-menu">
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('farms.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('farms.*') ? 'active' : ''); ?>">
                    <i class="fas fa-tractor"></i>
                    <span>Farms</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('houses.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('houses.*') ? 'active' : ''); ?>">
                    <i class="fas fa-home"></i>
                    <span>Houses</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('batches.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('batches.*') ? 'active' : ''); ?>">
                    <i class="fas fa-dove"></i>
                    <span>Bird Batches</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('fields.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('fields.*') ? 'active' : ''); ?>">
                    <i class="fas fa-border-all"></i>
                    <span>Fields</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('plantings.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('plantings.*') ? 'active' : ''); ?>">
                    <i class="fas fa-leaf"></i>
                    <span>Plantings</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('tasks.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('tasks.*') ? 'active' : ''); ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Tasks</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('medication-calendars.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('medication-calendars.*') ? 'active' : ''); ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Medication Calendars</span>
                </a>
            </div>
            <?php
                $user = auth()->user();
                // Show Employees menu to:
                // 1. Regular Users (they can be admins)
                // 2. Employees with manager access or above
                $canManageEmployees = false;
                if ($user instanceof \App\Models\User) {
                    // Regular users can see it - middleware will handle access
                    $canManageEmployees = true;
                } elseif ($user instanceof \App\Models\Employee) {
                    // Employees need manager access or above
                    $canManageEmployees = $user->isManager();
                }
            ?>
            <?php if($canManageEmployees): ?>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('employees.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('employees.*') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i>
                    <span>Employees</span>
                </a>
            </div>
            <?php endif; ?>
            <div class="sidebar-menu-item" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(46, 125, 50, 0.1);">
                <a href="<?php echo e(route('egg-productions.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('egg-productions.*') ? 'active' : ''); ?>">
                    <i class="fas fa-egg"></i>
                    <span>Egg Production</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('egg-sales.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('egg-sales.*') ? 'active' : ''); ?>">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Egg Sales</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('bird-sales.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('bird-sales.*') ? 'active' : ''); ?>">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Bird Sales</span>
                </a>
            </div>
            <div class="sidebar-menu-item">
                <a href="<?php echo e(route('expenses.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('expenses.*') ? 'active' : ''); ?>">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Expenses</span>
                </a>
            </div>
            <div class="sidebar-menu-item" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(46, 125, 50, 0.1);">
                <a href="<?php echo e(route('profile.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                    <i class="fas fa-user-cog"></i>
                    <span>Profile Settings</span>
                </a>
            </div>
        </nav>

        <?php if(auth()->guard()->check()): ?>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    <?php
                        $user = auth()->user();
                        $name = $user instanceof \App\Models\Employee ? $user->full_name : $user->name;
                        $role = $user instanceof \App\Models\Employee ? ucfirst($user->access_level) : ucfirst($user->role ?? 'User');
                    ?>
                    <?php echo e(substr($name, 0, 1)); ?>

                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?php echo e($name); ?></div>
                    <div class="sidebar-user-role"><?php echo e($role); ?></div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px; border-radius: var(--border-radius); box-shadow: var(--card-shadow);">
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('profile.index')); ?>">
                                <i class="fas fa-user me-2"></i> Profile Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>" class="mb-0">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </aside>

    <!-- Main Content -->
    <main class="main-container animate-in">
        <?php if(session('success')): ?>
            <div class="alert alert-modern alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        if (mobileMenuToggle && sidebar) {
            mobileMenuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('show');
                }
            });
        }

        // Close sidebar when clicking overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 992) {
                if (sidebar && !sidebar.contains(event.target) && 
                    mobileMenuToggle && !mobileMenuToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('show');
                }
            }
        });

        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        
        themeToggle.addEventListener('click', function() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            themeIcon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            
            // Save preference to localStorage
            localStorage.setItem('theme', newTheme);
            
            // Animate toggle
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 300);
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';

        // Add animation to cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe all cards and stat items
        document.querySelectorAll('.agri-card, .stat-card').forEach(card => {
            observer.observe(card);
        });

        // Close sidebar when clicking on links on mobile
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    const overlay = document.getElementById('sidebarOverlay');
                    if (overlay) overlay.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html><?php /**PATH D:\projects\priority_agribusiness\resources\views/layouts/app.blade.php ENDPATH**/ ?>