<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneEmailImageToSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::table('sites', function (Blueprint $table) {
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->string('image')->nullable();
    });
}

public function down(): void
{
    Schema::table('sites', function (Blueprint $table) {
        $table->dropColumn(['phone', 'email', 'image']);
    });
}
}
