<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #dde3f0;
                font-family: 'DM Sans', sans-serif;
                padding: 24px;
            }

            .login-wrapper {
                width: 100%;
                display: flex;
                justify-content: center;
            }

            .login-card {
                background: #ffffff;
                border-radius: 20px;
                box-shadow: 0 8px 32px rgba(108, 92, 231, 0.10);
                padding: 48px 40px 40px;
                width: 100%;
                max-width: 440px;
                animation: fadeUp .45s ease both;
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(18px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            .login-card h1 {
                font-family: 'Playfair Display', serif;
                font-size: 1.8rem;
                color: #1a1a2e;
                text-align: center;
                margin-bottom: 8px;
            }

            .subtitle {
                font-size: 0.9rem;
                color: #6b7280;
                text-align: center;
                margin-bottom: 32px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-size: 0.875rem;
                font-weight: 500;
                color: #1a1a2e;
                margin-bottom: 8px;
            }

            .form-group input {
                width: 100%;
                padding: 13px 16px;
                background: #f0f3fa;
                border: 1.5px solid #e2e8f0;
                border-radius: 12px;
                font-family: 'DM Sans', sans-serif;
                font-size: 0.95rem;
                color: #1a1a2e;
                outline: none;
                transition: border-color .2s, box-shadow .2s, background .2s;
            }

            .form-group input::placeholder { color: #adb5bd; }

            .form-group input:focus {
                border-color: #7c6ef5;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(124, 110, 245, 0.15);
            }

            .btn-login {
                display: block;
                width: 100%;
                padding: 14px;
                margin-top: 8px;
                background: #6c5ce7;
                color: #ffffff;
                border: none;
                border-radius: 12px;
                font-family: 'DM Sans', sans-serif;
                font-size: 1rem;
                font-weight: 500;
                cursor: pointer;
                transition: background .2s, transform .15s, box-shadow .2s;
            }

            .btn-login:hover {
                background: #5a4ed1;
                box-shadow: 0 4px 18px rgba(108, 92, 231, 0.35);
                transform: translateY(-1px);
            }

            .btn-login:active { transform: translateY(0); }

            .register-text {
                text-align: center;
                margin-top: 24px;
                font-size: 0.875rem;
                color: #6b7280;
            }

            .register-text a {
                color: #6c5ce7;
                font-weight: 500;
                text-decoration: none;
            }

            .register-text a:hover { text-decoration: underline; }

            .btn-back {
                display: inline-block;
                margin-bottom: 20px;
                font-size: 0.875rem;
                color: #6c5ce7;
                font-weight: 500;
                text-decoration: none;
                transition: color .2s, transform .2s;
            }

            .btn-back:hover {
                color: #5a4ed1;
                transform: translateX(-3px);
            }
        </style>
    </head>
    <body>
        {{ $slot }}
    </body>
</html>
