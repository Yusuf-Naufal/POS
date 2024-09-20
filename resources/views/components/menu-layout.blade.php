<html lang="en">
<head>
    <x-header></x-header>
</head>
<body>
    <x-nav-menu></x-nav-menu>
    <div class="w-full">
        {{ $slot }}
    </div>

    {{-- SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>