<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarbonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carbon', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity');
            $table->enum('activityType', ['miles', 'fuel']);
            $table->enum('fuelType', [
                'motorGasoline',
                'diesel',
                'aviationGasoline',
                'jetFuel'
            ]);
            $table->enum('mode', [
                'dieselCar',
                'petrolCar',
                'anyCar',
                'taxi',
                'economyFlight',
                'businessFlight',
                'firstclassFlight',
                'anyFlight',
                'motorbike',
                'bus',
                'transitRail'
            ]);
            $table->enum('country', ['usa', 'gbr', 'def']);
            $table->timestamp('expires_at', 0);
            $table->float('carbon', 8, 2);
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
        Schema::dropIfExists('carbon');
    }
}
