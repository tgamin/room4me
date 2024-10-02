<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('status')->nullable()->after('reservationId');
            $table->string('paymentId')->nullable()->after('status');
            $table->string('macAddress')->nullable()->after('paymentId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('paymentId');
            $table->dropColumn('macAddress');
        });
    }
}
