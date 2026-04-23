<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="/images/logo.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.__AI_CHAT_ENABLED__ = {{ config('ai.enabled') ? 'true' : 'false' }};</script>
</head>
<body>
    <div id="app"></div>
</body>
</html>