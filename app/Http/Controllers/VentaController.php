<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;      //importarmos para trabajar con transacciones bd
use Carbon\Carbon;                      //importarmos para trabajar con fechas
use App\Venta;                          //importarmos
use App\DetalleVenta;                   //importarmos
use App\User;                           //importar
use App\Notifications\NotifyAdmin;      //importar

class VentaController extends Controller{

    public function index(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
            ->join('users','ventas.idusuario','=','users.id')
            ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
            'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
            'ventas.estado','personas.nombre','users.usuario')
            ->orderBy('ventas.id', 'desc')->paginate(3);
        }
        else{
            $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
            ->join('users','ventas.idusuario','=','users.id')
            ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
            'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
            'ventas.estado','personas.nombre','users.usuario')
            ->where('ventas.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('ventas.id', 'desc')->paginate(3);
        }
        
        return [
            'pagination' => [
                'total'        => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page'     => $ventas->perPage(),
                'last_page'    => $ventas->lastPage(),
                'from'         => $ventas->firstItem(),
                'to'           => $ventas->lastItem(),
            ],
            'ventas' => $ventas
        ];
    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $venta = Venta::join('personas','ventas.idcliente','=','personas.id')                       // se hace join para obtener quien es el responsable del ingreso
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
        'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
        'ventas.estado','personas.nombre','users.usuario')
        ->where('ventas.id','=',$id)
        ->orderBy('ventas.id', 'desc')->take(1)->get();                                                       //con el metodo take solo tomamos uno en caso de existir varios
        
        return ['venta' => $venta];
    }

    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetalleVenta::join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->select('detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento',
        'articulos.nombre as articulo')
        ->where('detalle_ventas.idventa','=',$id)
        ->orderBy('detalle_ventas.id', 'desc')->get();                                                                 //get para obtener todos los detalles
        
        return ['detalles' => $detalles];
    }
    public function pdf(Request $request, $id) {
        $venta = Venta::join('personas','ventas.idcliente', '=', 'personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
        'ventas.num_comprobante','ventas.created_at','ventas.impuesto','ventas.total',
        'ventas.estado','personas.nombre','personas.tipo_documento','personas.num_documento',
        'personas.direccion','personas.email',
        'personas.telefono','users.usuario')
        ->where('ventas.id','=',$id)
        ->orderBy('ventas.id','desc')->take(1)->get();

        $detalles = DetalleVenta::join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->select('detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento',
        'articulos.nombre as articulo')
        ->where('detalle_ventas.idventa','=',$id)
        ->orderBy('detalle_ventas.id','desc')->get();

        //para mostrar el numero de documento
        $numventa=Venta::select('num_comprobante')->where('id',$id)->get(); //aqui filtramos con le where para que el id que tengo sea igual al que recibimos
        
        $pdf = \PDF::loadview('pdf.venta',['venta'=>$venta, 'detalles'=>$detalles]); //en en encabezado 'venta' le enviamos esa variable asi como el encabezado 'detalles'
        return $pdf->download('venta-'.$numventa[0]->num_comprobante.'.pdf'); //Retornamos la variable pdf donde nombramos la doc venta- mas el num_comprobante
    }

    public function store(Request $request){
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $mytime= Carbon::now('America/Lima');

            $venta = new Venta();
            $venta->idcliente = $request->idcliente;
            $venta->idusuario = \Auth::user()->id;
            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->serie_comprobante = $request->serie_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $mytime->toDateString();                                                              //envio fecha y ahora actual
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->estado = 'Registrado';
            $venta->save();

            $detalles = $request->data;//Array de detalles
            //Recorro todos los elementos

            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];          
                $detalle->descuento = $det['descuento'];          
                $detalle->save();
            } 
            
            $fechaActual = date('Y-m-d'); //obtener fecha actual en el formato Ymd y solo mostrar las ventas y compras de ese dia en especifico
            $numVentas = DB::table('ventas')->whereDate('created_at',$fechaActual)->count(); //variable con consulta para traer el numero de ventas de ese dia en especifico
            $numIngresos = DB::table('ingresos')->whereDate('created_at',$fechaActual)->count();  //variable con consulta para traer el numero de ingresos de ese dia en especifico
            
            //metemos estas 2 consultas en un arreglo (las notificaciones solo reciben arreglos)
            $arregloDatos = [
                'ventas' => [
                    'numero' => $numVentas,
                    'msj' => 'Ventas'
                ],
                'ingresos' => [
                    'numero' => $numIngresos,
                    'msj' => 'Ingresos'
                ]
            ];

            $allUsers = User::all(); //variable con consulta para obtener todos los usuarios a notificar
            
            //Recorremos con foreach y notificamos
            foreach ($allUsers as $notificar){
                User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos)); //consulta con findOrfail(parametro) y notifica con notify(instancia)
            }

            
            DB::commit();
            return [
                'id' => $venta->id
            ];//retornar el id de la venta que hemos generado
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function desactivar(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = Venta::findOrFail($request->id);
        $venta->estado = 'Anulado';
        $venta->save();
    }

}
