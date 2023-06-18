<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'stock_out_items';
    protected $with = ['details'];

    public function details() {
        return $this->belongsTo(ProductList::class, 'product_id','id');
    }
}
