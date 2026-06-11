<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            if (!Schema::hasColumn('bookings', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->after('status');
            }

            if (!Schema::hasColumn('bookings', 'invoice_date')) {
                $table->dateTime('invoice_date')->nullable()->after('invoice_number');
            }

            if (!Schema::hasColumn('bookings', 'invoice_subtotal')) {
                $table->integer('invoice_subtotal')->default(0)->after('invoice_date');
            }

            if (!Schema::hasColumn('bookings', 'invoice_transport')) {
                $table->integer('invoice_transport')->default(0)->after('invoice_subtotal');
            }

            if (!Schema::hasColumn('bookings', 'invoice_total')) {
                $table->integer('invoice_total')->default(0)->after('invoice_transport');
            }

            if (!Schema::hasColumn('bookings', 'invoice_sent_at')) {
                $table->dateTime('invoice_sent_at')->nullable()->after('invoice_total');
            }

        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $columns = [
                'invoice_number',
                'invoice_date',
                'invoice_subtotal',
                'invoice_transport',
                'invoice_total',
                'invoice_sent_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }

        });
    }
};
