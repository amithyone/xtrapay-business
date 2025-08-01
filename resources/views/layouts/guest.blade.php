<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Xtrabusiness') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Custom Auth Styles -->
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
        
        <style>
            :root {
                --bg-dark: #171716;
                --bg-card: #2C2C2E;
                --neon-red: #FF3B30;
                --neon-white: #FFFFFF;
                --text-primary: #FFFFFF;
                --text-secondary: #A1A1AA;
                --border-color: #3C3C3E;
            }

            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: 'Poppins', sans-serif;
                background-color: var(--bg-dark);
                background-image: linear-gradient(135deg, #171716 0%, #1a1a1a 50%, #171716 100%);
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                color: var(--text-primary);
                min-height: 100vh;
                min-height: 100dvh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            .gradient-text {
                background: linear-gradient(135deg, var(--neon-red), var(--neon-white));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            /* Background decoration - keeping your animated background */
            .bg-decoration {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                overflow: hidden;
                pointer-events: none;
                z-index: -1;
            }

            .bg-decoration::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 59, 48, 0.1) 0%, transparent 70%);
                animation: float 6s ease-in-out infinite;
            }

            .bg-decoration::after {
                content: '';
                position: absolute;
                bottom: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
                animation: float 8s ease-in-out infinite reverse;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(180deg); }
            }

            /* Container styles inspired by the provided design */
            .limiter {
                width: 100%;
                margin: 0 auto;
            }

            .container-login100 {
                width: 100%;
                min-height: 100vh;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                padding: 15px;
                position: relative;
            }

            .wrap-login100 {
                width: 100%;
                max-width: 400px;
                background: rgba(44, 44, 46, 0.95);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(60, 60, 62, 0.3);
                box-shadow: 
                    0 25px 50px rgba(0, 0, 0, 0.5),
                    0 0 0 1px rgba(255, 255, 255, 0.05),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                padding: 2.5rem;
                position: relative;
                overflow: hidden;
            }

            .wrap-login100::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, var(--neon-red), transparent);
            }

            .login100-form-title {
                font-size: 2rem;
                font-weight: 700;
                text-align: center;
                margin-bottom: 2rem;
                color: var(--text-primary);
            }

            /* Input wrapper styles */
            .wrap-input100 {
                position: relative;
                margin-bottom: 1.5rem;
            }

            .label-input100 {
                font-size: 0.875rem;
                font-weight: 500;
                color: var(--text-secondary);
                margin-bottom: 0.5rem;
                display: block;
            }

            .input100 {
                width: 100%;
                background: rgba(28, 28, 30, 0.8);
                border: 1px solid rgba(60, 60, 62, 0.5);
                border-radius: 20px;
                padding: 1rem 1rem 1rem 3.5rem;
                color: white;
                font-size: 1rem;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                box-sizing: border-box;
            }

            .input100:focus {
                border-color: var(--neon-red);
                box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.1);
                outline: none;
            }

            .input100::placeholder {
                color: rgba(161, 161, 170, 0.6);
            }

            /* Focus input icon */
            .focus-input100 {
                position: absolute;
                left: 1.25rem;
                top: 50%;
                transform: translateY(-50%);
                color: var(--text-secondary);
                font-size: 1.125rem;
                transition: all 0.3s ease;
                z-index: 2;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 20px;
                height: 20px;
                line-height: 1;
            }

            .focus-input100 i {
                padding-top: 30px;
                display: block;
                line-height: 1;
            }

            .wrap-input100:focus-within .focus-input100 {
                color: var(--neon-red);
            }

            /* Button styles */
            .container-login100-form-btn {
                width: 100%;
                margin-top: 2rem;
            }

            .wrap-login100-form-btn {
                position: relative;
                width: 100%;
            }

            .login100-form-bgbtn {
                position: absolute;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, var(--neon-red), var(--neon-white));
                border-radius: 20px;
                transition: all 0.3s ease;
            }

            .login100-form-btn {
                position: relative;
                width: 100%;
                background: transparent;
                border: none;
                border-radius: 20px;
                padding: 1rem 1.5rem;
                color: white;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                z-index: 1;
            }

            .wrap-login100-form-btn:hover .login100-form-bgbtn {
                transform: scale(1.02);
                box-shadow: 0 8px 25px rgba(255, 59, 48, 0.4);
            }

            /* Text styles */
            .txt1 {
                font-size: 0.875rem;
                color: var(--text-secondary);
                text-align: center;
                margin: 1.5rem 0;
            }

            .txt2 {
                font-size: 0.875rem;
                color: var(--neon-red);
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            .txt2:hover {
                color: var(--neon-white);
            }

            /* Logo container */
            .logo-container {
                text-align: center;
                margin-bottom: 2rem;
            }

            .logo-container img {
                height: 60px;
                width: auto;
                filter: brightness(0) invert(1);
            }

            /* Responsive */
            @media (max-width: 576px) {
                body {
                    min-height: 100vh;
                    min-height: 100dvh;
                }
                
                .bg-decoration {
                    width: 100vw;
                    height: 100vh;
                    height: 100dvh;
                }
                
                .wrap-login100 {
                    padding: 1.5rem 1.25rem;
                    max-width: 350px;
                    border-radius: 16px;
                }
                
                .logo-container img {
                    height: 40px;
                    width: auto;
                }
                
                .logo-container {
                    margin-bottom: 1.5rem;
                }
                
                .login100-form-title {
                    font-size: 1.5rem;
                    margin-bottom: 1.5rem;
                }
                
                .container-login100 {
                    padding: 10px;
                }
                
                .input100 {
                    padding: 0.875rem 0.875rem 0.875rem 3rem;
                    font-size: 0.9rem;
                }
                
                .focus-input100 {
                    left: 1rem;
                    font-size: 1rem;
                }
                
                .wrap-input100 {
                    margin-bottom: 1.25rem;
                }
                
                .container-login100-form-btn {
                    margin-top: 1.5rem;
                }
                
                .txt1 {
                    margin: 1rem 0;
                }
            }
            
            @media (max-width: 480px) {
                .wrap-login100 {
                    padding: 1.25rem 1rem;
                    max-width: 320px;
                    border-radius: 14px;
                }
                
                .logo-container img {
                    height: 35px;
                    width: auto;
                }
                
                .logo-container {
                    margin-bottom: 1.25rem;
                }
                
                .login100-form-title {
                    font-size: 1.25rem;
                    margin-bottom: 1.25rem;
                }
                
                .input100 {
                    padding: 0.75rem 0.75rem 0.75rem 2.75rem;
                    font-size: 0.875rem;
                }
                
                .focus-input100 {
                    left: 0.875rem;
                    font-size: 0.9rem;
                }
                
                .wrap-input100 {
                    margin-bottom: 1rem;
                }
                
                .container-login100-form-btn {
                    margin-top: 1.25rem;
                }
                
                .login100-form-btn {
                    padding: 0.875rem 1.25rem;
                    font-size: 0.9rem;
                }
                
                .txt1 {
                    margin: 0.75rem 0;
                    font-size: 0.8rem;
                }
                
                .txt2 {
                    font-size: 0.8rem;
                }
            }
            
            @media (max-width: 360px) {
                .wrap-login100 {
                    padding: 1rem 0.75rem;
                    max-width: 300px;
                    border-radius: 12px;
                }
                
                .logo-container img {
                    height: 30px;
                    width: auto;
                }
                
                .logo-container {
                    margin-bottom: 1rem;
                }
                
                .login100-form-title {
                    font-size: 1.125rem;
                    margin-bottom: 1rem;
                }
                
                .input100 {
                    padding: 0.625rem 0.625rem 0.625rem 2.5rem;
                    font-size: 0.8rem;
                }
                
                .focus-input100 {
                    left: 0.75rem;
                    font-size: 0.8rem;
                }
                
                .wrap-input100 {
                    margin-bottom: 0.875rem;
                }
                
                .container-login100-form-btn {
                    margin-top: 1rem;
                }
                
                .login100-form-btn {
                    padding: 0.75rem 1rem;
                    font-size: 0.8rem;
                }
                
                .txt1 {
                    margin: 0.5rem 0;
                    font-size: 0.75rem;
                }
                
                .txt2 {
                    font-size: 0.75rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Background decoration - keeping your animated background -->
        <div class="bg-decoration"></div>

        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <!-- Logo -->
                    <div class="logo-container">
                        <img src="{{ asset('build/assets/logo.svg') }}" alt="Xtrabusiness Logo" />
                    </div>

                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html> 