<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Balancer</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    <div id="app"
        data-user-email="{{ $userEmail ?? '' }}"
        data-is-history-page="{{ $isHistoryPage ? 'true' : 'false' }}"
        data-is-login-page="{{ $isLoginPage ? 'true' : 'false' }}"
    ></div>

    @vite('resources/js/app.js')
</body>
</html>