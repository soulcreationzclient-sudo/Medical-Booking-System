<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_billing_entries', function (Blueprint $table) {
            $table->foreignId('treatment_id')
                ->nullable()
                ->after('booking_id')
                ->constrained('treatments')
                ->nullOnDelete();

            $table->index(['hospital_id', 'paid_at'], 'idx_pbe_hospital_paid_at');
            $table->index(['is_paid', 'paid_at'], 'idx_pbe_is_paid_paid_at');
            $table->index('treatment_id', 'idx_pbe_treatment_id');
            $table->index('booking_id', 'idx_pbe_booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('patient_billing_entries', function (Blueprint $table) {
            $table->dropIndex('idx_pbe_hospital_paid_at');
            $table->dropIndex('idx_pbe_is_paid_paid_at');
            $table->dropIndex('idx_pbe_treatment_id');
            $table->dropIndex('idx_pbe_booking_id');
            $table->dropConstrainedForeignId('treatment_id');
        });
    }
};