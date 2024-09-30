<!DOCTYPE html>
<html lang="en">
<x-header></x-header>
<body class="flex flex-col bg-gray-50 min-h-screen items-center">
    <div class="w-full max-w-4xl p-4">
        <h1 class="font-semibold text-3xl text-center mt-3 text-purple-700">{{ $outlet->nama_outlet }} - Menu Produk</h1>
        <input class="hidden" id="IdOutlet" value="{{ $outlet->id }}"></input>

        <!-- Search Input -->
        <div class="relative flex items-center w-full mt-2">
            <input 
                type="text" 
                id="simple-search" 
                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-3 pl-10 transition ease-in-out duration-300 shadow-sm hover:shadow-lg" 
                placeholder="Search Produk name..." 
                required
            />
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 19l-3.5-3.5m0 0a7 7 0 111.5-1.5l-3.5 3.5z" />
            </svg>
        </div>

        <!-- Daftar Produk -->
        <div id="produk-list" class="mt-6">
            @foreach ($groupedProduks as $kategoriId => $produkGroup)
                <h2 class="text-xl font-semibold text-purple-800 mt-4">{{ $produkGroup->first()->kategoris->nama_kategori }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-2">
                    @foreach ($produkGroup as $item)
                    <div class="produk-item p-4 bg-white rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 cursor-pointer relative"
                        data-id="{{ $item->id }}" data-price="{{ $item->harga_jual }}">
                        
                        <div class="flex items-center space-x-4 {{ $item->status === 'Habis' ? 'cursor-not-allowed' : 'cursor-pointer' }} relative" {{ $item->status === 'Habis' ? '' : 'onclick=toggleItem(event)' }}>
                            <div class="relative">
                                <img class="w-16 h-16 rounded-lg object-cover shadow-md" src="{{ asset('assets/' . $item->foto ) }}" alt="Product Image">

                                @if($item->status === 'Habis')
                                <!-- Watermark overlay when the product is out of stock -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg">
                                    <span class="text-white text-lg font-bold">Habis</span>
                                </div>
                                @endif
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-lg text-purple-800 produk-name">{{ $item->nama_produk }}</p>
                                <p class="text-sm text-gray-600">Rp. {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Quantity controls (visible if quantity > 0) -->
                        <div class="mt-4" id="controls-{{ $item->id }}" style="display: none;">
                            <div class="flex items-center space-x-4">
                                <button onclick="decrementQuantity(event)" class="bg-red-500 text-white px-4 py-2 rounded">-</button>
                                <span class="text-lg font-semibold" id="quantity-{{ $item->id }}">0</span>
                                <button onclick="incrementQuantity(event)" class="bg-green-500 text-white px-4 py-2 rounded">+</button>
                            </div>
                            <p class="mt-2 text-sm">Total Harga: Rp. <span id="price-{{ $item->id }}">0</span></p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Button to Open Modal -->
        <button onclick="toggleModal()" id="modalButton" class="fixed bottom-4 right-4 bg-blue-700 text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Buat Pesanan
        </button>
    </div>

    <div id="orderReviewModal" class="modal-order fixed inset-0 flex items-center justify-center hidden w-full bg-gray-800 bg-opacity-75">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md md:min-w-[70%] transform transition-all duration-300">
            <form id="orderFormContent" class="space-y-4">
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800">Review Pesanan</h2>
                </div>
            
                <!-- Modal Body (Scrollable) -->
                <div class="modal-body p-6 max-h-96 overflow-y-auto">
                    <div class="md:flex gap-6">
                        <!-- Order Items Review -->
                        <div class="w-full md:w-1/2">
                            <h3 class="text-xl font-semibold text-gray-700 mb-4">Produk yang Dipesan</h3>
                            <!-- List of items to be dynamically inserted here -->
                            <div id="orderItemsList" class="space-y-4">
                                <p class="text-gray-600 italic">Belum ada produk yang ditambahkan.</p>
                            </div>


                            <!-- Total Price -->
                            <div class="flex justify-between items-center mt-4">
                                <h3 class="text-lg font-semibold">Total Harga: Rp. <span id="totalPrice">0</span></h3>
                            </div>
                        </div>

                        <!-- Order Form Section -->
                        <div class="w-full md:w-1/2">
                            <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-3 md:mt-0">Form Pesanan</h3>
                            <input type="hidden" id="resi" name="resi" value="generateResi()">
                            <!-- Nama Pemesan -->
                            <div>
                                <label for="nama_pemesan" class="block text-sm font-medium text-gray-600 mb-1">Nama Pemesan</label>
                                <input type="text" id="nama_pemesan" maxlength="20" name="nama_pemesan" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500" placeholder="Masukkan nama" required>
                            </div>
                            <!-- No Telp -->
                            <div>
                                <label for="no_telp" class="block text-sm font-medium text-gray-600 mb-1">No. Telp <span class="text-red-500 font-thin">*(nomor aktif)</span></label>
                                <input type="text" id="no_telp" name="no_telp"  oninput="this.value=this.value.replace(/[^0-9 +\-]/g,'');" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500" placeholder="No Wa Anda" required>
                            </div>
                            <!-- Jam Mengambil -->
                            <div>
                                <label for="jam_mengambil" class="block text-sm font-medium text-gray-600 mb-1">Jam Mengambil</label>
                                <input type="time" id="jam_mengambil" name="jam_mengambil" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500" required>
                            </div>
                            <!-- Pembayaran -->
                            <div>
                                <label for="pembayaran" class="block text-sm font-medium text-gray-600 mb-1">Pembayaran</label>
                                <select name="pembayaran" id="" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500" required>
                                    <option value="" disabled selected>Pilih Metode Pembayaran</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>
                            <!-- Catatan -->
                            <div>
                                <label for="catatan" class="block text-sm font-medium text-gray-600 mb-1">Catatan</label>
                                <textarea id="catatan" name="catatan" rows="4" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500" placeholder="Tambahkan catatan jika ada"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-200 flex justify-end space-x-4">
                    <button type="button" onclick="toggleModal()" class="bg-red-500 hover:bg-red-600 text-white py-2 px-6 rounded-lg transition-colors duration-300">Tutup</button>
                    <button type="submit" id="submit-order" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg transition-colors duration-300">Kirim Pesanan</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        let selectedProducts = {};

        function toggleItem(event) {
            const productElement = event.currentTarget.closest('[data-id]');
            const productId = productElement.getAttribute('data-id');
            const productPrice = parseInt(productElement.getAttribute('data-price'));
            const productName = productElement.querySelector('.text-lg').textContent;
            const productImage = productElement.querySelector('img').getAttribute('src');

            if (!selectedProducts[productId]) {
                selectedProducts[productId] = { 
                    name: productName, 
                    price: productPrice, 
                    quantity: 1, 
                    image: productImage 
                };
            } else {
                selectedProducts[productId].quantity += 1;
            }

            document.getElementById(`quantity-${productId}`).textContent = selectedProducts[productId].quantity;
            document.getElementById(`price-${productId}`).textContent = (selectedProducts[productId].price * selectedProducts[productId].quantity).toLocaleString('id-ID');
            document.getElementById(`controls-${productId}`).style.display = 'block';

            updateOrderItemsList();
        }

        function decrementQuantity(event) {
            const productElement = event.currentTarget.closest('[data-id]');
            const productId = productElement.getAttribute('data-id');

            if (selectedProducts[productId]) {
                selectedProducts[productId].quantity -= 1;

                if (selectedProducts[productId].quantity <= 0) {
                    delete selectedProducts[productId];
                    document.getElementById(`controls-${productId}`).style.display = 'none';
                } else {
                    document.getElementById(`quantity-${productId}`).textContent = selectedProducts[productId].quantity;
                    document.getElementById(`price-${productId}`).textContent = (selectedProducts[productId].price * selectedProducts[productId].quantity).toLocaleString('id-ID');
                }

                updateOrderItemsList();
            }
        }

        function incrementQuantity(event) {
            const productElement = event.currentTarget.closest('[data-id]');
            const productId = productElement.getAttribute('data-id');

            if (selectedProducts[productId]) {
                selectedProducts[productId].quantity += 1;
                document.getElementById(`quantity-${productId}`).textContent = selectedProducts[productId].quantity;
                document.getElementById(`price-${productId}`).textContent = (selectedProducts[productId].price * selectedProducts[productId].quantity).toLocaleString('id-ID');
                updateOrderItemsList();
            }
        }

        function toggleModal() {
            const orderReviewModal = document.getElementById('orderReviewModal');
            if (orderReviewModal.style.display === 'none' || orderReviewModal.style.display === '') {
                orderReviewModal.style.display = 'flex';
            } else {
                orderReviewModal.style.display = 'none';
            }
        }

        function updateOrderItemsList() {
            const orderItemsList = document.getElementById('orderItemsList');
            orderItemsList.innerHTML = ''; // Clear previous items

            let totalPrice = 0;
            let totalQuantity = 0;

            for (const [id, product] of Object.entries(selectedProducts)) {
                if (product.quantity > 0) {
                    // Create the list item for each product
                    const listItem = document.createElement('div');
                    listItem.classList.add('flex', 'justify-between', 'mb-4');
                    listItem.innerHTML = `
                        <div class="flex items-center space-x-4">
                            <img src="${product.image}" alt="Product Image" class="w-16 h-16 rounded-lg object-cover shadow-md">
                            <div>
                                <p class="font-semibold">${product.name}</p>
                                <p class="text-sm">Harga: Rp. <span class="price">${product.price.toLocaleString('id-ID')}</span></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="decrementQuantityInModal('${id}')" class="bg-red-500 text-white px-2 py-1 rounded">-</button>
                            <input value="${id}" class="id_produks hidden">
                            <input type="text" id="modal-quantity-${id}" value="${product.quantity}" class="quantity-input border rounded px-2 py-1 text-center w-16" readonly>
                            <button onclick="incrementQuantityInModal('${id}')" class="bg-green-500 text-white px-2 py-1 rounded">+</button>
                        </div>
                    `;
                    orderItemsList.appendChild(listItem);

                    // Update the total price and total quantity
                    totalPrice += product.price * product.quantity;
                    totalQuantity += product.quantity;
                }
            }

            // Update the total price in the modal
            document.getElementById('totalPrice').textContent = totalPrice.toLocaleString('id-ID');
        }

        function showOrderReview() {
            const totalItems = Object.values(selectedProducts).reduce((total, product) => total + product.quantity, 0);
            const totalPrice = Object.values(selectedProducts).reduce((total, product) => total + (product.quantity * product.price), 0);

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalPrice').textContent = new Intl.NumberFormat('id-ID').format(totalPrice);

            if (totalItems > 0 && totalPrice > 0) {
                updateOrderItemsList();

                document.getElementById('resi').value = generateResi();
                document.getElementById('orderFormContainer').classList.remove('hidden');
                
                // Show the order review modal and hide the original modal
                document.getElementById('modal').style.display = 'none';
                document.getElementById('orderReviewModal').style.display = 'flex';
            } else {
                alert('Tidak ada item yang dipesan.');
            }
        }

        function incrementQuantityInModal(productId) {
            selectedProducts[productId].quantity++;
            document.getElementById(`modal-quantity-${productId}`).value = selectedProducts[productId].quantity;
            updateOrderItemsList(); // Update the modal
            updateGridQuantity(productId); // Sync with grid
        }

        function decrementQuantityInModal(productId) {
            if (selectedProducts[productId].quantity > 1) {
                selectedProducts[productId].quantity--;
            } else {
                delete selectedProducts[productId];
            }
            document.getElementById(`modal-quantity-${productId}`).value = selectedProducts[productId]?.quantity || 0;
            updateOrderItemsList(); // Update the modal
            updateGridQuantity(productId); // Sync with grid
        }

       function updateGridQuantity(productId) {
            if (selectedProducts[productId]) {
                document.getElementById(`quantity-${productId}`).textContent = selectedProducts[productId].quantity;
                document.getElementById(`price-${productId}`).textContent = (selectedProducts[productId].quantity * selectedProducts[productId].price).toLocaleString('id-ID');
            } else {
                document.getElementById(`controls-${productId}`).style.display = 'none';
            }
        }

        document.getElementById('submit-order').addEventListener('click', async function(event) {
            event.preventDefault(); 

            const submitButton = document.getElementById('submit-order');
            const orderListItems = document.querySelectorAll('#orderItemsList .flex');

            if (orderListItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Ada Order',
                    text: 'Tidak ada item di dalam daftar pesanan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            // Validasi Form Pesanan
            const namaPemesan = document.getElementById('nama_pemesan').value.trim();
            const noTelp = document.getElementById('no_telp').value.trim();
            const jamMengambil = document.getElementById('jam_mengambil').value;
            const pembayaran = document.querySelector('select[name="pembayaran"]').value;
            
            if (!namaPemesan || !noTelp || !jamMengambil || !pembayaran) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Tidak Lengkap',
                    text: 'Mohon isi semua kolom yang diperlukan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            // Tampilkan konfirmasi sebelum melanjutkan
            const confirmation = await Swal.fire({
                title: 'Konfirmasi Order',
                text: 'Apakah Anda yakin ingin melakukan order ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            });

            // Jika pengguna memilih 'Tidak', hentikan eksekusi
            if (!confirmation.isConfirmed) {
                return;
            }

            // Show loading spinner using SweetAlert2
            Swal.fire({
                title: 'Proses Pesanan Sedang Berlangsung...',
                text: 'Mohon tunggu, pesanan Anda sedang diproses.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Collect order data
            const orderItems = [];
            Array.from(orderListItems).forEach(item => {
                const quantityElement = item.querySelector('.quantity-input');
                const priceElement = item.querySelector('.price');
                const idElement = item.querySelector('.id_produks');

                if (quantityElement && priceElement && idElement) {
                    const productId = idElement.value;
                    const quantity = parseInt(quantityElement.value, 10);
                    const price = parseInt(priceElement.innerText.replace(/\D/g, ''), 10);
                    const subtotal = quantity * price;

                    orderItems.push({
                        product_id: productId,  
                        quantity: quantity,
                        price: price,
                        subtotal: subtotal
                    });
                }
            });

            const resiNumber = document.getElementById('resi').value;
            const tanggalTransaksi = new Date().toISOString().slice(0, 10);

            const catatan = document.getElementById('catatan').value.trim();
            const IdOutlet = document.getElementById('IdOutlet').value; 

            const totalQty = orderItems.reduce((sum, item) => sum + item.quantity, 0);
            const totalBelanja = orderItems.reduce((sum, item) => sum + item.subtotal, 0);

            // Send data to the server
            try {
                const response = await fetch('/pesanan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        resi: resiNumber,
                        nama_pemesan: namaPemesan,
                        no_telp: noTelp,
                        id_outlet: IdOutlet,
                        tanggal: tanggalTransaksi,
                        jam_mengambil: jamMengambil,
                        pembayaran: pembayaran,
                        catatan: catatan,
                        total_qty: totalQty,
                        total_belanja: totalBelanja,
                        items: orderItems
                    })
                });

                Swal.close();

                if (response.ok) {
                    const data = await response.json();

                    // SweetAlert for success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Berhasil',
                        text: 'Terima kasih, Order Anda Akan Segera di Proses!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                    }).then(() => {
                        // Disable the button after successful order
                        submitButton.disabled = true;

                        // Redirect to the order details page with the resi number
                        window.location.href = `/struk-order/${data.order_id}`;
                    });
                } else {
                    // Handle error response
                    console.error('Error response:', await response.json());
                    Swal.fire({
                        icon: 'error',
                        title: 'Order Gagal',
                        text: 'Gagal melakukan order. Silakan coba lagi.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                    });
                }
            } catch (error) {
                console.error('Error saving transaction:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Order Gagal',
                    text: 'Gagal melakukan order. Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                });
            }
        });

        function generateResi() {
            // Helper function to generate random letters
            function getRandomLetters(length) {
                const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                let result = '';
                for (let i = 0; i < length; i++) {
                    result += letters.charAt(Math.floor(Math.random() * letters.length));
                }
                return result;
            }

            // Helper function to generate random digits
            function getRandomDigits(length) {
                const digits = '0123456789';
                let result = '';
                for (let i = 0; i < length; i++) {
                    result += digits.charAt(Math.floor(Math.random() * digits.length));
                }
                return result;
            }

            // Get the current date
            const now = new Date();
            const year = now.getFullYear().toString().slice(-2); // Last two digits of the year
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Zero-padded month

            // Format: LLMMYYLLDDDDD
            // LL - 2 random letters
            // MMYY - Current month and last two digits of the year
            // LL - 2 random letters
            // DDDDD - 5 random digits

            const resiNumber = `${getRandomLetters(2)}${month}${year}${getRandomLetters(2)}${getRandomDigits(5)}`;

            return resiNumber.toUpperCase(); // Ensure the result is in uppercase
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('simple-search');
            const outletItems = document.querySelectorAll('.produk-item');

            searchInput.addEventListener('input', function () {
                const searchTerm = searchInput.value.toLowerCase();

                // Loop through all outlet items and hide those that don't match the search term
                outletItems.forEach(function (item) {
                    const outletName = item.querySelector('.produk-name').textContent.toLowerCase();

                    if (outletName.includes(searchTerm)) {
                        item.style.display = 'block';  // Show the item if it matches
                    } else {
                        item.style.display = 'none';   // Hide the item if it doesn't match
                    }
                });
            });
        });

    </script>

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
