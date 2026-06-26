<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $trainingsQuery = Training::query();

        if ($search !== '') {
            $trainingsQuery->where(function ($query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('audience', 'like', "%{$search}%")
                    ->orWhere('trainer', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('google_sheet_url', 'like', "%{$search}%")
                    ->orWhere('participants', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if (filled($status)) {
            $trainingsQuery->where('status', $status);
        }

        $trainings = (clone $trainingsQuery)
            ->orderByDesc('training_date')
            ->orderByDesc('id')
            ->get();

        $editingTraining = null;
        if ($request->filled('edit')) {
            $editingTraining = Training::query()->find($request->integer('edit'));
        }

        $summary = [
            'total' => Training::query()->count(),
            'upcoming' => Training::query()->where('status', 'scheduled')->count(),
            'running' => Training::query()->where('status', 'in_progress')->count(),
            'completed' => Training::query()->where('status', 'completed')->count(),
            'cancelled' => Training::query()->where('status', 'cancelled')->count(),
            'attendance' => $this->attendanceRate(),
        ];

        return view('trainings.index', [
            'trainings' => $trainings,
            'editingTraining' => $editingTraining,
            'summary' => $summary,
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Gate::allows('manage-trainings'), 403);

        $validated = $this->validateTraining($request);

        $training = Training::create($validated);

        return redirect()
            ->route('trainings.index', ['edit' => $training->id])
            ->with('success', 'เพิ่มข้อมูลอบรมเรียบร้อยแล้ว');
    }

    public function update(Request $request, Training $training): RedirectResponse
    {
        abort_unless(Gate::allows('manage-trainings'), 403);

        $validated = $this->validateTraining($request);

        $training->update($validated);

        return redirect()
            ->route('trainings.index', ['edit' => $training->id])
            ->with('success', 'แก้ไขข้อมูลอบรมเรียบร้อยแล้ว');
    }

    public function destroy(Training $training): RedirectResponse
    {
        abort_unless(Gate::allows('manage-trainings'), 403);

        $training->delete();

        return redirect()
            ->route('trainings.index')
            ->with('success', 'ลบข้อมูลอบรมเรียบร้อยแล้ว');
    }

    private function validateTraining(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'training_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'audience' => ['nullable', 'string', 'max:255'],
            'trainer' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'google_sheet_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['required', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
            'capacity' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'attended' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'participants' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['capacity'] = (int) ($validated['capacity'] ?? 0);
        $validated['attended'] = (int) ($validated['attended'] ?? 0);

        return $validated;
    }

    private function attendanceRate(): int
    {
        $completed = Training::query()->where('status', 'completed');
        $capacity = (int) $completed->sum('capacity');

        if ($capacity <= 0) {
            return 0;
        }

        return (int) round(($completed->sum('attended') / $capacity) * 100);
    }
}
