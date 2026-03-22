<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // Make all old columns nullable so new prescriptions
            // (which use prescription_items instead) can be saved
            if (Schema::hasColumn('prescriptions', 'medicine_name')) {
                $table->string('medicine_name')->nullable()->change();
            }
            if (Schema::hasColumn('prescriptions', 'dosage')) {
                $table->string('dosage')->nullable()->change();
            }
            if (Schema::hasColumn('prescriptions', 'frequency')) {
                $table->string('frequency')->nullable()->change();
            }
            if (Schema::hasColumn('prescriptions', 'duration')) {
                $table->string('duration')->nullable()->change();
            }
            if (Schema::hasColumn('prescriptions', 'instructions')) {
                $table->string('instructions')->nullable()->change();
            }
            if (Schema::hasColumn('prescriptions', 'case_entry_id')) {
                $table->unsignedBigInteger('case_entry_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // Cannot safely revert nullable to not-null if data exists
    }
};