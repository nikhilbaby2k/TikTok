<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTikTokAttendanceAddFirstIn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tik_tok_attendance', function (Blueprint $table) {
            $table->integer('first_in')->default(0)->commnent = " For a particular day that Employee's First In = 1 ; For all other records = 0 for that day ";
            $table->integer('last_out')->default(0)->commnent = " For a particular day that Employee's Last Out = 1 ; For all other records = 0 for that day ";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tik_tok_attendance', function (Blueprint $table) {
            $table->dropColumn('first_in');
            $table->dropColumn('last_out');
        });
    }
}
