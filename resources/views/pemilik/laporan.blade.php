<x-master-layout>
    <style>
        /* Default styles for the calendar header */
        .fc-toolbar {
            font-size: 10px; /* Base font size */
        }

        /* Adjust font size for larger screens */
        @media (min-width: 768px) {
            .fc-toolbar {
                font-size: 16px; /* Larger font size for medium screens */
            }
        }

        /* Adjust font size for even larger screens */
        @media (min-width: 1024px) {
            .fc-toolbar {
                font-size: 20px; /* Larger font size for large screens */
            }
        }

        /* Additional styles for better appearance */
        .table-header {
            background-color: #f9fafb; /* Light gray background */
            color: #4a5568; /* Dark text */
        }

        .table-row:hover {
            background-color: #edf2f7; /* Light hover effect */
        }

        .input-readonly {
            background-color: #f7fafc; /* Lighter background */
            border: 1px solid #cbd5e0; /* Border color */
        }

        .calendar-container {
            background-color: #ffffff; /* White background */
            border-radius: 0.5rem; /* Rounded corners */
            padding: 1rem; /* Padding around the calendar */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
    </style>
    
    <div class="w-full p-1">
        {{-- INFORMASI PENJUALAN --}}
        <div class="flex justify-between items-center mb-6 mt-2">
            <h1 class="text-4xl font-extrabold text-gray-900">Informasi Penjualan</h1>
        </div>

        <div class="flex flex-col md:flex-row gap-8 bg-white shadow-lg rounded-lg p-6">
            <!-- Top 3 Favorite Food Products -->
            <div class="w-full mb-6">
                <h2 class="text-2xl font-bold mb-4 border-b-2 border-gray-300 pb-2">Top 3 Makanan Terfavorit</h2>
                <table class="w-full border-collapse bg-gray-50 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2 text-left font-medium">Nama Produk</th>
                            <th class="border px-4 py-2 text-center font-medium">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($foodProducts as $product)
                        <tr class="table-row hover:bg-gray-100 transition-colors">
                            <td class="border px-4 py-2">{{ $product->nama_produk }}</td>
                            <td class="border px-4 py-2 text-center">{{ $product->total_qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Top 3 Favorite Drink Products -->
            <div class="w-full mb-6">
                <h2 class="text-2xl font-bold mb-4 border-b-2 border-gray-300 pb-2">Top 3 Minuman Terfavorit</h2>
                <table class="w-full border-collapse bg-gray-50 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2 text-left font-medium">Nama Produk</th>
                            <th class="border px-4 py-2 text-center font-medium">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drinkProducts as $product)
                        <tr class="table-row hover:bg-gray-100 transition-colors">
                            <td class="border px-4 py-2">{{ $product->nama_produk }}</td>
                            <td class="border px-4 py-2 text-center">{{ $product->total_qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Information -->
            <div class="w-full">
                <h2 class="text-xl font-semibold mb-4 border-b-2 border-gray-300 pb-2">Informasi Keuangan</h2>
                <div class="space-y-4">
                    <div>
                        <label for="totalEarnings" class="block text-gray-700 text-sm font-medium">Penghasilan</label>
                        <input type="text" id="totalEarnings" value="Rp {{ number_format($earnings, 0, ',', '.') }}" readonly class="input-readonly w-full p-3 rounded-lg border border-gray-300 text-gray-900 text-lg focus:ring focus:border-blue-500">
                    </div>
                    <div>
                        <label for="totalProfit" class="block text-gray-700 text-sm font-medium">Keuntungan</label>
                        <input type="text" id="totalProfit" value="Rp {{ number_format($profit, 0, ',', '.') }}" readonly class="input-readonly w-full p-3 rounded-lg border border-gray-300 text-gray-900 text-lg focus:ring focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-2">
            <h1 class="text-2xl font-extrabold text-gray-900 mb-6 mt-4 text-left">Kalender Pemasukan</h1>
            <div id="calendar" class="calendar-container"></div>
        </div>

        {{-- Statistik Pembelian Produk --}}
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 mb-6 mt-4 text-left">Statistik Penjualan Produk</h1>
            
            <!-- Period Selector -->
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <!-- Product Selection Dropdown -->
                <select class="border border-gray-300 text-gray-700 py-2 px-4 rounded focus:outline-none focus:ring focus:border-blue-500" id="produk-select">
                    @foreach ($produk as $items)
                    <option value="{{ $items->id }}">{{ $items->nama_produk }}</option>
                    @endforeach
                </select>

                <!-- Time Period Selection Dropdown -->
                <select class="border border-gray-300 text-gray-700 py-2 px-4 rounded focus:outline-none focus:ring focus:border-blue-500" id="periode-select">
                    <option value="hari">Hari ini</option>
                    <option value="minggu">Minggu ini</option>
                    <option value="bulan">Bulan ini</option>
                </select>
            </div>

            <!-- Chart Section -->
            <div class="bg-white shadow-lg rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2 text-gray-700 text-center">Total Pembelian</h2>
                <div class="relative w-full h-64 md:h-80">
                    <canvas id="PembelianChart"></canvas>
                </div>
            </div>
        </div>
    </div>


    <script>
        function formatEarnings(value) {
            const screenWidth = window.innerWidth;
            if (screenWidth < 768) {
                return `${(value / 1000)}K`; // Format to K
            }
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value); // Format with "Rp" for larger screens
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    right: 'prev,next today',
                    center: 'title',
                    left: '',
                },
                events: '/laporan/earnings', // Fetch earnings data from the server
                eventContent: function(arg) {
                    const formattedTitle = formatEarnings(arg.event.title);
                    return { html: `<div class="text-center">${formattedTitle}</div>` };
                },
                eventColor: '#4CAF50', // Event background color
                eventTextColor: '#ffffff', // Event text color
                eventMaxStack: 3, // Maximum number of events to stack in a day cell
                height: 'auto', // Automatically adjust height
            });

            calendar.render();
        });

        let pembelianChart;

        function initializeChart() {
            let ctx = document.getElementById('PembelianChart').getContext('2d');
            pembelianChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Total Pembelian',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, 
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
        });

        document.getElementById('periode-select').addEventListener('change', function() {
            let selectedPeriod = this.value;
            let selectedProduct = document.getElementById('produk-select').value;

            fetchProductStats(selectedProduct, selectedPeriod);
        });

        document.getElementById('produk-select').addEventListener('change', function() {
            let selectedProduct = this.value;
            let selectedPeriod = document.getElementById('periode-select').value;

            fetchProductStats(selectedProduct, selectedPeriod);
        });

        function fetchProductStats(productId, period) {
            fetch(`/get-product-stats?product=${productId}&period=${period}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched data:', data); // Log the data for debugging
                    updateChart(data);
                })
                .catch(error => {
                    console.error('Error fetching product stats:', error);
                });
        }



        function updateChart(data) {
            if (pembelianChart) {
                // Check if data is an array
                if (Array.isArray(data)) {
                    let labels = data.map(item => item.date);
                    let values = data.map(item => item.total_qty);

                    pembelianChart.data.labels = labels;
                    pembelianChart.data.datasets[0].data = values;
                    pembelianChart.update();
                } else {
                    console.error('Unexpected data format:', data);
                }
            } else {
                console.error('Chart is not initialized');
            }
        }





    </script>
</x-master-layout>
