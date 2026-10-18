<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('clinic_id')->nullable()->index();
                $table->unsignedBigInteger('patient_id')->index();
                $table->unsignedBigInteger('doctor_id')->index();
                $table->dateTime('appointment_date');
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->index();
                $table->boolean('is_urgent')->default(false)->index();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
