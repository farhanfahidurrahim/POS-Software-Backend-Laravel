<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SaleReturnPayment extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function getSaleReturn()
    {
        return $this->belongsTo(SaleReturn::class,'sale_return_id');
    }

    public function getCustomer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
