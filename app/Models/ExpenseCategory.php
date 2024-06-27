<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    // public function branch(){
    //     return $this->belongsTo(Branch::class, 'branch_id');
    // }
}
