<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Item;
use App\Models\ModifierGroup;
use App\Models\Branch;
use Illuminate\Validation\Rule; // Import Rule for validation

/**
 * Class ItemController
 * Handles CRUD operations for Items within a Menu.
 */
class ItemController extends Controller
{
    /**
     * Display a listing of the items for a specific menu.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\View\View
     */
    public function index(Menu $menu)
    {
        // Eager load categories, modifierGroups, and branches for each item
        $items = Item::where('menu_id', $menu->id)
                     ->with(['categories', 'modifierGroups', 'branches'])
                     ->get();
        
        return view('items.index', compact('menu', 'items'));
    }

    /**
     * Show the form for creating a new item.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\View\View
     */
    public function create(Menu $menu)
    {
        // Get categories, modifier groups, and branches associated with this menu
        $categories = $menu->categories()->get();
        $modifierGroups = $menu->modifierGroups()->get();
        $branches = $menu->branches()->get(); 
        
        return view('items.create', compact('menu', 'categories', 'modifierGroups', 'branches'));
    }

    /**
     * Store a newly created item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Menu $menu)
    {
        $validatedData = $request->validate([
            'name.en' => 'required|string|max:255', // Updated for multi-language JSON
            'name.ar' => 'required|string|max:255', // Updated for multi-language JSON
            'description' => 'nullable|string', // Single language description
            'price' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['Active', 'Inactive'])], // Added status validation
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'modifier_group_ids' => 'nullable|array', // New: Validation for multiple modifier groups
            'modifier_group_ids.*' => 'exists:modifier_groups,id', // New: Validation for individual modifier group IDs
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $item = new Item();
        $item->menu_id = $menu->id;
        
        // Assign multi-language name
        $item->name = [
            'en' => $validatedData['name']['en'],
            'ar' => $validatedData['name']['ar'],
        ];

        $item->description = $validatedData['description'] ?? null;
        $item->price = $validatedData['price'];
        $item->status = $validatedData['status']; // Use validated status
        
        $item->save();

        // Sync relationships
        $item->categories()->sync($validatedData['category_ids'] ?? []);
        $item->modifierGroups()->sync($validatedData['modifier_group_ids'] ?? []); // Sync many-to-many modifier groups
        $item->branches()->sync($validatedData['branch_ids'] ?? []);

        return redirect()->route('menus.items.index', $menu->id)->with('success', 'Item created successfully.');
    }

    /**
     * Show the form for editing the specified item.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\View\View
     */
    public function edit(Menu $menu,Item $item) // Reverted: Removed Menu $menu parameter
    {
        // No security check here as per user request.
        // It's generally recommended to have this check for security.
        
        $menu = $item->menu; // Get the menu associated with the item

        if (!$menu) {
            // This case should ideally not happen if items are always linked to a menu
            return redirect()->back()->with('error', 'Item is not associated with a menu.');
        }

        // Eager load relationships needed for pre-populating checkboxes
        $item->load(['categories', 'modifierGroups', 'branches']);

        $categories = $menu->categories()->get();
        $modifierGroups = $menu->modifierGroups()->get();
        $branches = $menu->branches()->get();
        
        // Get IDs of currently assigned categories, modifier groups, and branches for pre-population
        $itemCategoryIds = $item->categories->pluck('id')->toArray();
        $itemModifierGroupIds = $item->modifierGroups->pluck('id')->toArray();
        $itemBranchIds = $item->branches->pluck('id')->toArray(); 

        return view('items.edit', compact(
            'item', 
            'menu', 
            'categories', 
            'itemCategoryIds', 
            'modifierGroups', 
            'itemModifierGroupIds', 
            'branches', 
            'itemBranchIds'
        ));
    }

    /**
     * Update the specified item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Menu $menu, Item $item) // Reverted: Removed Menu $menu parameter
    {
        // No security check here as per user request.
        // It's generally recommended to have this check for security.

        $validatedData = $request->validate([
            'name.en' => 'required|string|max:255', // Updated for multi-language JSON
            'name.ar' => 'required|string|max:255', // Updated for multi-language JSON
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'modifier_group_ids' => 'nullable|array',
            'modifier_group_ids.*' => 'exists:modifier_groups,id',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        // Update item attributes
        $item->update([
            'name' => [
                'en' => $validatedData['name']['en'],
                'ar' => $validatedData['name']['ar'],
            ],
            'price' => $validatedData['price'],
            'description' => $validatedData['description'],
            'status' => $validatedData['status'],
        ]);

        // Sync relationships
        $item->categories()->sync($validatedData['category_ids'] ?? []);
        $item->modifierGroups()->sync($validatedData['modifier_group_ids'] ?? []);
        $item->branches()->sync($validatedData['branch_ids'] ?? []);

        return redirect()->route('menus.items.index', $item->menu_id)->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Menu $menu, Item $item) // Retained Menu $menu parameter for route consistency
    {
        
        // Note: The original request for this method included the menu parameter, so it's kept.

        // Detach all many-to-many relationships before deleting the item
        $item->categories()->detach();
        $item->modifierGroups()->detach();
        $item->branches()->detach(); 
        
        $item->delete();

        return redirect()->route('menus.items.index', $menu->id)->with('success', 'Item deleted.');
    }
}
