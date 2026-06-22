<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiX POS</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/pageImg/5646546523465 - Copy.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
        :root {
            --color-primary: #004080;
            --color-primary-dark: #002d5a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333333;
            padding: 2rem;
        }

        .background-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.04;
            background-image:
                radial-gradient(circle at 25% 25%, var(--color-primary) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, var(--color-primary) 1px, transparent 1px);
            background-size: 60px 60px, 40px 40px;
            background-position: 0 0, 30px 30px;
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 64, 128, 0.1);
            border-radius: 24px;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            box-shadow:
                0 20px 60px rgba(0, 64, 128, 0.12),
                0 0 0 1px rgba(0, 64, 128, 0.05);
            animation: slideUp 0.8s ease-out;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Side - Logo Section */
        .logo-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            border-radius: 24px 0 0 24px;
            position: relative;
            overflow: hidden;
        }

        .logo-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 70% 70%, rgba(255, 255, 255, 0.04) 0%, transparent 50%);
            z-index: 1;
        }

        .logo-content {
            position: relative;
            z-index: 2;
            text-align: center;
            animation: fadeIn 1.2s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 300px;
            height: 300px;
            border-radius: 24px;
            margin: 0 auto 2rem;
            transition: transform 0.3s ease;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
        }


        .company-name {
            font-family: 'Poppins', sans-serif;
            font-size: 40px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: 2px;
        }

        .company-sub {
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        .tagline {
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 400;
            max-width: 260px;
            line-height: 1.6;
        }

        .decorative-line {
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, rgba(255,255,255,0.8), transparent);
            margin: 1.5rem auto;
        }

        /* Right Side - Form Section */
        .form-section {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-title {
            font-family: 'Poppins', sans-serif;
            font-size: 30px;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-family: 'DM Sans', sans-serif;
            color: #888888;
            font-size: 15px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1.6rem;
        }

        .form-label {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 0.7rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .required {
            color: #ff4757;
            margin-left: 4px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 16px 22px;
            background: #f8fafc;
            border: 2px solid rgba(0, 64, 128, 0.15);
            border-radius: 14px;
            color: #333333;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 400;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: rgba(0, 64, 128, 0.35);
        }

        /* Mask text input to look like password without browser icons */
        .password-masked {
            -webkit-text-security: disc;
            font-family: 'DM Sans', sans-serif;
        }
        .password-visible {
            -webkit-text-security: none;
        }

        .form-input:focus {
            border-color: var(--color-primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 64, 128, 0.08);
            transform: translateY(-2px);
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(0, 64, 128, 0.5);
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
            padding: 6px;
            border-radius: 8px;
        }

        .password-toggle:hover {
            color: var(--color-primary);
            background: rgba(0, 64, 128, 0.08);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            accent-color: var(--color-primary);
            cursor: pointer;
        }

        .checkbox-label {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: #666666;
            cursor: pointer;
            user-select: none;
        }

        .login-button {
            width: 100%;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #ffffff;
            border: none;
            padding: 18px;
            border-radius: 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 8px 24px rgba(0, 64, 128, 0.25);
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 35px rgba(0, 64, 128, 0.35);
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:active {
            transform: translateY(-1px);
        }

        .footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0, 64, 128, 0.08);
        }

        .footer-text {
            font-family: 'DM Sans', sans-serif;
            color: #aaaaaa;
            font-size: 12px;
            text-align: center;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
                align-items: flex-start;
            }

            .login-container {
                grid-template-columns: 1fr;
                max-width: 100%;
                min-height: auto;
                border-radius: 20px;
            }

            .logo-section {
                border-radius: 20px 20px 0 0;
                padding: 2.5rem 2rem;
            }

            .logo {
                width: 140px;
                height: 140px;
                margin-bottom: 1.2rem;
            }

            .company-name {
                font-size: 32px;
                margin-bottom: 0.3rem;
            }

            .company-sub {
                font-size: 11px;
                margin-bottom: 1.2rem;
            }

            .tagline {
                font-size: 13px;
                max-width: 280px;
            }

            .decorative-line {
                margin: 1rem auto;
            }

            .form-section {
                padding: 2rem 1.5rem;
                border-radius: 0 0 20px 20px;
            }

            .login-header {
                margin-bottom: 1.8rem;
            }

            .login-title {
                font-size: 26px;
            }

            .login-subtitle {
                font-size: 14px;
            }

            .form-input {
                padding: 14px 18px;
                font-size: 14px;
            }

            .login-button {
                padding: 16px;
                font-size: 14px;
            }
        }

        /* Loading animation */
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>

    <div class="login-container">
        <!-- Left Side - Logo Section -->
        <div class="logo-section">
            <div class="logo-content">
                <div class="logo">
                    <img src="{{ asset('assets/img/pageImg/78678687687.png') }}" alt="OptiX Logo">
                </div>
                
                <div class="decorative-line"></div>
                <div class="tagline">Smart, fast and reliable — your all-in-one POS solution built for modern businesses.</div>
            </div>
        </div>

        <!-- Right Side - Form Section -->
        <div class="form-section">
            <div class="login-header">
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to access your account</p>
            </div>

            <form class="login-form" action="{{ route('login.post') }}" method="POST" id="loginForm">
                {{ csrf_field() }}
                <div class="form-group">
                    <label class="form-label" for="email">
                        Email Address <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="Enter your email address"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        Password <span class="required">*</span>
                    </label>
                    <div class="input-wrapper password-wrapper">
                        <input
                            type="text"
                            id="password"
                            name="password"
                            class="form-input password-masked"
                            placeholder="Enter your password"
                            required
                            autocomplete="off"
                            data-lpignore="true"
                            data-1p-ignore
                            data-bwignore
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                            <span id="eyeIcon">👁</span>
                        </button>
                    </div>
                </div>

                @if ($errors->any())
                    <div style="background:#fff0f0;border:1px solid #ffb3b3;color:#cc0000;padding:12px 16px;border-radius:10px;margin-bottom:1.2rem;font-size:14px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="login-button" id="submitBtn">
                    Sign In
                </button>
            </form>

            <div class="footer">
                <p class="footer-text">2025 &copy; All Rights Reserved | OptiX POS</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.classList.contains('password-masked')) {
                passwordInput.classList.replace('password-masked', 'password-visible');
                eyeIcon.textContent = '👁‍🗨';
            } else {
                passwordInput.classList.replace('password-visible', 'password-masked');
                eyeIcon.textContent = '👁';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Signing In...';
            submitBtn.disabled = true;

            setTimeout(() => {
                submitBtn.classList.remove('loading');
                submitBtn.textContent = 'Sign In';
                submitBtn.disabled = false;
            }, 3000);
        });

        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        window.addEventListener('load', function() {
            const container = document.querySelector('.login-container');
            container.style.animation = 'slideUp 0.8s ease-out';

            const formElements = document.querySelectorAll('.form-group, .form-options, .login-button');
            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.animation = `fadeIn 0.6s ease-out ${0.2 + index * 0.1}s both`;
            });
        });
    </script>
</body>
</html>
