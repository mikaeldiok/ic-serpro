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
        Schema::create('lsp_skema_units', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("skema_id");
            $table->integer("order");
            $table->string("unit_code")->nullable();
            $table->text("name")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lsp_skema_units');
    }
};
