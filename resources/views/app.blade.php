<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    @vite('resources/css/app.css')
    @laravelPWA
</head>

<body class="antialiased overflow-x-hidden">
    <div id="__sip__point__base__app"></div>
    @vite('resources/js/app.js')
</body>

</html>
