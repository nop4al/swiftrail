<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Add booking_id relation
            if (!Schema::hasColumn('refunds', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->after('user_id')->constrained('bookings')->onDelete('cascade');
            }
            
            // Add SwiftPay wallet relation
            if (!Schema::hasColumn('refunds', 'swift_pay_wallet_id')) {
                $table->foreignId('swift_pay_wallet_id')->nullable()->after('booking_id')->constrained('swift_pay_wallets')->onDelete('set null');
            }
            
            // Add description field
            if (!Schema::hasColumn('refunds', 'description')) {
                $table->text('description')->nullable()->after('reason');
            }
            
            // Add refund request fields
            if (!Schema::hasColumn('refunds', 'refund_reason')) {
                $table->string('refund_reason')->nullable()->after('description');
            }
            
            // Rename order_id to ticket_number if it exists
            if (Schema::hasColumn('refunds', 'order_id') && !Schema::hasColumn('refunds', 'ticket_number')) {
                $table->renameColumn('order_id', 'ticket_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Drop foreign keys
            if (Schema::hasColumn('refunds', 'swift_pay_wallet_id')) {
                $table->dropForeign(['swift_pay_wallet_id']);
                $table->dropColumn('swift_pay_wallet_id');
            }
            
            if (Schema::hasColumn('refunds', 'booking_id')) {
                $table->dropForeign(['booking_id']);
                $table->dropColumn('booking_id');
            }
            
            // Drop new columns
            if (Schema::hasColumn('refunds', 'description')) {
                $table->dropColumn('description');
            }
            
            if (Schema::hasColumn('refunds', 'refund_reason')) {
                $table->dropColumn('refund_reason');
            }
        });
    }
};
