<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->text('phone_encrypted')->nullable()->change();
            $table->string('phone_bindex')->nullable()->change();
            $table->text('national_id_encrypted')->nullable()->change();
            $table->string('national_id_bindex')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->text('phone_encrypted')->nullable(false)->change();
            $table->string('phone_bindex')->nullable(false)->change();
            $table->text('national_id_encrypted')->nullable(false)->change();
            $table->string('national_id_bindex')->nullable(false)->change();
        });
    }
};
