<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['parent_id','name','slug','icon','image','description','position','is_active'];

    protected static function booted(): void
    {
        static::saving(function (self $c) {
            if (! $c->slug) {
                $c->slug = Str::slug($c->name).'-'.Str::lower(Str::random(4));
            }
        });
    }

    public function getRouteKeyName() { return 'slug'; }

    public function courses()  { return $this->hasMany(Course::class); }
    public function parent()   { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_id'); }

    public function publishedCourses() { return $this->courses()->where('is_published', true); }
}
