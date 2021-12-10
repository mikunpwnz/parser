<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Girl extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'first_name',
        'last_name',
        'bdate',
        'photo',
        'wrote',
        'need_to_write',
        'last_seen',
        'age',
        'instagram',
        'url_photo',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function notes()
    {
        return $this->belongsToMany(Note::class);
    }
}
