<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['branch_id', 'name', 'start_time', 'end_time', 'is_active', 'is_24_hours'];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_active' => 'boolean',
            'is_24_hours' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function schedules()
    {
        return $this->hasMany(ShiftUser::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
