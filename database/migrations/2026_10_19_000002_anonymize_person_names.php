<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'patients'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->select('id')
                ->orderBy('id')
                ->chunkById(500, function ($records) use ($table): void {
                    foreach ($records as $record) {
                        DB::table($table)
                            ->where('id', $record->id)
                            ->update(['name' => 'name '.$record->id]);
                    }
                });
        }
    }

    public function down(): void
    {
        // Anonymized names cannot be reversed to the original values.
    }
};
