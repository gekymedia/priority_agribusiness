<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name')); ?> - <?php echo $__env->yieldContent('title', 'Welcome'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --primary-light: #4caf50;
            --secondary: #ff9800;
            --accent: #8bc34a;
            --neutral: #f5f5f5;
            --neutral-dark: #e0e0e0;
            --text: #333333;
            --text-light: #666666;
            --card-shadow: 0 20px 60px rgba(0,0,0,0.1);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 24px;
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
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 5px;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 2rem;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.1) 0%, rgba(139, 195, 74, 0.05) 100%);
            z-index: -1;
        }

        .hero-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%232e7d32' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }

        /* Auth Card */
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .auth-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.15);
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--secondary));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .auth-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.3);
        }

        .auth-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Form Elements */
        .auth-card form {
            width: 100%;
        }

        .form-group-modern {
            margin-bottom: 1.8rem !important;
            width: 100% !important;
            position: relative;
        }

        .form-group-modern .invalid-feedback {
            display: block !important;
            margin-top: 0.5rem !important;
            color: var(--danger) !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
        }

        .form-label {
            font-weight: 600 !important;
            color: var(--text) !important;
            margin-bottom: 0.8rem !important;
            font-size: 0.95rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            width: 100% !important;
        }

        .form-label i {
            color: var(--primary) !important;
            font-size: 1rem !important;
            width: 20px !important;
            text-align: center !important;
            flex-shrink: 0 !important;
        }

        .form-control-modern,
        input.form-control-modern,
        input[type="email"].form-control-modern,
        input[type="password"].form-control-modern,
        input[type="text"].form-control-modern {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 2px solid var(--neutral-dark) !important;
            border-radius: 12px !important;
            padding: 14px 20px !important;
            font-size: 1rem !important;
            transition: var(--transition) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
            width: 100% !important;
            display: block !important;
            color: var(--text) !important;
            outline: none !important;
            box-sizing: border-box !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }

        .form-control-modern:focus,
        input.form-control-modern:focus,
        input[type="email"].form-control-modern:focus,
        input[type="password"].form-control-modern:focus,
        input[type="text"].form-control-modern:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 6px 25px rgba(46, 125, 50, 0.2) !important;
            transform: translateY(-2px) !important;
            background: rgba(255, 255, 255, 1) !important;
            outline: none !important;
        }

        .form-control-modern::placeholder,
        input.form-control-modern::placeholder {
            color: var(--text-light) !important;
            opacity: 0.7 !important;
        }

        .form-control-modern.is-invalid,
        input.form-control-modern.is-invalid {
            border-color: #f44336 !important;
            background: rgba(244, 67, 54, 0.05) !important;
        }

        .form-control-modern.is-invalid:focus,
        input.form-control-modern.is-invalid:focus {
            border-color: #f44336 !important;
            box-shadow: 0 6px 25px rgba(244, 67, 54, 0.2) !important;
        }

        /* Override Bootstrap defaults */
        .auth-card .form-control,
        .auth-card input[type="text"],
        .auth-card input[type="email"],
        .auth-card input[type="password"] {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 2px solid var(--neutral-dark) !important;
            border-radius: 12px !important;
            padding: 14px 20px !important;
        }

        /* Auth Buttons */
        .btn-auth {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none;
            color: white;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: var(--transition);
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .btn-auth:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(46, 125, 50, 0.4);
        }

        .btn-auth:active {
            transform: translateY(-1px);
        }

        .btn-auth-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: var(--transition);
            width: 100%;
        }

        .btn-auth-outline:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(46, 125, 50, 0.2);
        }

        /* Links */
        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            position: relative;
            display: inline-block;
        }

        .auth-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        .auth-link:hover {
            color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .auth-link:hover::after {
            width: 100%;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: var(--text-light);
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--neutral-dark), transparent);
        }

        .divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin: 0 auto 1.5rem;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.8rem;
        }

        .feature-text {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Footer */
        .auth-footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0,0,0,0.1);
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-in {
            animation: fadeIn 0.8s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 1rem;
            }
            
            .auth-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .auth-title {
                font-size: 2rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Theme Toggle for Guest */
        .guest-theme-toggle {
            position: fixed;
            top: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 100;
            transition: var(--transition);
        }

        .guest-theme-toggle:hover {
            transform: scale(1.1) rotate(180deg);
        }
    </style>
</head>
<body>
    <!-- Theme Toggle -->
    <div class="guest-theme-toggle" id="guestThemeToggle">
        <i class="fas fa-moon"></i>
    </div>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-bg"></div>
        <div class="hero-bg-pattern"></div>
        
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="auth-card animate-in">
                        <div class="auth-header">
                            <div class="auth-logo animate-float">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <h1 class="auth-title"><?php echo e(config('app.name')); ?></h1>
                            <p class="auth-subtitle">
                                <?php echo $__env->yieldContent('subtitle', 'Modern Agricultural Management System'); ?>
                            </p>
                        </div>

                        <?php if(session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" 
                                 style="border-radius: 12px; border: none; background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.05) 100%);">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo e(session('success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
                                 style="border-radius: 12px; border: none;">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo e($errors->first()); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php echo $__env->yieldContent('auth-content'); ?>

                        <div class="auth-footer">
                            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.</p>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <?php if (! empty(trim($__env->yieldContent('show-features')))): ?>
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-tractor"></i>
                            </div>
                            <h3 class="feature-title">Farm Management</h3>
                            <p class="feature-text">Track and manage all your farms efficiently with real-time monitoring.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-dove"></i>
                            </div>
                            <h3 class="feature-title">Poultry Tracking</h3>
                            <p class="feature-text">Monitor bird batches, health, and productivity all in one place.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h3 class="feature-title">Crop Planning</h3>
                            <p class="feature-text">Plan and track your planting schedules and field activities.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('guestThemeToggle');
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
            this.style.transform = 'scale(1.2) rotate(180deg)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 300);
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';

        // Form focus effects
        const formInputs = document.querySelectorAll('.form-control-modern');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation to feature cards on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.feature-card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html><?php /**PATH D:\projects\priority_agribusiness\resources\views/layouts/guest.blade.php ENDPATH**/ ?>