<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getAuthUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
