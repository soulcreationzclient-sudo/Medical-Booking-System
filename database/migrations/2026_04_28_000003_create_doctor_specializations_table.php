<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_specializations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('specialization_id');
            $table->timestamps();

            $table->unique(['doctor_id', 'specialization_id']);
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('specialization_id')->references('id')->on('specializations')->onDelete('cascade');
        });

        // Migrate existing single specialization_id into the pivot table
        if (Schema::hasColumn('doctors', 'specialization_id')) {
            $doctors = DB::table('doctors')->whereNotNull('specialization_id')->get();
            foreach ($doctors as $doctor) {
                DB::table('doctor_specializations')->insertOrIgnore([
                    'doctor_id'         => $doctor->id,
                    'specialization_id' => $doctor->specialization_id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_specializations');
    }
};