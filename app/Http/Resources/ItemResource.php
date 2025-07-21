<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// Import related resources if they exist, otherwise we'll define them or use basic arrays
// Assuming you might create CategoryResource, ModifierGroupResource, ModifierResource later
// For now, we'll use basic arrays for nested relationships if resources don't exist yet.

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'this' refers to the individual Item model instance being transformed
        return [
            'id' => (string) $this->id, // Always cast IDs to string for API consistency
            'name' => $this->name, // Assuming 'name' is already an array (for multi-language)
            'description' => $this->description,
            'price' => (float) $this->price, // Ensure price is a float
            'status' => $this->status,
            'is_available' => ($this->status === 'Active'), // Derived attribute
            'menu_id' => (string) $this->menu_id,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            
            // Include relationships.
            // Use whenLoaded() to only include if the relationship has been eager loaded.
            // This prevents N+1 query problems.
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'modifier_groups' => ModifierGroupResource::collection($this->whenLoaded('modifierGroups')),
            
            // If CategoryResource or ModifierGroupResource don't exist yet,
            // you can start with a simpler array transformation:
            /*
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => (string) $category->id,
                        'name' => $category->name,
                        // Add other category fields you need
                    ];
                });
            }),
            'modifier_groups' => $this->whenLoaded('modifierGroups', function () {
                return $this->modifierGroups->map(function ($group) {
                    return [
                        'id' => (string) $group->id,
                        'name' => $group->name,
                        // Add other modifier group fields you need
                        'modifiers' => $group->whenLoaded('modifiers', function () {
                            return $group->modifiers->map(function ($modifier) {
                                return [
                                    'id' => (string) $modifier->id,
                                    'name' => $modifier->name,
                                    'price' => (float) $modifier->price,
                                ];
                            });
                        }),
                    ];
                });
            }),
            */
        ];
    }
}