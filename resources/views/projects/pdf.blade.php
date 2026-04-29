<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $project->name }} - PDF Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #4F46E5; padding-bottom: 10px; }
        .project-name { font-size: 24px; font-weight: bold; color: #111; margin-bottom: 5px; }
        .project-desc { font-size: 14px; color: #666; }
        .stats { margin-bottom: 30px; width: 100%; }
        .stat-box { padding: 10px; background: #f9fafb; border: 1px solid #e5e7eb; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { background: #f3f4f6; text-align: left; padding: 8px; border: 1px solid #e5e7eb; }
        .table td { padding: 8px; border: 1px solid #e5e7eb; }
        .progress-bar { background: #e5e7eb; border-radius: 10px; height: 10px; width: 100px; display: inline-block; }
        .progress-fill { background: #4F46E5; height: 10px; border-radius: 10px; }
        .status-badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .status-active { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="header">
        <div class="project-name">{{ $project->name }}</div>
        <div class="project-desc">{{ $project->description }}</div>
    </div>

    <table class="stats">
        <tr>
            <td class="stat-box">
                <strong>สถานะ:</strong> {{ match($project->status) {
                    'planning' => 'กำลังวางแผน',
                    'active' => 'กำลังดำเนินการ',
                    'on_hold' => 'ระงับชั่วคราว',
                    'completed' => 'เสร็จสมบูรณ์',
                    'cancelled' => 'ยกเลิก',
                    default => $project->status
                } }}
            </td>
            <td class="stat-box">
                <strong>ความคืบหน้า:</strong> {{ $project->progress }}%
            </td>
            <td class="stat-box">
                <strong>ระยะเวลา:</strong> 
                {{ $project->start_date ? $project->start_date->format('d/m/Y') : '-' }} - 
                {{ $project->end_date ? $project->end_date->format('d/m/Y') : '-' }}
            </td>
        </tr>
    </table>

    <h3>รายการงานของโปรเจกต์ (Project Tasks)</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ชื่องาน</th>
                <th>สถานะ</th>
                <th>ความสำคัญ</th>
                <th>กำหนดส่ง</th>
                <th>ความคืบหน้า</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->tasks as $task)
            <tr>
                <td>
                    <strong>{{ $task->title }}</strong><br>
                    <small>{{ Str::limit($task->description, 100) }}</small>
                </td>
                <td>{{ match($task->status) {
                    'todo' => 'ยังไม่เริ่ม',
                    'in_progress' => 'กำลังทำ',
                    'review' => 'รอตรวจสอบ',
                    'completed' => 'เสร็จสิ้น',
                    default => $task->status
                } }}</td>
                <td>{{ match($task->priority) {
                    'low' => 'ต่ำ',
                    'medium' => 'ปานกลาง',
                    'high' => 'สูง',
                    'critical' => 'เร่งด่วน',
                    default => $task->priority
                } }}</td>
                <td>{{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $task->progress }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: center; color: #999;">
        Generated on {{ date('M d, Y H:i') }}
    </div>
</body>
</html>
