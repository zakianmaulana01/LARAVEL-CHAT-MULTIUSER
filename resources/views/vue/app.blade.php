<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Chat') }}</title>
    @vite(['resources/css/app.css', 'resources/js/vue/app.js'])
</head>
<body class="h-full bg-gray-100 antialiased">
    <div id="vue-app"></div>
</body>
</html>
