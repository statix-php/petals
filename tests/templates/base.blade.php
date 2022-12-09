<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $name }} | @yield('pagetitle')</title>
</head>
<body>

    <h2>This is the base layout</h2>

    @yield('body')
    
</body>
</html>