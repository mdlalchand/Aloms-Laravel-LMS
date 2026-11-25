<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'code', 'address', 'phone', 'email', 'manager_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function manager()  { return $this->belongsTo(User::class, 'manager_id'); }
    public function students() { return $this->hasMany(User::class)->where('role', 'student'); }
    public function users()    { return $this->hasMany(User::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function revenue(): float
    {
        return (float) Order::where('status', 'paid')
            ->whereHas('user', fn ($q) => $q->where('branch_id', $this->id))
            ->sum('total');
    }

    public function studentCount(): int
    {
        return $this->users()->where('role', 'student')->count();
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
