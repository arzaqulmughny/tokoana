<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    // protected $with = ['category', 'unit'];

    public function unit() {
        return $this->belongsTo(ProductUnit::class, 'unit_id', 'id');
    }

    public function category() {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
}
