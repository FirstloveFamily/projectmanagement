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
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role')->default('it_dev')->after('email');
            });
        }

        DB::table('users')
            ->whereNull('role')
            ->orWhereRaw("trim(coalesce(role, '')) = ''")
            ->orWhereNotIn('role', ['admin', 'it_dev', 'it_test'])
            ->update(['role' => 'it_dev']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('role', 'it_dev')
            ->update(['role' => null]);

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('role');
            });
        }
    }
};
