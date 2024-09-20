<x-user-layout>
    <x-aside-user :outlet="$Outlet"></x-aside-user>

    <div id="main-content" class="w-full transition-all duration-300">
        <x-nav-user></x-nav-user>

        <h1 class="text-3xl font-bold my-4 ml-3">Orderan</h1>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-4">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
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
                                "{{ number_format($order->total_belanja, 0, ',', '.') }}",
                                "{{ number_format($order->total_belanja, 0, ',', '.') }}",
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
        <div id="orderModal" class="w-full fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
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
                    <button id="processOrderButton" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Process</button>
                    <button id="denyOrderButton" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Denied</button>
                </div>
            </div>

        </div>

    </div>

    <script>
        function openModal(id, outletName, outletAddress, outletPhone, date, resi, customerName, pickupTime, customerTelp, totalQty, subtotal, total, notes, orderItems) {
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
            document.getElementById('modal-subtotal').textContent = `Rp. ${subtotal}`;
            document.getElementById('modal-total').textContent = `Rp. ${total}`;
            document.getElementById('modal-notes').textContent = notes;

            // Populate order items
            const orderItemsTable = document.getElementById('modal-order-items');
            orderItemsTable.innerHTML = ''; // Clear existing items
            orderItems.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200';
                row.innerHTML = `
                    <td class="py-2">${item.produk.nama_produk}</td>
                    <td class="py-2">${item.qty}</td>
                    <td class="py-2 text-right">Rp. ${item.subtotal}</td>
                `;
                orderItemsTable.appendChild(row);
            });

            // Set the global orderId
            window.currentOrderId = id;

            // Show the modal
            document.getElementById('orderModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const processOrderButton = document.getElementById('processOrderButton');
            const denyOrderButton = document.getElementById('denyOrderButton');

            // Function to update order status
            function updateOrderStatus(status) {
                if (!window.currentOrderId) {
                    alert('Order ID is not set.');
                    return;
                }

                fetch(`/orders/${window.currentOrderId}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ status }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Berhasil',
                            text: 'Order Akan Diproses',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            closeModal(); // Close modal if needed
                            location.reload();
                        });
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Order Gagal',
                            text: 'Status Tidak Berubah',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saving transaction:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Order Gagal',
                        text: 'Orderan gagal diproses',
                        confirmButtonText: 'OK'
                    });
                });
            }

            // Function to show confirmation alert and then update status
            function showConfirmationDialog(status) {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin mengubah status pesanan ini menjadi ${status}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ubah status!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateOrderStatus(status);
                    }
                });
            }

            // Event listeners for buttons
            processOrderButton.addEventListener('click', () => showConfirmationDialog('Process'));
            denyOrderButton.addEventListener('click', () => showConfirmationDialog('Denied'));
        });
    </script>
</x-user-layout>