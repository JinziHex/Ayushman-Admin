<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDaysToStaffLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_leave', function (Blueprint $table) {
            $table->string('start_day');
            $table->string('end_day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_leave', function (Blueprint $table) {
            $table->dropColumn('start_day');
            $table->dropColumn('end_day');
        });
    }
}
