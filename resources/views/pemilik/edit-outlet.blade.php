<x-master-layout>
    <div class="w-full p-2">
        <form id="outlet-form" action="{{ route('outlets.update', $outlet->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
            <div class="flex justify-between items-center flex-wrap md:flex-row mb-4">
                <h1 class="text-2xl font-bold">Edit Outlet</h1>
                <label class="flex items-center cursor-pointer">
                    <input type="hidden" id="status" name="status" value="Nonaktif">
                    <input type="checkbox" id="pos-toggle" class="sr-only peer" name="status-checkbox" {{ $outlet->status == 'Aktif' ? 'checked' : '' }}/>
                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                    <span class="ms-2 text-xl font-semibold text-gray-900 dark:text-gray-300">Tutup / Aktif</span>
                </label>
            </div>

            <!-- Card Container -->
            <div class="bg-white h-auto w-full shadow-md rounded-lg p-6">
                <div class="flex flex-wrap gap-4">
                    <div class="flex w-full items-center justify-center align-top gap-4 flex-col md:flex-row">
                        <div>
                            <!-- Upload Image Section -->
                            <div class="flex items-center justify-center w-64 h-64">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div id="image-preview" class="flex items-center justify-center w-full h-full hidden">
                                        <!-- Default SVG Icon -->
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                    </div>
                                    <input id="dropzone-file" type="file" class="hidden" accept="image/*"  name="foto" onchange="previewImage(event)" />
                                    <img class="w-full h-full rounded-md" id="image-update" src="{{ asset('assets/'. $outlet->foto) }}" alt="">
                                </label>
                            </div>

                            <div class="flex justify-center mt-5">
                                <button type="button" onclick="startCamera()" class="flex items-center justify-center p-3 bg-blue-500 text-white rounded-full hover:bg-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                        <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z"/>
                                        <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="w-full">
                            <!-- Form Fields -->
                            <div class="mb-4 w-full">
                                <label class="block text-sm font-medium text-gray-700" for="nama_outlet">Nama Outlet</label>
                                <input type="text" value="{{ $outlet->nama_outlet }}" id="nama_outlet" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="nama_outlet" required>
                            </div>

                            <div class="flex gap-4 flex-col md:flex-row">
                                <div class="w-full md:w-1/3 mb-4">
                                    <label class="block text-sm font-medium text-gray-700" for="no_telp">Telepon</label>
                                    <input type="text" value="{{ $outlet->no_telp }}" id="no_telp" maxlength="13" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9 +\-]/g,'');" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="no_telp" required>
                                </div>
                                <div class="w-full md:w-1/3 mb-4">
                                    <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                                    <input type="text" value="{{ $outlet->email }}" id="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="email" required>
                                </div>
                                <div class="relative w-full md:w-1/3 mb-4">
                                    <label for="pemilik" class="block text-sm font-medium text-gray-700">Pemilik</label>
                                    <input type="text" value="{{ $outlet->pemilik }}" id="pemilik" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="pemilik" required>
                                    
                                </div>
                            </div>

                            <div class="flex gap-4 flex-col md:flex-row">
                                <div class="w-full md:w-1/3 mb-4">
                                    <label class="block text-sm font-medium text-gray-700" for="instagram">Instagram <span class="font-thin text-gray-600">(Opsional)</span></label>
                                    <input type="text" value="{{ $outlet->instagram }}"  id="instagram" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="instagram" placeholder="Username Instagram">

                                </div>
                                <div class="w-full md:w-1/3 mb-4">
                                    <label class="block text-sm font-medium text-gray-700" for="facebook">Facebook <span class="font-thin text-gray-600">(Opsional)</span></label>
                                    <input type="text" value="{{ $outlet->facebook }}" id="facebook" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="facebook" placeholder="Username Facebook">
                                </div>
                                <div class="w-full md:w-1/3 mb-4">
                                    <label class="block text-sm font-medium text-gray-700" for="tiktok">Tiktok <span class="font-thin text-gray-600">(Opsional)</span></label>
                                    <input type="text" value="{{ $outlet->tiktok }}" id="tiktok" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="tiktok" placeholder="Username Tiktok">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 w-full">
                        <label class="block text-sm font-medium text-gray-700" for="alamat">Alamat</label>
                        <input type="text" value="{{ $outlet->alamat }}" id="alamat" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="alamat" required>
                    </div>

                    <div class="mb-4 w-full">
                        <label class="block text-sm font-medium text-gray-700" for="jam_operasional">Jam Operasional</label>
                        <div class="flex items-center space-x-2">
                            <input value="{{ $outlet->jam_buka }}" type="time" name="jam_buka" id="jam_buka" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <span>-</span>
                            <input value="{{ $outlet->jam_tutup }}" type="time" name="jam_tutup" id="jam_tutup" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mb-4 ">
                        <label class="block text-sm font-medium text-gray-700" for="alamat">Pin</label>
                        <input type="text" value="{{ $outlet->pin }}" id="pin" maxlength="4" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="pin" required>
                    </div>

                    <!-- Button Container -->
                    <div class="w-full flex justify-end">
                        <div>
                            <a href="{{ route('master.dashboard') }}" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                Batal
                            </a>
                            <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Ubah
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Main modal -->
        <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center flex items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Ambil Gambar
                        </h3>
                        <button type="button" onclick="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <div id="container" class="w-full">
                            <video autoplay="true" id="simplevideo" class="w-full h-64"></video>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="default-modal" onclick="takeSnapshot()" type="button" class="mt-2 p-2 bg-green-500 text-white rounded">Capture</button>
                        <button data-modal-hide="default-modal" onclick="closeModal()" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JavaScript for Image Preview -->
    <script>
        @if ($errors->any())
            let errorMessages = '';
            @if ($errors->has('email'))
                errorMessages += '{{ addslashes($errors->first('email')) }}\n'; // Specific email error
            @endif
            @if ($errors->has('error'))
                errorMessages += '{{ addslashes($errors->first('error')) }}\n'; 
            @endif

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
                title: "Registration gagal!",
                text: errorMessages // Display all error messages here
            });
        @endif

        var mediaStream = null; // Store the media stream globally

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                document.getElementById('image-update').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }   

        function startCamera() {
            // Show the modal
            document.getElementById('default-modal').classList.remove('hidden');
            var video = document.querySelector("#simplevideo");

            // Start video stream
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    mediaStream = stream; // Store the media stream
                    video.srcObject = stream;
                })
                .catch(function(err) {
                    console.error("Error accessing camera: ", err);
                    alert("Could not access camera. Please ensure you have granted permission.");
                });
        }

        function takeSnapshot() {
            var video = document.querySelector("#simplevideo");
            var canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            var context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            var dataURL = canvas.toDataURL('image/png');

            document.getElementById('image-update').src = dataURL;

            // Create a file object and set it as the value of the file input
            var file = dataURLtoFile(dataURL, 'snapshot.png');
            var fileInput = document.getElementById('dropzone-file');
            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            // Hide the modal after capture
            closeModal();
        }

        function closeModal() {
            document.getElementById('default-modal').classList.add('hidden');

            // Stop the video stream
            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
                mediaStream = null;
            }
        }

        // Helper function to convert dataURL to file
        function dataURLtoFile(dataURL, filename) {
            var arr = dataURL.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], filename, { type: mime });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const posToggle = document.getElementById('pos-toggle');
            const statusInput = document.getElementById('status');

            // Update the hidden status input based on the checkbox
            posToggle.addEventListener('change', function() {
                statusInput.value = this.checked ? 'Aktif' : 'Nonaktif';
            });

            // Check status before form submission
            const form = document.getElementById('outlet-form');
            form.addEventListener('submit', function() {
                // Ensure status is updated
                statusInput.value = posToggle.checked ? 'Aktif' : 'Nonaktif';
            });
        });
    </script>
</x-master-layout>