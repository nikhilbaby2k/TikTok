<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTikTokAttendanceAddWorkTimeProcessedStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tik_tok_attendance', function (Blueprint $table) {
            $table->integer('work_time_processed_status')->default(0)->comment = "1: processed / 0: Not-processed";
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
            $table->dropColumn('work_time_processed_status');
        });
    }
}
