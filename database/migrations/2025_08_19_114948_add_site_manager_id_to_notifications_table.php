<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiteManagerIdToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('site_manager_id')->nullable()->after('employee_id');

            // if you have a site_managers table and want relation:
            // $table->foreign('site_manager_id')->references('id')->on('site_managers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('site_manager_id');
        });
    }

}
