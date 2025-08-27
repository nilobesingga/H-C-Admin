<?php

namespace App\Events;

use App\Models\ChangeRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ClientRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChangeRequest $request;

    /**
     * Create a new event instance.
     */
    public function __construct(ChangeRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('client-requests'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
             'id' => $this->request->id ?? null,
            'model_type' => $this->request->model_type ?? 'Contact',
            'model_id' => $this->request->model_id ?? 'N/A',
            'description' => $this->request->field_name ?? 'No description provided.',
            'current_value' => $this->request->current_value ?? null,
            'proposed_value' => $this->request->proposed_value ?? null,
            'reason' => $this->request->reason ?? null,
            'status' => $this->request->status ?? null,
            'requested_by' => $this->request->created_by ?? 'Client',
            'created_at' => $this->request->created_at ?? now(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'request.created';
    }
}
