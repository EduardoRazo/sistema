<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyAdmin extends Notification
{
    use Queueable;
    public $GlobalDatos; //creamos variable global

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Array $datos) //Declaramos un array aqui en el constructor como parametro $datos
    {
      $this->GlobalDatos=$datos; //Almacenamos "Array $datos" de la instancia del constructor en esta variable  para poder utilizar la variable al enviar informacion a los canales seleccionados
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    //creamos funcion
    public function toDatabase() {
        return [  //retornamos un parametro que va a contener todo el array almacenado en nuestra variable GlobaldDatos
            'datos' => $this->GlobalDatos
        ];
    }

    //es metodo es para que en el momento le aparesca la notificacion
    public function toBroadcast($notifiable) {
        return [  //retornamos un parametro que va a contener todo el array almacenado en nuestra variable GlobaldDatos EN UN ARRAY DATA
            'data' => [
                'datos' => $this->GlobalDatos
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
