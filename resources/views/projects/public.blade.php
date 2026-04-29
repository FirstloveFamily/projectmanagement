<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} - Project Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <div class="w-full bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-primary-50 rounded-lg">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight">Project Tracker</span>
                </div>
                <a href="{{ route('projects.public.export-pdf', $project->slug) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                    <svg class="mr-2 -ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    <main class="flex-grow max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Hero Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary-50 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 right-40 -mt-20 w-64 h-64 bg-purple-50 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide
                                {{ match($project->status) {
                                    'active' => 'bg-blue-50 text-blue-700 border border-blue-100',
                                    'completed' => 'bg-green-50 text-green-700 border border-green-100',
                                    'planning' => 'bg-slate-50 text-slate-700 border border-slate-100',
                                    'cancelled' => 'bg-red-50 text-red-700 border border-red-100',
                                    default => 'bg-gray-50 text-gray-700 border border-gray-100'
                                } }}">
                                {{ match($project->status) {
                                    'active' => 'กำลังดำเนินการ',
                                    'completed' => 'เสร็จสมบูรณ์',
                                    'planning' => 'กำลังวางแผน',
                                    'on_hold' => 'ระงับชั่วคราว',
                                    'cancelled' => 'ยกเลิก',
                                    default => $project->status
                                } }}
                            </span>
                            <span class="text-sm text-slate-400 font-medium">Updated {{ $project->updated_at->diffForHumans() }}</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                    {{ $project->name }}
                    @if($project->company)
                        <span class="block text-xl text-primary-500 font-semibold mt-1">@ {{ $project->company->name }}</span>
                    @endif
                </h1>
                        <p class="text-lg text-slate-600 leading-relaxed max-w-3xl">{{ $project->description }}</p>
                    </div>
                    
                    <div class="flex-shrink-0 w-full lg:w-72 bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wider mb-3">Progress</h3>
                        <div class="flex items-end justify-between mb-2">
                            <span class="text-3xl font-bold text-primary-600">{{ $project->progress }}%</span>
                            <span class="text-sm text-slate-500 font-medium mb-1">completed</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $project->progress }}%"></div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200 grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-slate-500 font-medium uppercase">Start</div>
                                <div class="text-sm font-semibold text-slate-900 mt-1">
                                    {{ $project->start_date ? $project->start_date->format('M d, Y') : 'TBD' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 font-medium uppercase">End</div>
                                <div class="text-sm font-semibold text-slate-900 mt-1">
                                    {{ $project->end_date ? $project->end_date->format('M d, Y') : 'TBD' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-slate-900 mb-1">{{ $project->tasks->count() }}</div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Tasks</div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-green-600 mb-1">{{ $project->tasks->where('status', 'completed')->count() }}</div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Completed</div>
            </div>
             <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-blue-600 mb-1">{{ $project->tasks->where('status', 'in_progress')->count() }}</div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">In Progress</div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-slate-400 mb-1">{{ $project->tasks->where('status', 'todo')->count() }}</div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">To Do</div>
            </div>
        </div>

        <!-- Task List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Project Tasks</h2>
                <div class="text-sm text-slate-500">{{ $project->tasks->count() }} items</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Task Details</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">Priority</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-40">Due Date</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($project->tasks as $task)
                        <tr class="group hover:bg-slate-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="h-2 w-2 rounded-full mt-1.5 {{ match($task->priority) { 'critical' => 'bg-red-500', 'high' => 'bg-orange-500', 'medium' => 'bg-yellow-500', default => 'bg-slate-300' } }}"></div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-slate-900 group-hover:text-primary-600 transition-colors">{{ $task->title }}</div>
                                        @if($task->description)
                                            <div class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $task->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                    {{ match($task->status) {
                                        'completed' => 'bg-green-50 text-green-700 border border-green-100',
                                        'in_progress' => 'bg-blue-50 text-blue-700 border border-blue-100',
                                        'review' => 'bg-yellow-50 text-yellow-700 border border-yellow-100',
                                        'todo' => 'bg-slate-100 text-slate-600 border border-slate-200',
                                        default => 'bg-gray-100 text-gray-600'
                                    } }}">
                                    {{ match($task->status) {
                                        'todo' => 'ยังไม่เริ่ม',
                                        'in_progress' => 'กำลังทำ',
                                        'review' => 'รอตรวจสอบ',
                                        'completed' => 'เสร็จสิ้น',
                                        default => $task->status
                                    } }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                     <svg class="mr-1.5 h-4 w-4 {{ match($task->priority) { 'critical' => 'text-red-500', 'high' => 'text-orange-500', 'medium' => 'text-yellow-500', default => 'text-slate-400' } }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span class="text-sm text-slate-600 capitalize">
                                        {{ match($task->priority) {
                                            'low' => 'ต่ำ',
                                            'medium' => 'ปานกลาง',
                                            'high' => 'สูง',
                                            'critical' => 'เร่งด่วน',
                                            default => $task->priority
                                        } }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-600 flex items-center">
                                    <svg class="mr-1.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $task->due_date ? $task->due_date->format('M d') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="text-sm font-bold text-slate-700">{{ $task->progress }}%</span>
                                    <div class="w-16 bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-primary-500 h-1.5 rounded-full" style="width: {{ $task->progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-slate-50 rounded-full p-4 mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-slate-900 font-medium text-lg">No tasks found</h3>
                                    <p class="text-slate-500 mt-1 max-w-sm">This project doesn't have any tasks listed yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <footer class="bg-white border-t border-gray-100 mt-auto">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-sm text-slate-400">
                &copy; {{ date('Y') }} Internal Project Management System. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
