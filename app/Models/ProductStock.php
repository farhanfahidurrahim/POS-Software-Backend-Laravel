<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ["id"];
    
    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getVariation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
