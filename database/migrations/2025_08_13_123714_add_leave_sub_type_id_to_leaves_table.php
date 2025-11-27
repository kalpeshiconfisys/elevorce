<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->unsignedBigInteger('leave_sub_type_id')->nullable()->after('leave_type_id');

            // If you have a foreign key
            $table->foreign('leave_sub_type_id')->references('id')->on('leave_sub_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['leave_sub_type_id']);
            $table->dropColumn('leave_sub_type_id');
        });
    }
};
