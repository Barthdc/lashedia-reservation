<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lashedia Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @stack('styles')

    <style>
        :root {
            --pink-soft: #fff1f7;
            --pink-main: #f7c8d8;
            --pink-dark: #d96b9f;
            --purple-soft: #c8b6ff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --white-glass: rgba(255, 255, 255, .82);
            --shadow-soft: 0 18px 45px rgba(217, 107, 159, .15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Inter, Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(247, 200, 216, .55), transparent 32%),
                radial-gradient(circle at bottom left, rgba(200, 182, 255, .45), transparent 35%),
                linear-gradient(135deg, #fff7fb, #f9f7ff);
            color: var(--text-dark);
        }

        .admin-navbar {
            position: sticky;
            top: 0;
            z-index: 999;
            padding: 16px 22px;
            background: rgba(255, 241, 247, .92);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(217, 107, 159, .18);
            box-shadow: 0 12px 35px rgba(217, 107, 159, .12);
        }

        .admin-navbar-inner {
            max-width: 1500px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 20px;
            font-weight: 950;
            letter-spacing: -.5px;
        }

        .admin-logo {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--pink-main), var(--purple-soft));
            display: grid;
            place-items: center;
            color: white;
            font-weight: 950;
            box-shadow: 0 12px 24px rgba(217, 107, 159, .24);
        }

        .admin-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            list-style: none;
        }

        .admin-menu a,
        .logout-btn {
            border: 1px solid rgba(217, 107, 159, .24);
            background: rgba(255, 255, 255, .78);
            color: var(--text-dark);
            padding: 11px 17px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            cursor: pointer;
            transition: .25s;
        }

        .admin-menu a:hover,
        .admin-menu a.active,
        .logout-btn:hover {
            background: linear-gradient(135deg, var(--pink-main), var(--pink-dark));
            color: white;
            border-color: transparent;
            box-shadow: 0 12px 24px rgba(217, 107, 159, .22);
            transform: translateY(-1px);
        }

        .logout-form {
            display: inline;
        }

        .admin-content {
            max-width: 1500px;
            margin: auto;
            padding: 26px 18px 50px;
        }

        @media (max-width: 850px) {
            .admin-navbar-inner {
                flex-direction: column;
                align-items: stretch;
            }

            .admin-menu {
                flex-wrap: wrap;
            }

            .admin-menu a,
            .logout-btn {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<nav class="admin-navbar">
    <div class="admin-navbar-inner">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <div class="admin-logo">L</div>
            <span>Lashedia Admin</span>
        </a>

        <ul class="admin-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('admin.bookings.index') }}"
                   class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    Booking
                </a>
            </li>

            <li>
                <a href="{{ route('admin.gallery.index') }}"
                   class="{{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                    Gallery
                </a>
            </li>

            <li>
                <a href="{{ route('home') }}">
                    Lihat Website
                </a>
            </li>

            <li>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

<main class="admin-content">
    @yield('content')
</main>

@stack('scripts')

</body>
</html>
