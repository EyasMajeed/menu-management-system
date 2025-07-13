<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModifierGroup;
use App\Models\Modifier;
use App\Models\Menu;
use App\Models\Branch; // Ensure Branch model is imported
use Illuminate\Validation\Rule; // Import Rule for validation

/**
 * Class ModifierController
 *
 * Handles CRUD operations for Modifiers within a specific Menu.
 * Manages the display, creation, storage, viewing, editing, updating,
 * and deletion of modifier resources, including their association with
 * a single modifier group and multiple branches.
 */
class ModifierController extends Controller
{
    /**
     * Display a listing of the modifiers for a specific menu.
     *
     * Eager loads the associated modifier group and branches for each modifier
     * to optimize data retrieval for the index view.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function index(Menu $menu)
    {
        // Retrieve modifiers belonging to the given menu, eager loading their modifier group and branches.
        $modifiers = Modifier::where('menu_id', $menu->id)
                             ->with(['modifierGroup', 'branches'])
                             ->get();
        
        // Render the modifiers.index Blade view, passing the menu and modifiers data.
        return view('modifiers.index', compact('menu', 'modifiers'));
    }

    /**
     * Show the form for creating a new modifier.
     *
     * Fetches modifier groups and branches associated with the menu
     * to populate dropdowns/checkboxes in the modifier creation form.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function create(Menu $menu)
    {
        // Retrieve modifier groups belonging to the current menu.
        $modifierGroups = $menu->modifierGroups()->get();
        // Retrieve branches associated with the current menu.
        $branches = $menu->branches()->get(); 
        
        // Render the modifiers.create Blade view, passing menu, modifier groups, and branches data.
        return view('modifiers.create', compact('menu', 'modifierGroups', 'branches'));
    }

    /**
     * Store a newly created modifier in storage.
     *
     * Validates the incoming request data, creates a new Modifier record,
     * associating it with a single modifier group and multiple branches.
     * Includes a custom validation rule to ensure the selected modifier group
     * belongs to the same menu.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing modifier data.
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Menu $menu)
    {
        // Validate the incoming request data.
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'modifier_group_id' => [
                'nullable',
                'exists:modifier_groups,id',
                // Custom validation rule to ensure the selected modifier group belongs to this menu.
                function ($attribute, $value, $fail) use ($menu) {
                    if ($value && !ModifierGroup::where('id', $value)->where('menu_id', $menu->id)->exists()) {
                        $fail('The selected modifier group does not belong to this menu.');
                    }
                },
            ],
            'branch_ids' => 'nullable|array', // Validation for multiple branch IDs.
            'branch_ids.*' => 'exists:branches,id', // Validation for individual branch IDs.
        ]);

        // Create a new Modifier instance with the validated data.
        // 'name' is stored as a JSON array for multi-language support.
        // 'status' is hardcoded to 'Active' upon creation.
        $modifier = Modifier::create([
            'menu_id' => $menu->id,
            'name' => [
                'en' => $validatedData['name_en'],
                'ar' => $validatedData['name_ar'],
            ],
            'price' => $validatedData['price'],
            'description' => $validatedData['description'],
            'modifier_group_id' => $validatedData['modifier_group_id'] ?? null,
            'status' => 'Active',
        ]);

        // Synchronize the many-to-many relationship with branches.
        $modifier->branches()->sync($validatedData['branch_ids'] ?? []);

        // Redirect to the modifiers index page for the current menu with a success message.
        return redirect()->route('menus.modifiers.index', $menu->id)
                         ->with('success', 'Modifier created successfully.');
    }

    /**
     * Show the form for editing the specified modifier.
     *
     * Retrieves the associated menu, modifier groups for that menu,
     * and branches associated with the menu. It also prepares the IDs of branches
     * currently assigned to the modifier for pre-selection in the form.
     * Includes a security check to ensure the modifier belongs to the specified menu.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @param  \App\Models\Modifier  $modifier The Modifier model instance resolved by route model binding.
     * @return \Illuminate\View\View
     */
    public function edit(Menu $menu, Modifier $modifier)
    {
        // Security check: Ensure the modifier belongs to the specified menu.
        if ($modifier->menu_id !== $menu->id) {
            abort(403, 'Unauthorized action.');
        }

        // Retrieve modifier groups belonging to the current menu.
        $modifierGroups = $menu->modifierGroups()->get();
        // Retrieve branches associated with the current menu.
        $branches = $menu->branches()->get(); 
        
        // Get IDs of branches currently assigned to this modifier for pre-population in the form.
        $assignedBranchIds = $modifier->branches->pluck('id')->toArray(); 

        // Render the modifiers.edit Blade view, passing all necessary data.
        return view('modifiers.edit', compact('menu', 'modifier', 'modifierGroups', 'branches', 'assignedBranchIds'));
    }

    /**
     * Update the specified modifier in storage.
     *
     * Validates input, updates the Modifier record, and synchronizes its
     * many-to-many relationship with branches. Includes a custom validation rule
     * for the modifier group and a security check.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing updated modifier data.
     * @param  \App\Models\Menu  $menu The Menu model instance resolved by route model binding.
     * @param  \App\Models\Modifier  $modifier The Modifier model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Menu $menu, Modifier $modifier)
    {
        // Security check: Ensure the modifier belongs to the specified menu.
        if ($modifier->menu_id !== $menu->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request data.
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'modifier_group_id' => [
                'nullable',
                'exists:modifier_groups,id',
                // Custom validation rule to ensure the selected modifier group belongs to this menu.
                function ($attribute, $value, $fail) use ($menu) {
                    if ($value && !ModifierGroup::where('id', $value)->where('menu_id', $menu->id)->exists()) {
                        $fail('The selected modifier group does not belong to this menu.');
                    }
                },
            ],
            'status' => 'required|in:Active,Inactive', // Validate status against allowed values.
            'branch_ids' => 'nullable|array', // Validation for multiple branch IDs.
            'branch_ids.*' => 'exists:branches,id', // Validation for individual branch IDs.
        ]);

        // Update the Modifier instance with the validated data.
        // 'name' is updated as a JSON array for multi-language support.
        $modifier->update([
            'name' => [
                'en' => $validatedData['name_en'],
                'ar' => $validatedData['name_ar'],
            ],
            'price' => $validatedData['price'],
            'description' => $validatedData['description'],
            'modifier_group_id' => $validatedData['modifier_group_id'] ?? null,
            'status' => $validatedData['status'],
        ]);

        // Synchronize the many-to-many relationship with branches.
        $modifier->branches()->sync($validatedData['branch_ids'] ?? []);

        // Redirect to the modifiers index page for the current menu with a success message.
        return redirect()->route('menus.modifiers.index', $menu->id)
                         ->with('success', 'Modifier updated successfully.');
    }

    /**
     * Remove the specified modifier from storage.
     *
     * Detaches associated branches before deleting the modifier record.
     * Includes a security check to ensure the modifier belongs to the specified menu.
     *
     * @param  \App\Models\Menu  $menu The Menu model instance (for security check).
     * @param  \App\Models\Modifier  $modifier The Modifier model instance resolved by route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Menu $menu, Modifier $modifier)
    {
        // Security check: Ensure the modifier belongs to the specified menu.
        if ($modifier->menu_id !== $menu->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Detach all many-to-many relationships with branches before deleting the modifier.
        $modifier->branches()->detach();

        // Delete the Modifier record from the database.
        $modifier->delete();

        // Redirect to the modifiers index page for the current menu with a success message.
        return redirect()->route('menus.modifiers.index', $menu->id)
                         ->with('success', 'Modifier deleted successfully.');
    }
}
