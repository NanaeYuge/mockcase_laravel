<!DOCTYPE html>
<html lang="ja">
    <meta charset="UTF-8">
    <title>My App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>


</head>
<body>
    @include('components.header')
    <main>
        @yield('content')
    </main>
</body>
</html>
