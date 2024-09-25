<?php

namespace App\Jobs;

use App\Models\Pengajuan_Outlet;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TimeToDelete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pengajuanId;
    /**
     * Create a new job instance.
     */
    public function __construct($pengajuanId)
    {
        $this->pengajuanId = $pengajuanId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("TimeToDelete job started for ID: {$this->pengajuanId}");

        // Find the rejected pengajuan
        $pengajuan = Pengajuan_Outlet::find($this->pengajuanId);

        if ($pengajuan && $pengajuan->status === 'Rejected') {
            Log::info("Found pengajuan with ID: {$this->pengajuanId}. Deleting...");

            // Check if the photo exists and delete it
            if ($pengajuan->foto && Storage::exists('public/assets/' . $pengajuan->foto)) {
                Storage::delete('public/assets/' . $pengajuan->foto);
                Log::info("Deleted photo: {$pengajuan->foto}");
            }

            // Delete the pengajuan
            $pengajuan->delete();
            Log::info("Deleted pengajuan with ID: {$this->pengajuanId}");
        } else {
            Log::info("No pengajuan found or status is not 'Rejected'.");
        }
    }


}
