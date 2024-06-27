<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function variationValueTemplate()
    {
        return $this->belongsTo(VariationValueTemplate::class, 'variation_value_id');
    }

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}