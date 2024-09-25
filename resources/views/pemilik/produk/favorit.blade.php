<x-master-layout>
    <div class="flex gap-3 flex-col md:flex-row">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">Menu yang Terjual:</h1>
                <form method="GET" action="{{ route('master.produk.favorit') }}" class="flex items-center mt-4">
                    <select name="kategori" onchange="this.form.submit()" class="p-2 border rounded shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
                        <option value="">Pilih Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ $selectedKategori == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table id="produk-table" class="min-w-full bg-white rounded-lg shadow-lg">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left">Gambar</th>
                            <th scope="col" class="px-4 py-2 text-left">Nama</th>
                            <th scope="col" class="px-4 py-2 text-left">SKU</th>
                            <th scope="col" class="px-4 py-2 text-left">Kategori</th>
                            <th scope="col" class="px-4 py-2 text-left">Unit</th>
                            <th scope="col" class="px-4 py-2 text-left">Harga</th>
                            <th scope="col" class="px-4 py-2 text-left">Status</th>
                            <th scope="col" class="px-4 py-2 text-center">Total Terjual</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($terjual as $item)
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-4">
                                    <img class="w-16 h-16 object-cover rounded" src="{{ asset('storage/assets/' . $item->foto) }}" alt="Produk">
                                </td>
                                <td class="px-4 py-4">{{ $item->nama_produk }}</td>
                                <td class="px-4 py-4">{{ $item->sku }}</td>
                                <td class="px-4 py-4">{{ $item->kategoris->nama_kategori }}</td>
                                <td class="px-4 py-4">{{ $item->units->nama_unit }}</td>
                                <td class="px-4 py-4">Rp. {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ $item->status }}</td>
                                <td class="px-4 py-4 text-center">{{ $item->total_qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-master-layout>
