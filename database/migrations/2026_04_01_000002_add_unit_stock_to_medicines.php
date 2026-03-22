<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Only add if they don't already exist
            if (!Schema::hasColumn('medicines', 'unit')) {
                $table->string('unit')->default('tablet')->after('name');
            }
            if (!Schema::hasColumn('medicines', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('price');
            }
            if (!Schema::hasColumn('medicines', 'description')) {
                $table->string('description')->nullable()->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['unit', 'stock', 'description']);
        });
    }
};