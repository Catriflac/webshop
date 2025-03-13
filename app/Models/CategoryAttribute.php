<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    //
    protected $fillable = [
        'name',
        'value',
        'category_id',
    ];
}
