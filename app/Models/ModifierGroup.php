<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ModifierGroup
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $menu_id
 * @property array $name // Stored as JSON, cast to array for multi-language
 * @property string $type // e.g., 'single_select', 'multi_select'
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Menu $menu
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Modifier[] $modifiers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Item[] $items // Many-to-many
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Branch[] $branches
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ModifierGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModifierGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModifierGroup query()
 */

class ModifierGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'menu_id',
        'name',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'array', // Correctly cast to array for JSON column
        // 'type' is a string, no casting needed unless it's an enum in DB and you want specific handling
    ];

    /**
     * Get the menu that the ModifierGroup belongs to.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the modifiers for the ModifierGroup.
     */
    public function modifiers()
    {
        return $this->hasMany(Modifier::class);
    }

    /**
     * Get the items that are associated with the ModifierGroup (many-to-many).
     * This uses the 'item_modifier_group' pivot table.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_modifier_group');
    }

    /**
     * Get the branches that are associated with the ModifierGroup (many-to-many).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_modifier_group');
    }
}
