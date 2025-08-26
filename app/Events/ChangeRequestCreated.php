<?php

namespace App\Events;

use App\Models\RequestModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChangeRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public RequestModel $request;

    /**
     * Create a new event instance.
     */
    public function __construct(RequestModel $request)
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
            new Channel('requests'),
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
            'category' => $this->request->category ?? 'Change Request',
            'request_no' => $this->request->request_no ?? 'N/A',
            'description' => $this->request->description ?? 'No details provided.',
            'type' => $this->request->type ?? 'change_request',
            'company_id' => $this->request->company_id ?? null,
            'contact_id' => $this->request->contact_id ?? null,
            'created_by' => $this->request->created_by ?? 'User',
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

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ChangeRequest broadcast failed', [
            'exception' => $exception->getMessage(),
            'request_id' => $this->request->id ?? 'unknown'
        ]);
    }
}
