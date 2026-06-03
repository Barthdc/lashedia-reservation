<x-guest-layout>

<link rel="stylesheet" href="{{ asset('css/auth.css') }}">

<div class="login-wrapper">

    <div class="login-card">

        <!-- BACK -->
        <a href="{{ route('home') }}" class="btn-back">
            ← Kembali
        </a>

        <!-- TITLE -->
        <h1>
            Buat Akun Baru
        </h1>

        <p class="subtitle">
            Daftar untuk mulai menggunakan Lashedia
        </p>

        <!-- FORM -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- NAME -->
            <div class="form-group">

                <label for="name">
                    Nama Lengkap
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Jane Doe"
                    required
                    autofocus
                    autocomplete="name"
                >

                @error('name')
                    <p class="error-msg">
                        {{ $message }}
                    </p>
                @enderror

            </div>

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
                    autocomplete="email"
                >

                @error('email')
                    <p class="error-msg">
                        {{ $message }}
                    </p>
                @enderror

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
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                >

                @error('password')
                    <p class="error-msg">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="form-group">

                <label for="password_confirmation">
                    Konfirmasi Kata Sandi
                </label>

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                >

            </div>

            <!-- BUTTON -->
            <button type="submit" class="btn-auth">
                Daftar Sekarang
            </button>

            <!-- LOGIN -->
            <p class="register-text">

                Sudah punya akun?

                <a href="{{ route('login') }}">
                    Masuk
                </a>

            </p>

        </form>

    </div>

</div>

</x-guest-layout>
