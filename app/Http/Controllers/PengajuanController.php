<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\User;
use App\Models\Pengajuan_Outlet;
use App\Jobs\TimeToDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PengajuanController extends Controller
{
    public function indexAdmin(){
        $pengajuan = Pengajuan_Outlet::where('status', 'Pending')->get();

        $tertolak = Pengajuan_Outlet::where('status', 'Rejected')->get();

        return view('admin.pengajuan.pengajuan', compact('pengajuan', 'tertolak'));

    }

    public function show($id)
    {
        // Ambil data pengajuan beserta data pemilik dan outlet terkait
        $pengajuan = Pengajuan_Outlet::with('pemilik')->findOrFail($id);

        // Tampilkan view detail pengajuan dengan data yang didapat
        return view('admin.pengajuan.detail-pengajuan', compact('pengajuan'));
    }


    public function storeOutlet(Request $request){
         $validation = Validator::make($request->all(), [
            'nama_outlet' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'pin' => 'nullable|string|max:4',
            'email' => 'nullable|email|max:255',
            'id_pemilik' => 'required|exists:users,id',
            'alamat' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validate image
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation);
        }

        $foto = time() . '.' . $request->foto->extension();
        $request->foto->storeAs('public/assets/outlet', $foto);

        $outlet = new Pengajuan_Outlet();
        $outlet->nama_outlet = $request->nama_outlet;
        $outlet->no_telp = $request->no_telp;
        $outlet->email = $request->email;
        $outlet->pin = $request->pin;
        $outlet->id_pemilik = $request->id_pemilik;
        $outlet->instagram = $request->instagram;
        $outlet->facebook = $request->facebook;
        $outlet->tiktok = $request->tiktok;
        $outlet->alamat = $request->alamat;
        $outlet->foto = 'outlet/' . $foto;
        
        $outlet->save();

        return redirect()->route('master.outlet.register')->with('success', 'Outlet added successfully.');
    }

    public function approve($id)
    {
        // Find the application
        $pengajuan = Pengajuan_Outlet::findOrFail($id);

        // Prepare data to insert into the outlet table
        $outletData = [
            'nama_outlet' => $pengajuan->nama_outlet,
            'pemilik' => $pengajuan->pemilik->nama,
            'no_telp' => $pengajuan->no_telp,
            'email' => $pengajuan->email,
            'pin' => $pengajuan->pin,
            'alamat' => $pengajuan->alamat,
            'instagram' => $pengajuan->instagram,
            'tiktok' => $pengajuan->tiktok,
            'facebook' => $pengajuan->facebook,
            'status' => 'Aktif',
            'foto' => $pengajuan->foto,
        ];

        // Insert into the outlet table
        $outlet = Outlet::create($outletData);

        // Update the user's id_outlet
        User::where('email', $pengajuan->pemilik->email)->update([
            'id_outlet' => $outlet->id,
            'status' => 'Bekerja'
        ]);

        // Optionally, you can delete the approved application if it's no longer needed
        $pengajuan->delete();

        // Redirect back with success message
        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan telah disetujui dan outlet berhasil ditambahkan.');
    }

    public function reject($id)
    {
        // Find the application
        $pengajuan = Pengajuan_Outlet::findOrFail($id);

        // Update status to "Rejected"
        $pengajuan->status = 'Rejected';
        $pengajuan->save();

        // Dispatch a job to delete the pengajuan after 1 day
        TimeToDelete::dispatch($pengajuan->id)->delay(now()->addDay());
        // TimeToDelete::dispatch($pengajuan->id)->delay(now()->addSeconds(30));

        // Redirect back with success message
        return response()->json(['success' => true, 'message' => 'Pengajuan rejected successfully.']);
    }





}
