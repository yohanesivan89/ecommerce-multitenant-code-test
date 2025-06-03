<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Multi-Tenant eCommerce Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="padding: 0px; margin: 0px;">
    <div id="app"></div>
</body>
</html>