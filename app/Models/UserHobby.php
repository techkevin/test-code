<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHobby extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'hobby_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hobby()
    {
        return $this->belongsTo(Hobby::class);
    }
}
