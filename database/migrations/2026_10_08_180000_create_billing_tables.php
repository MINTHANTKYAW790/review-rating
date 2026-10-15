<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('price_mmk')->default(0);
            $table->unsignedInteger('duration_days')->default(0);
            $table->unsignedInteger('max_staff')->default(3);
            $table->unsignedInteger('max_managers')->default(0);
            $table->unsignedInteger('max_reviews')->nullable();
            $table->boolean('is_account_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('kbz_number')->nullable();
            $table->string('wave_number')->nullable();
            $table->string('account_name')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount_mmk');
            $table->string('status')->default('pending');
            $table->string('method');
            $table->string('reference');
            $table->string('screenshot_path');
            $table->text('owner_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('store_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('price_mmk')->default(0);
            $table->timestamps();

            $table->index(['store_id', 'status']);
            $table->index(['store_id', 'ends_at']);
        });

        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('price_mmk');
            $table->unsignedInteger('duration_days');
            $table->timestamps();
        });

        Schema::create('store_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('duration_days');
            $table->timestamp('consumed_at')->nullable();
            $table->foreignId('consumed_store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'consumed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_credits');
        Schema::dropIfExists('payment_items');
        Schema::dropIfExists('store_subscriptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('billing_settings');
        Schema::dropIfExists('plans');
    }
};
