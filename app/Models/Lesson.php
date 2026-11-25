<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id','section_id','title','type','video_url','attachment','content',
        'duration_minutes','position','is_free_preview','is_published',
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function course()   { return $this->belongsTo(Course::class); }
    public function section()  { return $this->belongsTo(Section::class); }
    public function progress() { return $this->hasMany(LessonProgress::class); }
    public function exams()    { return $this->hasMany(Exam::class); }

    /** Extract the youtube/vimeo embed url when possible. */
    public function embedUrl(): ?string
    {
        $url = $this->video_url;
        if (! $url) {
            return null;
        }
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{6,})/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }
        return $url;
    }

    public function isCompletedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }
        return $this->progress()->where('user_id', $user->id)->whereNotNull('completed_at')->exists();
    }
}
