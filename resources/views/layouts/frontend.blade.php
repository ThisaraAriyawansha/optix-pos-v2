<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>OptiX - Home</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/pageImg/5646546523465 - Copy.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        // Apply saved theme before paint to avoid a light/dark flash.
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="font-sans bg-page min-h-screen relative overflow-x-hidden transition-colors duration-200">

    @include('frontend.componenet.header')

    @yield('content')

    @include('frontend.componenet.buttomnav')

    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>

</body>
</html>
