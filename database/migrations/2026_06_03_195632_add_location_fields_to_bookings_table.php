<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('full_address')->nullable()->after('phone');
            $table->string('village')->nullable()->after('full_address');
            $table->string('district')->nullable()->after('village');
            $table->string('city')->nullable()->after('district');
            $table->string('province')->nullable()->after('city');
            $table->string('island')->nullable()->after('province');

            $table->decimal('latitude', 10, 7)->nullable()->after('island');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            $table->string('shipping_zone')->nullable()->after('longitude');
            $table->integer('shipping_cost')->default(0)->after('shipping_zone');
            $table->integer('flight_ticket_cost')->default(0)->after('shipping_cost');
            $table->integer('total_location_cost')->default(0)->after('flight_ticket_cost');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'full_address',
                'village',
                'district',
                'city',
                'province',
                'island',
                'latitude',
                'longitude',
                'shipping_zone',
                'shipping_cost',
                'flight_ticket_cost',
                'total_location_cost',
            ]);
        });
    }
};
