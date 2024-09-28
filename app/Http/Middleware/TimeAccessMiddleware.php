<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil jam saat ini dalam timezone Asia/Jakarta
        $currentTime = now()->setTimezone('Asia/Jakarta')->toTimeString();

        // Ambil ID outlet dari request atau route
        $id_outlet = $request->route('id');

        // Pengecekan jika ID outlet tidak ada di request
        if (!$id_outlet) {
            return redirect()->route('error.page')->with('message', 'ID outlet tidak ditemukan');
        }

        // Ambil outlet dari database
        $outlet = Outlet::find($id_outlet);

        // Pengecekan jika outlet tidak ditemukan
        if (!$outlet) {
            return redirect()->route('error.page')->with('message', 'Outlet tidak ditemukan');
        }

        // Pengecekan status outlet, misal 'Aktif' atau 'Nonaktif'
        if ($outlet->status !== 'Aktif') {
            return redirect()->route('error.page')->with('message', 'Outlet sedang tidak aktif');
        }

        // Ambil jam buka dan tutup dari outlet
        $startHour = $outlet->jam_buka;  // Jam mulai operasional dari database
        $endHour = $outlet->jam_tutup;   // Jam tutup operasional dari database

        // Pengecekan jam operasional outlet
        if ($currentTime < $startHour || $currentTime >= $endHour) {
            return redirect()->route('error.page')->with('message', 'Akses tidak diperbolehkan di luar jam operasional (' . \Carbon\Carbon::parse($startHour)->format('H:i') . ' - ' . \Carbon\Carbon::parse($endHour)->format('H:i') . ')');
        }

        // Jika semua pengecekan lolos, lanjutkan request
        return $next($request);
    }

}
