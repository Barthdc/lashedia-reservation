<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lashedia Admin</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="admin-body">

    {{-- NAVBAR USER --}}
    @include('partials.navbar')

    <div class="admin-wrapper">

        {{-- NAVBAR ADMIN --}}
        <nav class="admin-navbar">

            <div class="admin-logo">
                Lashedia Admin
            </div>

            <div class="admin-right">

                <ul class="admin-menu">
                    <li>

        <a
            href="{{ route('admin.dashboard') }}"
            class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.bookings.index') }}"
                           class="{{ request()->is('admin/bookings*') ? 'active' : '' }}">
                            Booking
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.gallery.index') }}"
                           class="{{ request()->is('admin/gallery*') ? 'active' : '' }}">
                            Gallery
                        </a>
                    </li>

                </ul>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="admin-btn">
                        Logout
                    </button>
                </form>

            </div>

        </nav>

        {{-- CONTENT --}}
        <div class="admin-container">
            @yield('content')
        </div>

    </div>

</body>
</html>
