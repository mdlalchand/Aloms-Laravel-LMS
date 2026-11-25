<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::with('manager')
            ->withCount(['users', 'students'])
            ->when($request->get('q'), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%")))
            ->orderBy('name')
            ->paginate(12)->withQueryString();

        return view('admin.branches.index', [
            'branches' => $branches,
            'totalRevenue' => (float) \App\Models\Order::where('status', 'paid')->sum('total'),
        ]);
    }

    public function create()
    {
        return view('admin.branches.form', [
            'branch' => new Branch(['is_active' => true]),
            'managers' => $this->managers(),
        ]);
    }

    public function store(Request $request)
    {
        Branch::create($this->validated($request));

        return redirect()->route('admin.branches.index')->with('success', 'Branch created.');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.form', [
            'branch' => $branch,
            'managers' => $this->managers(),
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $branch->update($this->validated($request, $branch));

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        // Detach users instead of deleting them.
        User::where('branch_id', $branch->id)->update(['branch_id' => null]);
        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Branch removed. Its users were unassigned.');
    }

    public function toggle(Branch $branch)
    {
        $branch->update(['is_active' => ! $branch->is_active]);

        return back()->with('success', $branch->name.' is now '.($branch->is_active ? 'active' : 'inactive').'.');
    }

    private function managers()
    {
        return User::whereIn('role', ['branch_manager', 'admin'])->orderBy('name')->get();
    }

    private function validated(Request $request, ?Branch $branch = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'code' => 'required|string|max:20|alpha_dash|unique:branches,code'.($branch ? ','.$branch->id : ''),
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:120',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
