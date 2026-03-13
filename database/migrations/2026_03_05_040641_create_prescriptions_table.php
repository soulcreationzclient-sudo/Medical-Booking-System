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
        Schema::create('prescriptions', function (Blueprint $table) {
               $table->id();

    $table->foreignId('case_entry_id')->constrained('caseentries')->cascadeOnDelete();

    $table->string('medicine_name');
    $table->string('dosage')->nullable();     // 500mg
    $table->string('frequency')->nullable();  // twice daily
    $table->string('duration')->nullable();   // 5 days
    $table->text('instructions')->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
