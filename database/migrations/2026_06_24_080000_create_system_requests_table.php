<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requester_name');
            $table->string('requester_email')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('request_type')->default('improve');
            $table->string('title');
            $table->string('objective')->nullable();
            $table->text('details')->nullable();
            $table->text('impact')->nullable();
            $table->string('priority')->default('medium');
            $table->string('desired_output')->nullable();
            $table->date('target_start_date')->nullable();
            $table->date('target_due_date')->nullable();
            $table->text('reference_url')->nullable();
            $table->string('status')->default('pending');
            $table->text('decision_note')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_requests');
    }
};
