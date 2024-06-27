<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ["id"];
    protected $casts = [
        'variation_id' => 'array',
    ];
    public function productVariation()
    {
        return $this->hasMany(Variation::class, 'id', 'variation_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function getSupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
