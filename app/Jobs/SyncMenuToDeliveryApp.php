<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Import necessary models and facades
use App\Models\Menu;
use App\Models\Branch;
use App\Models\BranchWorkingHour;
use App\Models\Category;
use App\Models\Item;
use App\Models\ModifierGroup;
use App\Models\Modifier;
use App\Services\MenuSyncService; // IMPORTANT: We'll use the service inside the job
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class SyncMenuToDeliveryApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Properties to hold the Menu and Branch models
    protected Menu $menu;
    protected Branch $branch;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Menu $menu The menu to sync.
     * @param \App\Models\Branch $branch The branch to sync the menu for.
     */
    public function __construct(Menu $menu, Branch $branch)
    {
        $this->menu = $menu;
        $this->branch = $branch;
    }

    /**
     * Execute the job.
     * This method contains the actual logic that will run in the background.
     */
    public function handle(): void
    {
        // Instantiate your MenuSyncService
        // This ensures the service's logic is cleanly separated and reusable
        $menuSyncService = new MenuSyncService();

        // Call the sync method of your service
        // The service will now perform the actual data fetching, payload formatting,
        // and HTTP request to the delivery platform API.
        $result = $menuSyncService->sync($this->menu, $this->branch);

        // You can log the result of the sync job here if needed
        if ($result['status'] === 'success') {
            Log::info("Job completed: Menu sync successful for Menu ID: {$this->menu->id}, Branch ID: {$this->branch->id}");
        } else {
            Log::error("Job failed: Menu sync failed for Menu ID: {$this->menu->id}, Branch ID: {$this->branch->id}. Error: " . ($result['message'] ?? 'Unknown error'));
            // If you want to retry on specific failures, you can throw an exception here
            // throw new \Exception($result['message']);
        }
    }

    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 3; // Retry the job up to 3 times if it fails

    /**
     * The number of seconds to wait before retrying the job.
     * @var int[]
     */
    public $backoff = [5, 10, 30]; // Wait 5s, then 10s, then 30s before retrying
}
