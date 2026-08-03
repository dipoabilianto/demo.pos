<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftUser extends Model
{
    protected $table = 'shift_user';

    protected $fillable = ['shift_id', 'user_id', 'day_of_week'];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
        ];
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
