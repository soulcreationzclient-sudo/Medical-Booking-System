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
        Schema::create('caseentries', function (Blueprint $table) {
               $table->id();

    $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
    $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
    $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
    $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();

    $table->text('complaints')->nullable();        // patient complaints
    $table->text('examination')->nullable();       // physical examination
    $table->text('diagnosis')->nullable();         // diagnosis
    $table->text('notes')->nullable();             // additional doctor notes

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caseentries');
    }
};
