<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thiết kế avatar</title>
    @vite('resources/js/avatar-generator/main.tsx')
</head>
<body>
    <div id="root"></div>
</body>
</html>
