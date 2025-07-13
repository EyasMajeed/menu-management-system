<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Item
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name // Stored as JSON, cast to array for multi-language
 * @property string|null $description
 * @property float $price
 * @property int $menu_id
 * @property string $status // e.g., 'Active', 'Inactive'
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read \App\Models\Menu $menu
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModifierGroup[] $modifierGroups // Many-to-many
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Branch[] $branches
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 */

class Item extends Model
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
        'price',
        'menu_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'array', // Correctly cast to array for JSON column
        'price' => 'decimal:2', // Assuming price has 2 decimal places
        // 'status' is a string, no casting needed unless it represents a boolean (0/1)
    ];

    /**
     * Get the categories that are associated with the Item (many-to-many).
     * Includes pivot table 'category_item'.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    /**
     * Get the menu that the Item belongs to.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the modifier groups that are associated with the Item (many-to-many).
     * This uses the 'item_modifier_group' pivot table.
     */
    public function modifierGroups()
    {
        return $this->belongsToMany(ModifierGroup::class, 'item_modifier_group');
    }

    /**
     * Get the branches that are associated with the Item (many-to-many).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_item');
    }


}
