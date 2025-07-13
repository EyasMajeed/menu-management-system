<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Validation\Rule;

/**
 * Class CategoryController
 *
 * Handles CRUD operations for Categories within a specific Menu.
 * Manages the display, creation, storage, viewing, editing, updating,
 * and deletion of category resources, including their item and branch associations.
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the categories for a specific menu.
     *
     * Eager loads associated items and branches for each category to optimize queries.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function index(Menu $menu)
    {
        // Retrieve categories belonging to the given menu, eager loading their items and branches.
        $categories = $menu->categories()->with(['items', 'branches'])->get();
        
        // Render the categories.index Blade view, passing the menu and categories data.
        return view('categories.index', compact('menu', 'categories'));
    }

    /**
     * Show the form for creating a new category.
     *
     * Fetches items belonging to the same menu (ordered by creation date descending)
     * and branches associated with the menu to populate assignment lists in the form.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function create(Menu $menu)
    {
        // Retrieve items associated with the current menu, ordered by creation date.
        $items = Item::where('menu_id', $menu->id)->orderBy('created_at', 'desc')->get();
        
        // Retrieve branches associated with the current menu.
        $branches = $menu->branches()->get(); 
        
        // Render the categories.create Blade view, passing menu, items, and branches data.
        return view('categories.create', compact('menu', 'items', 'branches'));
    }

    /**
     * Store a newly created category in storage.
     *
     * Validates input, creates a new Category record associated with the menu,
     * and synchronizes its many-to-many relationships with items (including position)
     * and branches.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing category data.
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Menu $menu)
    {
        // Validate the incoming request data.
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id', // Validate each item ID in the array.
            'item_order' => 'nullable|string', // Expected to be a JSON string from frontend for ordering.
            'branch_ids' => 'nullable|array', // Validation for multiple branch IDs.
            'branch_ids.*' => 'exists:branches,id', // Validation for individual branch IDs.
        ]);

        // Create a new Category instance associated with the menu.
        // 'name' is stored as a JSON array for multi-language support.
        // 'status' is hardcoded to 'Active' upon creation.
        $category = $menu->categories()->create([
            'name' => [
                'en' => $validatedData['name_en'],
                'ar' => $validatedData['name_ar'],
            ],
            'status' => 'Active',
        ]);

        // Decode the item order JSON string into a PHP array.
        $itemOrder = json_decode($validatedData['item_order'] ?? '[]', true);
        $itemIds = $validatedData['item_ids'] ?? []; // Get selected item IDs.

        // If both item IDs and item order are provided, prepare sync data with positions.
        if (!empty($itemIds) && !empty($itemOrder)) {
            $syncData = [];
            foreach ($itemOrder as $position => $itemId) {
                // Ensure the item ID from order is actually one of the selected items.
                if (in_array($itemId, $itemIds)) {
                    $syncData[$itemId] = ['position' => $position];
                }
            }
            // Sync the many-to-many relationship with items, including their positions.
            $category->items()->sync($syncData);
        } else {
            // If no items are selected or no order is provided, detach all existing items.
            $category->items()->detach();
        }

        // Synchronize the many-to-many relationship with branches.
        $category->branches()->sync($validatedData['branch_ids'] ?? []);

        // Redirect to the categories index page for the current menu with a success message.
        return redirect()->route('menus.categories.index', $menu->id)
                         ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     *
     * Eager loads associated items and branches for the category.
     *
     * @param  \App\Models\Category  $category The Category model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function show(Category $category)
    {
        // Eager load items and branches for the specific category.
        $category->load(['items', 'branches']); 
        
        // Render the categories.show Blade view, passing the category data.
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * Retrieves the associated menu, all items belonging to that menu (ordered),
     * and branches associated with the menu. It also prepares the IDs of branches
     * currently assigned to the category for pre-selection in the form.
     *
     * @param  \App\Models\Category  $category The Category model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        $menu = $category->menu; // Get the menu associated with this category.
        // Retrieve items associated with the category's menu, ordered by creation date.
        $items = Item::where('menu_id', $menu->id)->orderBy('created_at', 'desc')->get();
        // Retrieve branches associated with the category's menu.
        $branches = $menu->branches()->get(); 
        
        // Get IDs of branches currently assigned to this category for pre-population in the form.
        $assignedBranchIds = $category->branches->pluck('id')->toArray(); 

        // Render the categories.edit Blade view, passing all necessary data.
        return view('categories.edit', compact('category', 'menu', 'items', 'branches', 'assignedBranchIds'));
    }

    /**
     * Update the specified category in storage.
     *
     * Validates input, updates the Category record, and synchronizes its
     * many-to-many relationships with items (including position) and branches.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing updated category data.
     * @param  \App\Models\Category  $category The Category model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        // Validate the incoming request data.
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive', // Validate status against allowed values.
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id', // Validate each item ID in the array.
            'item_order' => 'nullable|string', // Expected to be a JSON string from frontend for ordering.
            'branch_ids' => 'nullable|array', // Validation for multiple branch IDs.
            'branch_ids.*' => 'exists:branches,id', // Validation for individual branch IDs.
        ]);

        // Update the Category instance with the validated data.
        // 'name' is updated as a JSON array for multi-language support.
        $category->update([
            'name' => [
                'en' => $validatedData['name_en'],
                'ar' => $validatedData['name_ar'],
            ],
            'status' => $validatedData['status'],
        ]);

        // Decode the item order JSON string into a PHP array.
        $itemOrder = json_decode($validatedData['item_order'] ?? '[]', true);
        $itemIds = $validatedData['item_ids'] ?? []; // Get selected item IDs.

        // If both item IDs and item order are provided, prepare sync data with positions.
        if (!empty($itemIds) && !empty($itemOrder)) {
            $syncData = [];
            foreach ($itemOrder as $position => $itemId) {
                // Ensure the item ID from order is actually one of the selected items.
                if (in_array($itemId, $itemIds)) {
                    $syncData[$itemId] = ['position' => $position];
                }
            }
            // Sync the many-to-many relationship with items, including their positions.
            $category->items()->sync($syncData);
        } else {
            // If no items are selected or no order is provided, detach all existing items.
            $category->items()->detach();
        }

        // Synchronize the many-to-many relationship with branches.
        $category->branches()->sync($validatedData['branch_ids'] ?? []);

        // Redirect to the categories index page for the current menu with a success message.
        return redirect()->route('menus.categories.index', $category->menu_id)
                         ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     *
     * Detaches associated items and branches before deleting the category record.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance (for security check).
     * @param  \App\Models\Category  $category The Category model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Menu $menu, Category $category)
    {
        // Security check: Ensure the category belongs to the specified menu.
        if ($category->menu_id !== $menu->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Detach all many-to-many relationships before deleting the category.
        $category->items()->detach();
        $category->branches()->detach(); 
        
        // Delete the Category record from the database.
        $category->delete();

        // Redirect to the categories index page for the current menu with a success message.
        return redirect()->route('menus.categories.index', $menu->id)
                         ->with('success', 'Category deleted successfully.');
    }
}
