<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Branch;
use App\Http\Controllers\Guardian;
use App\Http\Controllers\Teacher;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CertificateVerifyController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

/* ------------------------------------------------------------------
 |  PUBLIC
 |------------------------------------------------------------------*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/category/{category:slug}', [CourseController::class, 'category'])->name('categories.show');

Route::get('/ebooks', [EbookController::class, 'index'])->name('ebooks.index');
Route::get('/ebooks/{ebook:slug}', [EbookController::class, 'show'])->name('ebooks.show');
Route::get('/verify/{code}', [CertificateVerifyController::class, 'show'])->name('certificates.verify');

/* ------------------------------------------------------------------
 |  GUEST AUTH
 |------------------------------------------------------------------*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/* ------------------------------------------------------------------
 |  AUTHENTICATED (cart / checkout / reviews)
 |------------------------------------------------------------------*/
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{type}/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order:order_no}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/success/{order:order_no}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');

    Route::post('/courses/{course:slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/enroll/free/{course:slug}', [CourseController::class, 'enrollFree'])->name('courses.enroll-free');
});

/* ------------------------------------------------------------------
 |  STUDENT
 |------------------------------------------------------------------*/
Route::middleware(['auth', 'role:student,instructor,admin'])->prefix('my')->name('student.')->group(function () {
    Route::get('/', [Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-courses', [Student\DashboardController::class, 'courses'])->name('courses');
    Route::get('/orders', [Student\DashboardController::class, 'orders'])->name('orders');
    Route::get('/ebooks', [Student\DashboardController::class, 'ebooks'])->name('ebooks');
    Route::get('/certificates', [Student\DashboardController::class, 'certificates'])->name('certificates');
    Route::get('/profile', [Student\DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [Student\DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [Student\DashboardController::class, 'updatePassword'])->name('password.update');

    // Course player
    Route::get('/learn/{course:slug}', [Student\LearnController::class, 'show'])->name('learn');
    Route::get('/learn/{course:slug}/{lesson}', [Student\LearnController::class, 'lesson'])->name('learn.lesson');
    Route::post('/learn/{course:slug}/{lesson}/complete', [Student\LearnController::class, 'complete'])->name('learn.complete');
    Route::post('/learn/{course:slug}/{lesson}/heartbeat', [Student\LearnController::class, 'heartbeat'])->name('learn.heartbeat');

    // Exams
    Route::get('/exams', [Student\ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}', [Student\ExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{exam}/start', [Student\ExamController::class, 'start'])->name('exams.start');
    Route::get('/attempts/{attempt}', [Student\ExamController::class, 'attempt'])->name('attempts.show');
    Route::post('/attempts/{attempt}/save', [Student\ExamController::class, 'save'])->name('attempts.save');
    Route::post('/attempts/{attempt}/submit', [Student\ExamController::class, 'submit'])->name('attempts.submit');
    Route::get('/attempts/{attempt}/result', [Student\ExamController::class, 'result'])->name('attempts.result');

    // Certificate
    Route::get('/certificates/{course:slug}', [Student\CertificateController::class, 'show'])->name('certificates.show');

    // Ebook download
    Route::get('/ebooks/{ebook:slug}/read', [EbookController::class, 'read'])->name('ebooks.read');
});

/* ------------------------------------------------------------------
 |  ADMIN / INSTRUCTOR PANEL
 |------------------------------------------------------------------*/
Route::middleware(['auth', 'role:admin,instructor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::resource('courses', Admin\CourseController::class);
    Route::resource('exams', Admin\ExamController::class)->except('show');

    // Curriculum builder (explicit routes keep URLs predictable)
    Route::get('courses/{course}/sections/create', [Admin\SectionController::class, 'create'])->name('courses.sections.create');
    Route::post('courses/{course}/sections', [Admin\SectionController::class, 'store'])->name('courses.sections.store');
    Route::get('sections/{section}/edit', [Admin\SectionController::class, 'edit'])->name('sections.edit');
    Route::put('sections/{section}', [Admin\SectionController::class, 'update'])->name('sections.update');
    Route::delete('sections/{section}', [Admin\SectionController::class, 'destroy'])->name('sections.destroy');

    Route::get('lessons/create', [Admin\LessonController::class, 'create'])->name('lessons.create');
    Route::post('lessons', [Admin\LessonController::class, 'store'])->name('lessons.store');
    Route::get('lessons/{lesson}/edit', [Admin\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [Admin\LessonController::class, 'destroy'])->name('lessons.destroy');

    // Exam questions
    Route::get('exams/{exam}/questions', [Admin\QuestionController::class, 'index'])->name('exams.questions.index');
    Route::get('exams/{exam}/questions/create', [Admin\QuestionController::class, 'create'])->name('exams.questions.create');
    Route::post('exams/{exam}/questions', [Admin\QuestionController::class, 'store'])->name('exams.questions.store');
    Route::get('questions/{question}/edit', [Admin\QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('questions/{question}', [Admin\QuestionController::class, 'update'])->name('questions.update');
    Route::delete('questions/{question}', [Admin\QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::resource('ebooks', Admin\EbookController::class)->except('show');
    Route::resource('coupons', Admin\CouponController::class)->except('show');
    Route::resource('announcements', Admin\AnnouncementController::class)->except('show');

    Route::get('enrollments', [Admin\EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::delete('enrollments/{enrollment}', [Admin\EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::post('enrollments/manual', [Admin\EnrollmentController::class, 'store'])->name('enrollments.store');

    Route::get('students/create', [Admin\UserController::class, 'create'])->name('users.create');
    Route::post('students', [Admin\UserController::class, 'store'])->name('users.store');
    Route::get('students', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('students/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::get('students/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('students/{user}', [Admin\UserController::class, 'update'])->name('users.update');

    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order:order_no}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order:order_no}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('payments/{payment}/verify', [Admin\OrderController::class, 'verifyPayment'])->name('payments.verify');

    Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    // Live classes (admins and instructors)
    Route::resource('live-classes', Admin\LiveClassController::class)->except('show')
        ->parameters(['live-classes' => 'liveClass']);

    /* ------- admin-only modules: branches, sliders, accounts ------- */
    Route::middleware('role:admin')->group(function () {
        Route::resource('branches', Admin\BranchController::class)->except('show')
            ->parameters(['branches' => 'branch']);
        Route::resource('sliders', Admin\SliderController::class)->except('show');
        Route::post('sliders/{slider}/toggle', [Admin\SliderController::class, 'toggle'])->name('sliders.toggle');
        Route::post('branches/{branch}/toggle', [Admin\BranchController::class, 'toggle'])->name('branches.toggle');
    });
});

/* ------------------------------------------------------------------
 |  TEACHER PORTAL
 |------------------------------------------------------------------*/
Route::middleware(['auth', 'role:instructor,admin'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/', [Teacher\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [Teacher\DashboardController::class, 'courses'])->name('courses');
    Route::get('/students', [Teacher\DashboardController::class, 'students'])->name('students');
    Route::get('/students/{user}', [Teacher\DashboardController::class, 'student'])->name('students.show');
    Route::get('/earnings', [Teacher\DashboardController::class, 'earnings'])->name('earnings');
    Route::get('/live-classes', [Teacher\DashboardController::class, 'liveClasses'])->name('live-classes');
});

/* ------------------------------------------------------------------
 |  GUARDIAN PORTAL
 |------------------------------------------------------------------*/
Route::middleware(['auth', 'role:guardian,admin'])->prefix('guardian')->name('guardian.')->group(function () {
    Route::get('/', [Guardian\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/children/{child}', [Guardian\DashboardController::class, 'child'])->name('child');
});

/* ------------------------------------------------------------------
 |  BRANCH MANAGER PORTAL
 |------------------------------------------------------------------*/
Route::middleware(['auth', 'role:branch_manager,admin'])->prefix('branch')->name('branch.')->group(function () {
    Route::get('/', [Branch\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/students', [Branch\DashboardController::class, 'students'])->name('students');
    Route::get('/students/{student}', [Branch\DashboardController::class, 'student'])->name('students.show');
    Route::get('/staff', [Branch\DashboardController::class, 'staff'])->name('staff');
    Route::get('/revenue', [Branch\DashboardController::class, 'revenue'])->name('revenue');
});
