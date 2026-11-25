<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\LiveClass;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesAndOrganisationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function forgetGuards(): void
    {
        $this->app['auth']->forgetGuards();
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    /** Each role lands on its own dashboard. */
    public function test_each_role_has_its_own_dashboard(): void
    {
        foreach ([
            'admin@lms.test' => '/admin',
            'teacher0@lms.test' => '/teacher',
            'student@lms.test' => '/my',
            'guardian1@lms.test' => '/guardian',
            'manager1@lms.test' => '/branch',
        ] as $email => $prefix) {
            $response = $this->actingAs($this->user($email))->get($user = $this->user($email)->dashboardRoute());
            $response->assertOk();
            $this->assertStringContainsString($prefix, $user);
            $this->forgetGuards();
        }
    }

    /** Teachers only see their own courses, students and earnings. */
    public function test_teacher_portal(): void
    {
        $teacher = $this->user('teacher0@lms.test');

        $course = $teacher->courses()->firstOrFail();

        $this->actingAs($teacher)->get(route('teacher.dashboard'))->assertOk()->assertSee('Courses');
        $this->actingAs($teacher)->get(route('teacher.courses'))
            ->assertOk()
            ->assertSee($course->title);
        $this->actingAs($teacher)->get(route('teacher.students'))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.earnings'))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.live-classes'))->assertOk();

        // Seeded enrollments are randomised, so guarantee one student on this teacher's course.
        $learner = User::where('role', 'student')->orderBy('id')->firstOrFail();
        \App\Models\Enrollment::firstOrCreate(
            ['user_id' => $learner->id, 'course_id' => $course->id],
            ['price_paid' => $course->final_price, 'status' => 'active', 'enrolled_at' => now()->subDays(3)],
        );

        // only students enrolled in this teacher's own courses are visible
        $ownStudent = \App\Models\Enrollment::whereIn('course_id', $teacher->courses()->pluck('id'))
            ->whereHas('user', fn ($q) => $q->where('role', 'student'))
            ->firstOrFail()->user;
        $this->actingAs($teacher)->get(route('teacher.students.show', $ownStudent))->assertOk();

        $outsider = User::where('role', 'student')
            ->whereNotIn('id', \App\Models\Enrollment::whereIn('course_id', $teacher->courses()->pluck('id'))->pluck('user_id'))
            ->first();
        if ($outsider) {
            $this->actingAs($teacher)->get(route('teacher.students.show', $outsider))->assertForbidden();
        }

        // A student cannot enter the teacher portal
        $this->forgetGuards();
        $this->actingAs($ownStudent)->get(route('teacher.dashboard'))->assertForbidden();
    }

    /** Guardians can see their linked children and results only. */
    public function test_guardian_portal(): void
    {
        $guardian = $this->user('guardian1@lms.test');
        $child = $guardian->children()->firstOrFail();

        $this->actingAs($guardian)->get(route('guardian.dashboard'))->assertOk()->assertSee($child->name);
        $this->actingAs($guardian)->get(route('guardian.child', $child))->assertOk()->assertSee($child->name);

        $childIds = $guardian->children()->pluck('users.id')->all();
        $other = User::where('role', 'student')
            ->whereNotIn('id', $childIds)
            ->firstOrFail();
        $this->actingAs($guardian)->get(route('guardian.child', $other))->assertForbidden();
    }

    /** Branch manager sees only their own branch data. */
    public function test_branch_portal_is_scoped_to_branch(): void
    {
        $manager = $this->user('manager1@lms.test');
        $branch = $manager->branch;

        $this->assertNotNull($branch);

        $this->actingAs($manager)->get(route('branch.dashboard'))->assertOk()->assertSee($branch->name);
        $this->actingAs($manager)->get(route('branch.students'))->assertOk();
        $this->actingAs($manager)->get(route('branch.staff'))->assertOk();
        $this->actingAs($manager)->get(route('branch.revenue'))->assertOk();

        // manager of branch 1 must not open a student of branch 2
        $outsider = User::where('role', 'student')->where('branch_id', '!=', $branch->id)->first();
        if ($outsider) {
            $this->actingAs($manager)->get(route('branch.students.show', $outsider))->assertForbidden();
        }
    }

    /** Admin can CRUD branches, sliders and live classes. */
    public function test_admin_organisation_modules(): void
    {
        $admin = $this->user('admin@lms.test');

        // Branches
        $this->actingAs($admin)->get(route('admin.branches.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.branches.store'), [
            'name' => 'Sylhet Centre', 'code' => 'SYL-01', 'address' => 'Zindabazar, Sylhet',
            'phone' => '01711111111', 'email' => 'sylhet@redwansmethod.test',
        ])->assertRedirect(route('admin.branches.index'));
        $this->assertDatabaseHas('branches', ['code' => 'SYL-01']);
        $branch = Branch::where('code', 'SYL-01')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.branches.update', $branch), [
            'name' => 'Sylhet Centre (Main)', 'code' => 'SYL-01',
        ])->assertRedirect(route('admin.branches.index'));
        $this->assertDatabaseHas('branches', ['code' => 'SYL-01', 'name' => 'Sylhet Centre (Main)']);
        $this->actingAs($admin)->post(route('admin.branches.toggle', $branch))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.branches.destroy', $branch))->assertRedirect();
        $this->assertDatabaseMissing('branches', ['code' => 'SYL-01']);

        // Sliders
        $this->actingAs($admin)->get(route('admin.sliders.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.sliders.store'), [
            'title' => 'New batch announcement', 'badge' => 'HSC 2026',
            'subtitle' => 'Admission going on', 'button_text' => 'Join now',
            'button_url' => '/courses', 'theme' => 'emerald', 'sort_order' => 9, 'is_active' => 1,
        ])->assertRedirect(route('admin.sliders.index'));
        $slider = Slider::where('title', 'New batch announcement')->firstOrFail();
        $this->actingAs($admin)->post(route('admin.sliders.toggle', $slider))->assertRedirect();
        $this->assertFalse($slider->fresh()->is_active);
        $this->actingAs($admin)->delete(route('admin.sliders.destroy', $slider))->assertRedirect();
        $this->assertDatabaseMissing('sliders', ['title' => 'New batch announcement']);

        // Live classes
        $course = \App\Models\Course::firstOrFail();
        $this->actingAs($admin)->get(route('admin.live-classes.index'))->assertOk();
        $this->actingAs($admin)->post(route('admin.live-classes.store'), [
            'course_id' => $course->id, 'title' => 'Final revision class',
            'platform' => 'jitsi', 'meeting_url' => 'https://meet.jit.si/redwans-final',
            'starts_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'duration_minutes' => 90, 'is_published' => 1,
        ])->assertRedirect(route('admin.live-classes.index'));
        $live = LiveClass::where('title', 'Final revision class')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.live-classes.update', $live), [
            'course_id' => $course->id, 'title' => 'Final revision class (moved)',
            'platform' => 'jitsi', 'meeting_url' => 'https://meet.jit.si/redwans-final',
            'starts_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'duration_minutes' => 90, 'is_published' => 1,
        ])->assertRedirect(route('admin.live-classes.index'));
        $this->assertDatabaseHas('live_classes', ['title' => 'Final revision class (moved)']);
    }

    /** Home page hero comes from the slider table. */
    public function test_home_page_renders_active_sliders(): void
    {
        $active = Slider::where('is_active', true)->firstOrFail();
        $hidden = Slider::create([
            'title' => 'Hidden marketing slide', 'theme' => 'light', 'sort_order' => 99, 'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee($active->title)
            ->assertDontSee($hidden->title);
    }

    /** Admin-only modules are closed to managers/teachers. */
    public function test_organisation_modules_are_admin_only(): void
    {
        foreach (['manager1@lms.test', 'teacher0@lms.test', 'guardian1@lms.test'] as $email) {
            $this->actingAs($this->user($email))->get(route('admin.branches.index'))->assertForbidden();
            $this->forgetGuards();
        }
    }
}
