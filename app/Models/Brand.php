<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * Class Brand
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Menu[] $menus
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Branch[] $branches
 */


class Brand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the menus for the brand.
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get the branches for the brand.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
    
}
