<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $fillable = [
        'course_id', 'title', 'description', 'meeting_url', 'meeting_password',
        'recording_url', 'platform', 'starts_at', 'duration_minutes', 'is_published',
    ];

    protected $casts = ['starts_at' => 'datetime', 'is_published' => 'boolean'];

    public const PLATFORMS = ['Zoom', 'Google Meet', 'Jitsi', 'YouTube Live', 'Facebook Live', 'Offline / Classroom'];

    public function course() { return $this->belongsTo(Course::class); }

    public function scopePublished($q) { return $q->where('is_published', true); }

    public function endsAt() { return $this->starts_at?->copy()->addMinutes((int) $this->duration_minutes); }

    public function isUpcoming(): bool { return $this->starts_at && $this->starts_at->isFuture(); }

    public function isLive(): bool
    {
        return $this->starts_at
            && now()->between($this->starts_at, $this->endsAt() ?? $this->starts_at);
    }

    /** Join button only unlocks 10 minutes before the class starts. */
    public function canJoin(): bool
    {
        return $this->starts_at && now()->gte($this->starts_at->copy()->subMinutes(10))
            && now()->lte(($this->endsAt() ?? $this->starts_at)->copy()->addMinutes(30));
    }

    public function statusLabel(): string
    {
        return match (true) {
            $this->isLive() => 'Live now',
            $this->isUpcoming() => 'Upcoming',
            $this->recording_url !== null => 'Recording ready',
            default => 'Finished',
        };
    }

    public function statusBadge(): string
    {
        return match ($this->statusLabel()) {
            'Live now' => 'bg-rose-100 text-rose-700',
            'Upcoming' => 'bg-emerald-100 text-emerald-700',
            'Recording ready' => 'bg-indigo-100 text-indigo-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }
}
