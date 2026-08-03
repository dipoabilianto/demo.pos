<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'shift_id', 'branch_id', 'date',
        'check_in_time', 'check_out_time',
        'check_in_lat', 'check_in_lng',
        'check_out_lat', 'check_out_lng',
        'opening_balance', 'closing_balance',
        'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_time' => 'datetime:H:i:s',
            'check_out_time' => 'datetime:H:i:s',
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
