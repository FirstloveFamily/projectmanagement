<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PublicProjectController extends Controller
{
    public function show($slug)
    {
        $project = Project::where('slug', $slug)
            ->where('is_public', true)
            ->with(['tasks' => function($query) {
                $query->orderBy('sort_order')->orderBy('start_date');
            }])
            ->firstOrFail();

        return view('projects.public', compact('project'));
    }

    public function exportPdf($slug)
    {
        $project = Project::where('slug', $slug)
            ->where('is_public', true)
            ->with(['tasks' => function($query) {
                $query->orderBy('sort_order')->orderBy('start_date');
            }])
            ->firstOrFail();

        $pdf = Pdf::loadView('projects.pdf', compact('project'));
        
        return $pdf->download("project-{$project->slug}.pdf");
    }
}
