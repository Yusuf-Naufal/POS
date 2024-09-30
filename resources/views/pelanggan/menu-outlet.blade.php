<html lang="en">
<x-header></x-header>
<body class="flex justify-center items-center bg-gray-50 min-h-screen">
    <div class="w-full max-w-2xl p-4">
        <h1 class="font-semibold text-3xl text-center mt-3 text-purple-700">Pilih Outlet</h1>

        <!-- Search Bar -->
        <div class="mt-6 px-5">
            <div class="flex justify-center items-center space-x-4">
                <!-- Button -->
                <button data-modal-target="static-modal" data-modal-toggle="static-modal" class="w-1/3 h-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-3 flex items-center justify-center transition ease-in-out duration-300 shadow-md hover:shadow-lg dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                        <path d="M8 14h8m-8-4h2m-2 8h4M10 3H6a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-3.5M10 3V1m0 2v2"/>
                    </svg>
                    Cek Order
                </button>

                <!-- Search Input -->
                <div class="relative w-2/3 max-w-md">
                    <input 
                        type="text" 
                        id="simple-search" 
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-3 pl-10 transition ease-in-out duration-300 shadow-sm hover:shadow-lg" 
                        placeholder="Search Outlet name..." 
                        required
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19l-3.5-3.5m0 0a7 7 0 111.5-1.5l-3.5 3.5z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Outlet Cards -->
        <div id="outlet-list" class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 px-1">
            @foreach ($Outlet as $outlet)
                @php
                    $isClosed = now()->setTimezone('Asia/Jakarta')->toTimeString() < $outlet->jam_buka || now()->setTimezone('Asia/Jakarta')->toTimeString() >= $outlet->jam_tutup;
                @endphp
                
                <div class="outlet-item relative p-4 bg-white rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out">
                    <a href="{{ !$isClosed ? route('order.form', ['id' => $outlet->id]) : 'javascript:void(0)' }}" class="flex items-center space-x-6 @if($isClosed) pointer-events-none @endif">
                        <div class="relative">
                            <img class="w-20 h-20 rounded-lg object-cover shadow-md" src="{{ asset('public/assets/' . $outlet->foto ) }}" alt="Outlet Image">
                            @if($isClosed)
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg">
                                    <span class="text-white text-lg font-bold">Tutup</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-xl text-purple-800 outlet-name">{{ $outlet->nama_outlet }}</p>
                            <div class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                                <p class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($outlet->jam_buka)->format('H:i') }}</p>
                                <span class="text-gray-400">-</span>
                                <p class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($outlet->jam_tutup)->format('H:i') }}</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $outlet->pemilik }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>


    </div>

    {{-- Modal cek resi --}}
    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <form action="{{ route('cek.order') }}" method="GET">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Cek Order
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="static-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <label class="block text-sm font-medium text-gray-700" for="resi">Masukkan Resi Orderan:</label>
                        <input type="text" id="resi" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="resi" required>      
                    </div>
                    <!-- Modal footer -->
                    <div class="flex justify-end items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="static-modal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cek</button>
                        <button data-modal-hide="static-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('simple-search');
            const outletItems = document.querySelectorAll('.outlet-item');

            searchInput.addEventListener('input', function () {
                const searchTerm = searchInput.value.toLowerCase();

                // Loop through all outlet items and hide those that don't match the search term
                outletItems.forEach(function (item) {
                    const outletName = item.querySelector('.outlet-name').textContent.toLowerCase();

                    if (outletName.includes(searchTerm)) {
                        item.style.display = 'block';  // Show the item if it matches
                    } else {
                        item.style.display = 'none';   // Hide the item if it doesn't match
                    }
                });
            });
        });
    </script>

    <script src="../path/to/flowbite/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>
