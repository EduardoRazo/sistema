<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcliente')->unsigned();                                                                            //inidico indice foraneo
            $table->foreign('idcliente')->references('id')->on('personas');                                                      //agrego su refernecia al indice foraneo
            $table->integer('idusuario')->unsigned();                                                                            //agregar indice fk
            $table->foreign('idusuario')->references('id')->on('users');                                                         //agregar refernecia fk
            $table->string('tipo_comprobante',20);
            $table->string('serie_comprobante',7)->nullable();
            $table->string('num_comprobante',10);
            $table->dateTime('fecha_hora');
            $table->decimal('impuesto',4,2); //max carcateres = 4, precision = 2
            $table->decimal('total', 11,2);
            $table->string('estado',20);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
