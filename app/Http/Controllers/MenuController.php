<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Item;
use App\Models\ModifierGroup;
use App\Services\MenuSyncService;
use App\Jobs\SyncMenuToDeliveryApp;
use Illuminate\Support\Facades\Log;


/**
 * Class MenuController
 *
 * Handles CRUD operations for Menus.
 * This controller manages the display, creation, storage, viewing,
 * editing, updating, and deletion of menu resources.
 */
class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     *
     * Retrieves all menus from the database. If the request expects JSON,
     * it returns a JSON response; otherwise, it renders the menus.index view.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request.
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $menus = Menu::all(); // Fetch all Menu models from the database.

        // Check if the request expects a JSON response (e.g., from an API call).
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $menus
            ]);
        }

        // If not a JSON request, render the menus.index Blade view, passing the menus data.
        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new menu.
     *
     * Fetches all available Brands and Branches to populate dropdowns/checkboxes
     * in the menu creation form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $brands = Brand::all();   // Retrieve all Brand models.
        $branches = Branch::all(); // Retrieve all Branch models.

        // Render the menus.create Blade view, passing brands and branches data.
        return view('menus.create', compact('brands', 'branches'));
    }

    /**
     * Store a newly created menu in storage.
     *
     * Validates the incoming request data, creates a new Menu record,
     * and attaches selected branches to it via a many-to-many relationship.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing menu data.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data.
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id', // Validate each branch ID in the array.
            'cuisine_type' => 'nullable|string',
            'menu_type' => 'nullable|string',
        ]);

        // Create a new Menu instance with the validated data.
        // 'status' is hardcoded to 'Active' upon creation.
        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
            'brand_id' => $request->brand_id,
            'cuisine_type' => $request->cuisine_type,
            'menu_type' => $request->menu_type,
            'status' => 'Active',
        ]);

        // Attach the selected branches to the newly created menu using the many-to-many relationship.
        $menu->branches()->attach($request->branch_ids);

        // Redirect to the menus index page with a success message.
        return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
    }

    /**
     * Display the specified menu.
     *
     * Retrieves a specific menu by its ID and eager loads its related categories,
     * items within those categories, their modifier groups, and the menu's branches.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function show(Menu $menu)
    {
        // Eager load nested relationships for efficient data retrieval.
        // categories.items.modifierGroups: Loads categories, then items for each category, then modifier groups for each item.
        // branches: Loads branches associated with the menu.
        $menu->load(['categories.items.modifierGroups', 'branches']);

        // Fetch categories specifically with their items for the view, if needed separately.
        $categories = $menu->categories()->with('items')->get();

        // Render the menus.show Blade view, passing the menu and categories data.
        return view('menus.show', compact('menu', 'categories'));
    }

    /**
     * Show the form for editing the specified menu.
     *
     * Fetches all available Brands and Branches to populate the edit form,
     * ensuring that the currently associated branches are pre-selected.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function edit(Menu $menu)
    {
        $brands = Brand::all(); // Retrieve all Brand models.
        // Retrieve branches associated with the menu's brand.
        $branches = Branch::where('brand_id', $menu->brand_id)->get();

        // Render the menus.edit Blade view, passing the menu, brands, and branches data.
        return view('menus.edit', compact('menu', 'brands', 'branches'));
    }

    /**
     * Update the specified menu in storage.
     *
     * Validates the incoming request data, updates the Menu record,
     * and synchronizes its many-to-many relationship with branches.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing updated menu data.
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Menu $menu)
    {
        // Validate the incoming request data and capture it in $validatedData.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'cuisine_type' => 'nullable|string',
            'menu_type' => 'nullable|string',
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id', // Validate each branch ID in the array.
            'status' => 'required|in:Active,Inactive', // Validate status against allowed values.
        ]);

        // Update the Menu instance with the validated data.
        $menu->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'brand_id' => $validatedData['brand_id'],
            'cuisine_type' => $validatedData['cuisine_type'],
            'menu_type' => $validatedData['menu_type'],
            'status' => $validatedData['status'],
        ]);

        // Synchronize the many-to-many relationship with branches.
        // `sync` ensures that only the provided IDs remain attached, detaching others.
        $menu->branches()->sync($validatedData['branch_ids']);

        // Redirect to the menu's show page with a success message.
        return redirect()->route('menus.show', $menu->id)->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified menu from storage.
     *
     * Deletes the Menu record. Note: This method does not explicitly detach
     * or delete related records (like categories, items, modifier groups).
     * Ensure `onDelete('cascade')` is set in your migrations for foreign keys
     * if you want related data to be automatically removed.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Menu $menu)
    {
        // Delete the Menu record from the database.
        $menu->delete();

        // Redirect to the menus index page with a success message.
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully!');
    }

    /**
 * Handles the request to sync a menu to delivery apps.
 * Dispatches a job to the queue for background processing.
 *
 * @param \Illuminate\Http\Request $request
 * @param \App\Models\Menu $menu
 * @return \Illuminate\Http\RedirectResponse
 */
public function syncMenuToDeliveryApp(Request $request, Menu $menu)
{
    Log::info('[SYNC] Entered syncMenuToDeliveryApp', [
        'menu_id' => $menu->id,
        'input' => $request->all()
    ]);

    $selectedBranchIds = $request->input('branch_ids', []);

    if (empty($selectedBranchIds)) {
        Log::warning('[SYNC] No branches selected for sync.');
        return redirect()->back()->with('warning', 'Please select at least one branch to sync.');
    }

    $branches = Branch::whereIn('id', $selectedBranchIds)->get();

    if ($branches->isEmpty()) {
        Log::error('[SYNC] Branches not found for given IDs', ['branch_ids' => $selectedBranchIds]);
        return redirect()->back()->with('error', 'No valid branches selected for sync.');
    }

    foreach ($branches as $branch) {
        Log::info('[SYNC] Dispatching job', [
            'menu_id' => $menu->id,
            'branch_id' => $branch->id
        ]);

        SyncMenuToDeliveryApp::dispatch($menu, $branch);
    }

    Log::info('[SYNC] All dispatch calls made.');

    return redirect()->back()->with('success', 'Menu sync request submitted successfully! It will be processed in the background.');
}

}
