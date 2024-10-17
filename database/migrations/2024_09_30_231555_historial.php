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
        Schema::create('historial', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ingreso_id');
            $table->unsignedBigInteger('personal_id');

            $table->timestamp('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('presion', 10);
            $table->decimal('temperatura', 4, 2);
            $table->decimal('glucosa', 5, 2);
            $table->text('sintomatologia');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('ingreso_id')->references('id')->on('ingresos');
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
        Schema::dropIfExists('historial');
    }
};
