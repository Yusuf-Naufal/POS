<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- TAILWIND --}}
    <link rel="stylesheet" href="public/build/assets/app-DIl25HKw.css">
    <script src='public/build/assets/app-BmrwFrBv.js'></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full space-y-6">
        <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M12 9v6M4.293 4.293a1 1 0 011.414 0L12 8.586l6.293-6.293a1 1 0 111.414 1.414L13.414 10l6.293 6.293a1 1 0 01-1.414 1.414L12 12.414l-6.293 6.293a1 1 0 01-1.414-1.414L10.586 12 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
            <h1 class="text-4xl font-extrabold text-red-600">Error</h1>
        </div>
        <div class="text-center">
            <p class="text-gray-700 text-lg mb-6">{{ session('message') }}</p>
            <a onclick="window.history.back()" class="inline-flex items-center px-4 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-md shadow-md transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l5 5L20 7"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>
