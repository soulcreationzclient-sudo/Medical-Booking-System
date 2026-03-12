<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {

            if (!Schema::hasColumn('patients', 'ic_passport_no'))
                $table->string('ic_passport_no')->nullable()->after('phone_no');

            if (!Schema::hasColumn('patients', 'gender'))
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('ic_passport_no');

            if (!Schema::hasColumn('patients', 'dob'))
                $table->date('dob')->nullable()->after('gender');

            if (!Schema::hasColumn('patients', 'blood_type'))
                $table->string('blood_type')->nullable()->after('dob');

            if (!Schema::hasColumn('patients', 'marital_status'))
                $table->string('marital_status')->nullable()->after('blood_type');

            if (!Schema::hasColumn('patients', 'nationality'))
                $table->string('nationality')->nullable()->after('marital_status');

            if (!Schema::hasColumn('patients', 'address'))
                $table->text('address')->nullable()->after('nationality');

            if (!Schema::hasColumn('patients', 'state'))
                $table->string('state')->nullable()->after('address');

            if (!Schema::hasColumn('patients', 'city'))
                $table->string('city')->nullable()->after('state');

            if (!Schema::hasColumn('patients', 'postcode'))
                $table->string('postcode')->nullable()->after('city');

            if (!Schema::hasColumn('patients', 'country'))
                $table->string('country')->nullable()->default('Malaysia')->after('postcode');

            if (!Schema::hasColumn('patients', 'emergency_contact_name'))
                $table->string('emergency_contact_name')->nullable()->after('country');

            if (!Schema::hasColumn('patients', 'emergency_contact_no'))
                $table->string('emergency_contact_no')->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('patients', 'ic_passport_no')         ? 'ic_passport_no'         : null,
                Schema::hasColumn('patients', 'gender')                 ? 'gender'                 : null,
                Schema::hasColumn('patients', 'dob')                    ? 'dob'                    : null,
                Schema::hasColumn('patients', 'blood_type')             ? 'blood_type'             : null,
                Schema::hasColumn('patients', 'marital_status')         ? 'marital_status'         : null,
                Schema::hasColumn('patients', 'nationality')            ? 'nationality'            : null,
                Schema::hasColumn('patients', 'address')                ? 'address'                : null,
                Schema::hasColumn('patients', 'state')                  ? 'state'                  : null,
                Schema::hasColumn('patients', 'city')                   ? 'city'                   : null,
                Schema::hasColumn('patients', 'postcode')               ? 'postcode'               : null,
                Schema::hasColumn('patients', 'country')                ? 'country'                : null,
                Schema::hasColumn('patients', 'emergency_contact_name') ? 'emergency_contact_name' : null,
                Schema::hasColumn('patients', 'emergency_contact_no')   ? 'emergency_contact_no'   : null,
            ]));
        });
    }
};