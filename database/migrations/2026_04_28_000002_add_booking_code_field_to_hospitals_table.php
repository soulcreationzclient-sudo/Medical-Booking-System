<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'booking_code_field_id')) {
                $table->string('booking_code_field_id')->nullable()->after('appointment_time_field_id')
                      ->comment('Speedbots custom field ID for booking code (e.g. 188523)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn('booking_code_field_id');
        });
    }
};