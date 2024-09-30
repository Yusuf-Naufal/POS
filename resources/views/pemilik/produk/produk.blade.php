<x-master-layout>
    <div class="w-full">
        <div class="flex justify-between">
            <h1 class="text-3xl font-bold">Daftar Produk : </h1>
        </div>
        <div class="flex justify-between flex-wrap items-center mt-1 p-1">
            <a href="{{ route('master.produk.create') }}" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors duration-200 ease-in-out">
                Tambah Produk
            </a>
            <div class="flex items-center gap-4">
                <form method="GET" action="{{ route('master.produk.index') }}" class="flex items-center mt-4">
                    <label for="per_page" class="mr-2 text-gray-700">Items/page:</label>
                    <select name="per_page" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 shadow-sm transition duration-150 ease-in-out" 
                            id="per_page" 
                            onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <div class="relative w-auto">
                    <label for="simple-search" class="sr-only">Search</label>
                    <input type="text" id="simple-search" onkeyup="searchTable()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition duration-150 ease-in-out" placeholder="Search Produk..." required />
                </div>
            </div>
        </div>



        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table id="produk-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" style="background: gray">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-700"> 
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama
                        </th>
                        <th scope="col" class="px-6 py-3">
                            SKU
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Kategori
                        </th>
                        <th scope="col" class="px-6 py-3">
                            QTY
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Unit
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Harga
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3">
                            
                        </th>
                    </tr>
                </thead>

                <tbody id="produk-table-body"> 
                    @foreach ($produk as $index => $item) <!-- Changed $produk to $item -->
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <a target="_blank" class="w-4 h-4">
                                <img class="w-20 h-20" src="{{ asset('public/assets/' . $item->foto ) }}" alt="Produk">
                            </a>
                        </th>
                        <td class="px-6 py-4">
                            {{ $item->nama_produk }} <!-- Use $item instead of $produk -->
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->sku }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->kategoris->nama_kategori }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->stok_awal }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->units->nama_unit }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->harga_jual }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->status }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="relative flex items-center justify-center space-x-2 text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 w-fit">
                                <a href="{{ route('master.produk.edit', $item->id) }}" class="focus:outline-none">
                                    Ubah
                                </a>

                                <button class="dropdown-toggle-button" data-dropdown-id="dropdown-{{ $index }}" type="button" 
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none">
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>

                                <div id="dropdown-{{ $index }}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                        @if($item->status === 'Aktif')
                                            <li>
                                                <form action="{{ route('master.deactivate', $item->id) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">
                                                        Nonaktifkan
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <form action="{{ route('master.activate', $item->id) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <form id="delete-form-{{ $item->id }}" action="{{ route('produks.destroy', $item->id) }}" method="POST" class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left delete-button" data-produk-id="{{ $item->id }}">
                                                    Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="flex items-center justify-between mt-4 px-4">
        <div class="text-gray-700">
            Showing <span class="font-semibold">{{ $produk->firstItem() }}</span> to <span class="font-semibold">{{ $produk->lastItem() }}</span> of <span class="font-semibold">{{ $produk->total() }}</span> results
        </div>
        <div>
            {{ $produk->links('vendor.pagination.tailwind') }}
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-button');
            // Handle dropdown toggle
            document.querySelectorAll('.dropdown-toggle-button').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent event from bubbling up to the document
                    const dropdownId = this.getAttribute('data-dropdown-id');
                    const dropdownMenu = document.getElementById(dropdownId);
                    // Toggle visibility of the associated dropdown
                    dropdownMenu.classList.toggle('hidden');
                });
            });

            // Hide dropdown menu when clicking outside
            document.addEventListener('click', function(event) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
                        menu.classList.add('hidden');
                    }
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const produkId = this.getAttribute('data-produk-id');

                    // Show SweetAlert confirmation
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda tidak bisa mengembalikan produk yang dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, submit the form
                            document.getElementById('delete-form-' + produkId).submit();
                        }
                    });
                });
            });
        });

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

        document.addEventListener('DOMContentLoaded', function () {
            const paginationLinks = document.getElementById('pagination-links');

            if (paginationLinks) {
                paginationLinks.addEventListener('click', function (e) {
                    // Prevent the default behavior of the link
                    if (e.target.tagName === 'A') {
                        e.preventDefault();

                        // Fetch the URL from the clicked link
                        const url = e.target.href;
                        fetchPaginatedData(url);
                    }
                });
            }

            // Function to fetch data and update the table
            function fetchPaginatedData(url) {
                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        // Parse the returned HTML and update the table and pagination links
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data, 'text/html');

                        // Update the table body
                        const newTableBody = doc.getElementById('produk-table-body');
                        document.getElementById('produk-table-body').innerHTML = newTableBody.innerHTML;

                        // Update the pagination links
                        const newPaginationLinks = doc.getElementById('pagination-links');
                        document.getElementById('pagination-links').innerHTML = newPaginationLinks.innerHTML;
                    })
                    .catch(error => console.error('Error fetching paginated data:', error));
            }
        });
    </script>
</x-master-layout>