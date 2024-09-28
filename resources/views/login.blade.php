<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    @vite('resources/css/app.css')
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100 p-6">

    <div class="w-full max-w-md lg:min-w-fit mx-auto h-auto bg-white rounded-lg shadow-lg p-6 md:p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email"  
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password"  
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                </div>
                {{-- <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">Forgot your password?</a> --}}
            </div>

            <div>
                <button type="submit" 
                    class="w-full py-2 px-4 bg-indigo-600 text-white font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Login
                </button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('registerForm') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Belum punya akun?</a>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if ($errors->any())
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '{{ $error }}\n';
            @endforeach

            const Toast = Swal.mixin({
                toast: true,
                position: "top",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Toast.fire({
                icon: "error",
                title: "Login gagal!",
                text: errorMessages // Display all error messages here
            });
        @endif

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


        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');

            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent form submission

                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                // Check if email or password fields are empty
                if (email === "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Email belum di isi...',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return; // Stop further execution if email is empty
                }

                if (password === "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Password belum di isi..',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return; // Stop further execution if password is empty
                }

                // If both fields are filled, submit the form
                form.submit();
            });
        });
    </script>

</body>
</html>
