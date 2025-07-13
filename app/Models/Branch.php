<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Branch
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $brand_id
 * @property float $latitude
 * @property float $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Brand $brand
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Menu[] $menus
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BranchWorkingHour[] $workingHours
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Item[] $items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModifierGroup[] $modifierGroups
 */


class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand_id',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Get the brand that the Branch belongs to.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the menus that are associated with the Branch (many-to-many).
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class); // Uses default 'branch_menu' pivot table
    }

    /**
     * Get the working hours for the Branch.
     */
    public function workingHours()
    {
        return $this->hasMany(BranchWorkingHour::class);
    }

    /**
     * Get the categories that are associated with the Branch (many-to-many).
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'branch_category');
    }

    /**
     * Get the items that are associated with the Branch (many-to-many).
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'branch_item');
    }

    /**
     * Get the modifier groups that are associated with the Branch (many-to-many).
     */
    public function modifierGroups()
    {
        return $this->belongsToMany(ModifierGroup::class, 'branch_modifier_group');
    }


}
