<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model{
    //Agregar todos los campos que van a enviar y recibir valores de la bd a la App laravel
    protected $fillable =[
        'idcliente', 
        'idusuario',
        'tipo_comprobante',
        'serie_comprobante',
        'num_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'estado'
    ];
}
