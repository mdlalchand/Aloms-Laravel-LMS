<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code','type','value','min_amount','usage_limit','used_count','starts_at','expires_at','is_active'];
    protected $casts = ['starts_at' => 'datetime', 'expires_at' => 'datetime', 'is_active' => 'boolean'];

    public function isValidFor(float $amount): bool
    {
        if (! $this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return $amount >= (float) $this->min_amount;
    }

    public function discountFor(float $amount): float
    {
        $d = $this->type === 'percent' ? $amount * ((float) $this->value / 100) : (float) $this->value;
        return round(min($d, $amount), 2);
    }
}
