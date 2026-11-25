<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Every role the platform understands. */
    public const ROLES = [
        'admin'          => 'Admin',
        'instructor'     => 'Teacher / Instructor',
        'student'        => 'Student',
        'guardian'       => 'Guardian / Parent',
        'branch_manager' => 'Branch Manager',
    ];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'branch_id', 'phone', 'avatar', 'bio',
        'designation', 'is_active', 'provider',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /* ---------------- Role helpers ---------------- */

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isInstructor(): bool { return $this->role === 'instructor'; }
    public function isStudent(): bool { return $this->role === 'student'; }
    public function isGuardian(): bool { return $this->role === 'guardian'; }
    public function isBranchManager(): bool { return $this->role === 'branch_manager'; }

    /** Roles that may enter the back office. */
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'instructor', 'branch_manager'], true);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin'          => route('admin.dashboard'),
            'instructor'     => route('teacher.dashboard'),
            'branch_manager' => route('branch.dashboard'),
            'guardian'       => route('guardian.dashboard'),
            default          => route('student.dashboard'),
        };
    }

    /* ---------------- Relations ---------------- */

    public function courses()          { return $this->hasMany(Course::class, 'instructor_id'); }
    public function enrollments()      { return $this->hasMany(Enrollment::class); }
    public function orders()           { return $this->hasMany(Order::class); }
    public function reviews()          { return $this->hasMany(Review::class); }
    public function certificates()     { return $this->hasMany(Certificate::class); }
    public function examAttempts()     { return $this->hasMany(ExamAttempt::class); }
    public function lessonProgress()   { return $this->hasMany(LessonProgress::class); }

    public function branch()           { return $this->belongsTo(Branch::class); }
    public function managedBranches()  { return $this->hasMany(Branch::class, 'manager_id'); }

    /** Students linked to this guardian. */
    public function children()
    {
        return $this->belongsToMany(User::class, 'guardian_students', 'guardian_id', 'student_id')
            ->withPivot('relation')->withTimestamps();
    }

    /** Guardians watching this student. */
    public function guardians()
    {
        return $this->belongsToMany(User::class, 'guardian_students', 'student_id', 'guardian_id')
            ->withPivot('relation')->withTimestamps();
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['status', 'price_paid', 'enrolled_at', 'expires_at', 'completed_at'])
            ->withTimestamps();
    }

    public function hasCourse(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();
    }

    /** Overall completion % across every enrolled course. */
    public function overallProgress(): float
    {
        $courses = $this->enrolledCourses;

        if ($courses->isEmpty()) {
            return 0.0;
        }

        return round($courses->avg(fn (Course $c) => $c->progressFor($this)), 1);
    }

    public function avatarUrl(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : 'https://ui-avatars.com/api/?background=0f766e&color=fff&name='.urlencode($this->name);
    }

    public function initials(): string
    {
        return Str::of($this->name)->explode(' ')->take(2)
            ->map(fn ($p) => Str::upper(Str::substr($p, 0, 1)))->implode('');
    }
}
