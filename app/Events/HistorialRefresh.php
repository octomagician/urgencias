<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HistorialRefresh implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action; // 'created', 'updated', 'deleted'
    public $historial; // Datos del registro afectado

    public function __construct($action, $historial)
    {
        $this->action = $action;
        $this->historial = $historial;
    }

    public function broadcastOn()
    {
        // Usamos un canal público para que todos los clientes puedan recibir las actualizaciones
        \Log::info("Broadcasting to channel: historial", [
            'data' => $this->historial,
            'user' => auth()->user()->id ?? null
        ]);
        return new Channel('historial');
        //return new Channel('public-test-channel');
    }

    public function broadcastWith()
{
    return [
        'action' => $this->action,
        'historial' => [
            'id' => $this->historial->id,
            // otros campos importantes
        ],
        'timestamp' => now()->toDateTimeString()
    ];
}

    public function broadcastAs() //the historial name
    {
        return 'historial.refresh';
    }
}