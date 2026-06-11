<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Lashedia</title>

    {{-- CSS UTAMA --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}"
    >
</head>

<body>

    {{-- NAVBAR --}}
    @include('partials.navbar')

    {{-- KONTEN HALAMAN --}}
    <main>
        @yield('content')
    </main>

    {{-- SCRIPT AUTO HIDE NAVBAR --}}
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            let lastScrollTop = 0;

            const navbar =
                document.querySelector('.navbar-premium') ||
                document.querySelector('.navbar');

            if(!navbar){
                return;
            }

            navbar.classList.add('navbar-show');

            window.addEventListener('scroll', function(){
                const currentScroll =
                    window.pageYOffset ||
                    document.documentElement.scrollTop;

                if(currentScroll > lastScrollTop && currentScroll > 120){
                    navbar.classList.add('navbar-hide');
                    navbar.classList.remove('navbar-show');
                }else{
                    navbar.classList.remove('navbar-hide');
                    navbar.classList.add('navbar-show');
                }

                lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
            });
        });
    </script>

</body>
</html>
