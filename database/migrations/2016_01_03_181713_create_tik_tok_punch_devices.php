<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTikTokPunchDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tik_tok_punch_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device_name')->comment = "Front Door, Exit Door";
            $table->string('device_type')->comment = "Biometric, RFID";

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tik_tok_punch_devices');
    }
}
