<nav class="navbar-premium">

    <div class="navbar-inner">

        <!-- LOGO -->
        <a href="{{ route('home') }}" class="navbar-logo">
            <div class="logo-circle">
                <img
                    src="{{ asset('images/lashedia-logo.png') }}"
                    alt="Logo Lashedia"
                >
            </div>

            <span>Lashedia</span>
        </a>

        <!-- PROFILE MOBILE TOP -->
        <div class="mobile-profile-top">

            @auth

                <button
                    type="button"
                    class="mobile-profile-name"
                    id="mobileProfileName"
                >
                    {{ Str::limit(Auth::user()->name, 8) }}
                </button>

                <div
                    class="mobile-profile-popup"
                    id="mobileProfilePopup"
                >
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="mobile-logout-btn"
                        >
                            Logout
                        </button>
                    </form>
                </div>

            @else

                <a
                    href="{{ route('register') }}"
                    class="mobile-login-btn"
                >
                    Daftar
                </a>

            @endauth

        </div>

        <!-- HAMBURGER MOBILE -->
        <button
            class="mobile-toggle"
            id="mobileToggle"
            type="button"
            aria-label="Toggle Menu"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- MENU -->
        <ul class="navbar-menu" id="navbarMenu">

            <!-- MENU UMUM -->
            <li>
                <a
                    href="{{ route('home') }}"
                    class="{{ request()->routeIs('home') ? 'active' : '' }}"
                >
                    Home
                </a>
            </li>

            <li>
                <a
                    href="{{ route('penata-rias') }}"
                    class="{{ request()->routeIs('penata-rias') ? 'active' : '' }}"
                >
                    Artist
                </a>
            </li>

            <li>
                <a
                    href="{{ route('galeri') }}"
                    class="{{ request()->routeIs('galeri') ? 'active' : '' }}"
                >
                    Galeri
                </a>
            </li>

            <!-- MENU USER LOGIN NON-ADMIN -->
            @auth

                @if(Auth::user()->role !== 'admin')

                    <li>
                        <a
                            href="{{ route('pesan') }}"
                            class="{{ request()->routeIs('pesan') ? 'active' : '' }}"
                        >
                            Pesan Sekarang
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('riwayat') }}"
                            class="{{ request()->routeIs('riwayat') ? 'active' : '' }}"
                        >
                            Riwayat
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('notifikasi') }}"
                            class="notif-btn {{ request()->routeIs('notifikasi') ? 'active' : '' }}"
                        >
                            🔔

                            @if(isset($notifCount) && $notifCount > 0)
                                <span class="notif-badge">
                                    {{ $notifCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                @endif

                <!-- MENU ADMIN -->
                @if(Auth::user()->role === 'admin')

                    <li>
                        <a
                            href="{{ route('pesan') }}"
                            class="{{ request()->routeIs('pesan') ? 'active' : '' }}"
                        >
                            Pesan Sekarang
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('riwayat') }}"
                            class="{{ request()->routeIs('riwayat') ? 'active' : '' }}"
                        >
                            Riwayat
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('notifikasi') }}"
                            class="notif-btn {{ request()->routeIs('notifikasi') ? 'active' : '' }}"
                        >
                            🔔

                            @if(isset($notifCount) && $notifCount > 0)
                                <span class="notif-badge">
                                    {{ $notifCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                @endif

            @endauth

        </ul>

        <!-- PROFILE DESKTOP -->
        <div class="navbar-right">

            @auth

                <div class="profile-dropdown">

                    <button
                        class="profile-btn-premium"
                        type="button"
                    >
                        {{ Str::limit(Auth::user()->name, 8) }}
                    </button>

                    <div class="profile-menu">

                        @if(Auth::user()->role === 'admin')

                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="admin-btn"
                            >
                                Dashboard Admin
                            </a>

                            <a
                                href="{{ route('admin.bookings.index') }}"
                                class="admin-btn"
                            >
                                Booking Admin
                            </a>

                            <a
                                href="{{ route('admin.gallery.index') }}"
                                class="admin-btn"
                            >
                                Gallery Admin
                            </a>

                        @else

                            <a
                                href="{{ route('pesan') }}"
                                class="admin-btn"
                            >
                                Pesan Sekarang
                            </a>

                            <a
                                href="{{ route('riwayat') }}"
                                class="admin-btn"
                            >
                                Riwayat
                            </a>

                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="logout-btn"
                            >
                                Logout
                            </button>
                        </form>

                    </div>

                </div>

            @else

                <a
                    href="{{ route('register') }}"
                    class="profile-btn-premium"
                >
                    Daftar
                </a>

            @endauth

        </div>

    </div>

</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileToggle = document.getElementById('mobileToggle');
        const navbarMenu = document.getElementById('navbarMenu');

        const mobileProfileName = document.getElementById('mobileProfileName');
        const mobileProfilePopup = document.getElementById('mobileProfilePopup');

        if (mobileToggle && navbarMenu) {
            mobileToggle.addEventListener('click', function (event) {
                event.stopPropagation();

                navbarMenu.classList.toggle('active');
                mobileToggle.classList.toggle('active');

                if (mobileProfilePopup) {
                    mobileProfilePopup.classList.remove('active');
                }
            });
        }

        if (mobileProfileName && mobileProfilePopup) {
            mobileProfileName.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                mobileProfilePopup.classList.toggle('active');
            });
        }

        document.addEventListener('click', function (event) {
            if (
                mobileProfilePopup &&
                mobileProfileName &&
                !mobileProfileName.contains(event.target) &&
                !mobileProfilePopup.contains(event.target)
            ) {
                mobileProfilePopup.classList.remove('active');
            }
        });

        if (navbarMenu) {
            const menuLinks = navbarMenu.querySelectorAll('a');

            menuLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    navbarMenu.classList.remove('active');

                    if (mobileToggle) {
                        mobileToggle.classList.remove('active');
                    }

                    if (mobileProfilePopup) {
                        mobileProfilePopup.classList.remove('active');
                    }
                });
            });
        }
    });
</script>
