<x-master-layout>
    <div class="w-full p-2">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-center mb-6 p-4 bg-white shadow rounded-lg">
            <h1 class="text-2xl font-bold text-gray-800">Resi: {{ $transaksi->resi }}</h1>
            <div class="flex flex-wrap gap-2 mt-4 lg:mt-0">
                <button type="button" class="text-purple-700 border border-purple-700 hover:bg-purple-800 hover:text-white focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200 ease-in-out">
                    Print
                </button>
                <a href="{{ route('transaksi.edit', $transaksi->resi) }}" class="bg-purple-700 text-white hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200 ease-in-out">
                    Ubah
                </a>
                <button id="dropdownMenuIconButton" data-dropdown-toggle="dropdownDots" class="bg-white text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-50 rounded-lg p-2 text-sm transition duration-200 ease-in-out">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
                        <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div id="dropdownDots" class="hidden z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconButton">
                        <li>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Batalkan</a>
                        </li>
                        <li>
                            <a href="{{ route('master.transaksi.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Kembali</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <!-- Transaction Details Section -->
        <div class="bg-gray-100 p-6 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex justify-between mb-2">
                        <h2 class="font-semibold">Resi:</h2>
                        <p>{{ $transaksi->resi }}</p>
                    </div>
                    <div class="flex justify-between mb-2">
                        <h2 class="font-semibold">Tanggal / Waktu:</h2>
                        <p>{{ $transaksi->created_at }}</p>
                    </div>
                    <div class="flex justify-between mb-2">
                        <h2 class="font-semibold">Tanggal:</h2>
                        <p>{{ $transaksi->tanggal_transaksi }}</p>
                    </div>
                    <div class="flex justify-between">
                        <h2 class="font-semibold">Status:</h2>
                        <p>{{ $transaksi->status }}</p>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <h2 class="font-semibold">Jumlah Item:</h2>
                        <p>{{ $transaksi->total_qty }}</p>
                    </div>
                    <div class="flex justify-between mb-2">
                        <h2 class="font-semibold">Outlet:</h2>
                        <p>{{ $transaksi->outlet->nama_outlet }}</p>
                    </div>
                    <div class="flex justify-between mb-2">
                        <h2 class="font-semibold">Alamat:</h2>
                        <p>{{ $transaksi->outlet->alamat }}</p>
                    </div>
                    <div class="flex justify-between">
                        <h2 class="font-semibold">No Telepon:</h2>
                        <p>{{ $transaksi->outlet->no_telp }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full flex justify-end my-3">
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative">
                    <input type="text" id="simple-search" onkeyup="searchTable()" class="md:w-56 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Produk..." required />
                </div>
        </div>

        <!-- Order Details Table -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-6">
            <table id="produk-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Produk</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">QTY</th>
                        <th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3">Satuan</th>
                        <th class="px-6 py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detailTransaksi as $key => $items)
                        <tr class="bg-white dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $key + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $items->produk->nama_produk }}</span>
                                    <span class="text-gray-500 text-sm">SKU : {{ $items->produk->sku }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $items->produk->kategoris->nama_kategori }}</td>
                            <td class="px-6 py-4">{{ $items->qty }}</td>
                            <td class="px-6 py-4">{{ $items->produk->harga_jual }}</td>
                            <td class="px-6 py-4">{{ $items->produk->units->nama_unit }}</td>
                            <td class="px-6 py-4">{{ $items->subtotal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Additional Information -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="Catatan" class="font-semibold mb-2 block text-gray-700">Catatan:</label>
                    <textarea id="Catatan" rows="10" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-200" readonly>{{ $transaksi->catatan }}</textarea>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-2">
                        <h2 class="text-lg font-semibold text-gray-700">Subtotal:</h2>
                        <p id="subtotal" class="text-lg font-medium text-gray-900">{{ number_format($transaksi->total_belanja, 2) }}</p>
                    </div>
                    <div class="flex justify-between items-center pb-2">
                        <h2 class="text-lg font-semibold text-gray-700">Diskon:</h2>
                        <p class="text-lg font-medium text-gray-900">-</p>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-300 pb-2">
                        <h2 class="text-lg font-semibold text-gray-700">Penyesuaian:</h2>
                        <p class="text-lg font-medium text-gray-900">-</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-700">Total:</h2>
                        <p id="total" class="text-lg font-bold text-purple-700">{{ number_format($transaksi->total_belanja, 2) }}</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-700">Sisa Tagihan:</h2>
                        <span>
                            @if($transaksi->status == 'Selesai')
                                <span class="text-green-500">Lunas</span>
                            @else
                                <span class="text-red-500">Belum Lunas</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function searchTable() {
            // Get the search input value
            const input = document.getElementById('simple-search');
            const filter = input.value.toLowerCase();

            // Get the table and tbody elements
            const table = document.getElementById('produk-table');
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = tbody.getElementsByTagName('tr');

            // Loop through all table rows and hide those that don't match the search input
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let matched = false;

                // Loop through all cells in the current row
                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell.textContent.toLowerCase().includes(filter)) {
                        matched = true;
                        break;
                    }
                }

                // Show or hide the row based on whether it matched
                rows[i].style.display = matched ? '' : 'none';
            }
        }
    </script>
</x-master-layout>
