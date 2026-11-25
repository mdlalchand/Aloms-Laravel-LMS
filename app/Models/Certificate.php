<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = ['user_id','course_id','code','issued_at'];
    protected $casts = ['issued_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $c) {
            $c->code ??= 'CERT-'.Str::upper(Str::random(10));
            $c->issued_at ??= now();
        });
    }

    public function user()   { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }

    public function getRouteKeyName() { return 'code'; }
}
