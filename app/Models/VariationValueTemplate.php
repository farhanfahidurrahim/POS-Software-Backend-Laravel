<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationValueTemplate extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function templateValue(){
        return $this->belongsTo(VariationTemplate::class, 'variation_template_id', 'id');
    }
}
