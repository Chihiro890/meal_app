<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // (hasMany)1つのカテゴリーは複数の記事に紐付いている
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
}
