<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $with = ['detail'];

    public function detail() {
        return $this->belongsTo(ProductList::class, 'product_id', 'id');
    }
}
