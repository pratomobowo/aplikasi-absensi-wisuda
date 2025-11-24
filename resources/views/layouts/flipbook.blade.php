<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Buku Wisuda' }}</title>
    <script defer src="https://cdn.tailwindcss.com"></script>
    {{ $slot }}
</head>
<body class="m-0 p-0 overflow-hidden">
    {{ $slot }}
</body>
</html>
