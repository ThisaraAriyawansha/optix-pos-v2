<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>OptiX - Home</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/pageImg/5646546523465 - Copy.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-gray-50 min-h-screen relative overflow-x-hidden">

    @include('frontend.componenet.header')

    @yield('content')

    @include('frontend.componenet.buttomnav')

</body>
</html>
