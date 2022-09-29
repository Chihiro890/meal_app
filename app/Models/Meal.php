<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        // 'category_id',
        'body',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    public function getImagePathAttribute()
    {
        return 'images/meals/' . $this->image;
    }
}
