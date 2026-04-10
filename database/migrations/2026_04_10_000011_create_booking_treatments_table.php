<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained('treatments')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'treatment_id'], 'uq_booking_treatment_unique');
            $table->index(['booking_id', 'treatment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_treatments');
    }
};