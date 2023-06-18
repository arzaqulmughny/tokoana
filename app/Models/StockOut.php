<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    use HasFactory;
    protected $table = 'stock_out_history';
    protected $guarded = ['id'];
    protected $dateFormat = 'U';

    public function items() {
        return $this->hasMany(StockOutItem::class, 'history_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
