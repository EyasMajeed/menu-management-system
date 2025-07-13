<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BranchWorkingHour
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $branch_id
 * @property string $day_of_week // e.g., 'Sunday', 'Monday'
 * @property bool $is_closed
 * @property string|null $opening_time // Stored as time (HH:MM:SS)
 * @property string|null $closing_time // Stored as time (HH:MM:SS)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Branch $branch
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BranchWorkingHour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchWorkingHour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchWorkingHour query()
 */
class BranchWorkingHour extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'day_of_week',
        'is_closed',
        'opening_time',
        'closing_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_closed' => 'boolean', // Cast tinyint(1) to boolean
    ];

    /**
     * Get the branch that the working hour belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
