<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('clinic_id')->nullable();
                $table->unsignedBigInteger('patient_id');
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->decimal('doctor_share', 10, 2)->default(0.00);
                $table->decimal('clinic_share', 10, 2)->default(0.00);
                $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid', 'refunded'])->default('unpaid');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
