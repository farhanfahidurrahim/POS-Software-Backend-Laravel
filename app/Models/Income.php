<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function getIncomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }
}
