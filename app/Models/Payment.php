<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'proof_path', 'status', 'verified_by', 'admin_notes'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
