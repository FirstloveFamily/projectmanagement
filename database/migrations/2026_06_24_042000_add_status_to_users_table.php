<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('status')->default('active')->after('role');
            });
        }

        DB::table('users')
            ->whereNull('status')
            ->orWhereRaw("trim(coalesce(status, '')) = ''")
            ->orWhereNotIn('status', ['active', 'pending', 'suspended'])
            ->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('status', 'active')
            ->update(['status' => null]);

        if (Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('status');
            });
        }
    }
};
