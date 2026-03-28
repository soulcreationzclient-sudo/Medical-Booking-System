<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'wa_status')) {
                $table->string('wa_status')->nullable()->after('status')
                      ->comment('WhatsApp delivery status: delivered, failed, read');
            }
            if (!Schema::hasColumn('bookings', 'wa_sent_at')) {
                $table->timestamp('wa_sent_at')->nullable()->after('wa_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['wa_status', 'wa_sent_at']);
        });
    }
};