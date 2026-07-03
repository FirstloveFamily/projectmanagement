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
        Schema::table('projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'objective')) {
                $table->text('objective')->nullable()->after('description');
            }

            if (! Schema::hasColumn('projects', 'risk')) {
                $table->text('risk')->nullable()->after('objective');
            }

            if (! Schema::hasColumn('projects', 'notes')) {
                $table->text('notes')->nullable()->after('risk');
            }
        });

        DB::table('projects')
            ->whereNull('objective')
            ->whereNotNull('description')
            ->update(['objective' => DB::raw('description')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('projects', 'notes')) {
            Schema::table('projects', function (Blueprint $table): void {
                $table->dropColumn('notes');
            });
        }

        if (Schema::hasColumn('projects', 'risk')) {
            Schema::table('projects', function (Blueprint $table): void {
                $table->dropColumn('risk');
            });
        }

        if (Schema::hasColumn('projects', 'objective')) {
            Schema::table('projects', function (Blueprint $table): void {
                $table->dropColumn('objective');
            });
        }
    }
};
