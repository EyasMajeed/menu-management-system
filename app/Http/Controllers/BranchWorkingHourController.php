<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import Rule for advanced validation
use App\Models\Branch;
use App\Models\BranchWorkingHour;
use App\Models\Menu;

/**
 * Class BranchWorkingHourController
 *
 * Handles the management of working hours and location for branches
 * associated with a specific menu.
 */
class BranchWorkingHourController extends Controller
{
    /**
     * Display a listing of branches with their working hours for a specific menu.
     *
     * Eager loads working hours for each branch and orders them by day of the week.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function index(Menu $menu)
    {
        // Retrieve branches associated with the given menu.
        // Eager load working hours for each branch, ordering them by day of the week
        // using a raw SQL FIELD() function for custom sort order.
        $branches = $menu->branches()->with(['workingHours' => function($query) {
            $query->orderByRaw("FIELD(day_of_week, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
        }])->get();

        // Define the ordered days of the week for consistent display and iteration in the view.
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Render the working_hours.index Blade view, passing menu, branches, and days of week.
        return view('working_hours.index', compact('menu', 'branches', 'daysOfWeek'));
    }

    /**
     * Show the form for editing the working hours and location of a specific branch.
     *
     * Includes a security check to ensure the branch is associated with the given menu.
     * Prepares working hours data keyed by day of the week for easy access in the view.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @param  \App\Models\Branch  $branch The Branch model instance resolved by route model binding.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Menu $menu, Branch $branch)
    {
        // Security check: Ensure the branch is associated with the current menu.
        if (!$menu->branches->contains($branch)) {
            abort(403, 'Branch not associated with this menu.');
        }

        // Retrieve working hours for the branch and key them by capitalized day of the week
        // for easy lookup in the view (e.g., $workingHours['Monday']).
        $workingHours = $branch->workingHours->keyBy(function ($wh) {
            return ucfirst($wh->day_of_week);
        });

        // Define the ordered days of the week for consistent display and iteration in the view.
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Render the working_hours.edit Blade view, passing menu, branch, working hours, and days of week.
        return view('working_hours.edit', compact('menu', 'branch', 'workingHours', 'daysOfWeek'));
    }

    /**
     * Update the working hours and location for the specified branch.
     *
     * Validates input, updates the branch's latitude and longitude, and
     * updates or creates BranchWorkingHour records for each day of the week.
     * Includes a security check and conditional validation for time fields.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing working hours and location data.
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @param  \App\Models\Branch  $branch The Branch model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Menu $menu, Branch $branch)
    {
        // Security check: Ensure the branch is associated with the current menu.
        if (!$menu->branches->contains($branch)) {
            abort(403, 'Branch not associated with this menu.');
        }

        // Define the ordered days of the week for iteration.
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Define base validation rules for latitude and longitude.
        $rules = [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];

        // Dynamically add validation rules for each day's working hours.
        foreach ($daysOfWeek as $day) {
            // Rule for the 'is_closed' checkbox (boolean value).
            $rules["{$day}_is_closed"] = 'nullable|boolean';

            // Rules for opening_time: required if 'is_closed' is false, and must be in H:i format.
            $rules["{$day}_opening_time"] = [
                'nullable',
                Rule::requiredIf(fn () => !$request->boolean("{$day}_is_closed")),
                'date_format:H:i',
            ];

            // Rules for closing_time: required if 'is_closed' is false, must be H:i format,
            // and must be after the opening_time for the same day.
            $rules["{$day}_closing_time"] = [
                'nullable',
                Rule::requiredIf(fn () => !$request->boolean("{$day}_is_closed")),
                'date_format:H:i',
                'after:' . $day . '_opening_time', // Ensures closing time is after opening time.
            ];
        }

        // Validate all incoming request data against the defined rules.
        $validated = $request->validate($rules);

        // Update the branch's latitude and longitude.
        $branch->update([
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        // Iterate through each day of the week to update or create working hour records.
        foreach ($daysOfWeek as $day) {
            $isClosed = $request->boolean("{$day}_is_closed"); // Get boolean value for 'is_closed'.
            
            // Set opening/closing times to null if the branch is closed for the day.
            $openingTime = $isClosed ? null : ($validated["{$day}_opening_time"] ?? null);
            $closingTime = $isClosed ? null : ($validated["{$day}_closing_time"] ?? null);

            // Update or create a BranchWorkingHour record for the specific branch and day.
            // The 'day_of_week' is stored in lowercase in the database.
            BranchWorkingHour::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'day_of_week' => strtolower($day), // Match database format.
                ],
                [
                    'is_closed' => $isClosed,
                    'opening_time' => $openingTime,
                    'closing_time' => $closingTime,
                ]
            );
        }

        // Redirect to the working hours index page for the current menu with a success message.
        return redirect()->route('menus.working-hours.index', $menu->id)
            ->with('success', 'Working hours and location updated successfully for ' . ($branch->name['en'] ?? $branch->name ?? 'N/A') . '.');
    }
}
