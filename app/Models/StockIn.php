<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'stock_in_history';
    protected $dateFormat = 'U';

    public function supplier() {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function items() {
        return $this->hasMany(StockInItem::class, 'history_id', 'id');
    }
}
