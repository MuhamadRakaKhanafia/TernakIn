@extends('layouts.app')

@section('title', 'Login - TernakIN')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Masuk ke TernakIN</h2>
            <p>Masukkan kredensial Anda untuk melanjutkan</p>
        </div>

        <form id="loginForm" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <div class="error-message" id="emailError"></div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <div class="error-message" id="passwordError"></div>
            </div>

            <button type="submit" class="btn btn-primary auth-btn">
                <span id="btnText">Masuk</span>
                <div id="btnLoader" class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </button>
        </form>



        <div class="auth-links">
            <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 120px);
    padding: 20px;
}

.auth-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 400px;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h2 {
    color: var(--primary-color);
    margin-bottom: 10px;
    font-size: 24px;
}

.auth-header p {
    color: #666;
    margin: 0;
}



.auth-form {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: var(--text-dark);
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.error-message {
    color: #e74c3c;
    font-size: 14px;
    margin-top: 5px;
    min-height: 18px;
}

.auth-btn {
    width: 100%;
    padding: 12px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.auth-btn:hover:not(:disabled) {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.auth-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-loader {
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-links {
    text-align: center;
    margin-top: 20px;
}

.auth-links a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.social-login {
    margin: 20px 0;
}

.divider {
    display: flex;
    align-items: center;
    margin: 20px 0;
    text-align: center;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e1e5e9;
}

.divider span {
    padding: 0 15px;
    color: #666;
    font-size: 14px;
    background: white;
}

.social-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 15px;
    border: 2px solid transparent;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    gap: 10px;
}

.btn-google {
    background: #fff;
    color: #333;
    border-color: #ddd;
}

.btn-google:hover {
    background: #f8f9fa;
    border-color: #ccc;
}

.btn-facebook {
    background: #1877f2;
    color: white;
}

.btn-facebook:hover {
    background: #166fe5;
}

.btn i {
    font-size: 18px;
}

.auth-links a:hover {
    text-decoration: underline;
}

@media (max-width: 480px) {
    .auth-card {
        padding: 30px 20px;
    }

    .auth-header h2 {
        font-size: 20px;
    }

    .social-buttons {
        gap: 8px;
    }

    .btn {
        padding: 10px 12px;
        font-size: 14px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const submitBtn = this.querySelector('.auth-btn');

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // Show loading
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'flex';

    try {
        const formData = new FormData(this);
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password')
            })
        });

        const data = await response.json();

        if (data.success) {
            // Store token if needed
            if (data.access_token) {
                localStorage.setItem('auth_token', data.access_token);
            }

            // Redirect to dashboard
            window.location.href = '{{ route("dashboard") }}';
        } else {
            // Show errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById(field + 'Error');
                    if (errorEl) {
                        errorEl.textContent = data.errors[field][0];
                    }
                });
            } else if (data.error) {
                document.getElementById('emailError').textContent = data.error;
            }
        }
    } catch (error) {
        console.error('Login error:', error);
        document.getElementById('emailError').textContent = 'Terjadi kesalahan. Silakan coba lagi.';
    } finally {
        // Hide loading
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    }
});
</script>
@endpush
