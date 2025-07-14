<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Branch;
use App\Models\BranchWorkingHour;
use App\Models\Category;
use App\Models\Item;
use App\Models\ModifierGroup;
use App\Models\Modifier; 
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException; 

class MenuSyncService
{
    protected string $deliveryPlatformApiUrl = 'http://127.0.0.1:8001/api/mock-menu-sync';

    public function sync(Menu $menu, Branch $branch): array
    {
        try {
            // Eager load all necessary relationships
            $menu->load([
                'categories.items.modifierGroups.modifiers',
                'branches.workingHours'
            ]);

            // Get the branch's working hours
            $branchWorkingHours = $branch->workingHours->keyBy('day_of_week');
            $scheduleTime = $this->formatScheduleTime($branchWorkingHours);

            $categoriesPayload = $this->formatCategories($menu->categories, $scheduleTime);
            $itemsPayload = $this->formatItems($menu->items, $scheduleTime);

            $allModifierGroups = collect();
            foreach ($menu->items as $item) {
                // Ensure modifierGroups are loaded for the item before merging
                $item->loadMissing('modifierGroups.modifiers'); // Load if not already loaded by initial load
                $allModifierGroups = $allModifierGroups->merge($item->modifierGroups);
            }
            $allModifierGroups = $allModifierGroups->unique('id');
            $toppingsPayload = $this->formatToppings($allModifierGroups);

            $payload = [
                'event' => 'menu_sync',
                'event_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'branch_id' => (string) $branch->id,
                'payload' => [
                    'categories' => $categoriesPayload,
                    'items' => $itemsPayload,
                    'toppings' => $toppingsPayload,
                    'branch_schedule_time' => $scheduleTime,
                ],
            ];


            // Send the POST request with an explicit timeout and throw exceptions on errors
            $response = Http::timeout(120) // Set timeout to 120 seconds
                            ->throw() // Throws RequestException for 4xx or 5xx responses
                            ->post($this->deliveryPlatformApiUrl, $payload);

            
            $branchNameForMessage = $branch->name['en'] ?? $branch->name;

            if ($response->successful()) {
                Log::info("Menu sync successful for Menu ID: {$menu->id}, Branch ID: {$branch->id}", [
                    'response' => $response->json(),
                ]);
                return [
                    'status' => 'success',
                    'message' => "Menu for branch '{$branchNameForMessage}' synced successfully.",
                    'response' => $response->json(),
                ];
            } else {
                // This block should ideally not be reached if ->throw() is used for 4xx/5xx
                // but kept as a fallback for other non-successful scenarios if any.
                Log::error("Menu sync failed for Menu ID: {$menu->id}, Branch ID: {$branch->id}", [
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'payload_sent' => $payload,
                ]);
                return [
                    'status' => 'error',
                    'message' => "Failed to sync menu for branch '{$branchNameForMessage}'. Status: {$response->status()}",
                    'response' => $response->json(),
                    'error_details' => $response->body(),
                ];
            }

        } catch (RequestException $e) { // Catch specific Guzzle/HTTP client exceptions (4xx/5xx responses)
            Log::error("HTTP Client Error during menu sync for Menu ID: {$menu->id}, Branch ID: {$branch->id}. Error: " . $e->getMessage(), [
                'status_code' => $e->response ? $e->response->status() : 'N/A',
                'response_body' => $e->response ? $e->response->body() : 'N/A',
                'request_url' => $e->request->getUri(),
                'payload_sent' => $payload,
                'exception_trace' => $e->getTraceAsString(),
            ]);
            $branchNameForMessage = $branch->name['en'] ?? $branch->name;
            return [
                'status' => 'error',
                'message' => "HTTP Sync Error for branch '{$branchNameForMessage}': " . $e->getMessage(),
                'response' => $e->response ? $e->response->json() : null,
                'error_details' => $e->response ? $e->response->body() : $e->getMessage(),
            ];
        } catch (\Exception $e) { // Catch any other unexpected exceptions
            Log::error("An unexpected error occurred during menu sync for Menu ID: {$menu->id}, Branch ID: {$branch->id}. Error: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            $branchNameForMessage = $branch->name['en'] ?? $branch->name;
            return [
                'status' => 'error',
                'message' => "An error occurred during menu sync for branch '{$branchNameForMessage}': " . $e->getMessage(),
                'response' => null,
                'error_details' => $e->getTraceAsString(),
            ];
        }
    }

    /**
     * Formats branch working hours into the required schedule_time JSON structure.
     *
     * @param \Illuminate\Support\Collection $workingHours A collection of BranchWorkingHour models keyed by day_of_week.
     * @return array The formatted schedule_time array.
     */
    protected function formatScheduleTime($workingHours): array
    {
        $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $schedule = [];

        foreach ($daysOfWeek as $day) {
            $dayWorkingHour = $workingHours->get($day);
            
            if ($dayWorkingHour && !$dayWorkingHour->is_closed) {
                $schedule[$day][] = [
                    'start' => Carbon::parse($dayWorkingHour->opening_time)->format('H:i'),
                    'end' => Carbon::parse($dayWorkingHour->closing_time)->format('H:i'),
                    'is_visible' => true,
                ];
            } else {
                $schedule[$day][] = [
                    'start' => '00:00',
                    'end' => '00:00',
                    'is_visible' => false,
                ];
            }
        }
        return $schedule;
    }

    /**
     * Formats categories into the required JSON structure.
     *
     * @param \Illuminate\Support\Collection $categories A collection of Category models.
     * @param array $scheduleTime The branch's schedule time to apply to categories.
     * @return array The formatted categories array.
     */
    protected function formatCategories($categories, $scheduleTime): array
    {
        $formattedCategories = [];
        foreach ($categories as $category) {
            $formattedCategories[] = [
                'id' => (string) $category->id,
                'name' => $category->name,
                'description' => $category->description ?? '',
                'schedule_time' => $scheduleTime,
            ];
        }
        return $formattedCategories;
    }

    /**
     * Formats items into the required JSON structure.
     *
     * @param \Illuminate\Support\Collection $items A collection of Item models.
     * @param array $scheduleTime The branch's schedule time to apply to items.
     * @return array The formatted items array.
     */
    protected function formatItems($items, $scheduleTime): array
    {
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'id' => (string) $item->id,
                'name' => $item->name,
                'description' => $item->description ?? '',
                'price' => (float) $item->price,
                'is_available' => ($item->status === 'Active'),
                'categories' => $item->categories->pluck('id')->map(fn($id) => (string) $id)->toArray(),
                'images' => [],
                'schedule_time' => $scheduleTime,
            ];
        }
        return $formattedItems;
    }

    /**
     * Formats modifier groups (toppings) and their modifiers (options) into the required JSON structure.
     *
     * @param \Illuminate\Support\Collection $modifierGroups A collection of ModifierGroup models.
     * @return array The formatted toppings array.
     */
    protected function formatToppings($modifierGroups): array
    {
        $formattedToppings = [];
        foreach ($modifierGroups as $modifierGroup) {
            $options = [];
            foreach ($modifierGroup->modifiers as $modifier) {
                $options[] = [
                    'id' => (string) $modifier->id,
                    'name' => $modifier->name,
                    'price' => (float) $modifier->price,
                ];
            }

            // Ensure items relationship is loaded or handled carefully here
            // This line could cause N+1 if not eager loaded properly
            $itemIds = $modifierGroup->items->pluck('id')->map(fn($id) => (string) $id)->toArray();

            $formattedToppings[] = [
                'id' => (string) $modifierGroup->id,
                'name' => $modifierGroup->name,
                'is_required' => (bool) $modifierGroup->is_required,
                'min' => (int) $modifierGroup->min_selection,
                'max' => (int) $modifierGroup->max_selection,
                'max_per_quantity' => (int) $modifierGroup->max_per_quantity,
                'options' => $options,
                'items' => $itemIds,
            ];
        }
        return $formattedToppings;
    }
}
