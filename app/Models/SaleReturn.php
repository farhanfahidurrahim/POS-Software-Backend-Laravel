<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function getSale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function productVariation()
    {
        return $this->belongsTo(Variation::class, 'product_variation_id');
    }

    public function getCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getAuthUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
