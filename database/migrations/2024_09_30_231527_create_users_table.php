<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; 

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_photo_path')->nullable();
            $table->unsignedBigInteger('persona_id')->nullable();
            $table->unsignedBigInteger('tipo_id')->nullable();
            $table->string('verification_code')->nullable(); // Código de verificación
            $table->timestamp('verification_code_expires_at')->nullable(); // Fecha de expiración del código
            $table->timestamps();
            $table->softDeletes();
            $table->rememberToken();
            
            $table->foreign('persona_id')->references('id')->on('personas');
            $table->foreign('tipo_id')->references('id')->on('tipos_de_personal');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
