<x-admin-layout>
    <div class="w-full justify-start">
        <div class="flex justify-between w-full">
            <h1 class="text-3xl font-bold">Daftar Pemilik</h1>
        </div>

        <div class="mt-2 mb-1 flex flex-col md:flex-row justify-between items-center">
            <a href="{{ route('admin.users.create') }}" type="button" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                Tambah Pemilik
            </a>
            <div class="flex gap-3 items-center">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center mt-4">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="page" value="{{ request('page') }}">
                    <label for="per_page" class="mr-2 text-gray-700">Items/page:</label>
                    <select name="per_page" 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 shadow-sm transition duration-150 ease-in-out" 
                            id="rowsPerPage" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <div class="md:w-72">
                    <div class="flex items-center max-w-sm mx-auto">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <input type="text" id="simple-search" onkeyup="searchTable()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Pemilik..." required />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table id="pemilik-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" style="background: gray">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-700"> 
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama
                        </th>
                        <th scope="col" class="px-6 py-3">
                            No Telpon
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3">
                            
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($User as $index => $user)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <a target="_blank" class="w-4 h-4">
                                <img class="w-20 h-20 rounded-md" src="{{ asset('public/assets/' . $user->foto ) }}" alt="User">
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->nama  }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->no_telp  }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->status }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="relative flex items-center justify-center space-x-2 text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 w-fit">
                                <!-- Button for 'Ubah' -->
                                <a href="{{ route('admin.users.edit', $user->id) }}" type="button" 
                                    class="focus:outline-none">
                                    Ubah
                                </a>

                                <!-- Dropdown Toggle Button -->
                                <button class="dropdown-toggle-button" data-dropdown-id="dropdown-{{ $index }}" type="button" 
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none">
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div id="dropdown-{{ $index }}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                        @if($user->status == 'Bekerja')
                                            <li>
                                                <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">
                                                        Berhentikan
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="delete-button block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left" data-user-id="{{ $user->id }}">
                                                    Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">No results found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="flex items-center justify-between mt-4 px-4">
        <div class="text-gray-700">
           Showing <span id="current-range"></span> of <span id="total-items"></span> results
        </div>
        <div id="pagination">
            <!-- Pagination links will go here -->
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
                    const userId = this.getAttribute('data-user-id');
                    const form = document.getElementById('delete-form-' + userId); // Corrected form ID

                    // Show SweetAlert confirmation
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda tidak bisa mengembalikan User yang dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, submit the form
                            form.submit(); // Use the form variable instead of produkId
                        }
                    });
                });
            });
        });

        let currentPage = 1;
        let rowsPerPage = 5;

        function searchTable() {
            // Get search input and table rows
            const input = document.getElementById('simple-search').value.toLowerCase();
            const table = document.getElementById('pemilik-table');
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = tbody.getElementsByTagName('tr');
            const rowsPerPageSelect = document.getElementById('rowsPerPage');
            
            // Update rows per page
            rowsPerPage = parseInt(rowsPerPageSelect.value);

            // Filter rows based on search query
            let filteredRows = [];
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let matched = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(input)) {
                        matched = true;
                        break;
                    }
                }
                if (matched) {
                    filteredRows.push(rows[i]);
                } else {
                    rows[i].style.display = 'none';  // Hide rows that don't match
                }
            }

            // Display the filtered rows using pagination
            paginateRows(filteredRows);
        }

        function paginateRows(filteredRows) {
            const paginationDiv = document.getElementById('pagination');
            const totalItems = filteredRows.length;
            const totalPages = Math.ceil(totalItems / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Update page information
            document.getElementById('total-items').innerText = totalItems;
            document.getElementById('current-range').innerText = `${start + 1} to ${Math.min(end, totalItems)}`;

            // Hide all rows first
            for (let i = 0; i < filteredRows.length; i++) {
                filteredRows[i].style.display = 'none';
            }

            // Show only the rows for the current page
            for (let i = start; i < end && i < filteredRows.length; i++) {
                filteredRows[i].style.display = '';
            }

            // Build pagination links
            paginationDiv.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const pageLink = document.createElement('button');
                pageLink.innerText = i;
                pageLink.classList.add('btn-class');
                if (i === currentPage) {
                    pageLink.classList.add('active-page');
                }
                pageLink.addEventListener('click', () => {
                    currentPage = i;
                    paginateRows(filteredRows);
                });
                paginationDiv.appendChild(pageLink);
            }
        }

        // Initialize the table with pagination on page load
        window.onload = searchTable;

    </script>
</x-admin-layout>