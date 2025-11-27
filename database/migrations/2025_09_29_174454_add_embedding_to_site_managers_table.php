<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmbeddingToSiteManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_managers', function (Blueprint $table) {
             $table->longText('embedding')->nullable()->after('mobile_version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_managers', function (Blueprint $table) {
             $table->dropColumn('embedding');
        });
    }
}
