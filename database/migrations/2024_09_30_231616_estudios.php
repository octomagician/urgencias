<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipos_de_estudios_id');
            $table->unsignedBigInteger('personal_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tipos_de_estudios_id')->references('id')->on('tipos_de_estudios');
            $table->foreign('personal_id')->references('id')->on('personal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudios');
    }
};
