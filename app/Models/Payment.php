<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id','gateway','transaction_id','sender_number','amount','status','payload'];
    protected $casts = ['payload' => 'array', 'amount' => 'float'];

    public function order() { return $this->belongsTo(Order::class); }
}
