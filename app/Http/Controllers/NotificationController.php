<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use Auth;

class NotificationController extends Controller{
    
    public function get() {
        //return Notification::all(); //regresamos todas las notificaciones
        $unreadNotifications = Auth::user()->unreadNotifications;//Antes del return mostrar las notificaciones no leidas
        $fechaActual = date('Y-m-d');
        foreach ($unreadNotifications as $notification) {
            //si no coincide con el dia actual lo tomare como leido
            if ($fechaActual != $notification->created_at->toDateString()) {
                $notification->markAsRead();
            }
        }

        return Auth::user()->unreadNotifications; //vamo a retornar las notificaciones no leidas del ususario logeado
    }
}
