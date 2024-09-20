<!DOCTYPE html>
<html lang="id">
<x-header></x-header>
<body class="bg-gray-100 p-10">
    <div class="max-w-lg mx-auto p-4 bg-white shadow-lg rounded-lg">
        <!-- Header Outlet Info -->
        <div class="text-center mb-6">
            <h1 class="text-xl font-semibold">{{ $order->outlet->nama_outlet }}</h1>
            <p class="text-sm text-gray-500">{{ $order->outlet->alamat }}</p>
            <p class="text-sm text-gray-500">{{ $order->outlet->no_telp }}</p>
        </div>

        <!-- Divider with Title -->
        <div class="relative flex items-center justify-center mb-4">
            <hr class="w-full border-t border-gray-200">
            <span class="absolute px-3 bg-white text-gray-600">Struk Order</span>
        </div>

        <!-- Order Details -->
        <div class="mb-4">
            <div class="flex justify-between text-sm">
                <p class="font-medium">Tanggal: {{ $order->tanggal }}</p>
                <p class="font-medium">Resi: {{ $order->resi }}</p>
                <p class="font-medium hidden">{{ $order->id }}</p>
            </div>
            <p class="text-sm text-gray-600">Pemesan: {{ $order->nama_pemesan }}</p>
            <p class="text-sm text-gray-600">Jam Ambil: {{ $order->jam_mengambil }}</p>
            <p class="text-sm text-gray-600">No Telpon: {{ $order->no_telp }}</p>
        </div>

        <!-- Dotted Divider -->
        <hr class="border-t-2 border-dashed border-gray-400 my-4">

        <!-- Order Items -->
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="py-2">Produk</th>
                    <th class="py-2">Qty</th>
                    <th class="py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->detailOrders as $detail)
                <tr class="border-b border-gray-200">
                    <td class="py-2">{{ $detail->produk->nama_produk }}</td>
                    <td class="py-2">{{ $detail->qty }}</td>
                    <td class="py-2 text-right">Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Dotted Divider -->
        <hr class="border-t-2 border-dashed border-gray-400 my-4">

        <!-- Total Summary -->
        <div class="text-sm font-medium">
            <div class="flex justify-between">
                <p>Total Qty:</p>
                <p>{{ $order->total_qty }}</p>
            </div>
            <div class="flex justify-between">
                <p>Sub Total:</p>
                <p>Rp. {{ number_format($order->total_belanja, 0, ',', '.') }}</p>
            </div>
            <div class="flex justify-between text-lg font-semibold">
                <p>Total:</p>
                <p>Rp. {{ number_format($order->total_belanja, 0, ',', '.') }}</p>
            </div>
            <div class="flex justify-between text-lg font-semibold">
                <p>Status:</p>
                <p>{{ $order->status }}</p>
            </div>
        </div>

        <!-- Dotted Divider -->
        <hr class="border-t-2 border-dashed border-gray-400 my-4">

        <!-- Notes Section -->
        <div class="mt-4">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold">Catatan</h2>
                <p class="text-red-600 font-extralight text-xs">*Tunjukan stuk ini untuk mengambil pesanan</p>
            </div>
            <p class="text-sm text-gray-600">{{ $order->catatan ?? 'Tidak ada catatan' }}</p>
        </div>

        <p class="text-red-600 font-extralight text-xs mt-4">bisa batalkan pesanan sebelum diprocess -></p>
    </div>

    <div data-dial-init class="fixed end-6 bottom-6 group">
        <div id="speed-dial-menu-bottom-right" class="flex flex-col items-center hidden mb-4 space-y-2">
            @if ($order->status === 'Pending')
            <a href="" data-order-id="{{ $order->id }}" type="button" data-tooltip-target="tooltip-download" data-tooltip-placement="left" class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 dark:border-gray-600 shadow-sm dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M3 16.74L7.76 12L3 7.26L7.26 3L12 7.76L16.74 3L21 7.26L16.24 12L21 16.74L16.74 21L12 16.24L7.26 21zm9-3.33l4.74 4.75l1.42-1.42L13.41 12l4.75-4.74l-1.42-1.42L12 10.59L7.26 5.84L5.84 7.26L10.59 12l-4.75 4.74l1.42 1.42z"/></svg>
                <span class="sr-only">Batal Pesanan</span>
            </a>
            <div id="tooltip-download" role="tooltip" class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Batal Pesanan
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
            @endif
            <a href="{{ route('order.pdf', $order->id) }}" type="button" data-tooltip-target="tooltip-download" data-tooltip-placement="left" class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 dark:border-gray-600 shadow-sm dark:hover:text-white dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                    <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Download</span>
            </a>
            <div id="tooltip-download" role="tooltip" class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Download
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
            <a href="{{ route('daftar-outlet.index') }}" type="button" data-tooltip-target="tooltip-copy" data-tooltip-placement="left" class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 dark:border-gray-600 dark:hover:text-white shadow-sm dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:focus:ring-gray-400">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 0 1-1.414 0l-5-5a1 1 0 0 1 0-1.414l5-5a1 1 0 0 1 1.414 1.414L4.414 9H17a1 1 0 1 1 0 2H4.414l3.293 3.293a1 1 0 0 1 0 1.414z" clip-rule="evenodd"/>
                </svg>
                <span class="sr-only">Kembali</span>
            </a>
            <div id="tooltip-copy" role="tooltip" class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Kembali
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </div>
        <button type="button" data-dial-toggle="speed-dial-menu-bottom-right" aria-controls="speed-dial-menu-bottom-right" aria-expanded="false" class="flex items-center justify-center text-white bg-blue-700 rounded-full w-14 h-14 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800">
            <svg class="w-5 h-5 transition-transform group-hover:rotate-45" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
            </svg>
            <span class="sr-only">Open actions menu</span>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select all elements with data-order-id attribute
            document.querySelectorAll('[data-order-id]').forEach(cancelButton => {
                cancelButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    let orderId = this.getAttribute('data-order-id');

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, batalkan!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Mengirim permintaan AJAX untuk menghapus order
                            fetch(`/order/${orderId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Dibatalkan!',
                                        data.message,
                                        'success'
                                    ).then(() => {
                                        // Redirect atau reload halaman setelah berhasil
                                        window.location.href = '/daftar-outlet';
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        'Gagal membatalkan pesanan',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire(
                                    'Terjadi Kesalahan!',
                                    'Terjadi kesalahan. Silakan coba lagi.',
                                    'error'
                                );
                            });
                        }
                    });
                });
            });
        });


    </script>
    <!-- Flowbite Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
