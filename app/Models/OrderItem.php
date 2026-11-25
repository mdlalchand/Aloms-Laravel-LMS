<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id','item_type','item_id','title','price','qty'];
    protected $casts = ['price' => 'float'];

    public function order() { return $this->belongsTo(Order::class); }

    public function item()
    {
        return match ($this->item_type) {
            'course' => Course::find($this->item_id),
            'ebook'  => Ebook::find($this->item_id),
            default  => null,
        };
    }
}
