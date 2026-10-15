<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('reviews')
            ->where('follow_up_status', 'to_contact')
            ->update(['follow_up_status' => 'inbox']);

        DB::table('reviews')
            ->whereIn('follow_up_status', ['contacted', 'closed'])
            ->update(['follow_up_status' => 'done']);

        DB::table('reviews')
            ->where('follow_up_status', 'none')
            ->whereNull('read_at')
            ->update(['follow_up_status' => 'inbox']);

        DB::table('reviews')
            ->where('follow_up_status', 'none')
            ->whereNotNull('read_at')
            ->update(['follow_up_status' => 'done']);
    }

    public function down(): void
    {
        DB::table('reviews')
            ->where('follow_up_status', 'inbox')
            ->update(['follow_up_status' => 'to_contact']);

        DB::table('reviews')
            ->where('follow_up_status', 'done')
            ->update(['follow_up_status' => 'closed']);
    }
};
