<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $dateFormat = 'U';

    public function items () {
        return $this->hasMany(SaleItem::class, 'history_id', 'id');
    }

    public function user () {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
