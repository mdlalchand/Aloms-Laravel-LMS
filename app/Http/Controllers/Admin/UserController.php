<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['branch'])->withCount(['enrollments', 'courses', 'orders']);

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }
        if ($branch = $request->get('branch')) {
            $query->where('branch_id', $branch);
        }
        if ($q = $request->get('q')) {
            $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"));
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(20)->withQueryString(),
            'branches' => Branch::orderBy('name')->get(),
            'counts' => collect(User::ROLES)->mapWithKeys(fn ($label, $key) => [$key => User::where('role', $key)->count()])
                ->prepend(User::count(), 'all')->all(),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(['role' => 'student', 'is_active' => true]),
            'roles' => User::ROLES,
            'branches' => Branch::orderBy('name')->get(),
            'students' => User::where('role', 'student')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:'.implode(',', array_keys(User::ROLES)),
            'branch_id' => 'nullable|exists:branches,id',
            'designation' => 'nullable|string|max:120',
            'password' => ['required', 'string', Password::min(8)],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $user = User::create($data);

        if ($user->isGuardian()) {
            $user->children()->sync($request->input('children', []));
        }

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user->load([
                'branch', 'enrollments.course', 'orders.items', 'examAttempts.exam',
                'certificates.course', 'children', 'guardians', 'courses',
            ]),
        ]);
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => User::ROLES,
            'branches' => Branch::orderBy('name')->get(),
            'students' => User::where('role', 'student')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:'.implode(',', array_keys(User::ROLES)),
            'branch_id' => 'nullable|exists:branches,id',
            'designation' => 'nullable|string|max:120',
            'bio' => 'nullable|string|max:1000',
            'password' => ['nullable', 'string', Password::min(8)],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $user->update($data);

        if ($user->isGuardian()) {
            $user->children()->sync($request->input('children', []));
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }
}
