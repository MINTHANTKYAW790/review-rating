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
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('customer_phone')->nullable()->after('comment');
            $table->boolean('contact_requested')->default(false)->after('customer_phone');
            $table->string('image_url')->nullable()->after('contact_requested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['customer_phone', 'contact_requested', 'image_url']);
        });
    }
};
