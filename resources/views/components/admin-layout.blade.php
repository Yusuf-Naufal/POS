<html lang="en">
<x-header></x-header>
<body>
    <x-aside-admin></x-aside-admin>

    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
            {{ $slot }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logoutButton = document.getElementById('logout-button');

            if (logoutButton) {
                logoutButton.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default link behavior

                    // Show SweetAlert confirmation
                    Swal.fire({
                        title: 'Konfirmasi Logout',
                        text: "Apakah Anda yakin ingin keluar?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Logout!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('logout') }}'; // Redirect to logout route
                        }
                    });
                });
            }
        });
    </script>

{{-- SCRIPT --}}
<script src="../path/to/flowbite/dist/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

{{-- SCAN BARCODE --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="html5-qrcode.min.js"></script>
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>

{{-- SWEETALERT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            width: 600,
            timer: 3000,
            position: 'top-end',
            toast: true,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    @endif
</script>



</body>
</html>