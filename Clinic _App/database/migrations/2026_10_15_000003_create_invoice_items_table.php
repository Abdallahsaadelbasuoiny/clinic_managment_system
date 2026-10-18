<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id')->index();
                $table->string('description');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->decimal('total_price', 10, 2)->default(0.00);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
