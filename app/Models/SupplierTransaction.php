<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getSupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}