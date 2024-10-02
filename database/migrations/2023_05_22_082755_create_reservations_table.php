<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->nullable()->references('id')->on('listings');
            $table->foreignId('guest_id')->nullable()->references('id')->on('guests');
            $table->string('idRes');
            $table->date('dateCheckin');
            $table->date('dateCheckout');
            $table->string('idListing')->nullable();
            $table->string('idGuest')->nullable();
            $table->string('confCode');
            $table->decimal('amount', $precision = 8, $scale = 2);
            $table->string('statut');
            $table->string('debit_card_statut')->nullable();
            $table->boolean('servicesOrdered')->default(false);
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->string('prename')->nullable();
            $table->string('platform');
            $table->string('email')->nullable();
            $table->date('date_ajout');
            $table->integer('smsCount')->default(0);
            $table->integer('instructionsCount')->default(0);
            $table->string('identity_document')->nullable();
            $table->string('checking_time')->nullable();
            $table->string('checkout_time')->nullable();
            $table->string('name_adress')->nullable();
            $table->integer('nombre_personne')->nullable();
            $table->integer('nombre_lits')->nullable();
            $table->integer('beds_to_prepare')->nullable();
            $table->integer('double_beds')->default(0);
            $table->integer('single_beds')->default(0);
            $table->decimal('insurance', $precision = 8, $scale = 2)->nullable();
            $table->longText('object')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
