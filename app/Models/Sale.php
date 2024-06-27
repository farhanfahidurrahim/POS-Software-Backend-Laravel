<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    // public function productVariation()
    // {
    //     return $this->belongsTo(Variation::class, 'product_variation_id');
    // }

    public function getCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getCourier()
    {
        return $this->belongsTo(CourierShipping::class, 'courier_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function getVariations()
    // {
    //     return $this->belongsTo(Variation::class, 'variation_id');
    // }

    public function getVariations()
    {
        return $this->belongsToMany(Variation::class);
    }
}
