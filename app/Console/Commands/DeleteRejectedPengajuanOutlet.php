<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Pengajuan_Outlet;
use Illuminate\Console\Command;

class DeleteRejectedPengajuanOutlet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-rejected-pengajuan-outlet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus data pengajuan outlet yang ditolak setalah 1 hari pengajuan';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneDayAgo = Carbon::now()->subDay();

        // Find the rejected records that were updated more than 1 day ago
        $rejectedOutlets = Pengajuan_Outlet::where('status', 'Rejected')
            ->where('updated_at', '<=', $oneDayAgo)  // Updated more than 1 day ago
            ->get();

        // Loop through and delete each record
        foreach ($rejectedOutlets as $pengajuan) {
            $pengajuan->delete();
        }

        // Log how many records were deleted
        $deletedCount = $rejectedOutlets->count();
        $this->info("Deleted {$deletedCount} rejected pengajuan_outlet records.");

        return 0;
    }

}
