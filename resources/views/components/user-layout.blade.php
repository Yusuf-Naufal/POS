<!DOCTYPE html>
<html lang="en">
    <x-header></x-header>
<body class="bg-gray-100">

    <div class="w-full h-screen p-3">
        {{ $slot }}
    </div>

    <script>
        // Sidebar toggle script
        const sidebarToggle = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const closeToggle = document.getElementById('close-sidebar');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            mainContent.classList.toggle('lg:pl-[17rem]');
            mainContent.classList.toggle('w-full');
        });

        closeToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            mainContent.classList.toggle('lg:pl-[17rem]');
            mainContent.classList.toggle('w-full');
        });

        // Ensure initial state on page load
        window.addEventListener('DOMContentLoaded', () => {
            if (sidebar.classList.contains('-translate-x-full')) {
                mainContent.classList.add('w-full');
            }
        });

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

    
</body>
</html>
