<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'no_show_flow_id')) {
                $table->string('no_show_flow_id')->nullable()->after('reschedule_flow_id')
                      ->comment('Speedbots flow ID triggered when booking marked as No Show');
            }
            if (!Schema::hasColumn('hospitals', 'completed_flow_id')) {
                $table->string('completed_flow_id')->nullable()->after('no_show_flow_id')
                      ->comment('Speedbots flow ID triggered when booking marked as Completed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['no_show_flow_id', 'completed_flow_id']);
        });
    }
};