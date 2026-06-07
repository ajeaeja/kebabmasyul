<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Masyul Kebab Kemitraan</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #2c1a1d, #111318 70%);
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            padding: 1.5rem;
            overflow-y: auto;
            position: relative;
            zoom: 80%;
        }

        /* Ambient Glowing Background Objects */
        .ambient-glow-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(238, 77, 45, 0.12) 0%, rgba(0,0,0,0) 70%);
            top: -200px;
            right: -100px;
            z-index: 0;
            pointer-events: none;
        }

        .ambient-glow-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 120, 45, 0.08) 0%, rgba(0,0,0,0) 70%);
            bottom: -150px;
            left: -100px;
            z-index: 0;
            pointer-events: none;
        }

        .login-container {
            z-index: 10;
            width: 100%;
            max-width: 450px;
            position: relative;
        }

        .card-login {
            background: rgba(25, 28, 36, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 2.75rem 2.25rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo h3 {
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .brand-logo span {
            color: #ee4d2d;
        }

        .brand-logo p {
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }

        .form-control-custom {
            background-color: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            box-shadow: 0 0 0 3px rgba(238, 77, 45, 0.25) !important;
            border-color: #ee4d2d !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #ee4d2d, #ff763b);
            color: #fff;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            transition: all 0.25s ease;
            box-shadow: 0 5px 15px rgba(238, 77, 45, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(238, 77, 45, 0.45);
            background: linear-gradient(135deg, #ff5e36, #ff8b54);
            color: #fff;
        }

        .checkbox-custom input {
            accent-color: #ee4d2d;
        }

        .alert-custom {
            background-color: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #f87171;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        /* Password Toggle styling */
        .input-group-custom .form-control-custom {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .input-group-custom .input-group-text {
            background-color: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: none !important;
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
            cursor: pointer;
        }

        /* Demo credentials note style */
        .demo-credentials {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="d-flex flex-column align-items-center justify-content-center min-vh-100 w-100 py-4" style="position: relative; z-index: 10;">
        <div class="login-container mb-4">
            <div class="card-login">
                <div class="brand-logo">
                    <h3>Masyul <span>Kebab</span></h3>
                    <p>Sistem Informasi Manajemen Kemitraan (RAD)</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-custom alert-dismissible fade show p-3 mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show p-3 mb-4" role="alert" style="border-radius: 12px; font-size: 0.85rem;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email" value="{{ old('email') }}" placeholder="admin@masyulkebab.com" required autocomplete="email" autofocus>
                    </div>

                     <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group input-group-custom">
                            <input type="password" class="form-control form-control-custom" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                            <span class="input-group-text text-muted" id="togglePassword">
                                <i class="bi bi-eye-slash-fill" id="togglePasswordIcon" style="color: #94a3b8;"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4 d-flex justify-content-between align-items-center checkbox-custom">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-white" style="font-size: 0.825rem;" for="remember">
                                Ingat Sesi Saya
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100 mb-2">Masuk</button>
                </form>
            </div>
        </div>

        <div class="text-center text-wrap px-3" style="font-size: 0.75rem; color: #cbd5e1; max-width: 600px; letter-spacing: 0.5px;">
            &copy; 2026 Sistem Manajemen UMKM Masyul Kebab - Jeanna Aprilia. Hak Cipta Dilindungi
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const togglePasswordIcon = document.querySelector('#togglePasswordIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            if (type === 'password') {
                togglePasswordIcon.classList.remove('bi-eye-fill');
                togglePasswordIcon.classList.add('bi-eye-slash-fill');
            } else {
                togglePasswordIcon.classList.remove('bi-eye-slash-fill');
                togglePasswordIcon.classList.add('bi-eye-fill');
            }
        });
    </script>
</body>
</html>
