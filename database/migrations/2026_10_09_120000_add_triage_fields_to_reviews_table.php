<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_important')->default(false)->after('read_at');
            $table->string('follow_up_status', 32)->default('none')->after('is_important');
            $table->text('admin_remark')->nullable()->after('follow_up_status');
            $table->foreignId('remarked_by')->nullable()->after('admin_remark')->constrained('users')->nullOnDelete();
            $table->timestamp('remarked_at')->nullable()->after('remarked_by');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('remarked_by');
            $table->dropColumn([
                'is_important',
                'follow_up_status',
                'admin_remark',
                'remarked_at',
            ]);
        });
    }
};
