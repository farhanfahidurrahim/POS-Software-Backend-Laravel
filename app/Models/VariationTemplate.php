<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function values()
    {
        return $this->hasMany(VariationValueTemplate::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
