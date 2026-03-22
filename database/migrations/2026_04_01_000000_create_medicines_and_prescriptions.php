 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Run this BEFORE the financial features migration if your project
 * does NOT yet have a medicines table. If you already have one, skip this file.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('medicines')) {
            Schema::create('medicines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
                $table->string('name');
                $table->string('unit')->default('tablet'); // tablet, ml, mg, etc.
                $table->decimal('price', 10, 2)->default(0); // price per unit
                $table->unsignedInteger('stock')->default(0); // current in-stock quantity
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Prescriptions table — each prescription is attached to a booking
        if (!Schema::hasTable('prescriptions')) {
            Schema::create('prescriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
                $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Prescription line items — medicines prescribed
        if (!Schema::hasTable('prescription_items')) {
            Schema::create('prescription_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
                $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('price_at_time', 10, 2)->default(0); // snapshot of price when prescribed
                $table->string('dosage_instructions')->nullable(); // e.g. "1 tablet morning & night"
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medicines');
    }
};