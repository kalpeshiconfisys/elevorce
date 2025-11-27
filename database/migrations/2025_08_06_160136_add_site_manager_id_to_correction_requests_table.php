<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiteManagerIdToCorrectionRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('site_manager_id')->nullable()->after('employee_id');
            
        });
    }

    public function down()
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->dropColumn('site_manager_id');
        });
    }
}
