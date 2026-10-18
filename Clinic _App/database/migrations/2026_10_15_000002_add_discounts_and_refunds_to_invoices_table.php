<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0.00)->after('patient_id');
            }
            if (!Schema::hasColumn('invoices', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0.00)->after('subtotal');
            }
            if (!Schema::hasColumn('invoices', 'discount_reason')) {
                $table->string('discount_reason')->nullable()->after('discount');
            }
            if (!Schema::hasColumn('invoices', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('payment_status');
            }
            if (!Schema::hasColumn('invoices', 'refund_reason')) {
                $table->text('refund_reason')->nullable()->after('is_locked');
            }
            if (!Schema::hasColumn('invoices', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_reason');
            }
            if (!Schema::hasColumn('invoices', 'refunded_by_user_id')) {
                $table->unsignedBigInteger('refunded_by_user_id')->nullable()->after('refunded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount',
                'discount_reason',
                'is_locked',
                'refund_reason',
                'refunded_at',
                'refunded_by_user_id',
            ]);
        });
    }
};
