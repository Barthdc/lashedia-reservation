<x-guest-layout>

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

<div class="login-wrapper">

    <div class="login-card">

        <a href="{{ route('home') }}" class="btn-back">
            ← Kembali
        </a>

        <h1>Selamat Datang Kembali</h1>

        <p class="subtitle">
            Masuk ke akun Anda untuk melanjutkan
        </p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- EMAIL -->
            <div class="form-group">
                <label for="email">
                    Alamat Email
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="user@example.com"
                    required
                    autofocus
                >
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label for="password">
                    Kata Sandi
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••"
                    required
                >
            </div>

            <!-- BUTTON -->
            <button type="submit" class="btn-auth">
                Masuk
            </button>

            <!-- REGISTER -->
            <p class="register-text">
                Belum punya akun?
                <a href="{{ route('register') }}">
                    Daftar
                </a>
            </p>

        </form>

    </div>

</div>

</x-guest-layout>
