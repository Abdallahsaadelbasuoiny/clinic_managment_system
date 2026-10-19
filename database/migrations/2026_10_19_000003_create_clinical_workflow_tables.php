<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id')->index();
            $table->unsignedBigInteger('patient_id')->index();
            $table->unsignedBigInteger('appointment_id')->nullable()->index();
            $table->unsignedBigInteger('doctor_id')->index();
            $table->dateTime('visit_date');
            $table->text('chief_complaint')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('examination')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->dateTime('follow_up_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id')->index();
            $table->unsignedBigInteger('medical_record_id')->index();
            $table->unsignedBigInteger('patient_id')->index();
            $table->unsignedBigInteger('doctor_id')->index();
            $table->string('status')->default('issued');
            $table->text('notes')->nullable();
            $table->dateTime('issued_at');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prescription_id')->index();
            $table->string('medicine_name');
            $table->string('dosage');
            $table->string('frequency');
            $table->string('duration');
            $table->unsignedInteger('quantity');
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('diagnostic_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id')->index();
            $table->unsignedBigInteger('medical_record_id')->index();
            $table->unsignedBigInteger('patient_id')->index();
            $table->unsignedBigInteger('doctor_id')->index();
            $table->string('type');
            $table->string('test_name');
            $table->text('instructions')->nullable();
            $table->string('status')->default('ordered')->index();
            $table->text('result')->nullable();
            $table->dateTime('ordered_at');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_orders');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medical_records');
    }
};
