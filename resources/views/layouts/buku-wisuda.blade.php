<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Buku Wisuda' }}</title>
    @vite
</head>
<body class="bg-gray-100">
    {{ $slot }}
</body>
</html>
