<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];

    public function designation(){
        return $this->hasOne(Designation::class,'id','designation_id');
    }
}
