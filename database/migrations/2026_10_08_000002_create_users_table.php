<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('clinic_id')->nullable();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('role', ['owner', 'admin', 'doctor', 'receptionist', 'nurse', 'patient'])->default('patient');
                $table->boolean('must_change_password')->default(false);
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
