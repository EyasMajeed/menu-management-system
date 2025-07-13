<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Category
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $menu_id
 * @property array $name // Stored as JSON, cast to array for multi-language
 * @property string $status // e.g., 'Active', 'Inactive'
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Menu $menu
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Item[] $items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Branch[] $branches
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 */

class Category extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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
        // 'status' is a string, no casting needed unless it represents a boolean (0/1)
    ];

    /**
     * Get the menu that the category belongs to.
     */
    public function menu() 
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the items for the category (many-to-many).
     * Includes pivot column 'position' and timestamps from 'category_item' table.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_item')->withPivot('position')->withTimestamps();
    }

    /**
     * Get the branches that are associated with the category (many-to-many).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_category');
    }

}
