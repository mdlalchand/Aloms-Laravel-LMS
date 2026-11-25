<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = ['exam_id','user_id','started_at','submitted_at','score','correct_count','wrong_count','status'];
    protected $casts = ['started_at' => 'datetime', 'submitted_at' => 'datetime'];

    public function exam()    { return $this->belongsTo(Exam::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function answers() { return $this->hasMany(AttemptAnswer::class); }

    public function isPassed(): bool
    {
        return $this->score >= $this->exam->pass_marks;
    }

    public function remainingSeconds(): int
    {
        $end = $this->started_at->copy()->addMinutes($this->exam->duration_minutes);
        return max(0, now()->diffInSeconds($end, false));
    }
}
