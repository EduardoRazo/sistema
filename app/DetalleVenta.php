<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model{
    //Agregar todos los campos que van a enviar y recibir valores de la bd a la App laravel
    protected $table = 'detalle_ventas';                                                           //para indicar que esta es mi tabla y no el nombre por default
    protected $fillable = [
        'idventa', 
        'idarticulo',
        'cantidad',
        'precio',
        'descuento'
    ];
    public $timestamps = false;                                                                    //cambiamos el estado a false ya que no utilizamos timestamps
}
