<?php

namespace App\Http\Controllers;


use App\Models\Outlet;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OutletController extends Controller
{
    public function index()
    {
        $outlet = Outlet::all();
        return view('admin.outlet.outlet', [
            'Outlet' => $outlet,
        ]);
    }

    public function create(): View
    {
        $user = User::where('role', 'Master')->get();
        return view('admin.outlet.add-outlet',[
            'Users' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nama_outlet' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'pin' => 'nullable|string|max:6',
            'email' => 'nullable|email|max:255',
            'pemilik' => 'nullable|string|max:255',
            'alamat' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validate image
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation);
        }

        $foto = time() . '.' . $request->foto->extension();
        $request->foto->storeAs('public/assets/outlet', $foto);

        $outlet = new Outlet();
        $outlet->nama_outlet = $request->nama_outlet;
        $outlet->no_telp = $request->no_telp;
        $outlet->email = $request->email;
        $outlet->pin = $request->pin;
        $outlet->pemilik = $request->pemilik;
        $outlet->instagram = $request->instagram;
        $outlet->facebook = $request->facebook;
        $outlet->tiktok = $request->tiktok;
        $outlet->alamat = $request->alamat;
        $outlet->foto = 'outlet/' . $foto;
        
        $outlet->save();

        return redirect()->route('outlets.index')->with('success', 'Outlet added successfully.');
    }

    public function edit($id)
    {
        // Temukan outlet berdasarkan id
        $outlet = Outlet::findOrFail($id);
        $user = User::where('role', 'Master')->get();

        // Tampilkan view edit dengan data outlet
        return view('admin.outlet.edit-outlet', compact('outlet' , 'user'));
    }

    public function autocomplete(Request $request)
    {
        $term = $request->get('term');

        $users = User::where('name', 'LIKE', '%' . $term . '%')
                     ->get(['id', 'name']); // Ambil ID dan nama pengguna

        return response()->json($users);
    }

    public function update(Request $request, $id){
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama_outlet' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'pin' => 'nullable|string|max:6',
            'email' => 'nullable|email|max:255',
            'pemilik' => 'nullable|string|max:255',
            'alamat' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation)->withInput();
        }

        $outlet = Outlet::findOrFail($id);

        // Jika ada foto baru yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($outlet->foto && Storage::exists('public/assets/' . $outlet->foto)) {
            // Menghapus file yang ada di path penyimpanan
            Storage::delete('public/assets/' . $outlet->foto);
        }

            // Simpan foto baru
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/assets/outlet', $foto);
            $outlet->foto = 'outlet/' . $foto;
        }

        // Perbarui atribut outlet
        $outlet->nama_outlet = $request->nama_outlet;
        $outlet->no_telp = $request->no_telp;
        $outlet->email = $request->email;
        $outlet->pin = $request->pin;
        $outlet->pemilik = $request->pemilik;
        $outlet->alamat = $request->alamat;
        $outlet->instagram = $request->instagram;
        $outlet->facebook = $request->facebook;
        $outlet->tiktok = $request->tiktok;
        $outlet->save();

        return redirect()->route('outlets.index')->with('success', 'Outlet updated successfully!');
    }

    public function destroy($id)
    {
        $outlet = Outlet::find($id);
        
        if ($outlet) {
            // Delete the photo file if it exists
            if ($outlet->foto) {
                // Assuming the photos are stored in the 'storage/assets' directory
                $photoPath = public_path('storage/assets/' . $outlet->foto);
                if (file_exists($photoPath)) {
                    unlink($photoPath); // Delete the file
                }
            }

            // Delete the outlet record
            $outlet->delete();
            
            return redirect()->route('outlets.index')->with('success', 'Outlet deleted successfully.');
        }

        return redirect()->route('outlets.index')->with('error', 'Outlet not found.');
    }

    public function deactivate($id)
    {
        $outlet = Outlet::find($id);

        if ($outlet) {
            $outlet->status = 'Nonaktif'; // Change status to 'Inactive' or however you represent deactivation
            $outlet->save();
            return redirect()->route('outlets.index')->with('success', 'Outlet status updated to Inactive.');
        }

        return redirect()->route('outlets.index')->with('error', 'Outlet not found.');
    }

    public function activate($id)
    {
        $outlet = Outlet::find($id);

        if ($outlet) {
            $outlet->status = 'Aktif'; // Change status back to 'Active'
            $outlet->save();
            return redirect()->route('outlets.index')->with('success', 'Outlet status updated to Active.');
        }

        return redirect()->route('outlets.index')->with('error', 'Outlet not found.');
    }



}
