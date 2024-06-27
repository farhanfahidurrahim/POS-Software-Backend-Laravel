<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ["id"];

    public function getPurchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function getAuthUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
