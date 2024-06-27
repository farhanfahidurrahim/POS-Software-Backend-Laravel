<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ["id"];

    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getSupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getExpense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }

    public function getPurchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
    public function getSale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}
