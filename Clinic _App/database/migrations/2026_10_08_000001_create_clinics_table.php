<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clinics')) {
            Schema::create('clinics', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('subdomain')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->enum('status', ['active', 'suspended', 'expired'])->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
