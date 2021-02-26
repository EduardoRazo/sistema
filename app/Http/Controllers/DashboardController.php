<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller{

    public function __invoke(Request $request) {                                                   //utilizamos la funcion invoke porque el controlador solo tiene una función, una ventaja al escribir su ruta no tenemos que escribir el metodo
        
        $anio=date('Y');                                                                           //con el metodo date obtenemos la fecha actual solo en formato año 'Y' 

        //crear variable para guardar el query que traiga datos a nuestra grafica
        $ingresos=DB::table('ingresos as i')
        ->select(DB::raw('MONTH(i.fecha_hora) as mes'),                                            //utilizamos metodo "raw" para obtener solo el mes del campo
                    DB::raw('YEAR(i.fecha_hora) as anio'),
                    DB::raw('SUM(i.total) as total')) 
        ->whereYear('i.fecha_hora',$anio)                                                          //filtramos para que solo nos muestre los meses y totales del año actual
        ->groupBy(DB::raw('MONTH(i.fecha_hora)'),DB::raw('YEAR(i.fecha_hora)'))                    //agrupamos por mes y por el año
        ->where('i.estado','=','Registrado')
        ->get();                                                                                   //metodo para obtener todos los registros

        //crear variable para guardar el query que traiga datos a nuestra grafica
        $ventas=DB::table('ventas as v')
        ->select(DB::raw('MONTH(v.fecha_hora) as mes'),                                            //utilizamos metodo "raw" para obtener solo el mes del campo
                 DB::raw('YEAR(v.fecha_hora) as anio'),
                 DB::raw('SUM(v.total) as total')) 
        ->whereYear('v.fecha_hora',$anio)                                                          //filtramos para que solo nos muestre los meses y totales del año actual
        ->groupBy(DB::raw('MONTH(v.fecha_hora)'),DB::raw('YEAR(v.fecha_hora)'))                    //agrupamos por mes y por el año
        ->where('v.estado','=','Registrado')
        ->get();                                                                                   //metodo para obtener todos los registros

            return ['ingresos' =>$ingresos, 'ventas' =>$ventas, 'anio' => $anio];                  //regresamos en encabezados las variables
        
    }

}
