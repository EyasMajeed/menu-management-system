<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Modifier
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $menu_id
 * @property int $modifier_group_id
 * @property array $name // Stored as JSON, cast to array for multi-language
 * @property float $price
 * @property string|null $description
 * @property string $status // e.g., 'Active', 'Inactive'
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\ModifierGroup $modifierGroup
 * @property-read \App\Models\Menu $menu
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Branch[] $branches
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Modifier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Modifier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Modifier query()
 */
class Modifier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'menu_id',
        'modifier_group_id',
        'name',
        'price',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'array', // Correctly cast to array for JSON column
    ];

    /**
     * Get the modifier group that the Modifier belongs to.
     */
    public function modifierGroup()
    {
        return $this->belongsTo(ModifierGroup::class);
    }
    
    /**
     * Get the menu that the Modifier belongs to.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the branches that are associated with the Modifier (many-to-many).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_modifier');
    }
}
