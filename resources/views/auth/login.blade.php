<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login — BK Digital</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
    <style>
        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: #f0f4f8;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Subtle grid pattern overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                linear-gradient(135deg, #0d47a1 0%, #1565c0 40%, #1976d2 70%, #42a5f5 100%);
            z-index: -1;
        }

        /* Decorative circles */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 15% 85%, rgba(255,255,255,.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 15%, rgba(255,255,255,.06) 0%, transparent 40%);
            z-index: -1;
        }

        .login-wrap {
            width: 100%;
            max-width: 380px;
        }

        /* Card */
        .login-card {
            background: #fff;
            border-radius: 1.25rem;
            border: none;
            box-shadow:
                0 4px 6px rgba(0,0,0,.04),
                0 20px 48px rgba(0,0,0,.15),
                0 0 0 1px rgba(255,255,255,.1);
            overflow: hidden;
        }

        /* Top stripe */
        .card-stripe {
            height: 4px;
            background: linear-gradient(90deg, #1565c0, #42a5f5, #1e88e5);
        }

        .card-body-inner {
            padding: 2rem 2rem 1.75rem;
        }

        /* Logo */
        .login-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1565c0, #1976d2);
            border-radius: .875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.6rem;
            color: #fff;
            box-shadow: 0 6px 20px rgba(21,101,192,.35);
        }

        .brand-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: .2rem;
        }

        .brand-sub {
            font-size: .78rem;
            color: #78909c;
            letter-spacing: .02em;
        }

        /* Form inputs */
        .form-label {
            font-size: .78rem;
            font-weight: 600;
            color: #455a64;
            margin-bottom: .35rem;
        }

        .input-group-text {
            background: #f5f7fa;
            border-color: #dde3ec;
            color: #78909c;
        }

        .form-control {
            border-color: #dde3ec;
            font-size: .875rem;
            padding: .55rem .75rem;
            color: #263238;
        }

        .form-control:focus {
            border-color: #1976d2;
            box-shadow: 0 0 0 3px rgba(25,118,210,.12);
        }

        .form-control::placeholder { color: #b0bec5; }

        .input-group > .form-control:not(:last-child) { border-right: 0; }

        .btn-toggle-pwd {
            border-color: #dde3ec;
            background: #f5f7fa;
            color: #78909c;
            font-size: .8rem;
        }
        .btn-toggle-pwd:hover { background: #eceff1; color: #546e7a; border-color: #cfd8dc; }

        /* Submit button */
        .btn-login {
            background: linear-gradient(135deg, #1565c0, #1e88e5);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: .9rem;
            padding: .65rem;
            border-radius: .6rem;
            letter-spacing: .02em;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(21,101,192,.3);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            box-shadow: 0 6px 18px rgba(21,101,192,.4);
            transform: translateY(-1px);
            color: #fff;
        }
        .btn-login:active { transform: translateY(0); }

        /* Alert */
        .alert-login {
            background: #fdecea;
            border: 1px solid #f5c6c6;
            border-radius: .6rem;
            color: #b71c1c;
            font-size: .8rem;
            padding: .6rem .9rem;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            padding: .85rem 1rem;
            border-top: 1px solid #f0f4f8;
            background: #fafbfc;
        }
        .login-footer small { font-size: .72rem; color: #90a4ae; }
    </style>
</head>
<body>
<div class="login-wrap">

    <div class="login-card">
        <div class="card-stripe"></div>

        <div class="card-body-inner">
            {{-- Brand --}}
            <div class="text-center mb-4">
                <div class="login-logo">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div class="brand-name">BK Digital</div>
                <div class="brand-sub">Sistem Informasi Bimbingan Konseling</div>
            </div>

            {{-- Alerts --}}
            @if(session('status'))
            <div class="alert alert-success py-2 mb-3" style="font-size:.8rem">
                <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert-login mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                <span>Email atau password tidak valid. Silakan coba lagi.</span>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope-fill" style="font-size:.8rem"></i>
                        </span>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="email@sekolah.id"
                            required autofocus autocomplete="email"/>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill" style="font-size:.8rem"></i>
                        </span>
                        <input type="password" name="password" id="pwdInput"
                            class="form-control"
                            placeholder="Masukkan password"
                            required autocomplete="current-password"/>
                        <button type="button" class="btn btn-toggle-pwd" onclick="togglePwd()" tabindex="-1">
                            <i class="bi bi-eye-fill" id="pwdIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>
        </div>

        <div class="login-footer">
            <small>&copy; {{ date('Y') }} BK Digital &mdash; Sistem Administrasi Bimbingan Konseling</small>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const i  = document.getElementById('pwdInput');
    const ic = document.getElementById('pwdIcon');
    i.type   = i.type === 'password' ? 'text' : 'password';
    ic.className = i.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
}
</script>
</body>
</html>
