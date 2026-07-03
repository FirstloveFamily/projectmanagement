<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
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

        $editingUser = null;
        if ($request->filled('edit')) {
            $editingUser = User::query()->withCount('projects')->find($request->integer('edit'));
        }

        $summary = [
            'users' => User::query()->count(),
            'with_projects' => User::query()->has('projects')->count(),
            'without_projects' => User::query()->doesntHave('projects')->count(),
            'admin' => User::query()->where('role', 'admin')->count(),
            'it_dev' => User::query()->where('role', 'it_dev')->count(),
            'it_test' => User::query()->where('role', 'it_test')->count(),
            'active' => User::query()->where('status', 'active')->count(),
            'pending' => User::query()->where('status', 'pending')->count(),
            'suspended' => User::query()->where('status', 'suspended')->count(),
        ];

        return view('users.index', [
            'users' => $users,
            'editingUser' => $editingUser,
            'summary' => $summary,
            'filters' => [
                'q' => $search,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $validated = $this->validateUser($request);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        $user = User::create($validated);

        return redirect()
            ->route('users.index', ['edit' => $user->id])
            ->with('success', 'เพิ่มผู้ใช้เรียบร้อยแล้ว');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $validated = $this->validateUser($request, $user->id);

        if (filled($validated['password'] ?? null)) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make((string) $request->string('password'));
        }

        if (! $request->filled('password')) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index', ['edit' => $user->id])
            ->with('success', 'แก้ไขผู้ใช้เรียบร้อยแล้ว');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(Gate::allows('manage-users'), 403);

        if ($user->projects()->exists()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ผู้ใช้นี้ยังมีโปรเจกต์ผูกอยู่ กรุณาย้ายเจ้าของโปรเจกต์ก่อนลบ');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateUser(Request $request, ?int $ignoreUserId = null): array
    {
        $emailRule = Rule::unique('users', 'email');
        if ($ignoreUserId) {
            $emailRule = $emailRule->ignore($ignoreUserId);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $emailRule,
            ],
            'role' => ['required', Rule::in(['admin', 'it_dev', 'it_test'])],
            'status' => ['required', Rule::in(['active', 'pending', 'suspended'])],
            'password' => [$ignoreUserId ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
        ]);
    }
}
