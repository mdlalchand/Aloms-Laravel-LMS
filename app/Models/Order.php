<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = ['order_no','user_id','coupon_id','subtotal','discount','total','status','name','phone','email','address','note'];

    protected $casts = ['subtotal' => 'float', 'discount' => 'float', 'total' => 'float'];

    protected static function booted(): void
    {
        static::creating(function (self $o) {
            $o->order_no ??= 'RM'.now()->format('ymd').Str::upper(Str::random(6));
        });
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function items()   { return $this->hasMany(OrderItem::class); }
    public function payments(){ return $this->hasMany(Payment::class); }
    public function coupon()  { return $this->belongsTo(Coupon::class); }

    public function getRouteKeyName() { return 'order_no'; }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'paid' => 'bg-emerald-100 text-emerald-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'failed', 'cancelled' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
