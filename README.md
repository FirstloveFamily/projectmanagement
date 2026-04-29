# 🚀 Project Blueprint: Internal Project Management System

## 🤖 AI Assistant Instructions
This document serves as the absolute source of truth for generating this project. 
**Please read the entire document before writing any code.** Execute the development in the sequential phases listed under "Execution Phases". Do not skip phases. After completing a phase, verify the functionality before moving to the next.

---

## 📌 Project Overview
A professional Internal Project Management System for a single organization. 
Key features include Project creation, Task management, Dependencies, a Drag & Drop Gantt Chart, and a public-facing project status viewer (No Login required).

## 🧰 Technology Stack & Global Rules
- **Backend:** PHP 8.2+, Laravel 11
- **Admin Panel:** FilamentPHP 3.3, Livewire v3
- **Database:** SQLite (Must ensure strict compatibility, e.g., using `cascadeOnDelete()`)
- **Key Packages:** - `dhtmlxGantt` (Vanilla JS/CSS for Gantt Chart)
  - `barryvdh/laravel-dompdf` (For PDF Export)
- **UI/UX Guidelines:** Clean Minimal, White + Soft Gray backgrounds, Primary Color Indigo (`#4F46E5`), Rounded Corners (`rounded-xl` or `rounded-2xl`), Soft Shadows (`shadow-sm` or `shadow-md`).

---

## 🗄️ Database Schema & Relationships

### 1. `users` (Default Laravel table)
- Relationships: `hasMany` Projects, `hasMany` Tasks.

### 2. `projects`
- `id` (Primary Key)
- `name` (string)
- `slug` (string, unique)
- `description` (text, nullable)
- `start_date` (date, nullable)
- `end_date` (date, nullable)
- `status` (string, default: 'planning')
- `progress` (integer, default: 0)
- `is_public` (boolean, default: false)
- `created_by` (foreignId -> users.id, nullOnDelete)
- Relationships: `belongsTo` User (Creator), `hasMany` Tasks.

### 3. `tasks`
- `id` (Primary Key)
- `project_id` (foreignId -> projects.id, cascadeOnDelete)
- `title` (string)
- `description` (text, nullable)
- `assigned_to` (foreignId -> users.id, nullOnDelete)
- `start_date` (date, nullable)
- `due_date` (date, nullable)
- `completed_at` (dateTime, nullable)
- `status` (string, default: 'todo')
- `priority` (string, default: 'medium')
- `progress` (integer, default: 0)
- `sort_order` (integer, default: 0)
- Relationships: `belongsTo` Project, `belongsTo` User (Assignee), `hasMany` TaskDependencies (as source and target).

### 4. `task_dependencies`
- `id` (Primary Key)
- `task_id` (foreignId -> tasks.id, cascadeOnDelete)
- `depends_on_task_id` (foreignId -> tasks.id, cascadeOnDelete)

---

## 🛠️ Execution Phases (Step-by-Step)

### Phase 1: Environment & Database Setup
1. Configure `.env` for SQLite connection (`DB_CONNECTION=sqlite`, remove other DB keys).
2. Create `database/database.sqlite`.
3. Generate Migrations and Models for `Project`, `Task`, and `TaskDependency`.
4. Define strict Eloquent relationships and `$fillable` or `$guarded` properties in all Models.
5. Run migrations.

### Phase 2: Admin Panel (Filament Setup)
1. Install Filament 3.3 (`php artisan filament:install --panels`).
2. Generate `ProjectResource` and `TaskResource`.
3. Configure form schemas and table columns for both resources. 
   - Note: Use Filament's native layout components (Grids, Sections).
4. Create Dashboard Widgets: Total Projects, Active Projects, Overdue Tasks, and Completion %. Register them in the Filament Dashboard.

### Phase 3: Gantt Chart Integration
1. Create a Custom Filament Page named `ProjectGantt`.
2. Assume `dhtmlxGantt` assets are available at `public/gantt/` (do not try to npm install it, just reference the CDN or local public path in the view).
3. Create an API endpoint or a method in the Livewire component to fetch Tasks and Dependencies formatted specifically for dhtmlxGantt (`data` array and `links` array).
4. Implement the Gantt Chart rendering in the custom page's view.

### Phase 4: Public Viewer & PDF Export (Front-end)
1. Install `barryvdh/laravel-dompdf`.
2. Create `PublicProjectController`.
3. Define Route: `GET /projects/{slug}`. Ensure it only returns projects where `is_public = true` or throws 404.
4. Create a Blade view (`resources/views/projects/public.blade.php`) using Tailwind CSS adhering to the UI/UX Guidelines.
   - Include: Hero Section, Progress Bar, and Task List Table.
5. Define Route: `GET /projects/{slug}/export-pdf` to generate and download a PDF snapshot of the project using DOMPDF.

---
**AI Acknowledgment:** Start with Phase 1. Inform the user when Phase 1 is complete and wait for confirmation before proceeding to Phase 2.
