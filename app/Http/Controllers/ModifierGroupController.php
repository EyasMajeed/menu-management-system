<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModifierGroup;
use App\Models\Modifier;
use App\Models\Menu;
use App\Models\Item; 
use App\Models\Branch; 
use Illuminate\Validation\Rule; 


class ModifierGroupController extends Controller
{
    /**
     * Display a listing of the modifier groups for a specific menu.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\View\View
     */
    public function index(Menu $menu)
    {
        // Eager load modifiers, items, and branches for each modifier group
        $modifierGroups = $menu->modifierGroups()->with(['modifiers', 'items', 'branches'])->get();
        return view('modifier_groups.index', compact('menu', 'modifierGroups'));
    }

    /**
     * Show the form for creating a new modifier group.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\View\View
     */
    public function create(Menu $menu)
    {
        $branches = $menu->branches()->get();
        // Get items associated with this menu to populate the assignment list
        $items = Item::where('menu_id', $menu->id)->get();
        return view('modifier_groups.create', compact('menu', 'branches', 'items'));
    }

    /**
     * Store a newly created modifier group in storage.
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
            'type' => ['required', Rule::in(['optional', 'required'])], // Using Rule for consistency
            'min_selection' => 'nullable|integer|min:0',
            'max_selection' => 'nullable|integer|min:0',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'item_ids' => 'nullable|array', // New: Validation for multiple items
            'item_ids.*' => 'exists:items,id', // New: Validation for individual item IDs
        ]);

        // Ensure min_selection is not greater than max_selection if both are provided
        if (isset($validatedData['min_selection']) && isset($validatedData['max_selection']) && $validatedData['min_selection'] > $validatedData['max_selection']) {
            return back()->withErrors(['min_selection' => 'Min selection cannot be greater than Max selection.'])->withInput();
        }

        $modifierGroup = $menu->modifierGroups()->create([
            'name' => [
                'en' => $validatedData['name']['en'],
                'ar' => $validatedData['name']['ar'],
            ],
            'type' => $validatedData['type'],
            'min_selection' => $validatedData['min_selection'] ?? 0,
            'max_selection' => $validatedData['max_selection'] ?? 0,
        ]);

        // Sync relationships
        $modifierGroup->branches()->sync($validatedData['branch_ids'] ?? []);
        $modifierGroup->items()->sync($validatedData['item_ids'] ?? []); // New: Sync many-to-many items

        return redirect()->route('menus.modifier-groups.index', $menu->id)
                         ->with('success', 'Modifier Group created successfully.');
    }

    /**
     * Show the form for editing the specified modifier group.
     *
     * @param  \App\Models\Menu  $menu
     * @param  \App\Models\ModifierGroup  $modifierGroup
     * @return \Illuminate\View\View
     */
    public function edit(Menu $menu, ModifierGroup $modifierGroup)
    {
        // This check ensures the modifier group belongs to the menu
        if ($modifierGroup->menu_id !== $menu->id) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load relationships needed for pre-populating checkboxes
        $modifierGroup->load(['items', 'branches']); // Load items and branches

        // Fetch all items associated with this menu to populate the assignment list
        $items = Item::where('menu_id', $menu->id)->get();
        // Get IDs of items currently assigned to this modifier group
        $assignedItemIds = $modifierGroup->items->pluck('id')->toArray();

        // Fetch branches associated with this menu to populate the assignment list
        $branches = $menu->branches()->get();
        // Get IDs of branches currently assigned to this modifier group
        $assignedBranchIds = $modifierGroup->branches->pluck('id')->toArray();

        // Pass all necessary variables to the view
        return view('modifier_groups.edit', compact(
            'menu',
            'modifierGroup',
            'items', // Pass all items
            'assignedItemIds', // Pass assigned item IDs
            'branches',
            'assignedBranchIds'
        ));
    }

    /**
     * Update the specified modifier group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @param  \App\Models\ModifierGroup  $modifierGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Menu $menu, ModifierGroup $modifierGroup)
    {
        if ($modifierGroup->menu_id !== $menu->id) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name.en' => 'required|string|max:255', // Updated for multi-language JSON
            'name.ar' => 'required|string|max:255', // Updated for multi-language JSON
            'type' => ['required', Rule::in(['optional', 'required'])],
            'min_selection' => 'nullable|integer|min:0',
            'max_selection' => 'nullable|integer|min:0',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'item_ids' => 'nullable|array', // New: Validation for multiple items
            'item_ids.*' => 'exists:items,id', // New: Validation for individual item IDs
        ]);

        // Ensure min_selection is not greater than max_selection if both are provided
        if (isset($validatedData['min_selection']) && isset($validatedData['max_selection']) && $validatedData['min_selection'] > $validatedData['max_selection']) {
            return back()->withErrors(['min_selection' => 'Min selection cannot be greater than Max selection.'])->withInput();
        }

        $modifierGroup->update([
            'name' => [
                'en' => $validatedData['name']['en'],
                'ar' => $validatedData['name']['ar'],
            ],
            'type' => $validatedData['type'],
            'min_selection' => $validatedData['min_selection'] ?? 0,
            'max_selection' => $validatedData['max_selection'] ?? 0,
        ]);

        // Sync relationships
        $modifierGroup->branches()->sync($validatedData['branch_ids'] ?? []);
        $modifierGroup->items()->sync($validatedData['item_ids'] ?? []); // New: Sync many-to-many items

        return redirect()->route('menus.modifier-groups.index', $menu->id)
                         ->with('success', 'Modifier Group updated successfully.');
    }

    /**
     * Remove the specified modifier group from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @param  \App\Models\ModifierGroup  $modifierGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Menu $menu, ModifierGroup $modifierGroup)
    {
        if ($modifierGroup->menu_id !== $menu->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Detach all many-to-many relationships before deleting the modifier group
        $modifierGroup->branches()->detach();
        $modifierGroup->items()->detach(); // New: Detach items

        // Consider if you want to delete associated modifiers as well.
        // If 'modifier_group_id' on 'modifiers' table has onDelete('cascade'),
        // then modifiers will be deleted automatically.
        // Otherwise, you might need: $modifierGroup->modifiers()->delete();

        $modifierGroup->delete();
        return redirect()->route('menus.modifier-groups.index', $menu->id)
                         ->with('success', 'Modifier Group deleted successfully.');
    }
}
