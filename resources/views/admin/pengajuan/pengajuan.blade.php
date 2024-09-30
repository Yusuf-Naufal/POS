<x-admin-layout>
    <div class="w-full h-full">
        {{-- TABLE PENGAJUAN --}}
        <div class="h-3/5 border-b border-gray-300 mb-1">
            <div class="mb-2 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Daftar Pengajuan</h1>
                <div class="md:w-72">
                    <div class="flex items-center max-w-sm mx-auto">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <input type="text" id="pengajuan-search" onkeyup="searchPengajuan()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Pengajuan..." required />
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden shadow-md sm:rounded-lg mt-4">
                <div class="max-h-[380px] overflow-y-auto"> <!-- Set max height and enable scrolling -->
                    <table id="table-pengajuan" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 bg-gray-50">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Foto</th>
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="px-6 py-3">Pemilik</th>
                                <th scope="col" class="px-6 py-3">Telepon</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($pengajuan as $index => $pengajuan)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200" data-id="{{ $pengajuan->id }}">
                                <td class="px-6 py-4">
                                    <a href="{{ asset('public/assets/' . $pengajuan->foto) }}" target="_blank" class="block w-20 h-20 rounded-lg overflow-hidden shadow-md">
                                        <img src="{{ asset('public/assets/' . $pengajuan->foto) }}" alt="Foto {{ $pengajuan->nama_outlet }}" class="object-cover w-full h-full">
                                    </a>
                                </td>
                                <td class="px-6 py-4">{{ $pengajuan->nama_outlet }}</td>
                                <td class="px-6 py-4">{{ $pengajuan->pemilik->nama }}</td>
                                <td class="px-6 py-4">{{ $pengajuan->no_telp }}</td>
                                <td class="px-6 py-4">{{ $pengajuan->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold {{ $pengajuan->status == 'Disetujui' ? 'text-green-800 bg-green-200' : ($pengajuan->status == 'Ditolak' ? 'text-red-800 bg-red-200' : 'text-yellow-800 bg-yellow-200') }} rounded-lg">
                                        {{ $pengajuan->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="#" id="setuju-button" data-id="{{ $pengajuan->id }}" class="px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-150" aria-label="Setujui">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M16.972 6.251a2 2 0 0 0-2.72.777l-3.713 6.682l-2.125-2.125a2 2 0 1 0-2.828 2.828l4 4c.378.379.888.587 1.414.587l.277-.02a2 2 0 0 0 1.471-1.009l5-9a2 2 0 0 0-.776-2.72"/>
                                            </svg>
                                        </a>

                                        <a href="#" id="tolak-button" data-id="{{ $pengajuan->id }}" class="px-3 py-2 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all duration-150" aria-label="Tolak">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15l-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152l2.758 3.15a1.2 1.2 0 0 1 0 1.698"/>
                                            </svg>
                                        </a>

                                        <a href="{{ route('admin.pengajuan.detail-pengajuan', $pengajuan->id) }}" class="px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-150" aria-label="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px" viewBox="0 0 20 20" fill="currentColor">
                                                <g><path fill-rule="evenodd" d="M10 16.5c4.897 0 9-2.308 9-5.5s-4.103-5.5-9-5.5S1 7.808 1 11s4.103 5.5 9 5.5m0-9c3.94 0 7 1.722 7 3.5s-3.06 3.5-7 3.5s-7-1.722-7-3.5s3.06-3.5 7-3.5" clip-rule="evenodd"/><path d="M9 3.5a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0zm4.02.304a1 1 0 0 1 1.96.392l-.5 2.5a1 1 0 0 1-1.96-.392zm-6.04 0a1 1 0 0 0-1.96.392l.5 2.5a1 1 0 0 0 1.96-.392zM2.857 4.986a1 1 0 1 0-1.714 1.029l1.5 2.5a1 1 0 1 0 1.714-1.03zm14.286 0a1 1 0 0 1 1.715 1.029l-1.5 2.5a1 1 0 0 1-1.716-1.03z"/><path fill-rule="evenodd" d="M10 14a3.5 3.5 0 1 0 0-7a3.5 3.5 0 0 0 0 7m0-5a1.5 1.5 0 1 1 0 3a1.5 1.5 0 0 1 0-3" clip-rule="evenodd"/></g>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        {{-- TABLE PENOLAKAN --}}
        <div class="h-2/5">
            <div class="mb-2 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Daftar Pengajuan yang Ditolak</h1>
                <div class="md:w-72">
                    <div class="flex items-center max-w-sm mx-auto">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <input type="text" id="penolakan-search" onkeyup="searchPenolakan()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Penolakan..." required />
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
                <div class="max-h-[230px] overflow-y-auto">
                    <table id="penolakan-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 bg-gray-50">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="px-6 py-3">Pemilik</th>
                                <th scope="col" class="px-6 py-3">Telepon</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            @foreach ($tertolak as $index => $pengajuan)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                                <td class="px-6 py-4">{{ $pengajuan->nama_outlet }}</td>
                                <td class="px-6 py-4">{{ $pengajuan->pemilik->nama }}</td>
                                <td class="px-6 py-4">{{ $pengajuan->no_telp }}</td>
                                <td class="px-6 py-4">{{ $pengajuan->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">
                                        {{ $pengajuan->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.pengajuan.detail-pengajuan', $pengajuan->id) }}" class="px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-150" aria-label="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px" viewBox="0 0 20 20" fill="currentColor">
                                                <g><path fill-rule="evenodd" d="M10 16.5c4.897 0 9-2.308 9-5.5s-4.103-5.5-9-5.5S1 7.808 1 11s4.103 5.5 9 5.5m0-9c3.94 0 7 1.722 7 3.5s-3.06 3.5-7 3.5s-7-1.722-7-3.5s3.06-3.5 7-3.5" clip-rule="evenodd"/><path d="M9 3.5a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0zm4.02.304a1 1 0 0 1 1.96.392l-.5 2.5a1 1 0 0 1-1.96-.392zm-6.04 0a1 1 0 0 0-1.96.392l.5 2.5a1 1 0 0 0 1.96-.392zM2.857 4.986a1 1 0 1 0-1.714 1.029l1.5 2.5a1 1 0 1 0 1.714-1.03zm14.286 0a1 1 0 0 1 1.715 1.029l-1.5 2.5a1 1 0 0 1-1.716-1.03z"/><path fill-rule="evenodd" d="M10 14a3.5 3.5 0 1 0 0-7a3.5 3.5 0 0 0 0 7m0-5a1.5 1.5 0 1 1 0 3a1.5 1.5 0 0 1 0-3" clip-rule="evenodd"/></g>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
       document.querySelectorAll('#setuju-button').forEach((button) => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const pengajuanId = this.getAttribute('data-id');

                if (!pengajuanId) {
                    Swal.fire('Error!', 'Pengajuan ID not found.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Apa kamu yakin?',
                    text: 'Kamu menyetujui pengajuan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, setuju!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/pengajuan/${pengajuanId}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    Swal.fire('Approved!', 'The submission has been approved.', 'success');
                                    location.reload();
                                } else {
                                    Swal.fire('Error!', data.message || 'There was an issue approving the submission.', 'error');
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Failed to process the request.', 'error');
                            });
                    }
                });
            });
        });

        document.querySelectorAll('#tolak-button').forEach((button) => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const pengajuanId = this.getAttribute('data-id');

                if (!pengajuanId) {
                    Swal.fire('Error!', 'Pengajuan ID not found.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Apa kamu yakin?',
                    text: 'Kamu menolak pengajuan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Tolak!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/pengajuan/${pengajuanId}/reject`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    Swal.fire('Rejected!', 'The submission has been rejected.', 'success');
                                    location.reload();
                                } else {
                                    Swal.fire('Error!', data.message || 'There was an issue rejecting the submission.', 'error');
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Failed to process the request.', 'error');
                            });
                    }
                });
            });
        });

        function searchPengajuan() {
            // Get the search input value
            const input = document.getElementById('pengajuan-search');
            const filter = input.value.toLowerCase();

            // Get the table and tbody elements
            const table = document.getElementById('table-pengajuan');
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
        function searchPenolakan() {
            // Get the search input value
            const input = document.getElementById('penolakan-search');
            const filter = input.value.toLowerCase();

            // Get the table and tbody elements
            const table = document.getElementById('penolakan-table');
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
</x-admin-layout>
