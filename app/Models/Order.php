<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_number', 'user_id', 'cart_id', 'status', 'total_amount', 'expired_at'];

    protected $dates = ['expired_at'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipmentLogs()
    {
        return $this->hasMany(ShipmentLog::class)->orderBy('created_at', 'desc');
    }
}
