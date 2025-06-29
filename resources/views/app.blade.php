<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Balancer</title>
    <!-- Добавьте version для принудительного обновления -->
{{--  --}}
    
    <!-- Подключение Vite -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    {{-- <div id="app"></div> --}}
    <div id="app" data-user-email="{{ $userEmail }}"></div>
    <script>
        window.userEmail = document.getElementById('app').dataset.userEmail;
    </script>
</body>
</html>