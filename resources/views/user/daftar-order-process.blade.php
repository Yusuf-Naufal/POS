<x-user-layout>
    <x-aside-user :outlet="$Outlet"></x-aside-user>

    <div id="main-content" class="w-full transition-all duration-300">
        <x-nav-user></x-nav-user>

        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold my-4 ml-3">Order : "Process"</h1>
            <div class="max-w-96 min-w-80">
                <div class="flex items-center max-w-sm mx-auto">
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <input type="text" id="simple-search" onkeyup="searchTable()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Orderan..." required />
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-2">
            <div class="overflow-x-auto">
                <table id="order-table" class="min-w-full bg-white">
                    <thead class="bg-gray-600 border-b">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-white">Resi</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-white">Nama Pemesan</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-white">No. Telepon</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-white">Tanggal</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-white">Total Qty</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-white">Total Belanja</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-white">Jam Mengambil</th>
                        </tr>
                    </thead>
                    <tbody id="data-body" class="text-gray-700 text-sm">
                        @foreach ($PendingOrders  as $order)
                            <tr class="border-b hover:bg-gray-100 cursor-pointer" onclick='openModal(
                                "{{ $order->id }}",
                                "{{ $order->outlet->nama_outlet }}",
                                "{{ $order->outlet->alamat }}",
                                "{{ $order->outlet->no_telp }}",
                                "{{ $order->tanggal }}",
                                "{{ $order->resi }}",
                                "{{ $order->nama_pemesan }}",
                                "{{ $order->jam_mengambil }}",
                                "{{ $order->no_telp }}",
                                "{{ $order->total_qty }}",
                                "{{ $order->total_belanja }}",
                                "{{ $order->catatan ?? 'Tidak ada catatan' }}",
                                @json($order->detailOrders)
                            )'>
                                <td class="py-3 px-4">{{ $order->resi }}</td>
                                <td class="py-3 px-4">{{ $order->nama_pemesan }}</td>
                                <td class="py-3 px-4">{{ $order->no_telp }}</td>
                                <td class="py-3 px-4">{{ $order->tanggal }}</td>
                                <td class="py-3 px-4">{{ $order->total_qty }}</td>
                                <td class="py-3 px-4">Rp. {{ number_format($order->total_belanja, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">{{ $order->jam_mengambil }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Structure -->
        <div id="orderModal" class="w-full fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-40 hidden">
            <div class="w-full max-w-3xl mx-auto p-6 bg-white shadow-lg rounded-lg relative">
                <!-- Close Button -->
                <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Header Outlet Info -->
                <div class="text-center mb-6">
                    <h1 id="modal-outlet-name" class="text-2xl font-semibold"></h1>
                    <p id="modal-outlet-address" class="text-sm text-gray-500"></p>
                    <p id="modal-outlet-phone" class="text-sm text-gray-500"></p>
                </div>

                <!-- Divider with Title -->
                <div class="relative flex items-center justify-center mb-6">
                    <hr class="w-full border-t border-gray-200">
                    <span class="absolute px-3 bg-white text-gray-600">Struk Order</span>
                </div>

                <div class="modal-body mt-6 max-h-96 overflow-y-auto">
                    <!-- Order Details -->
                    <div class="mb-6">
                        <div class="flex justify-between text-sm mb-2">
                            <p class="font-medium" id="modal-date"></p>
                            <p class="font-medium" id="modal-resi"></p>
                        </div>
                        <p class="text-sm text-gray-600" id="modal-customer-name"></p>
                        <p class="text-sm text-gray-600" id="modal-pickup-time"></p>
                        <p class="text-sm text-gray-600" id="modal-customer-telp"></p>
                    </div>

                    <!-- Dotted Divider -->
                    <hr class="border-t-2 border-dashed border-gray-400 my-4">

                    <!-- Order Items -->
                    <table class="w-full text-sm text-left text-gray-500 mb-4">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="py-2">Produk</th>
                                <th class="py-2">Qty</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="modal-order-items">
                            <!-- Order items will be dynamically inserted here -->
                        </tbody>
                    </table>

                    <!-- Dotted Divider -->
                    <hr class="border-t-2 border-dashed border-gray-400 my-4">

                    <!-- Total Summary -->
                    <div class="text-sm font-medium mb-4">
                        <div class="flex justify-between">
                            <p>Total Qty:</p>
                            <p id="modal-total-qty"></p>
                        </div>
                        <div class="flex justify-between">
                            <p>Sub Total:</p>
                            <p id="modal-subtotal"></p>
                        </div>
                        <div class="flex justify-between text-lg font-semibold">
                            <p>Total:</p>
                            <p id="modal-total"></p>
                        </div>
                    </div>
                </div>

                <!-- Dotted Divider -->
                <hr class="border-t-2 border-dashed border-gray-400 my-4">

                <!-- Notes Section -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold">Catatan</h2>
                        <p class="text-red-600 font-extralight text-xs">*Tunjukan struk ini untuk mengambil pesanan</p>
                    </div>
                    <p id="modal-notes" class="text-sm text-gray-600"></p>
                </div>

                <div class="flex justify-end space-x-4 mt-6">
                    <button id="finisOrderButton" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Bayar</button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="paymentModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg w-1/3">
                <h2 class="text-lg font-semibold mb-4">Pembayaran</h2>
    
                <div class="mb-4">
                    <label for="totalBelanja" class="block text-sm font-medium text-gray-700">Total Belanja</label>
                    <input type="text" id="totalBelanja" class="border border-gray-300 px-3 py-2 rounded w-full" readonly>
                </div>
    
                <div class="mb-4">
                    <label for="bayar" class="block text-sm font-medium text-gray-700">Bayar</label>
                    <input type="number" id="bayar" class="border border-gray-300 px-3 py-2 rounded w-full" placeholder="Masukkan jumlah pembayaran">
                </div>
    
                <div class="mb-4">
                    <label for="kembali" class="block text-sm font-medium text-gray-700">Kembali</label>
                    <input type="text" id="kembali" class="border border-gray-300 px-3 py-2 rounded w-full" readonly>
                </div>
    
                <div class="flex justify-end space-x-2">
                    <button id="cancelButton" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</button>
                    <button id="confirmPaymentButton" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        let totalAmount = 0;
        window.currentOrderId = {{ $order->id ?? 'null' }};
        window.orderDetails = @json($order->detailOrders ?? []);

        function openModal(id, outletName, outletAddress, outletPhone, date, resi, customerName, pickupTime, customerTelp, totalQty, totalBelanja, notes, orderItems) {
            // Set modal content
            document.getElementById('modal-outlet-name').textContent = outletName;
            document.getElementById('modal-outlet-address').textContent = outletAddress;
            document.getElementById('modal-outlet-phone').textContent = outletPhone;
            document.getElementById('modal-date').textContent = `Tanggal: ${date}`;
            document.getElementById('modal-resi').textContent = `Resi: ${resi}`;
            document.getElementById('modal-customer-name').textContent = `Pemesan: ${customerName}`;
            document.getElementById('modal-pickup-time').textContent = `Jam Ambil: ${pickupTime}`;
            document.getElementById('modal-customer-telp').textContent = `No. Telepon: ${customerTelp}`;
            document.getElementById('modal-total-qty').textContent = totalQty;
            document.getElementById('modal-subtotal').textContent = `Rp. ${parseInt(totalBelanja).toLocaleString('id-ID')}`;
            document.getElementById('modal-total').textContent = `Rp. ${parseInt(totalBelanja).toLocaleString('id-ID')}`;
            document.getElementById('modal-notes').textContent = notes;

            // Populate order items
            const orderItemsTable = document.getElementById('modal-order-items');
            orderItemsTable.innerHTML = ''; // Clear existing items
            orderItems.forEach(item => {
                console.log('Order Item:', item);
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200';
                row.innerHTML = `
                    <td class="py-2 hidden">${item.id_produk}</td>
                    <td class="py-2">${item.produk.nama_produk}</td>
                    <td class="py-2">${item.qty}</td>
                    <td class="py-2 text-right">Rp. ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                `;
                orderItemsTable.appendChild(row);
            });

            totalAmount = parseInt(totalBelanja); // Update totalAmount

            // Set the global orderId
            window.currentOrderId = id;

            // Show the modal
            document.getElementById('orderModal').classList.remove('hidden');
        }


        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        function searchTable() {
            // Get the search input value
            const input = document.getElementById('simple-search');
            const filter = input.value.toLowerCase();

            // Get the table and tbody elements
            const table = document.getElementById('order-table');
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
            // Order and Payment Modals
            const orderModal = document.getElementById('orderModal');
            const paymentModal = document.getElementById('paymentModal');
            const finisOrderButton = document.getElementById('finisOrderButton');
            const cancelButton = document.getElementById('cancelButton');
            const totalBelanja = document.getElementById('totalBelanja');
            const bayarInput = document.getElementById('bayar');
            const kembaliInput = document.getElementById('kembali');
            const confirmPaymentButton = document.getElementById('confirmPaymentButton');

            function openPaymentModal() {
                orderModal.classList.add('hidden');
                paymentModal.classList.remove('hidden');

                // Set the total amount in the payment modal
                totalBelanja.value = `Rp. ${totalAmount.toLocaleString('id-ID')}`;
            }

            function closePaymentModal() {
                paymentModal.classList.add('hidden');
                orderModal.classList.remove('hidden');

                bayarInput.value = '';
                kembaliInput.value = '';
            }

            finisOrderButton.addEventListener('click', openPaymentModal);

            cancelButton.addEventListener('click', closePaymentModal);

            confirmPaymentButton.addEventListener('click', function () {
                const bayar = parseFloat(bayarInput.value) || 0;
                const totalAmount = parseFloat(totalBelanja.value.replace(/[^\d]/g, ''));

                if (bayar < totalAmount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaksi Gagal',
                        text: 'Uang Pembayaran Kurang',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // First, send the request to update the transaction
                    fetch('/update-transaction', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            orderId: window.currentOrderId,
                            totalAmount: totalAmount,
                            payment: bayar,
                            change: bayar - totalAmount,
                            details: window.orderDetails || [] // Ensure to pass the order details correctly
                        })
                    })
                    .then(response => response.text()) // Get the response as text first
                    .then(text => {
                        console.log('Server response text:', text); // Debug: Log the raw text
                        return JSON.parse(text); // Attempt to parse as JSON
                    })
                    .then(data => {
                        if (data.success) {
                            // If the transaction was successful, show the success alert
                            Swal.fire({
                                icon: 'success',
                                title: 'Transaksi Berhasil',
                                text: 'Terima kasih, transaksi Anda telah berhasil!',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // After the success alert is confirmed, reload the page or perform any other actions
                                window.location.reload();
                            });
                        } else {
                            // If the server response indicates failure, show an error alert
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: data.error || 'Gagal memproses transaksi.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        // Handle any errors that occurred during the fetch
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: error.message,
                            confirmButtonText: 'OK'
                        });
                    });
                }

            });

            bayarInput.addEventListener('input', function () {
                const bayar = parseFloat(bayarInput.value) || 0;
                const kembali = bayar - totalAmount;
                kembaliInput.value = kembali >= 0 ? `Rp. ${kembali.toLocaleString('id-ID')}` : 'Rp. 0';
            });
        });




    </script>


</x-user-layout>