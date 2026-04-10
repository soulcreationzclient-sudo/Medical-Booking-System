<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 100)->nullable();
            $table->enum('category', ['consultation', 'treatment', 'operation', 'medicine', 'other'])
                ->default('treatment');
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hospital_id', 'name'], 'uq_treatments_hospital_name');
            $table->unique(['hospital_id', 'code'], 'uq_treatments_hospital_code');
            $table->index(['hospital_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};