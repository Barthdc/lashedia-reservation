<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            if (!Schema::hasColumn('bookings', 'full_address')) {
                $table->text('full_address')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('bookings', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('full_address');
            }

            if (!Schema::hasColumn('bookings', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }

            if (!Schema::hasColumn('bookings', 'mua_latitude')) {
                $table->decimal('mua_latitude', 10, 7)->nullable()->after('longitude');
            }

            if (!Schema::hasColumn('bookings', 'mua_longitude')) {
                $table->decimal('mua_longitude', 10, 7)->nullable()->after('mua_latitude');
            }

            if (!Schema::hasColumn('bookings', 'distance_km')) {
                $table->decimal('distance_km', 8, 2)->default(0)->after('mua_longitude');
            }

            if (!Schema::hasColumn('bookings', 'transport_cost')) {
                $table->integer('transport_cost')->default(0)->after('distance_km');
            }

            if (!Schema::hasColumn('bookings', 'transport_note')) {
                $table->string('transport_note')->nullable()->after('transport_cost');
            }

        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $columns = [
                'full_address',
                'latitude',
                'longitude',
                'mua_latitude',
                'mua_longitude',
                'distance_km',
                'transport_cost',
                'transport_note',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }

        });
    }
};
