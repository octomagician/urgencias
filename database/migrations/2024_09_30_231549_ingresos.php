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
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pacientes_id');
            $table->unsignedBigInteger('diagnostico_id');
            $table->unsignedBigInteger('camas_id');
            $table->unsignedBigInteger('personal_id');
            $table->date('fecha_ingreso');
            $table->text('motivo_ingreso');
            $table->date('fecha_alta')->nullable();
            $table->timestamps();

            $table->foreign('pacientes_id')->references('id')->on('pacientes');
            $table->foreign('diagnostico_id')->references('id')->on('diagnosticos');
            $table->foreign('camas_id')->references('id')->on('camas');
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
        Schema::dropIfExists('ingresos');
    }
};
