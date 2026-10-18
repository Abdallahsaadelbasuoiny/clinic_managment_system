<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
            if (!Schema::hasColumn('patients', 'national_id')) {
                $table->string('national_id')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['phone', 'national_id']);
        });
    }
};
