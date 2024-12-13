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
        Schema::create('lsps', function (Blueprint $table) {
            $table->id();
            $table->string("encrypted_id",255)->nullable();
            $table->text("name")->nullable();
            $table->string("sk_lisensi",100)->nullable();
            $table->string("no_lisensi",100)->nullable();
            $table->string("jenis",100)->nullable();
            $table->string("no_telp",100)->nullable();
            $table->string("no_hp",100)->nullable();
            $table->string("no_fax",100)->nullable();
            $table->string("email",100)->nullable();
            $table->string("website",100)->nullable();
            $table->string("masa_berlaku_sert",100)->nullable();
            $table->string("status_lisensi", 50)->nullable();
            $table->text("alamat")->nullable();
            $table->string("logo_image",255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lsps');
    }
};
