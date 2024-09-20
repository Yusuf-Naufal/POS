<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Outlet;
use App\Models\Produk;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
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
            return redirect()->back()->withErrors($validation->errors());
        }

        // Cek kredensial login
        if (auth()->attempt($request->only('email', 'password'))) {
            // Ambil pengguna yang sedang login
            $user = auth()->user();

            // Arahkan pengguna berdasarkan role mereka
            switch ($user->role) {
                case 'Master':
                    return redirect()->route('master.dashboard')->withCookie(cookie()->forever('everLogin', true));
                case 'Admin':
                    return redirect()->route('dashboard.admin')->withCookie(cookie()->forever('everLogin', true));
                default:
                    return redirect()->route('users.menu')->withCookie(cookie()->forever('everLogin', true));
            }
        }

        // Jika login gagal
        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function registerView()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nama' => 'required|max:255',
            'username' => 'required|max:255|unique:users,username',
            'no_telp' => 'required|max:15',
            'alamat' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validate image
        ]);

        if ($validation->fails()) {
            return $validation;
        }

        $foto = time() . '.' . $request->foto->extension();
        $request->foto->storeAs('public/assets/profile', $foto);

        $user = new User();
        $user->nama = $request->nama;
        $user->no_telp = $request->no_telp;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->password = Hash::make($request->password); // Hash the password
        // $user->password = $request->password;
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

    public function indexAdmin()
    {
        $user = User::where('role', 'Master')->get();
        return view('admin.user.user', [
            'User' => $user,
        ]);
    }

    public function deactivatePemilik($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'Berhenti';
            $user->id_outlet = NULL;
            $user->save();
            return redirect()->route('admin.users.index')->with('success', 'User status updated to Berhenti.');
        }

        return redirect()->route('admin.users.index')->with('error', 'User tidak ada.');
    }

    public function activatePemilik($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'Bekerja';
            $user->id_outlet = NULL;
            $user->save();
            return redirect()->route('admin.users.index')->with('success', 'User status updated to Bekerja.');
        }

        return redirect()->route('admin.users.index')->with('error', 'User tidak ada.');
    }

    public function editPemilik($id)
    {
        // Temukan user berdasarkan id
        $user = User::findOrFail($id);
        $outlets = Outlet::all();

        // Tampilkan view edit dengan data user
        return view('admin.user.edit-user', compact('user', 'outlets'));
    }

    public function createPemilik()
    {
        // Temukan user berdasarkan id
        $outlets = Outlet::all();

        // Tampilkan view edit dengan data user
        return view('admin.user.add-user', compact( 'outlets'));
    }

    public function storePemilik(Request $request)
    {
        // Validate the input
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15',
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

    public function updatePemilik(Request $request, $id)
    {
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
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
            return back()->withErrors($validation)->withInput();
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

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');

        // return $request;
    }

    public function indexMaster()
    {
        $user = auth()->user();

        // Dapatkan ID outlet dari pengguna yang sedang login
        $userOutletId = $user->id_outlet;

        // Ambil pengguna dengan role 'User' dan id_outlet sesuai dengan ID outlet login
        $users = User::where('role', 'User')
                    ->where('id_outlet', $userOutletId)
                    ->get();

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
        // Validate the input
        $validation = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15',
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
        return redirect()->route('master.users.index')->with('success', 'User created successfully.');
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
            'no_telp' => 'required|string|max:20',
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
            return back()->withErrors($validation)->withInput();
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

        return redirect()->route('master.users.index')->with('success', 'User updated successfully!');

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
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
        } else {
            return redirect()->route('master.users.index')->with('success', 'User deleted successfully!');
        }
    }



}
