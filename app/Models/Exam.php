<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'course_id','lesson_id','title','description','duration_minutes','total_marks','pass_marks',
        'negative_marking','starts_at','ends_at','attempt_limit','shuffle_questions','is_free','is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'shuffle_questions' => 'boolean',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'total_marks' => 'float',
        'pass_marks' => 'float',
    ];

    public function course()    { return $this->belongsTo(Course::class); }
    public function lesson()    { return $this->belongsTo(Lesson::class); }
    public function questions() { return $this->hasMany(Question::class)->orderBy('position'); }
    public function attempts()  { return $this->hasMany(ExamAttempt::class); }

    public function isOpen(): bool
    {
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        return true;
    }

    public function attemptsLeftFor(User $user): ?int
    {
        if (! $this->attempt_limit) {
            return null;
        }
        return max(0, $this->attempt_limit - $this->attempts()->where('user_id', $user->id)->count());
    }

    public function bestScoreFor(User $user): float
    {
        return (float) $this->attempts()->where('user_id', $user->id)->max('score');
    }
}
