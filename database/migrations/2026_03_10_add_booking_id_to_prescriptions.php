<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'booking_id')) {
                // Add booking_id as nullable so existing rows don't break
                $table->unsignedBigInteger('booking_id')->nullable()->after('id');
                $table->foreign('booking_id')
                      ->references('id')->on('bookings')
                      ->cascadeOnDelete();
            }

            // Make case_entry_id nullable (prescription can exist without case entry)
            $table->unsignedBigInteger('case_entry_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
        });
    }
};