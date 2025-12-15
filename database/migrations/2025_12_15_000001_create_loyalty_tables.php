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
        // Loyalty levels/tiers
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Silver, Gold, Platinum
            $table->integer('min_points')->default(0); // Minimum points to reach this tier
            $table->decimal('benefit_multiplier', 3, 2)->default(1.00); // Point multiplier (e.g., 1.5x)
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // User loyalty balance
        Schema::create('user_loyalty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->bigInteger('total_points')->default(0);
            $table->foreignId('tier_id')->constrained('loyalty_tiers')->default(1);
            $table->integer('points_this_month')->default(0);
            $table->timestamp('tier_updated_at')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });

        // Loyalty transactions (history)
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('points_change'); // Positive for earn, negative for redeem
            $table->string('type'); // 'earn', 'redeem', 'bonus', 'expire'
            $table->text('description');
            $table->string('reference_type')->nullable(); // 'ticket', 'promo', 'bonus'
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of related record
            $table->timestamps();
        });

        // Rewards catalog
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('points_required');
            $table->string('reward_type'); // 'discount', 'voucher', 'free_ticket', 'points'
            $table->json('reward_details'); // { code: "REWARD123", discount: 50000, etc }
            $table->integer('stock')->nullable(); // -1 for unlimited
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User claimed rewards
        Schema::create('user_loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reward_id')->constrained('loyalty_rewards')->onDelete('cascade');
            $table->timestamp('claimed_at');
            $table->timestamp('expired_at')->nullable();
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_loyalty_rewards');
        Schema::dropIfExists('loyalty_rewards');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('user_loyalty');
        Schema::dropIfExists('loyalty_tiers');
    }
};
