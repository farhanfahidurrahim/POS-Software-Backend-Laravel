<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ["id"];

    public function getExpenseCategory(){
        return $this->belongsTo(ExpenseCategory::class,"expense_category_id","id");
    }

    public function branch(){
        return $this->belongsTo(Branch::class,"branch_id","id");
    }

    public function expensePayment(){
        return $this->hasOne(ExpensePayment::class,"expense_id","id");
    }
}
