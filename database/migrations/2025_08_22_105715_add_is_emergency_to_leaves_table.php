<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEmergencyToLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->boolean('is_emergency')->default(false)->after('rejected_reason');
            // change `reason` to the column after which you want this field
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('is_emergency');
        });
    }
}
