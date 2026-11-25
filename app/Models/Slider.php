<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'badge', 'title', 'subtitle', 'image', 'button_text', 'button_url',
        'theme', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public const THEMES = [
        'emerald' => 'Emerald (default)',
        'indigo'  => 'Indigo',
        'rose'    => 'Rose',
        'amber'   => 'Amber',
        'slate'   => 'Slate',
    ];

    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('sort_order'); }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
    }

    /** Tailwind gradient + accent classes per theme (safely whitelisted). */
    public function palette(): array
    {
        return match ($this->theme) {
            'indigo' => ['from-indigo-950 via-slate-900 to-slate-900', 'bg-indigo-400/10 border-indigo-400/30 text-indigo-200', 'text-indigo-400', 'bg-indigo-500 text-slate-950 hover:bg-indigo-400'],
            'rose'   => ['from-rose-950 via-slate-900 to-slate-900', 'bg-rose-400/10 border-rose-400/30 text-rose-200', 'text-rose-400', 'bg-rose-500 text-white hover:bg-rose-400'],
            'amber'  => ['from-amber-950 via-slate-900 to-slate-900', 'bg-amber-400/10 border-amber-400/30 text-amber-200', 'text-amber-400', 'bg-amber-400 text-slate-950 hover:bg-amber-300'],
            'slate'  => ['from-slate-800 via-slate-900 to-slate-950', 'bg-white/10 border-white/20 text-slate-200', 'text-slate-300', 'bg-white text-slate-900 hover:bg-slate-100'],
            default  => ['from-slate-900 via-slate-900 to-emerald-950', 'bg-emerald-400/10 border-emerald-400/30 text-emerald-200', 'text-emerald-400', 'bg-emerald-500 text-slate-950 hover:bg-emerald-400'],
        };
    }
}
