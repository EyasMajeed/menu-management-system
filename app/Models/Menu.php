<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Menu
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $brand_id
 * @property int|null $branch_id // Nullable if a menu can exist without a primary branch
 * @property string|null $cuisine_type
 * @property string|null $menu_type
 * @property string $status // e.g., 'Active', 'Inactive'
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Brand $brand
 * @property-read \App\Models\Branch|null $branch // The single primary branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Branch[] $branches // Multiple associated branches via pivot
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Item[] $items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModifierGroup[] $modifierGroups
 */

class Menu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'brand_id',
        'branch_id',
        'cuisine_type',
        'menu_type',
        'status',
    ];

    /**
     * Get the brand that owns the Menu.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the single primary branch that this Menu belongs to.
     * This uses the 'branch_id' column directly on the menus table.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the multiple branches that are associated with the Menu.
     * This uses the 'menu_branch' pivot table for many-to-many relationships.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    /**
     * Get the categories for the Menu.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the items for the Menu.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the modifier groups for the Menu.
     */
    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class);
    }

}
