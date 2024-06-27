<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ["id"];
    public function productVariation(){
        return $this->hasMany(Variation::class, 'id', 'variation_id');
    }
    public function purchase(){
        return $this->hasOne(Purchase::class, 'id', 'purchase_id');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
