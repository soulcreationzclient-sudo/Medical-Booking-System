<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add consultation_fee to doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->default(0)->after('qualification');
        });

        // 2. Add stock column to medicines table (if medicines table exists)
        if (Schema::hasTable('medicines')) {
            Schema::table('medicines', function (Blueprint $table) {
                if (!Schema::hasColumn('medicines', 'stock')) {
                    $table->unsignedInteger('stock')->default(0)->after('price');
                }
            });
        }

        // 3. Patient billing entries — consultation fees, medicine fees, operations
        Schema::create('patient_billing_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            // type: consultation | medicine | treatment | operation | custom_profit | custom_expense
            $table->enum('type', ['consultation', 'medicine', 'treatment', 'operation', 'custom_profit', 'custom_expense']);
            $table->string('description');
            $table->decimal('amount', 10, 2)->default(0);
            // For past treatments/operations it is just a note (amount = 0)
            $table->boolean('is_past_note')->default(false);
            // Payment status
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 4. Hospital financial ledger (overall profit & expense tracking)
        Schema::create('hospital_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            // type: profit | expense
            $table->enum('type', ['profit', 'expense']);
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('entry_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_financials');
        Schema::dropIfExists('patient_billing_entries');

        if (Schema::hasTable('medicines') && Schema::hasColumn('medicines', 'stock')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }

        if (Schema::hasColumn('doctors', 'consultation_fee')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->dropColumn('consultation_fee');
            });
        }
    }
};