<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model{
    //Declarar para recibir y enviar campos de la tabla
    protected $fillable=['type','notifiable_id', 'notifiable_type','data'];
}
