<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('patients')) {
            Schema::create('patients', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('clinic_id')->nullable();
                $table->string('name');
                $table->date('date_of_birth')->nullable();
                $table->enum('gender', ['male', 'female'])->nullable();
                $table->text('address')->nullable();
                $table->text('phone_encrypted')->nullable();
                $table->string('phone_bindex')->nullable();
                $table->text('national_id_encrypted')->nullable();
                $table->string('national_id_bindex')->nullable();
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
