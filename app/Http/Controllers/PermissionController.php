<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $search = trim((string) $request->query('q', ''));

        $usersQuery = User::query()
            ->withCount('projects')
            ->latest('id');

        if ($search !== '') {
            $usersQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->get();

        return view('permissions.index', [
            'users' => $users,
            'filters' => [
                'q' => $search,
            ],
            'summary' => [
                'users' => User::query()->count(),
                'admin' => User::query()->where('role', 'admin')->count(),
                'it_dev' => User::query()->where('role', 'it_dev')->count(),
                'it_test' => User::query()->where('role', 'it_test')->count(),
                'active' => User::query()->where('status', 'active')->count(),
                'pending' => User::query()->where('status', 'pending')->count(),
                'suspended' => User::query()->where('status', 'suspended')->count(),
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $validated = $request->validate([
            'role' => ['required', Rule::in(array_keys(User::roleLabels()))],
            'status' => ['required', Rule::in(array_keys(User::statusLabels()))],
        ]);

        $user->update($validated);

        return redirect()
            ->route('permissions.index')
            ->with('success', "อัปเดตสิทธิ์ของ {$user->name} เรียบร้อยแล้ว");
    }
}
