<x-master-layout>
    <div class="w-full p-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Transaksi - Resi: {{ $transaksi->resi }}</h1>
            <div class="flex gap-2">
                <a type="button" href="{{ route('detail-transaksi', $transaksi->resi) }}" class="text-gray-500 hover:text-white border border-gray-500 hover:bg-gray-500 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Batal
                </a>
                
            </div>
        </div>

        <!-- Form Start -->
        <form id="editForm" action="{{ route('transaksi.update', $transaksi->id) }}" method="POST">
            @csrf
            @method('PUT')
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

            <!-- Order Details Table -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-6">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">QTY</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3">Satuan</th>
                            <th class="px-6 py-3">Subtotal</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-table-body">
                        @foreach ($detailTransaksi as $key => $items)
                            <tr class="bg-white dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <select name="produk[{{ $key }}][id]" class="border border-gray-300 rounded-md w-full p-2 produk-select" data-key="{{ $key }}">
                                        @foreach ($semuaProduks as $produk)
                                            <option value="{{ $produk->id }}" data-price="{{ $produk->harga_jual }}" data-kategori="{{ $produk->kategoris->nama_kategori }}" {{ $produk->id == $items->produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" class="border border-gray-300 rounded-md w-full p-2 kategori-input" value="{{ $items->produk->kategoris->nama_kategori }}" readonly />
                                </td>
                                <td class="px-6 py-4">
                                    <input name="produk[{{ $key }}][qty]" type="number" class="border border-gray-300 rounded-md w-full p-2 qty-input" value="{{ $items->qty }}" data-key="{{ $key }}" />
                                </td>
                                <td class="px-6 py-4 harga">{{ $items->produk->harga_jual }}</td>
                                <td class="px-6 py-4">{{ $items->produk->units->nama_unit }}</td>
                                <td class="px-6 py-4 subtotal">{{ $items->subtotal }}</td>
                                <td class="px-6 py-4">
                                    <button type="button" class="text-red-500 hover:underline remove-item" data-key="{{ $key }}">Hapus</button>
                                </td>
                            </tr>
                        @endforeach

                        <!-- Add New Product Row Template -->
                        <tr id="new-product-row" class="bg-white dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 hidden">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">#</td>
                            <td class="px-6 py-4">
                                <select name="produk[new][id]" class="border border-gray-300 rounded-md w-full p-2 produk-select">
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produksYangBelumAda as $produk)
                                        <option value="{{ $produk->id }}"
                                            data-price="{{ $produk->harga_jual }}"
                                            data-kategori="{{ $produk->kategoris->nama_kategori }}"
                                            data-unit="{{ $produk->units->nama_unit }}">
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" class="border border-gray-300 rounded-md w-full p-2 kategori-input" value="" readonly />
                            </td>
                            <td class="px-6 py-4">
                                <input name="produk[new][qty]" type="number" class="border border-gray-300 rounded-md w-full p-2 qty-input" value="1" />
                            </td>
                            <td class="px-6 py-4 harga">-</td>
                            <td class="px-6 py-4 unit">-</td>
                            <td class="px-6 py-4 subtotal">-</td>
                            <td class="px-6 py-4">
                                <button type="button" class="text-green-500 hover:underline add-item">Baru</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Add New Product Button -->
                <div class="px-6 py-4">
                    <button type="button" id="add-row-button" class="text-green-500 hover:underline">Tambah Baris Baru</button>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-gray-100 p-6 rounded-lg shadow-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Catatan Section -->
                    <div class="space-y-4">
                        <label for="Catatan" class="block text-lg font-semibold text-gray-700">Catatan:</label>
                        <textarea id="Catatan" name="catatan" rows="10" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-150 ease-in-out">{{ $transaksi->catatan }}</textarea>
                    </div>

                    <!-- Summary Section -->
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
                            <p class="text-lg font-medium text-gray-900">
                                @if($transaksi->status == 'Selesai')
                                    Lunas
                                @else
                                    Belum Lunas
                                @endif
                            </p>
                        </div>
                        <button id="submit-button" type="button" class="bg-purple-600 text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-150 ease-in-out">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" id="transaction-id" value="{{ $transaksi->id }}">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const updateTotals = () => {
                let subtotal = 0;
                document.querySelectorAll('.subtotal').forEach((element) => {
                    const value = parseFloat(element.textContent.replace(/[^0-9.-]+/g, '')) || 0;
                    subtotal += value;
                });

                // Update subtotal and total
                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('total').textContent = subtotal.toFixed(2);
            };

            // Function to attach event listeners
            const attachEventListeners = () => {
                document.querySelectorAll('.produk-select').forEach(select => {
                    select.addEventListener('change', function() {
                        const row = this.closest('tr');
                        const selectedOption = this.options[this.selectedIndex];
                        
                        // Update the category and price fields
                        const kategoriInput = row.querySelector('.kategori-input');
                        const hargaCell = row.querySelector('.harga');
                        const qtyInput = row.querySelector('.qty-input');
                        const unitCell = row.querySelector('.unit');
                        const subtotalCell = row.querySelector('.subtotal');
                        
                        // Get selected product data
                        const price = parseFloat(selectedOption.dataset.price) || 0;
                        const kategori = selectedOption.dataset.kategori || '';
                        const unit = selectedOption.dataset.unit || '';

                        // Update fields
                        kategoriInput.value = kategori;
                        hargaCell.textContent = price.toFixed(2);
                        unitCell.textContent = unit;
                        
                        // Update subtotal based on quantity and price
                        const qty = parseFloat(qtyInput.value) || 0;
                        const subtotal = price * qty;
                        subtotalCell.textContent = subtotal.toFixed(2);
                        updateTotals();
                    });
                });

                document.querySelectorAll('.qty-input').forEach(input => {
                    input.addEventListener('input', function() {
                        const row = this.closest('tr');
                        const harga = parseFloat(row.querySelector('.harga').textContent) || 0;
                        const qty = parseFloat(this.value) || 0;
                        const subtotalCell = row.querySelector('.subtotal');

                        // Update subtotal
                        const subtotal = harga * qty;
                        subtotalCell.textContent = subtotal.toFixed(2);
                        updateTotals();
                    });
                });

                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        const row = this.closest('tr');
                        
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            text: "Apakah Anda yakin ingin menghapus item ini?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                row.remove();
                                updateTotals();
                            }
                        });
                    });
                });
            };

            let newRowCounter = @json($detailTransaksi->count()); // Start with the current count of rows
            // Add event listener to dynamically added rows
            document.getElementById('add-row-button').addEventListener('click', function() {
                newRowCounter++; // Increment the counter for unique row identification

                const newRow = document.querySelector('#new-product-row').cloneNode(true);
                newRow.classList.remove('hidden'); // Show the new row

                // Update names for new row fields
                newRow.querySelector('select').name = `produk[${newRowCounter}][id]`;
                newRow.querySelector('input[name="produk[new][qty]"]').name = `produk[${newRowCounter}][qty]`;

                // Reset values for new row
                newRow.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    if (input.name.includes('qty')) {
                        input.value = 1; // Default value for quantity
                    }
                });
                newRow.querySelector('select').value = '';
                newRow.querySelector('.kategori-input').value = '';
                newRow.querySelector('.harga').textContent = '-';
                newRow.querySelector('.unit').textContent = '-';
                newRow.querySelector('.subtotal').textContent = '-';

                // Replace button text with "Hapus"
                const addButton = newRow.querySelector('.add-item');
                addButton.classList.remove('text-green-500');
                addButton.classList.add('text-red-500');
                addButton.textContent = 'Hapus';
                addButton.classList.remove('add-item');
                addButton.classList.add('remove-item');

                // Add the new row to the table
                document.querySelector('#transaction-table-body').appendChild(newRow);

                // Debug: Check the new row data
                console.log('New row data:', newRow.innerHTML);

                attachEventListeners(); // Ensure this function is defined and attached
            });

            document.getElementById('submit-button').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('editForm'));

                // Convert FormData to a structured object
                const produk = Array.from(formData.entries()).reduce((acc, [key, value]) => {
                    const match = key.match(/^produk\[(\d+)\]\[(\w+)\]$/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!acc[index]) acc[index] = {};
                        acc[index][field] = value;
                    }
                    return acc;
                }, []);

                // Filter out any empty entries
                const filteredProduk = produk.filter(item => item && Object.keys(item).length > 0);
                
                // Debug: Check the produk data before sending
                console.log('Produk Data:', filteredProduk);

                fetch(`/transaksi/${document.getElementById('transaction-id').value}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ produk: filteredProduk })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil',
                            text: data.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaksi Gagal',
                        text: 'Gagal melakukan transaksi. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                });
            });

            attachEventListeners();
        });
    </script>

</x-master-layout>
