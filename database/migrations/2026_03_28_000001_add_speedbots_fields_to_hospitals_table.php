<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            // Only add columns that don't already exist

            if (!Schema::hasColumn('hospitals', 'token')) {
                $table->string('token')->nullable()->after('flow_id');
            }

            if (!Schema::hasColumn('hospitals', 'accept_flow_id')) {
                $table->string('accept_flow_id')->nullable()->after('token');
            }

            if (!Schema::hasColumn('hospitals', 'reject_flow_id')) {
                $table->string('reject_flow_id')->nullable()->after('accept_flow_id');
            }

            if (!Schema::hasColumn('hospitals', 'reschedule_flow_id')) {
                $table->string('reschedule_flow_id')->nullable()->after('reject_flow_id');
            }

            if (!Schema::hasColumn('hospitals', 'datetime_field_id')) {
                $table->string('datetime_field_id')->nullable()->after('reschedule_flow_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn([
                'token',
                'accept_flow_id',
                'reject_flow_id',
                'reschedule_flow_id',
                'datetime_field_id',
            ]);
        });
    }
};