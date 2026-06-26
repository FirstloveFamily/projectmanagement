<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\SystemRequestAttachment;
use App\Models\SystemRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SystemRequestController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $requestsQuery = SystemRequest::query()
            ->with(['company', 'requester', 'projectOwner', 'approvedBy', 'rejectedBy', 'project', 'attachments'])
            ->latest('id');

        if ($search !== '') {
            $requestsQuery->where(function ($query) use ($search): void {
                $query->where('requester_name', 'like', "%{$search}%")
                    ->orWhere('requester_email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('objective', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%")
                    ->orWhere('impact', 'like', "%{$search}%")
                    ->orWhere('desired_output', 'like', "%{$search}%");
            });
        }

        if (filled($status)) {
            $requestsQuery->where('status', $status);
        }

        $requests = $requestsQuery->get();

        $editingRequest = null;
        if ($request->filled('edit')) {
            $editingRequest = SystemRequest::query()
                ->with(['attachments'])
                ->find($request->integer('edit'));
        }

        $summary = [
            'total' => SystemRequest::query()->count(),
            'pending' => SystemRequest::query()->where('status', 'pending')->count(),
            'approved' => SystemRequest::query()->where('status', 'approved')->count(),
            'rejected' => SystemRequest::query()->where('status', 'rejected')->count(),
            'projects' => Project::query()->whereNotNull('source_request_id')->count(),
        ];

        $companies = Company::query()->orderBy('name')->get();
        $users = User::query()->orderBy('name')->get();

        return view('requests.index', [
            'requests' => $requests,
            'editingRequest' => $editingRequest,
            'summary' => $summary,
            'companies' => $companies,
            'users' => $users,
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function queue(Request $request): View
    {
        abort_unless(Gate::allows('manage-requests'), 403);

        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $requestsQuery = SystemRequest::query()
            ->with(['company', 'requester', 'projectOwner', 'approvedBy', 'rejectedBy', 'project', 'attachments'])
            ->latest('id');

        if ($search !== '') {
            $requestsQuery->where(function ($query) use ($search): void {
                $query->where('requester_name', 'like', "%{$search}%")
                    ->orWhere('requester_email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('objective', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%")
                    ->orWhere('impact', 'like', "%{$search}%")
                    ->orWhere('desired_output', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all' && filled($status)) {
            $requestsQuery->where('status', $status);
        }

        $requests = $requestsQuery->get();

        $summary = [
            'total' => SystemRequest::query()->count(),
            'pending' => SystemRequest::query()->where('status', SystemRequest::STATUS_PENDING)->count(),
            'approved' => SystemRequest::query()->where('status', SystemRequest::STATUS_APPROVED)->count(),
            'rejected' => SystemRequest::query()->where('status', SystemRequest::STATUS_REJECTED)->count(),
            'with_project' => SystemRequest::query()->whereNotNull('project_id')->count(),
        ];

        $users = User::query()->orderBy('name')->get();

        return view('requests.queue', [
            'requests' => $requests,
            'summary' => $summary,
            'users' => $users,
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function report(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $requestsQuery = SystemRequest::query()
            ->with(['company', 'requester', 'projectOwner', 'approvedBy', 'rejectedBy', 'project', 'attachments'])
            ->latest('id');

        if ($search !== '') {
            $requestsQuery->where(function ($query) use ($search): void {
                $query->where('requester_name', 'like', "%{$search}%")
                    ->orWhere('requester_email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('objective', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%")
                    ->orWhere('impact', 'like', "%{$search}%")
                    ->orWhere('desired_output', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all' && filled($status)) {
            $requestsQuery->where('status', $status);
        }

        $requests = $requestsQuery->get();

        $summary = [
            'total' => SystemRequest::query()->count(),
            'pending' => SystemRequest::query()->where('status', SystemRequest::STATUS_PENDING)->count(),
            'approved' => SystemRequest::query()->where('status', SystemRequest::STATUS_APPROVED)->count(),
            'rejected' => SystemRequest::query()->where('status', SystemRequest::STATUS_REJECTED)->count(),
        ];

        return view('requests.report', [
            'requests' => $requests,
            'summary' => $summary,
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function show(SystemRequest $systemRequest): View
    {
        $systemRequest->load(['company', 'requester', 'projectOwner', 'approvedBy', 'rejectedBy', 'project', 'attachments']);

        return view('requests.show', [
            'requestItem' => $systemRequest,
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequestForm($request);

        $validated['requester_user_id'] = $request->user()?->id;
        $validated['status'] = SystemRequest::STATUS_PENDING;

        $attachments = $request->file('attachments', []);

        $systemRequest = DB::transaction(function () use ($validated, $attachments) {
            $systemRequest = SystemRequest::create($validated);
            $this->storeAttachments($systemRequest, $attachments);

            return $systemRequest;
        });

        return redirect()
            ->route('requests.index', ['edit' => $systemRequest->id])
            ->with('success', 'ส่งคำร้องเรียบร้อยแล้ว');
    }

    public function update(Request $request, SystemRequest $systemRequest): RedirectResponse
    {
        abort_unless(Gate::allows('manage-requests'), 403);

        $validated = $this->validateRequestForm($request);

        $attachments = $request->file('attachments', []);

        DB::transaction(function () use ($systemRequest, $validated, $attachments): void {
            $systemRequest->update($validated);
            $this->storeAttachments($systemRequest, $attachments);
        });

        return redirect()
            ->route('requests.index', ['edit' => $systemRequest->id])
            ->with('success', 'แก้ไขคำร้องเรียบร้อยแล้ว');
    }

    public function destroy(SystemRequest $systemRequest): RedirectResponse
    {
        abort_unless(Gate::allows('manage-requests'), 403);

        $systemRequest->load('attachments');

        foreach ($systemRequest->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->path);
        }

        $systemRequest->delete();

        return redirect()
            ->route('requests.index')
            ->with('success', 'ลบคำร้องเรียบร้อยแล้ว');
    }

    public function destroyAttachment(SystemRequest $systemRequest, SystemRequestAttachment $attachment): RedirectResponse
    {
        abort_unless(Gate::allows('manage-requests'), 403);

        abort_if($attachment->system_request_id !== $systemRequest->id, 404);

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return redirect()
            ->route('requests.show', $systemRequest)
            ->with('success', 'ลบไฟล์แนบเรียบร้อยแล้ว');
    }

    public function approve(Request $request, SystemRequest $systemRequest): RedirectResponse
    {
        abort_unless(Gate::allows('manage-requests'), 403);

        if ($systemRequest->project_id) {
            return redirect()->route('requests.show', $systemRequest);
        }

        $validated = $request->validate([
            'project_owner_user_id' => ['required', 'exists:users,id'],
            'project_start_date' => ['nullable', 'date'],
            'project_due_date' => ['nullable', 'date', 'after_or_equal:project_start_date'],
            'decision_note' => ['nullable', 'string'],
        ]);

        $approvedByUserId = $request->user()?->id;

        $project = DB::transaction(function () use ($systemRequest, $validated, $approvedByUserId) {
            $project = Project::create([
                'company_id' => $systemRequest->company_id,
                'user_id' => $validated['project_owner_user_id'],
                'name' => $systemRequest->title,
                'description' => $systemRequest->details,
                'objective' => $systemRequest->objective,
                'risk' => $systemRequest->impact,
                'notes' => trim((string) ($systemRequest->desired_output ? "ผลลัพธ์ที่ต้องการ: {$systemRequest->desired_output}\n" : '') . (string) $systemRequest->reference_url),
                'source_request_id' => $systemRequest->id,
                'status' => 'active',
                'start_date' => $validated['project_start_date'] ?? $systemRequest->target_start_date ?? Carbon::today(),
                'due_date' => $validated['project_due_date'] ?? $systemRequest->target_due_date ?? Carbon::today()->addDays(7),
            ]);

            $systemRequest->update([
                'status' => SystemRequest::STATUS_APPROVED,
                'approved_by_user_id' => $approvedByUserId,
                'approved_at' => now(),
                'decision_note' => $validated['decision_note'] ?? null,
                'project_owner_user_id' => $validated['project_owner_user_id'],
                'project_id' => $project->id,
            ]);

            return $project;
        });

        return redirect()
            ->route('requests.show', $systemRequest)
            ->with('success', 'อนุมัติและสร้าง Project เรียบร้อยแล้ว');
    }

    public function reject(Request $request, SystemRequest $systemRequest): RedirectResponse
    {
        abort_unless(Gate::allows('manage-requests'), 403);

        $validated = $request->validate([
            'decision_note' => ['nullable', 'string'],
        ]);

        $systemRequest->update([
            'status' => SystemRequest::STATUS_REJECTED,
            'rejected_by_user_id' => $request->user()?->id,
            'rejected_at' => now(),
            'decision_note' => $validated['decision_note'] ?? null,
        ]);

        return redirect()
            ->route('requests.index')
            ->with('success', 'ปฏิเสธคำร้องเรียบร้อยแล้ว');
    }

    private function validateRequestForm(Request $request): array
    {
        return $request->validate([
            'requester_name' => ['required', 'string', 'max:255'],
            'requester_email' => ['nullable', 'email', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'request_type' => ['required', Rule::in(['add', 'improve', 'bug', 'other'])],
            'title' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'impact' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'desired_output' => ['nullable', 'string'],
            'target_start_date' => ['nullable', 'date'],
            'target_due_date' => ['nullable', 'date', 'after_or_equal:target_start_date'],
            'reference_url' => ['nullable', 'url', 'max:2048'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt'],
        ]);
    }

    /**
     * Store newly uploaded attachments for a request.
     *
     * @param  array<int, \Illuminate\Http\UploadedFile>  $attachments
     */
    private function storeAttachments(SystemRequest $systemRequest, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if (! $attachment) {
                continue;
            }

            $path = $attachment->store('system-requests/' . $systemRequest->id, 'public');

            SystemRequestAttachment::create([
                'system_request_id' => $systemRequest->id,
                'original_name' => $attachment->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $attachment->getMimeType(),
                'size_bytes' => $attachment->getSize() ?: 0,
            ]);
        }
    }
}
