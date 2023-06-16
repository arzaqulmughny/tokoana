<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'stock_in_history';

    public function supplier() {
        return $this->hasOne(Supplier::class, 'supplier_id','id');
    }
}