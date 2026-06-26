@props([
    'title' => config('app.name', 'Laravel'),
    'description' => '',
    'active' => 'dashboard',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>
    @if ($description !== '')
        <meta name="description" content="{{ $description }}">
    @endif

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen bg-[#eef2f8] text-slate-900 antialiased">
    <div
        class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,.15),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(14,165,233,.12),_transparent_28%),linear-gradient(180deg,#f7f9fd_0%,#eef2f8_100%)]">
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,rgba(15,23,42,.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,.03)_1px,transparent_1px)] bg-[size:28px_28px] opacity-40">
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-[1600px] flex-col lg:flex-row">
            <x-frontend-menu :active="$active" />

            <main class="flex-1 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
