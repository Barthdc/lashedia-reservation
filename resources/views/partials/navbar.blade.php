<nav class="navbar-premium">

    <div class="navbar-inner">

        <!-- LOGO -->
        <a href="{{ route('home') }}" class="navbar-logo">
            <div class="logo-circle">
                <img
                    src="{{ asset('images/lashedia-logo.png') }}"
                    alt="Logo"
                >
            </div>
            <span>Lashedia</span>
        </a>

        <!-- HAMBURGER -->
        <button class="mobile-toggle" id="mobileToggle" type="button">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- MENU -->
        <ul class="navbar-menu" id="navbarMenu">

            <li>
                <a href="{{ route('home') }}"
                   class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
            </li>

            <li>
                <a href="{{ route('penata-rias') }}"
                   class="{{ request()->routeIs('penata-rias') ? 'active' : '' }}">
                    Artist
                </a>
            </li>

            <li>
                <a href="{{ route('galeri') }}"
                   class="{{ request()->routeIs('galeri') ? 'active' : '' }}">
                    Galeri
                </a>
            </li>

            <li>
                <a href="{{ route('pesan') }}"
                   class="{{ request()->routeIs('pesan') ? 'active' : '' }}">
                    Pesan Sekarang
                </a>
            </li>

            @auth
                <li>
                    <a href="{{ route('riwayat') }}"
                       class="{{ request()->routeIs('riwayat') ? 'active' : '' }}">
                        Riwayat
                    </a>
                </li>
            @endauth

            <li>
                <a href="{{ route('tentang-kami') }}"
                   class="{{ request()->routeIs('tentang-kami') ? 'active' : '' }}">
                    Tentang Kami
                </a>
            </li>

            @auth
                <li>
                    <a href="{{ route('notifikasi') }}"
                       class="notif-btn">
                        🔔

                        @if(isset($notifCount) && $notifCount > 0)
                            <span class="notif-badge">
                                {{ $notifCount }}
                            </span>
                        @endif
                    </a>
                </li>
            @endauth

            <!-- PROFILE MOBILE -->
            <li class="mobile-profile">

                @auth

                    <div class="mobile-user">
                        {{ Auth::user()->name }}
                    </div>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.bookings.index') }}"
                           class="mobile-admin-btn">
                            Dashboard Admin
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="mobile-logout-btn">
                            Logout
                        </button>
                    </form>

                @else

                    <a href="{{ route('login') }}" class="mobile-login-btn">
                        Login
                    </a>

                    <a href="{{ route('register') }}" class="mobile-register-btn">
                        Book Now
                    </a>

                @endauth

            </li>

        </ul>

        <!-- RIGHT DESKTOP -->
        <div class="navbar-right">

            @auth

                <div class="profile-dropdown">

                    <button class="profile-btn-premium" type="button">
                        {{ Str::limit(Auth::user()->name, 5) }}
                    </button>

                    <div class="profile-menu">

                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.bookings.index') }}"
                               class="admin-btn">
                                Dashboard Admin
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="logout-btn">
                                Logout
                            </button>
                        </form>

                    </div>

                </div>

            @else

                <a href="{{ route('register') }}" class="nav-book-btn">
                    Book Now
                </a>

            @endauth

        </div>

    </div>

</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileToggle = document.getElementById('mobileToggle');
        const navbarMenu = document.getElementById('navbarMenu');

        if (mobileToggle && navbarMenu) {
            mobileToggle.addEventListener('click', function () {
                navbarMenu.classList.toggle('active');
                mobileToggle.classList.toggle('active');
            });
        }
    });
</script>
