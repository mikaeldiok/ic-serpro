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
        Schema::create('lsp_tuks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("lsp_id");
            $table->integer("order")->nullable();
            $table->string("tuk_code")->nullable();
            $table->string("type")->nullable();
            $table->text("name")->nullable();
            $table->text("address")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lsp_tuks');
    }
};