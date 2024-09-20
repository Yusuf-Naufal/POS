<!DOCTYPE html>
<html lang="en">
<x-header></x-header>
<body class="flex flex-col bg-gray-50 min-h-screen items-center">
    <div class="w-full max-w-4xl p-4">
        <h1 class="font-semibold text-3xl text-center mt-3 text-purple-700">{{ $outlet->nama_outlet }} - Menu Produk</h1>
        <input class="hidden" id="IdOutlet" value="{{ $outlet->id }}"></input>

        <!-- Daftar Produk -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 px-4">
            @foreach ($produks as $item)
            <div class="p-4 bg-white rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 cursor-pointer relative"
                data-id="{{ $item->id }}" data-price="{{ $item->harga_jual }}">
                
                <div class="flex items-center space-x-4 {{ $item->status === 'Habis' ? 'cursor-not-allowed' : 'cursor-pointer' }} relative" {{ $item->status === 'Habis' ? '' : 'onclick=toggleItem(event)' }}>
                    <div class="relative">
                        <img class="w-16 h-16 rounded-lg object-cover shadow-md" src="{{ asset('storage/assets/' . $item->foto ) }}" alt="Product Image">

                        @if($item->status === 'Habis')
                        <!-- Watermark overlay when the product is out of stock -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg">
                            <span class="text-white text-lg font-bold">Habis</span>
                        </div>
                        @endif
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-lg text-purple-800">{{ $item->nama_produk }}</p>
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


        <!-- Button to Open Modal -->
        <button onclick="toggleModal()" id="modalButton" class="fixed bottom-4 right-4 bg-blue-700 text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Buat Pesanan
        </button>
    </div>

    <!-- Responsive Modal for Order Review with Scrollable Body -->
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
                                <input type="text" id="nama_pemesan" name="nama_pemesan" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500" placeholder="Masukkan nama" required>
                            </div>
                            <!-- No Telp -->
                            <div>
                                <label for="no_telp" class="block text-sm font-medium text-gray-600 mb-1">No. Telp <span class="text-red-500 font-thin">*(nomor aktif)</span></label>
                                <input type="text" id="no_telp" name="no_telp" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500" placeholder="No Wa Anda" required>
                            </div>
                            <!-- Jam Mengambil -->
                            <div>
                                <label for="jam_mengambil" class="block text-sm font-medium text-gray-600 mb-1">Jam Mengambil</label>
                                <input type="time" id="jam_mengambil" name="jam_mengambil" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500">
                            </div>
                            <!-- Pembayaran -->
                            <div>
                                <label for="pembayaran" class="block text-sm font-medium text-gray-600 mb-1">Pembayaran</label>
                                <select name="pembayaran" id="" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-500">
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

            // Disable buttons to prevent multiple submissions
            document.getElementById('submit-order').disabled = true;

            const orderListItems = document.querySelectorAll('#orderItemsList .flex');

            if (orderListItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Ada Order',
                    text: 'Tidak ada item di dalam daftar pesanan.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Collect order data
            const orderItems = [];
            const items = Array.from(orderListItems).map(item => {
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
                } else {
                    // console.error('Missing quantity, price, or product ID element');
                }
            });

            const namaPemesan = document.getElementById('nama_pemesan').value;
            const IdOutlet = document.getElementById('IdOutlet').value;
            const noTelp = document.getElementById('no_telp').value;
            const jamMengambil = document.getElementById('jam_mengambil').value;
            const pembayaran = document.querySelector('select[name="pembayaran"]').value;
            const catatan = document.getElementById('catatan').value;
            const resiNumber = document.getElementById('resi').value; // Assuming resi is already set
            const tanggalTransaksi = new Date().toISOString().slice(0, 10);

            const totalQty = orderItems.reduce((sum, item) => sum + item.quantity, 0);
            const totalBelanja = orderItems.reduce((sum, item) => sum + item.subtotal, 0);

            // Show loading spinner using SweetAlert2
            Swal.fire({
                title: 'Proses Pesanan Sedang Berlangsung...',
                text: 'Mohon tunggu, pesanan Anda sedang diproses.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading(); // Display the loading spinner
                }
            });


            // Send data to the server
            try {
                console.log('resiNumber', resiNumber, namaPemesan);
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

                    // const waMessage = `Halo, berikut adalah detail pesanan Anda:\n\n` +
                    //     `Resi: ${resiNumber}\n` +
                    //     `Pemesan: ${namaPemesan}\n` +
                    //     `No Telp: ${noTelp}\n` +
                    //     `Tanggal: ${tanggalTransaksi}\n` +
                    //     `Jam Ambil: ${jamMengambil}\n\n` +
                    //     `Detail Pesanan:\n` +
                    //     orderItems.map(item => `${item.nama_produk} x ${item.qty} - Rp. ${item.subtotal.toLocaleString('id-ID')}`).join('\n') + `\n\n` +
                    //     `Total Qty: ${totalQty}\n` +
                    //     `Total Belanja: Rp. ${totalBelanja.toLocaleString('id-ID')}\n\n` +
                    //     `*Tunjukkan struk ini untuk mengambil pesanan*`;

                    // // Open WhatsApp with the pre-filled message
                    // window.open(`https://wa.me/${noTelp}?text=${encodeURIComponent(waMessage)}`, '_blank');


                    // Redirect to order details page with the generated resi number
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Berhasil',
                        text: 'Terima kasih, Order Anda Akan Segera di Proses!',
                        confirmButtonText: 'OK'
                    }).then(() => {
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
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                console.error('Error saving transaction:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Order Gagal',
                    text: 'Gagal melakukan order. Silakan coba lagi.',
                    confirmButtonText: 'OK'
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


    </script>

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
