<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public const FOLDER_NAME_PHOTO = 'photos';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'contact_number',
        'category_id',
        'photo',
    ];

    protected $appends = [
        'hobby_ids',
    ];

    public function hobbies()
    {
        return $this->hasMany(UserHobby::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getHobbyIdsAttribute()
    {
        return $this->hobbies->pluck('hobby_id')->toArray();
    }
}
