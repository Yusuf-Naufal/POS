<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Produk;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            // Cek kredensial login
            if (auth()->attempt($request->only('email', 'password'))) {
                // Ambil pengguna yang sedang login
                $user = auth()->user();

                // Arahkan pengguna berdasarkan role mereka
                switch ($user->role) {
                    case 'Master':
                        if (auth()->user()->id_outlet) {
                            // If login is successful, send a success flash message
                            return redirect()->route('master.dashboard')
                                ->with('success', 'Anda Berhasil Login!!Selamat datang, ' . $user->nama)
                                ->withCookie(cookie()->forever('everLogin', true));
                        } else {
                            return redirect()->route('master.outlet.register')
                                ->with('success', 'Anda Berhasil Login, Silakan registrasi outlet')
                                ->withCookie(cookie()->forever('everLogin', true));
                        }
                    case 'Admin':
                        return redirect()->route('dashboard.admin')
                            ->with('success', 'Haloo Admin!!Selamat datang, ' . $user->nama)
                            ->withCookie(cookie()->forever('everLogin', true));
                    default:
                        return redirect()->route('users.menu')
                            ->with('success', 'Anda Berhasil Login!!Selamat datang, ' . $user->nama)
                            ->withCookie(cookie()->forever('everLogin', true));
                }
            } else {
                // Email benar tapi password salah
                return redirect()->back()->withErrors([
                    'password' => 'Password Anda Salah!!',
                ])->withInput();
            }
        }

        // Jika login gagal dan email tidak ada di sistem
        return redirect()->back()->withErrors([
            'email' => 'Email Tidak Terdaftar!!',
        ])->withInput();
    }

    public function checkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $exists = User::where('email', $request->email)->exists();
            Log::info($request->email);

            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'Email sudah ada!' : 'Email belum terdaftar!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking email: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memeriksa email.'], 500);
        }
    }

    public function verifyDateOfBirth(Request $request)
    {
        // Validate that the 'date' field is required and is a valid date
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d', // Ensure the format is YYYY-MM-DD
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Check if the date from the request matches the user's 'tanggal_lahir'
        $isValid = $user->tanggal_lahir === $request->date;

        // Return the validation result as a JSON response
        return response()->json(['isValid' => $isValid]);
    }


    public function registerView()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nama' => 'required|max:255',
            'username' => 'required|max:255|unique:users,username',
            'no_telp' => 'required|max:13',
            'alamat' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg', 
        ]);

        if ($validation->fails()) {
            // Check if the email already exists
            if ($request->filled('email') && User::where('email', $request->email)->exists()) {
                return redirect()->back()->withErrors([
                    'email' => 'Email sudah ada, silakan gunakan email lain.'
                ])->withInput();
            }

            // General validation error message
            return redirect()->back()->withErrors([
                'error' => 'Registrasi error, coba ulang lagi.'
            ])->withInput();
        }

        // Handle the image upload if exists
        if ($request->hasFile('image')) {
            $foto = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/assets/profile', $foto);
        } else {
            $foto = null; // Handle cases where no image is uploaded
        }

        $user = new User();
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->password = Hash::make($request->password); // Hash the password
        $user->role = 'Master';
        $user->status = 'Pending';
        $user->alamat = $request->alamat;
        $user->foto = 'profile/' . $foto;

        $user->save();

        return redirect()->route('loginForm')->with('success', 'Akun berhasil dibuat');
    }


    public function logout()
    {
        auth()->logout();

        return redirect()->route('loginForm');
    }

    public function editProfile($id)
    {
        $user = User::findOrFail($id); 

        if (auth()->user()->id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('pemilik.user.edit-profile', compact('user'));
    }

    public function editAdmin($id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.edit-profile', compact('user'));
    }


    // =================================================== ADMIN ================================================== //

    public function indexAdminPemilik()
    {
        $perPage = request()->get('per_page', 10);
        
        $user = User::where('role', 'Master')
        // ->whereIn('status', ['Bekerja','Berhenti'])
        ->paginate($perPage);

        return view('admin.user.pemilik.user', [
            'User' => $user,
        ]);
    }

    public function indexAdminKaryawan()
    {
        $perPage = request()->get('per_page', 10);
        
        $users = User::where('role', 'User')
            ->whereIn('status', ['Bekerja', 'Berhenti'])
            ->paginate($perPage);


        return view('admin.user.karyawan.user', [
            'User' => $users,
        ]);
    }

    public function deactivateUser($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'Berhenti';
            $user->id_outlet = NULL;
            $user->save();

            if($user->role === 'Master'){
                return redirect()->route('admin.users.index')->with('success', 'User status updated to Berhenti.');
            }else{
                return redirect()->route('admin.karyawan.index')->with('success', 'User status updated to Berhenti.');
            }
        }

        if($user->role === 'Master'){
            return redirect()->route('admin.users.index')->with('success', 'User status updated to Berhenti.');
        }else{
            return redirect()->route('admin.karyawan.index')->with('success', 'User status updated to Berhenti.');
        }
    }

    public function activateUser($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'Bekerja';
            $user->id_outlet = NULL;
            $user->save();
            if($user->role === 'Master'){
                return redirect()->route('admin.users.index')->with('success', 'User status updated to Berhenti.');
            }else{
                return redirect()->route('admin.karyawan.index')->with('success', 'User status updated to Berhenti.');
            }
        }

        if($user->role === 'Master'){
            return redirect()->route('admin.users.index')->with('success', 'User status updated to Berhenti.');
        }else{
            return redirect()->route('admin.karyawan.index')->with('success', 'User status updated to Berhenti.');
        }
    }

    public function edit($id)
    {
        // Temukan user berdasarkan id
        $user = User::findOrFail($id);
        $outlets = Outlet::all();

        // Tampilkan view edit dengan data user
        return view('admin.user.pemilik.edit-user', compact('user', 'outlets'));
    }

    public function createPemilik()
    {
        // Temukan user berdasarkan id
        $outlets = Outlet::all();

        // Tampilkan view edit dengan data user
        return view('admin.user.pemilik.add-user', compact( 'outlets'));
    }

    public function createKaryawanAdmin()
    {
        // Temukan user berdasarkan id
        $outlets = Outlet::all();

        // Tampilkan view edit dengan data user
        return view('admin.user.karyawan.add-user', compact( 'outlets'));
    }

    public function storePemilik(Request $request)
    {
        // Validate the input
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        // If validation fails, return to the form with errors
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        // Handle file upload if it exists
        if ($request->hasFile('foto')) {
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/assets/profile', $foto);
        } else {
            $foto = null;
        }

        // Create a new user
        $user = new User();
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->password = Hash::make($request->password); // Hash the password
        $user->alamat = $request->alamat;
        $user->role = 'Master';
        $user->foto = $foto ? 'profile/' . $foto : null;
        
        $user->save();

        // Redirect back with a success message
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'status' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg',
            'id_outlet' => 'nullable|exists:outlet,id',
        ]);

        if ($validation->fails()) {
            // Check if the email already exists
            if ($request->filled('email') && User::where('email', $request->email)->exists()) {
                return redirect()->back()->withErrors([
                    'email' => 'Email sudah ada, silakan gunakan email lain.'
                ])->withInput();
            }

            // General validation error message
            return redirect()->back()->withErrors([
                'error' => 'Update error, coba ulang lagi.'
            ])->withInput();
        }

        $user = User::findOrFail($id);

        // Jika ada foto baru yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/assets/' . $user->foto)) {
                Storage::delete('public/assets/' . $user->foto);
            }

            // Simpan foto baru
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/assets/profile', $foto);
            $user->foto = 'profile/' . $foto;
        }

        // Perbarui atribut user
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->alamat = $request->alamat;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->status = $request->status;
        $user->id_outlet = $request->id_outlet;
        $user->save();

        if($request->role === 'Master'){
            return redirect()->route('admin.users.index')->with('success', 'User Berhasil Diupdate');
        }else{
            return redirect()->route('admin.karyawan.index')->with('success', 'User Berhasil Diupdate');
        }

        // return $request;
    }

    // ====================================== MASTER ============================================== //

    public function indexMaster()
    {
        $user = auth()->user();

        // Dapatkan ID outlet dari pengguna yang sedang login
        $userOutletId = $user->id_outlet;

        $perPage = request()->get('per_page', 10);

        // Ambil pengguna dengan role 'User' dan id_outlet sesuai dengan ID outlet login
        $users = User::where('role', 'User')
                    ->where('id_outlet', $userOutletId)
                    ->paginate($perPage);

        // Jika perlu, dapatkan outlet yang terkait dengan pengguna ini
        $outlets = $user->outlet;

        return view('pemilik.user.user', [
            'users' => $users,
            'outlets' => $outlets
        ]);
    }

    public function createKaryawan()
    {
        return view('pemilik.user.add-user');
    }
    

    public function storeKaryawan(Request $request)
    {
        $login = auth()->user()->role;

        // Validate the input
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'status' =>'nullable',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        // If validation fails, return to the form with errors
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        // Handle file upload if it exists
        if ($request->hasFile('foto')) {
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/assets/profile', $foto);
        } else {
            $foto = null;
        }

        $user = auth()->user();

        // Dapatkan ID outlet dari pengguna yang sedang login
        $userOutletId = $user->id_outlet;

        // Create a new user
        $user = new User();
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->password = Hash::make($request->password); // Hash the password
        $user->alamat = $request->alamat;
        $user->role = 'User';
        $user->id_outlet = $userOutletId;
        $user->status = $request->status;
        $user->foto = $foto ? 'profile/' . $foto : null;
        
        $user->save();

        // Redirect back with a success message
        if($login === 'Master'){
            return redirect()->route('master.users.index')->with('success', 'User Berhasil Dibuat!');
        }else{
            return redirect()->route('admin.karyawan.index')->with('success', 'User Berhasil Dibuat!');
        }
    }

    public function editKaryawan($id)
    {
        // Temukan user berdasarkan id
        $user = User::findOrFail($id);

        $users = auth()->user();

        // Dapatkan ID outlet dari pengguna yang sedang login
        $userOutletId = $users->id_outlet;

        // Tampilkan view edit dengan data user
        return view('pemilik.user.edit-user', compact('user' ,'userOutletId'));
    }

    public function updateKaryawan(Request $request, $id)
    {
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'status' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg',
            'id_outlet' => 'nullable|exists:outlet,id',
        ]);

        if ($validation->fails()) {
            // Check if the email already exists
            if ($request->filled('email') && User::where('email', $request->email)->exists()) {
                return redirect()->back()->withErrors([
                    'email' => 'Email sudah ada, silakan gunakan email lain.'
                ])->withInput();
            }

            // General validation error message
            return redirect()->back()->withErrors([
                'error' => 'Update error, coba ulang lagi.'
            ])->withInput();
        }

        $user = User::findOrFail($id);

        // Jika ada foto baru yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/assets/' . $user->foto)) {
                Storage::delete('public/assets/' . $user->foto);
            }

            // Simpan foto baru
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/assets/profile', $foto);
            $user->foto = 'profile/' . $foto;
        }

        

        // Perbarui atribut user
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;
        $user->username = $request->username;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->alamat = $request->alamat;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->status = $request->status;
        $user->id_outlet = $request->id_outlet;
        $user->save();

        return redirect()->route('master.users.index')->with('success', 'Karyawan Berhasil Diupdate!');

         // return $request;
    }

    public function deactivateKaryawan($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'Berhenti';
            $user->save();
            return redirect()->route('master.users.index')->with('success', 'User status updated to Berhenti.');
        }

        return redirect()->route('master.users.index')->with('error', 'User tidak ada.');
    }

    public function activateKaryawan($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'Bekerja';
            $user->save();
            return redirect()->route('master.users.index')->with('success', 'User status updated to Bekerja.');
        }

        return redirect()->route('master.users.index')->with('error', 'User tidak ada.');
    }

    public function destroy($id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Delete the profile photo if it exists
        if ($user->foto && Storage::exists('public/assets/' . $user->foto)) {
            Storage::delete('public/assets/' . $user->foto);
        }

        // Delete the user
        $user->delete();

        // Determine where to redirect based on a condition (e.g., role or some other criteria)
        if (auth()->user()->role === 'Admin') {
            if($user->role == 'Master'){
                return redirect()->route('admin.users.index')->with('success', 'User Berhasil Dihapus!');
            }else{
                return redirect()->route('admin.karyawan.index')->with('success', 'User Berhasil Dihapus!');
            }
        } else {
            return redirect()->route('master.users.index')->with('success', 'User Berhasil Dihapus!');
        }
    }

    public function updateProfile(Request $request, $id)
    {
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'status' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg',
            'id_outlet' => 'nullable|exists:outlet,id',
        ]);

        if ($validation->fails()) {
            // Check if the email already exists
            if ($request->filled('email') && User::where('email', $request->email)->exists()) {
                return redirect()->back()->withErrors([
                    'email' => 'Email sudah ada, silakan gunakan email lain.'
                ])->withInput();
            }

            // General validation error message
            return redirect()->back()->withErrors([
                'error' => 'Update error, coba ulang lagi.'
            ])->withInput();
        }

        $user = User::findOrFail($id);

        // Jika ada foto baru yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/assets/' . $user->foto)) {
                Storage::delete('public/assets/' . $user->foto);
            }

            // Simpan foto baru
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/assets/profile', $foto);
            $user->foto = 'profile/' . $foto;
        }



        // Perbarui atribut user
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;
        $user->username = $request->username;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->alamat = $request->alamat;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->status = $request->status;
        $user->id_outlet = $request->id_outlet;
        $user->save();

        if(auth()->user()->role === 'Admin'){
            return redirect()->route('dashboard.admin')->with('success', 'Profile Berhasil di Update');
        }elseif(auth()->user()->role === 'Master'){
            return redirect()->route('master.dashboard')->with('success', 'Profile Berhasil di Update');
        }else{
            return redirect()->route('users.menu')->with('success', 'Profile Berhasil di Update');
        }
        

        // return $request;
    }



}
