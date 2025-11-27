<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('replacement_shifts', function (Blueprint $table) {
            // If you want just a normal nullable integer column
            $table->unsignedBigInteger('leave_id')->nullable()->after('id');

            // If you want foreign key relation with leaves table
            // $table->foreign('leave_id')->references('id')->on('leaves')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replacement_shifts', function (Blueprint $table) {
            // Drop foreign first if you created it
            // $table->dropForeign(['leave_id']);
            $table->dropColumn('leave_id');
        });
    }
};
