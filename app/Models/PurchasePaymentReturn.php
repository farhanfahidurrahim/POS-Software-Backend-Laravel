<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchasePaymentReturn extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ["id"];

    public function getPurchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }

    public function getAuthUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
