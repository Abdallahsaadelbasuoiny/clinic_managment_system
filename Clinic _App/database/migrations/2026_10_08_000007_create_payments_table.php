<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('clinic_id')->nullable();
                $table->unsignedBigInteger('invoice_id')->index();
                $table->decimal('amount_paid', 10, 2);
                $table->enum('method', ['cash', 'card', 'instapay'])->default('cash');
                $table->unsignedBigInteger('received_by_user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
