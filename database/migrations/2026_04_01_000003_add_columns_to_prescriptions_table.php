<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'patient_id')) {
                $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('prescriptions', 'hospital_id')) {
                $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->nullOnDelete()->after('patient_id');
            }
            if (!Schema::hasColumn('prescriptions', 'doctor_id')) {
                $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete()->after('hospital_id');
            }
            if (!Schema::hasColumn('prescriptions', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete()->after('doctor_id');
            }
            if (!Schema::hasColumn('prescriptions', 'notes')) {
                $table->text('notes')->nullable()->after('booking_id');
            }
        });

        // Add missing columns to prescription_items if table exists
        if (Schema::hasTable('prescription_items')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                if (!Schema::hasColumn('prescription_items', 'prescription_id')) {
                    $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->cascadeOnDelete()->after('id');
                }
                if (!Schema::hasColumn('prescription_items', 'medicine_id')) {
                    $table->foreignId('medicine_id')->nullable()->constrained('medicines')->nullOnDelete()->after('prescription_id');
                }
                if (!Schema::hasColumn('prescription_items', 'quantity')) {
                    $table->unsignedInteger('quantity')->default(1)->after('medicine_id');
                }
                if (!Schema::hasColumn('prescription_items', 'price_at_time')) {
                    $table->decimal('price_at_time', 10, 2)->default(0)->after('quantity');
                }
                if (!Schema::hasColumn('prescription_items', 'dosage_instructions')) {
                    $table->string('dosage_instructions')->nullable()->after('price_at_time');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('patient_id');
            $table->dropConstrainedForeignId('hospital_id');
            $table->dropConstrainedForeignId('doctor_id');
            $table->dropConstrainedForeignId('booking_id');
            $table->dropColumn('notes');
        });

        if (Schema::hasTable('prescription_items')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('prescription_id');
                $table->dropConstrainedForeignId('medicine_id');
                $table->dropColumn(['quantity', 'price_at_time', 'dosage_instructions']);
            });
        }
    }
};