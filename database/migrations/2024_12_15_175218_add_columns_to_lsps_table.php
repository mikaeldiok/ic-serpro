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
        Schema::table('lsps', function (Blueprint $table) {
            $table->text("notes")->nullable();
            $table->string("subtype")->nullable();
            $table->string("pimpinan")->nullable();
            $table->integer("asesi_per_tahun")->nullable();
            $table->bigInteger("status_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lsps', function (Blueprint $table) {
            $table->dropColumn(["notes","subtype","pimpinan","asesi_per_tahun","status_id"]);
        });
    }
};
