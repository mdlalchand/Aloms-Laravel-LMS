<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    protected $fillable = ['exam_attempt_id','question_id','option_id','answer_text','is_correct','marks_awarded'];
    protected $casts = ['is_correct' => 'boolean'];

    public function attempt()  { return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id'); }
    public function question() { return $this->belongsTo(Question::class); }
    public function option()   { return $this->belongsTo(Option::class); }
}
