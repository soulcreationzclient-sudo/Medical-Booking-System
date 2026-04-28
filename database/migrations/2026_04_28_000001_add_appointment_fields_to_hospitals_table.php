<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'appointment_date_field_id')) {
                $table->string('appointment_date_field_id')->nullable()->after('datetime_field_id')
                      ->comment('Speedbots custom field name/ID for appointment date (e.g. appointment_date or 983376)');
            }
            if (!Schema::hasColumn('hospitals', 'appointment_time_field_id')) {
                $table->string('appointment_time_field_id')->nullable()->after('appointment_date_field_id')
                      ->comment('Speedbots custom field name/ID for appointment time (e.g. appointment_time or 972343)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['appointment_date_field_id', 'appointment_time_field_id']);
        });
    }
};